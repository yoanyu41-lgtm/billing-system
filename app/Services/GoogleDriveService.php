<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private ?string $serviceAccountJson;
    private ?string $folderId;

    public function __construct()
    {
        $this->serviceAccountJson = Setting::where('key', 'google_drive_service_account_json')->value('value');
        $this->folderId = Setting::where('key', 'google_drive_folder_id')->value('value');
    }

    /**
     * Get temporary Google Drive OAuth2 Access Token
     * Uses Cache to optimize token lifetime (1 hour validity, cached for 55 mins)
     */
    public function getAccessToken(): ?string
    {
        if (empty($this->serviceAccountJson)) {
            Log::warning('Google Drive Service Account JSON key is not configured.');
            return null;
        }

        return Cache::remember('google_drive_access_token', 3300, function () {
            try {
                $credentials = json_decode($this->serviceAccountJson, true);
                if (json_last_error() !== JSON_ERROR_NONE || !isset($credentials['private_key']) || !isset($credentials['client_email'])) {
                    Log::error('Invalid Google Service Account JSON structure.');
                    return null;
                }

                $privateKey = $credentials['private_key'];
                $clientEmail = $credentials['client_email'];

                // JWT Header
                $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
                
                // JWT Claim set
                $now = time();
                $claim = json_encode([
                    'iss' => $clientEmail,
                    'scope' => 'https://www.googleapis.com/auth/drive',
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'exp' => $now + 3600,
                    'iat' => $now
                ]);

                // Base64 Url Encoding
                $base64UrlHeader = $this->base64UrlEncode($header);
                $base64UrlClaim = $this->base64UrlEncode($claim);

                $signatureInput = $base64UrlHeader . "." . $base64UrlClaim;
                $signature = '';

                // Sign JWT with Private Key
                if (!openssl_sign($signatureInput, $signature, $privateKey, 'SHA256')) {
                    Log::error('Failed to sign Google Service Account JWT.');
                    return null;
                }

                $base64UrlSignature = $this->base64UrlEncode($signature);
                $jwt = $signatureInput . "." . $base64UrlSignature;

                // Request Access Token from Google OAuth Endpoint
                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ]);

                if ($response->successful()) {
                    return $response->json()['access_token'] ?? null;
                }

                Log::error('Google OAuth token request failed: ' . $response->body());
                return null;

            } catch (\Throwable $e) {
                Log::error('Google Drive authentication exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Upload database backup file to Google Drive using multipart upload format
     */
    public function uploadFile(string $filename, string $fileContent): bool
    {
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            Log::error('Failed to obtain Google Drive access token for upload.');
            return false;
        }

        try {
            $boundary = '-------314159265358979323846';
            $delimiter = "\r\n--" . $boundary . "\r\n";
            $closeDelimiter = "\r\n--" . $boundary . "--\r\n";

            $metadata = [
                'name' => $filename,
                'mimeType' => 'application/sql',
            ];

            if (!empty($this->folderId)) {
                $metadata['parents'] = [$this->folderId];
            }

            // Construct multipart/related HTTP request body
            $body = $delimiter
                . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
                . json_encode($metadata)
                . $delimiter
                . "Content-Type: application/sql\r\n\r\n"
                . $fileContent
                . $closeDelimiter;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'multipart/related; boundary=' . $boundary,
                'Content-Length' => strlen($body),
            ])->withBody($body, 'multipart/related')->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful()) {
                Log::info("Backup file '{$filename}' uploaded to Google Drive successfully.");
                return true;
            }

            Log::error("Failed to upload backup to Google Drive: " . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error("Google Drive upload exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Base64 URL Safe encoding helper
     */
    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
