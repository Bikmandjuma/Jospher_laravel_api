<?php

namespace App\Http\Controllers;

use App\Services\MTNPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(MTNPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function initiatePayment(Request $request){
        $request->validate([
            'phone' => 'required|digits:10',
            'amount' => 'required|numeric|min:1',
        ]);

        $referenceId = Str::uuid();
        $phoneNumber = '250' . substr($request->phone, 1);
        $receiverPhoneNumber = "0785389000";
        $receiverName = 'Job-sphere-rwanda';
        $amount = $request->amount;
        $duration = $request->duration;
        $activeDays = $duration * 30;
        $startDate = now();
        $endDate = now()->addMonths($duration);

        try {

            $this->paymentService->requestToPay($phoneNumber, $amount, $referenceId, $receiverPhoneNumber);

            $message = "You are about to make a payment of " . $amount . " FRW to " . $receiverName . ". Do you wish to continue?";

            Log::info($message);
            $userId = Auth::guard('user')->user()->id;

            Payment::create([
                'user_id' => $userId,
                'reference_id' => $referenceId,
                'phone' => $phone,
                'amount' => $amount,
                'duration' => $duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'active_days' => $activeDays,
                'status' => 'PENDING',
            ]);

            return response()->json(['message' => $message, 'reference_id' => $referenceId]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function handleCallback(Request $request)
    {
        Log::info('MTN Callback: ', $request->all());

        $data = $request->all();

        if ($data['status'] === 'SUCCESSFUL') {

            Payment::where('reference_id', $data['externalId'])->update(['status' => 'PAID']);

            return response()->json(['message' => 'Payment confirmed successfully.']);

        }

        Payment::where('reference_id', $data['externalId'])->update(['status' => $data['status']]);

        return response()->json(['message' => 'Payment status updated.']);
    }
}
