<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class KhqrService
{
    /**
     * Parse EMVCo / KHQR payload into tag-value pairs.
     *
     * @param string $payload
     * @return array
     */
    public function parsePayload(string $payload): array
    {
        $tags = [];
        $i = 0;
        $len = mb_strlen($payload, 'UTF-8');
        
        while ($i < $len) {
            if ($i + 4 > $len) {
                break;
            }
            $tag = mb_substr($payload, $i, 2, 'UTF-8');
            $length = (int)mb_substr($payload, $i + 2, 2, 'UTF-8');
            $value = mb_substr($payload, $i + 4, $length, 'UTF-8');
            
            $tags[$tag] = $value;
            $i += 4 + $length;
            
            if ($tag === '63') {
                break;
            }
        }
        
        return $tags;
    }

    /**
     * Generate dynamic KHQR payload with specific amount and currency.
     *
     * @param string|null $basePayload
     * @param float $amount
     * @param string $currency
     * @return string|null
     */
    public function generatePayload(?string $basePayload, float $amount, string $currency = 'USD'): ?string
    {
        if (empty($basePayload)) {
            return null;
        }
        $tags = $this->parsePayload(trim($basePayload));
        if (empty($tags)) {
            return null;
        }

        // Check if this is a merchant QR that supports dynamic amount
        $isMerchantQr = isset($tags['26']) || isset($tags['27']) || isset($tags['28']) || isset($tags['29']);
        $isPersonalQr = isset($tags['30']);
        
        // If the base payload is a personal P2P QR code (Tag 30), it does not support dynamic amount pre-filling.
        // We return the original base payload exactly as it is to guarantee 100% scanning success.
        if ($isPersonalQr || !$isMerchantQr) {
            Log::warning('QR Code ប្រភេទនេះមិនគាំទ្រចំនួនទឹកប្រាក់ស្វ័យប្រវត្តិទេ។ កំពុងប្រើ QR Code ដើម។');
            return trim($basePayload);
        }

        // Check current initiation method
        $currentInitiationMethod = $tags['01'] ?? '11';
        
        // Only force to dynamic if it's already '12' or if it's a merchant QR
        if ($currentInitiationMethod === '11') {
            // Static QR - some banks may not support dynamic conversion
            Log::info('QR Code នេះជាប្រភេទ Static (01=11)។ ធនាគារខ្លះប្រហែលជាមិនទទួលយកការប្តូរទៅ Dynamic។');
        }
        
        // Set to Dynamic QR
        $tags['01'] = '12';

        $currencyCode = (strtoupper($currency) === 'KHR') ? '116' : '840';
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        if ($currencyCode === '116') {
            $finalAmount = (strtoupper($currency) === 'KHR') ? $amount : round($amount * $exchangeRate);
            $tags['54'] = (string)round($finalAmount);
        } else {
            $finalAmount = $amount;
            $tags['54'] = number_format($finalAmount, 2, '.', '');
        }
        $tags['53'] = $currencyCode;


        // Dynamically add Wing-specific Tag 99 with current and future expiration timestamps (24h)
        if (strpos($basePayload, 'wing_khqr@wing') !== false) {
            $nowMs = round(microtime(true) * 1000);
            $expireMs = $nowMs + 24 * 60 * 60 * 1000; // 24 hours later
            $tags['99'] = "0013{$nowMs}0113{$expireMs}";
        }

        // Reconstruct EMVCo string (preserve original tag order, placing 54 right after 53)
        $orderedTags = [];
        foreach ($tags as $tag => $value) {
            if ($tag === '54') {
                continue;
            }
            $orderedTags[$tag] = $value;
            if ($tag === '53' && isset($tags['54'])) {
                $orderedTags['54'] = $tags['54'];
            }
        }
        if (isset($tags['54']) && !isset($orderedTags['54'])) {
            $orderedTags['54'] = $tags['54'];
        }
        
        $reconstructed = '';
        foreach ($orderedTags as $tag => $value) {
            if ($tag == '63') {
                continue; // Skip CRC tag for now
            }
            $reconstructed .= $tag . str_pad(mb_strlen($value, 'UTF-8'), 2, '0', STR_PAD_LEFT) . $value;
        }


        // Append tag 63 prefix for CRC
        $reconstructed .= '6304';

        // Calculate and append CRC16
        $crc = $this->calculateCRC16($reconstructed);
        
        return $reconstructed . $crc;
    }


    /**
     * Calculate CRC16 checksum (CCITT-FALSE).
     *
     * @param string $payload
     * @return string
     */
    public function calculateCRC16(string $payload): string
    {
        $crc = 0xFFFF;
        for ($c = 0; $c < strlen($payload); $c++) {
            $crc ^= ord($payload[$c]) << 8;
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        return sprintf('%04X', $crc);
    }

    /**
     * Generate QR code image from payload using public API, save to local storage, and return relative path.
     *
     * @param string $payload
     * @return string|null
     */
    public function generateQrCodeImage(string $payload): ?string
    {
        try {
            $url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($payload);
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $imageContent = $response->body();
                
                // Create directory if not exists
                if (!Storage::disk('public')->exists('temp_qrs')) {
                    Storage::disk('public')->makeDirectory('temp_qrs');
                }
                
                $filename = 'temp_qrs/qr_' . uniqid() . '.png';
                Storage::disk('public')->put($filename, $imageContent);
                
                return $filename;
            }
        } catch (\Throwable $e) {
            Log::error('Failed to generate dynamic QR code image: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Generate deep link for mobile banking apps with pre-filled amount.
     * This works for Personal QR codes where dynamic QR is not supported.
     *
     * @param string $payload KHQR payload
     * @param float $amount Amount in original currency
     * @param string $currency Currency code (USD or KHR)
     * @return array Array containing deep links for different banks
     */
    public function generateDeepLinks(string $payload, float $amount, string $currency = 'USD'): array
    {
        $tags = $this->parsePayload($payload);
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
        
        // Convert amount based on currency
        $amountUSD = ($currency === 'KHR') ? round($amount / $exchangeRate, 2) : $amount;
        $amountKHR = ($currency === 'USD') ? round($amount * $exchangeRate) : $amount;
        
        $deepLinks = [];
        
        // ABA Bank Deep Link
        $deepLinks['aba'] = [
            'name' => 'ABA Bank',
            'url' => "aba://payment?qr=" . urlencode($payload) . "&amount=" . $amountUSD,
            'app_url' => "https://www.ababank.com/download",
            'supported' => true
        ];
        
        // Wing Bank Deep Link
        if (strpos($payload, 'wing') !== false || isset($tags['30'])) {
            $deepLinks['wing'] = [
                'name' => 'Wing Bank',
                'url' => "wing://scan?qr=" . urlencode($payload) . "&amount=" . $amountKHR,
                'app_url' => "https://www.wingmoney.com/download",
                'supported' => true
            ];
        }
        
        // ACLEDA Bank Deep Link
        $deepLinks['acleda'] = [
            'name' => 'ACLEDA Bank',
            'url' => "acledaunity://khqr?data=" . urlencode($payload) . "&amount=" . $amountUSD,
            'app_url' => "https://www.acledabank.com.kh/mobile",
            'supported' => true
        ];
        
        // Sathapana Bank Deep Link
        $deepLinks['sathapana'] = [
            'name' => 'Sathapana Bank',
            'url' => "sathapana://pay?qr=" . urlencode($payload) . "&amount=" . $amountKHR,
            'app_url' => "https://www.sathapana.com.kh/mobile",
            'supported' => true
        ];
        
        // Phillip Bank Deep Link
        $deepLinks['phillip'] = [
            'name' => 'Phillip Bank',
            'url' => "phillipbank://payment?qr=" . urlencode($payload) . "&amount=" . $amountUSD,
            'app_url' => "https://www.phillipbank.com.kh/mobile",
            'supported' => true
        ];
        
        // Generic KHQR Deep Link (Universal - works with most KHQR-compatible apps)
        $deepLinks['universal'] = [
            'name' => 'Universal KHQR',
            'url' => "khqr://pay?qr=" . urlencode($payload) . "&amount=" . $amountUSD . "&currency=" . $currency,
            'app_url' => null,
            'supported' => true
        ];
        
        return $deepLinks;
    }

    /**
     * Generate payment page URL with deep link support.
     * This creates a smart payment page that shows QR code and deep link buttons.
     *
     * @param string $payload KHQR payload
     * @param float $amount Amount
     * @param string $currency Currency
     * @param string $reference Payment reference/invoice number
     * @return string URL to payment page
     */
    public function generateSmartPaymentUrl(string $payload, float $amount, string $currency, string $reference): string
    {
        $data = [
            'payload' => $payload,
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'expires_at' => now()->addHours(24)->timestamp
        ];
        
        // Encode data for URL
        $encodedData = base64_encode(json_encode($data));
        
        return route('payment.smart', ['data' => $encodedData]);
    }
}
