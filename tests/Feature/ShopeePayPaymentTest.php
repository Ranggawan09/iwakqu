<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Services\ShopeePayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class ShopeePayPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set mock config for ShopeePay
        Config::set('services.shopeepay.client_id', 'test_client_id');
        Config::set('services.shopeepay.client_secret', 'test_client_secret');
        Config::set('services.shopeepay.merchant_id', 'test_merchant_id');
        Config::set('services.shopeepay.external_store_id', 'test_store_id');
        
        $privateKey = "-----BEGIN PRIVATE KEY-----\n" .
            "MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAMQNkr6mE4mq6A7i\n" .
            "NCZkiJLkrUyTQBvwFLUmuSy8+4EoQBXQeDY9dr44Yi1K3wavOmjcG5KDXChQGfa7\n" .
            "4bw/msEVHdm0xd6yemMYDkv4m1Fij8kNsZXqfhei8xzZZFsGdqP2w3bdPDErk3z6\n" .
            "XieAEIr+edKr2KWqpXTmhctI4AS9AgMBAAECgYEAp6bfxS6p6IEA/rFLXUV9UPhC\n" .
            "hggpu3pbium5UlutSy6LVtw14FRBNbtroUW0YLf53+/RmEHCzippeYgDKoLNpy5J\n" .
            "WPmNCg7RMfsGgZX1lPO99F2LXLFVWmOctxufyifJuUiSdHwal9rjjaAWxxMEGTZD\n" .
            "N2aE+SjwubxqzrEuy7UCQQDl5VIiqi+IbpOMndojkT4tzmi8QR/BXDdMT24KatFr\n" .
            "d5/VTukJ0fntQUIeFFvArWyYk+3SrCQYcnDJvOWB/SYnAkEA2lCANlfWHsDJwZEe\n" .
            "6u/skIZ8cfIkzYXAKFfQxiU7DrKUrmLP225mc2FccOBXEfTFOwawRv+o5Y9OzUcR\n" .
            "TynQewJBAJCEfgOIlGThjiOBP5XIQhwtey2MitfUjnaMIBKwX4F9K56+AkTIGKKK\n" .
            "uXOLPLp8yp2HsKMUz4QGvNw0wNncN1UCQDLyYllMOj3HA85WTX7KKsy3dccpmQkV\n" .
            "U3iWtbPn8FZHuobPrG4q32HBsM7uq6MXGgfiUbTf6MxZmywwj4uH2I0CQQC00kfN\n" .
            "HpGjcky1eHlujL2/DXJXQMOwDzVaN4TZ92bBbYioVE2EkTZg5LZXO1YOEOvUHBlM\n" .
            "FOHMR6t53FyAl7vL\n" .
            "-----END PRIVATE KEY-----";

        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
            "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEDZK+phOJqugO4jQmZIiS5K1M\n" .
            "k0Ab8BS1JrksvPuBKEAV0Hg2PXa+OGItSt8Grzpo3BuSg1woUBn2u+G8P5rBFR3Z\n" .
            "tMXesnpjGA5L+JtRYo/JDbGV6n4XovMc2WRbBnaj9sN23TwxK5N8+l4ngBCK/nnS\n" .
            "q9ilqqV05oXLSOAEvQIDAQAB\n" .
            "-----END PUBLIC KEY-----";

        Config::set('services.shopeepay.private_key', $privateKey);
        Config::set('services.shopeepay.shopeepay_public_key', $publicKey);
    }

    /**
     * Test signature verification helper.
     */
    public function test_signature_verification_flow()
    {
        $shopeePayService = new ShopeePayService();

        $callbackUrl = 'https://example.com/api/shopeepay/callback';
        $body = json_encode(['status' => 'success']);
        $timestamp = date('Y-m-d\TH:i:sP');

        // stringToSign: POST:callbackURL:Hex(SHA256(requestBody)):timestamp
        $bodyHash = strtolower(hash('sha256', $body));
        $stringToSign = "POST:{$callbackUrl}:{$bodyHash}:{$timestamp}";

        // Sign using our mock private key (which mimics ShopeePay signing it)
        $privKey = openssl_pkey_get_private(config('services.shopeepay.private_key'));
        openssl_sign($stringToSign, $signatureBytes, $privKey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signatureBytes);

        // Verify signature using service (which uses public key config)
        $isValid = $shopeePayService->verifyCallbackSignature($callbackUrl, $body, $timestamp, $signature);

        $this->assertTrue($isValid);
    }

    /**
     * Test QRIS creation flow with mocked HTTP responses.
     */
    public function test_generate_qris_successfully()
    {
        Http::fake([
            '*/v1.0/access-token/b2b' => Http::response([
                'responseCode' => '2007300',
                'responseMessage' => 'Successful',
                'accessToken' => 'mocked_b2b_access_token',
                'tokenType' => 'Bearer',
                'expiresIn' => '900'
            ], 200),
            '*/v1.0/qr/qr-mpm-generate' => Http::response([
                'responseCode' => '2004700',
                'responseMessage' => 'Successful',
                'qrContent' => '00020101021226540016ID...',
                'qrUrl' => 'https://example.com/qr.png',
                'additionalInfo' => [
                    'storeName' => 'IwakQu Store'
                ]
            ], 200)
        ]);

        $order = new Order();
        $order->id = 123;
        $order->total_price = 50000;

        $shopeePayService = new ShopeePayService();
        $result = $shopeePayService->generateQRIS($order);

        $this->assertNotNull($result);
        $this->assertEquals('00020101021226540016ID...', $result['qr_content']);
        $this->assertEquals('https://example.com/qr.png', $result['qr_url']);
    }

    /**
     * Test webhook callback route with fake signature and correct headers.
     */
    public function test_webhook_successfully_updates_order_status()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not loaded.');
        }

        Notification::fake();

        // 1. Create a dummy order
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'address' => 'Jakarta, Indonesia',
            'phone' => '081234567890',
            'total_price' => 50000,
            'status' => 'menunggu_pembayaran',
        ]);

        // 2. Generate a valid signature for the webhook request body
        $callbackUrl = route('shopeepay.callback');
        $payload = [
            'originalReferenceNo' => 'SPP-999',
            'originalPartnerReferenceNo' => 'IWAKQU-' . $order->id . '-' . time(),
            'externalStoreId' => 'test_store_id',
            'amount' => [
                'value' => '50000.00',
                'currency' => 'IDR'
            ],
            'latestTransactionStatus' => '00', // Success
            'additionalInfo' => [
                'merchantId' => 'test_merchant_id',
                'productType' => 2,
                'userIdHash' => 'some_user_hash',
                'terminalId' => 'T2903',
                'paymentChannel' => 1
            ]
        ];
        $body = json_encode($payload);
        $timestamp = date('Y-m-d\TH:i:sP');

        $bodyHash = strtolower(hash('sha256', $body));
        $stringToSign = "POST:{$callbackUrl}:{$bodyHash}:{$timestamp}";

        $privKey = openssl_pkey_get_private(config('services.shopeepay.private_key'));
        openssl_sign($stringToSign, $signatureBytes, $privKey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signatureBytes);

        // 3. Post to the webhook URL
        $response = $this->withHeaders([
            'X-SIGNATURE' => $signature,
            'X-TIMESTAMP' => $timestamp,
        ])->postJson(route('shopeepay.callback'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'responseCode' => '2005200',
            'responseMessage' => 'Successful'
        ]);

        // 4. Verify order database status is updated
        $order->refresh();
        $this->assertEquals('dibayar', $order->status);
        $this->assertEquals('qris_shopeepay', $order->payment_method);
        $this->assertEquals('SPP-999', $order->transaction_id);
    }
}
