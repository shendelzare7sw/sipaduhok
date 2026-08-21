<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\PaywuzPaymentStatusService;
use App\Services\PaywuzService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PaywuzWebhookController extends Controller
{
    public function __invoke(Request $request, PaywuzPaymentStatusService $statusService, PaywuzService $paywuz): JsonResponse
    {
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid JSON payload.'], 400);
        }

        $signature = (string) $request->header('X-Paywuz-Signature');
        $deliveryId = (string) $request->header('X-Paywuz-Delivery');
        $headerEvent = trim((string) $request->header('X-Paywuz-Event'));
        $allowedEvents = [
            'transaction.settlement',
            'transaction.paid',
            'transaction.failed',
            'transaction.cancelled',
        ];

        if (! preg_match('/^sha256=[a-f0-9]{64}$/i', $signature)
            || blank($deliveryId)
            || strlen($deliveryId) > 100
            || ! in_array($headerEvent, $allowedEvents, true)) {
            return response()->json(['message' => 'Missing or invalid Paywuz headers.'], 400);
        }

        // SDK resmi mengirim body transaksi datar dan event pada X-Paywuz-Event.
        $validator = Validator::make($payload, [
            'id' => ['required', 'string', 'max:100'],
            'orderId' => ['required', 'string', 'max:64'],
            'amount' => ['required', 'integer', 'min:1'],
            'fee' => ['required', 'integer', 'min:0'],
            'totalPayment' => ['required', 'integer', 'min:1'],
            'paymentMethod' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:pending,settlement,success,failed,cancelled,expired'],
            'timestamp' => ['required', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid webhook payload.'], 400);
        }

        $eventMatchesStatus = match ($headerEvent) {
            'transaction.settlement' => $payload['status'] === 'settlement',
            'transaction.paid' => $payload['status'] === 'success',
            'transaction.failed' => in_array($payload['status'], ['failed', 'expired'], true),
            'transaction.cancelled' => $payload['status'] === 'cancelled',
            default => false,
        };

        if (! $eventMatchesStatus) {
            return response()->json(['message' => 'Webhook event does not match transaction status.'], 400);
        }

        try {
            $eventTime = CarbonImmutable::parse((string) $payload['timestamp']);
        } catch (Throwable) {
            return response()->json(['message' => 'Invalid webhook timestamp.'], 400);
        }

        $toleranceSeconds = max(60, min((int) config('services.paywuz.webhook_tolerance_seconds', 900), 3600));
        if ($eventTime->diffInSeconds(CarbonImmutable::now()) > $toleranceSeconds) {
            return response()->json(['message' => 'Webhook timestamp is outside the accepted window.'], 400);
        }

        $payments = Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $payload['orderId'])
            ->get();

        $keys = $payments->isNotEmpty()
            ? array_filter([$paywuz->apiKey($payments->first()->payment_environment)])
            : $paywuz->apiKeys();

        $signatureValid = collect($keys)->contains(function (string $apiKey) use ($rawBody, $signature): bool {
            return hash_equals('sha256='.hash_hmac('sha256', $rawBody, $apiKey), $signature);
        });

        if (! $signatureValid) {
            return response()->json(['message' => 'Invalid webhook signature.'], 403);
        }

        if ($payments->isNotEmpty() && (int) $payments->sum('jumlah_bayar') !== (int) $payload['amount']) {
            return response()->json(['message' => 'Webhook amount does not match payment.'], 422);
        }

        if ($payments->isNotEmpty() && $payments->pluck('transaction_id')->filter()->contains(
            fn (string $reference) => ! hash_equals($reference, (string) $payload['id'])
        )) {
            return response()->json(['message' => 'Webhook reference does not match payment.'], 422);
        }

        $inserted = DB::table('payment_webhook_deliveries')->insertOrIgnore([
            'provider' => 'paywuz',
            'delivery_id' => $deliveryId,
            'event' => $headerEvent,
            'pembayaran_id' => $payments->first()?->id,
            'payload_hash' => hash('sha256', $rawBody),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted === 0) {
            return response()->json(['message' => 'Webhook already processed.']);
        }

        if ($payments->isEmpty()) {
            DB::table('payment_webhook_deliveries')->where('delivery_id', $deliveryId)->update([
                'processed_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Webhook accepted; payment not found.'], 202);
        }

        try {
            $statusService->apply((string) $payload['orderId'], $payload);
            DB::table('payment_webhook_deliveries')->where('delivery_id', $deliveryId)->update([
                'processed_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $exception) {
            DB::table('payment_webhook_deliveries')->where('delivery_id', $deliveryId)->delete();
            throw $exception;
        }

        return response()->json(['message' => 'Webhook processed.']);
    }
}
