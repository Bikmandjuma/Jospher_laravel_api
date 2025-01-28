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
            ]);

            // Prepare data
            $referenceId = Str::uuid();
            $phoneNumber = '250' . substr($request->phone, 1); // Format phone number
            $receiverPhoneNumber = "0785389000"; // Your receiver's phone number
            $receiverName = 'Job-sphere-rwanda'; // Your receiver's name
            $amount = $request->amount;
            $duration = $request->duration;
            $activeDays = $duration * 30; // Calculate the number of active days
            $startDate = now(); // Current date
            $endDate = now()->addMonths($duration); // Calculate end date

            // Call the payment service to initiate the payment
            $this->paymentService->requestToPay($phoneNumber, $amount, $referenceId, $receiverPhoneNumber);

            // Logging and preparing the message
            $message = "You are about to make a payment of " . $amount . " FRW to " . $receiverName . ". Do you wish to continue?";
            Log::info($message);

            // Assuming the user is logged in
            $userId = Auth::guard('user')->user()->id;

            // Create payment record in the database
            Payment::create([
                'user_id' => $userId,
                'reference_id' => $referenceId,
                'phone' => $request->phone,
                'amount' => $amount,
                'duration' => $duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'active_days' => $activeDays,
                'status' => 'PENDING',
            ]);

            // Return a response with the reference ID
            return response()->json(['message' => $message, 'reference_id' => $referenceId]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors() // This will return the validation error details
            ], 422);

        } catch (\Exception $e) {
            // Log the error to Laravel logs
            \Log::error('Payment initiation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Payment initiation failed. ' . $e->getMessage()
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
