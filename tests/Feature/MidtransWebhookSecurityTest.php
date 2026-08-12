<?php

namespace Tests\Feature;

use App\Http\Controllers\MidtransWebhookController;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use ReflectionClass;
use Tests\TestCase;

class MidtransWebhookSecurityTest extends TestCase
{
    public function test_webhook_dengan_signature_invalid_ditolak_403(): void
    {
        $service = new class extends MidtransService
        {
            public function __construct()
            {
                // No credential, database, or external gateway is used.
            }

            public function verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)
            {
                return false;
            }
        };

        $reflection = new ReflectionClass(MidtransWebhookController::class);
        /** @var MidtransWebhookController $controller */
        $controller = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('midtransService');
        $property->setAccessible(true);
        $property->setValue($controller, $service);

        $request = Request::create('/midtrans/notification', 'POST', [
            'order_id' => 'QA-INVALID-SIGNATURE',
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'invalid-signature',
        ]);

        $response = $controller->notification($request);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('error', $response->getData(true)['status']);
        $this->assertSame('Invalid signature', $response->getData(true)['message']);
    }
}
