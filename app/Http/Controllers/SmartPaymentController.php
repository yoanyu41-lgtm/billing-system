<?php

namespace App\Http\Controllers;

use App\Services\KhqrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmartPaymentController extends Controller
{
    protected $khqrService;

    public function __construct(KhqrService $khqrService)
    {
        $this->khqrService = $khqrService;
    }

    /**
     * Show smart payment page with QR code and deep link buttons.
     */
    public function show(Request $request)
    {
        try {
            $encodedData = $request->get('data');
            
            if (!$encodedData) {
                abort(404, 'Payment data not found');
            }
            
            $data = json_decode(base64_decode($encodedData), true);
            
            // Check if payment link has expired
            if (isset($data['expires_at']) && $data['expires_at'] < now()->timestamp) {
                return view('payment.expired');
            }
            
            $payload = $data['payload'];
            $amount = $data['amount'];
            $currency = $data['currency'];
            $reference = $data['reference'] ?? null;
            
            // Generate QR code image
            $qrImagePath = $this->khqrService->generateQrCodeImage($payload);
            
            // Generate deep links for all supported banks
            $deepLinks = $this->khqrService->generateDeepLinks($payload, $amount, $currency);
            
            // Parse QR code to check if it's personal or merchant
            $tags = $this->khqrService->parsePayload($payload);
            $isPersonalQr = isset($tags['30']);
            
            return view('payment.smart', [
                'qrImageUrl' => $qrImagePath ? asset('storage/' . $qrImagePath) : null,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'deepLinks' => $deepLinks,
                'isPersonalQr' => $isPersonalQr,
                'payload' => $payload
            ]);
            
        } catch (\Exception $e) {
            Log::error('Smart payment page error: ' . $e->getMessage());
            abort(500, 'Unable to load payment page');
        }
    }
}
