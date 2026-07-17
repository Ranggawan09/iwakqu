<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShopeePayService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $merchantId;
    protected string $storeId;
    protected string $privateKey;
    protected string $publicKey;
    protected string $baseUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->clientId = config('services.shopeepay.client_id', '');
        $this->clientSecret = config('services.shopeepay.client_secret', '');
        $this->merchantId = config('services.shopeepay.merchant_id', '');
        $this->storeId = config('services.shopeepay.external_store_id', '');
        $this->privateKey = config('services.shopeepay.private_key', '');
        $this->publicKey = config('services.shopeepay.shopeepay_public_key', '');
        $this->isProduction = (bool) config('services.shopeepay.is_production', false);

        $this->baseUrl = $this->isProduction
            ? 'https://api.snap.airpay.co.id'
            : 'https://api.snap.uat.airpay.co.id';
    }

    /**
     * Parse and load Private Key resource.
     */
    protected function getPrivateKeyResource()
    {
        $keyContent = $this->privateKey;
        if (empty($keyContent)) {
            Log::error('[ShopeePay] Private key is empty.');
            return null;
        }

        if (!str_contains($keyContent, '-----BEGIN')) {
            // Check if it's a file path
            if (file_exists($keyContent)) {
                $keyContent = file_get_contents($keyContent);
            } else {
                // Wrap raw RSA key if needed
                $keyContent = "-----BEGIN PRIVATE KEY-----\n" . wordwrap($keyContent, 64, "\n", true) . "\n-----END PRIVATE KEY-----";
            }
        }

        $res = openssl_pkey_get_private($keyContent);
        if (!$res) {
            Log::error('[ShopeePay] Failed to parse private key: ' . openssl_error_string());
        }
        return $res;
    }

    /**
     * Parse and load Public Key resource.
     */
    protected function getPublicKeyResource()
    {
        $keyContent = $this->publicKey;
        if (empty($keyContent)) {
            Log::error('[ShopeePay] Public key is empty.');
            return null;
        }

        if (!str_contains($keyContent, '-----BEGIN')) {
            if (file_exists($keyContent)) {
                $keyContent = file_get_contents($keyContent);
            } else {
                $keyContent = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($keyContent, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
            }
        }

        $res = openssl_pkey_get_public($keyContent);
        if (!$res) {
            Log::error('[ShopeePay] Failed to parse public key: ' . openssl_error_string());
        }
        return $res;
    }

    /**
     * Retrieve the access token, using cache when available.
     */
    public function getAccessToken(): ?string
    {
        return Cache::remember('shopeepay_b2b_access_token', 800, function () {
            return $this->requestAccessToken();
        });
    }

    /**
     * Fetch access token from ShopeePay API.
     */
    protected function requestAccessToken(): ?string
    {
        $timestamp = date('Y-m-d\TH:i:sP'); // ISO 8601
        $stringToSign = $this->clientId . '|' . $timestamp;

        $privKeyRes = $this->getPrivateKeyResource();
        if (!$privKeyRes) {
            return null;
        }

        if (!openssl_sign($stringToSign, $signature, $privKeyRes, OPENSSL_ALGO_SHA256)) {
            Log::error('[ShopeePay] RSA Signing failed.');
            return null;
        }

        $base64Signature = base64_encode($signature);
        $endpoint = $this->baseUrl . '/v1.0/access-token/b2b';
        
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-CLIENT-KEY' => $this->clientId,
                'X-TIMESTAMP'  => $timestamp,
                'X-SIGNATURE'  => $base64Signature,
            ])->post($endpoint, [
                'grantType' => 'client_credentials',
            ]);

            Log::info('[ShopeePay] Token request response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->successful()) {
                return $response->json('accessToken');
            }
        } catch (\Exception $e) {
            Log::error('[ShopeePay] Token request exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Generate Dynamic QRIS for MPM
     */
    public function generateQRIS($order): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            Log::error('[ShopeePay] Access token is missing.');
            return null;
        }

        $timestamp = date('Y-m-d\TH:i:sP');
        $endpointPath = '/v1.0/qr/qr-mpm-generate';
        $endpointUrl = $this->baseUrl . $endpointPath;

        // X-EXTERNAL-ID must be a unique numeric string <= 36 characters unique per day.
        $externalId = $order->id . time();
        $externalId = substr(preg_replace('/[^0-9]/', '', $externalId), 0, 36);

        $body = [
            'partnerReferenceNo' => 'IWAKQU-' . $order->id . '-' . time(),
            'amount' => [
                'value' => number_format((float) $order->total_price, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'merchantId' => $this->merchantId,
            'validityPeriod' => date('Y-m-d\TH:i:sP', time() + 1200), // Expiry 20 minutes
            'additionalInfo' => [
                'externalStoreId' => $this->storeId,
                'convenienceFeeIndicator' => '01',
            ]
        ];

        // Minify body for signature calculation (no space/indentation)
        $minifiedBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        
        // Lowercase(HexEncode(SHA256(MinifiedBody)))
        $bodyHash = strtolower(hash('sha256', $minifiedBody));

        // stringToSign: HTTPMethod + ":" + EndpointURL + ":" + AccessToken + ":" + bodyHash + ":" + X-TIMESTAMP
        $stringToSign = "POST:{$endpointPath}:{$token}:{$bodyHash}:{$timestamp}";

        // HMAC-SHA512 signature using clientSecret
        $signature = base64_encode(hash_hmac('sha512', $stringToSign, $this->clientSecret, true));

        try {
            $response = Http::withHeaders([
                'Content-Type'   => 'application/json',
                'X-TIMESTAMP'    => $timestamp,
                'Authorization'  => 'Bearer ' . $token,
                'X-PARTNER-ID'   => $this->clientId,
                'X-EXTERNAL-ID'  => $externalId,
                'CHANNEL-ID'     => 'PC',
                'X-SIGNATURE'    => $signature,
            ])->withBody($minifiedBody, 'application/json')->post($endpointUrl);

            Log::info('[ShopeePay] QRIS request response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->successful() && $response->json('responseCode') === '2004700') {
                return [
                    'qr_content' => $response->json('qrContent'),
                    'qr_url'     => $response->json('qrUrl'),
                ];
            }
        } catch (\Exception $e) {
            Log::error('[ShopeePay] QRIS request exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Verify Callback (Webhook) Signature using ShopeePay Public Key
     */
    public function verifyCallbackSignature(string $callbackUrl, string $bodyPayload, string $timestamp, string $signatureToVerify): bool
    {
        $pubKeyRes = $this->getPublicKeyResource();
        if (!$pubKeyRes) {
            return false;
        }

        // stringToSign: POST:callbackURL:Hex(SHA256(requestBody)):timestamp
        $bodyHash = strtolower(hash('sha256', $bodyPayload));
        $stringToSign = "POST:{$callbackUrl}:{$bodyHash}:{$timestamp}";

        $signatureBytes = base64_decode($signatureToVerify);
        $result = openssl_verify($stringToSign, $signatureBytes, $pubKeyRes, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }
}
