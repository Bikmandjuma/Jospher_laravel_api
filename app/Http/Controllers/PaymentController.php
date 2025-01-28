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
        
        try {
            $request->validate([
                'phone' => 'required|digits:10',
                'amount' => 'required|numeric|min:1',
                'duration' => 'required|integer|min:1',
            ]);

            $phoneNumber = '250' . substr($request->phone, 1); // Format phone number
            $amount = $request->amount;
            $duration = $request->duration;

            $activeDays = $duration * 30; // Calculate the number of active days
            $startDate = now(); // Current date
            $endDate = now()->addMonths($duration); // Calculate end date

            // Call the payment service to initiate the payment
            $paymentResponse = $this->paymentService->requestToPay($phoneNumber, $amount);

            // Assuming the user is logged in
            $userId = Auth::guard('user')->id();
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            // Create payment record in the database
            Payment::create([
                'user_id' => $userId,
                'reference_id' => $paymentResponse['reference_id'],
                'phone' => $request->phone,
                'amount' => $amount,
                'duration' => $duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'active_days' => $activeDays,
                'status' => 'PENDING',
            ]);

            // Return a success response
            return response()->json([
                'message' => 'Payment initiated successfully. Awaiting confirmation.',
                'reference_id' => $paymentResponse['reference_id'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Payment initiation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Payment initiation failed. ' . $e->getMessage(),
            ], 500);
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
