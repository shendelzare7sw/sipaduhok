<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\MidtransPaymentStatusService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransPaymentStatusService $statusService, MidtransService $midtrans): JsonResponse
    {
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid JSON payload.'], 400);
        }

        $validator = Validator::make($payload, [
            'order_id' => ['required', 'string', 'max:64'],
            'status_code' => ['required', 'string', 'max:3'],
            'gross_amount' => ['required', 'string', 'max:30'],
            'signature_key' => ['required', 'string', 'size:128'],
            'transaction_status' => ['required', 'string', 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid notification payload.'], 400);
        }

        $orderId = $payload['order_id'];
        $payments = Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $orderId)
            ->get();

        $keys = $payments->isNotEmpty()
            ? array_filter([$midtrans->serverKey($payments->first()->payment_environment)])
            : $midtrans->serverKeys();

        if (! $midtrans->verifySignature(
            $orderId,
            $payload['status_code'],
            $payload['gross_amount'],
            $payload['signature_key'],
            $keys,
        )) {
            return response()->json(['message' => 'Invalid notification signature.'], 403);
        }

        // Notifikasi uji dari dashboard Midtrans memakai order fiktif.
        // Balas sukses agar Midtrans tidak terus mengirim ulang.
        if ($payments->isEmpty()) {
            return response()->json(['message' => 'Notification accepted; payment not found.']);
        }

        if (! is_numeric($payload['gross_amount'])
            || (float) $payload['gross_amount'] !== (float) $payments->sum('jumlah_bayar')) {
            Log::critical('Nominal notifikasi Midtrans tidak sesuai data pembayaran.', [
                'order_id' => $orderId,
                'gross_amount' => $payload['gross_amount'],
            ]);

            return response()->json(['message' => 'Notification amount does not match payment.'], 422);
        }

        $deliveryId = 'midtrans:'.hash('sha256', $rawBody);
        $inserted = DB::table('payment_webhook_deliveries')->insertOrIgnore([
            'provider' => MidtransService::GATEWAY,
            'delivery_id' => $deliveryId,
            'event' => mb_substr((string) $payload['transaction_status'], 0, 100),
            'pembayaran_id' => $payments->first()->id,
            'payload_hash' => hash('sha256', $rawBody),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted === 0) {
            return response()->json(['message' => 'Notification already processed.']);
        }

        try {
            // Signature Midtrans tidak mencakup transaction_status, sehingga
            // status final selalu diambil ulang dari Core API.
            $statusService->sync($orderId, throw: true);

            DB::table('payment_webhook_deliveries')->where('delivery_id', $deliveryId)->update([
                'processed_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $exception) {
            DB::table('payment_webhook_deliveries')->where('delivery_id', $deliveryId)->delete();

            Log::error('Notifikasi Midtrans gagal diproses dan akan dikirim ulang.', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Notification could not be processed.'], 503);
        }

        return response()->json(['message' => 'Notification processed.']);
    }
}
