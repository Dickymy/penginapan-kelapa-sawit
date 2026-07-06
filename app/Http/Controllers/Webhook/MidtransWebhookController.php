<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __construct(private MidtransPaymentService $paymentService) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        try {
            $this->paymentService->handleWebhook($payload);
        } catch (\Exception $e) {
            // Log but still return 200 to prevent Midtrans retries on our errors
            \Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'order_id' => $payload['order_id'] ?? 'unknown',
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
