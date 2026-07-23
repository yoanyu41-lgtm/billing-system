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



        // If the base payload is a personal P2P QR code (Tag 30), it does not support dynamic amount pre-filling.
        // We return the original base payload exactly as it is to guarantee 100% scanning success.
        if (isset($tags['30'])) {
            return trim($basePayload);
        }

        // Force initiation method to '12' (Dynamic QR) to allow pre-filled amounts.
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
}
