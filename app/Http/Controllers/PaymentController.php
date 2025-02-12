<?php

namespace App\Http\Controllers;

use App\Services\MTNPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $momoService;

    public function __construct(MTNPaymentService $momoService)
    {
        $this->momoService = $momoService;
    }

    public function requestToPay()
    {
        $accessToken = $this->momoService->getAccessToken();
        
        if (!$accessToken) {
            Log::error('Failed to get access token from MTN service'); // Log error
            return response()->json(['error' => 'Failed to get access token'], 500);
        }

        $paymentResponse = $this->momoService->requestToPay($accessToken);

        if ($paymentResponse) {
            return response()->json(['message' => 'Payment request sent successfully', 'data' => $paymentResponse]);
        }

        Log::error('Payment request failed', ['access_token' => $accessToken, 'payment_response' => $paymentResponse]); // Log error with additional data
        return response()->json(['error' => 'Payment request failed'], 500);
    }
}
