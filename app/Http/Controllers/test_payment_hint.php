<?php
// In your system, since you want to manage the payment and access control based on the payment duration (e.g., 3 months) and number of active days (e.g., 91 days), you can achieve this using a Payment model and extend it to track the payment status, duration, and the association with the authenticated user (user_id). You don’t necessarily need to create a new model, but you will need to track additional fields to manage user access and payment details effectively.

// Here’s a step-by-step approach to achieve this:

// 1. Add Necessary Fields to the Payment Model:
// You can enhance the existing Payment model to include fields such as:

// user_id: The ID of the authenticated user who made the payment.
// amount: The amount paid by the user.
// duration: The duration of the payment (e.g., 3 months).
// start_date: The date when the payment was made, marking the start of the access period.
// end_date: The date when the user’s access will expire based on the payment duration.
// active_days: The number of days the user is allowed to access the system actively (e.g., 91 days).
// 2. Create a Migration to Add New Fields:
// Create a migration to add the necessary columns to the payments table:

// bash
// Copy
// Edit
// php artisan make:migration add_payment_details_to_payments_table --table=payments
// In the migration file, add the following:

// php
// Copy
// Edit

public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('amount', 10, 2); // Amount paid
        $table->integer('duration'); // Duration in months
        $table->date('start_date'); // Start date of the payment
        $table->date('end_date'); // End date of the payment
        $table->integer('active_days'); // Number of active days
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['user_id', 'amount', 'duration', 'start_date', 'end_date', 'active_days']);
    });
}

// Run the migration to update your database:

// bash
// Copy
// Edit
// php artisan migrate
// 3. Update the Payment Controller:
// Modify the PaymentController to handle payment processing, update the duration, and calculate the end_date and active_days when the user makes a payment.

// Here's an updated version of your initiatePayment method in the PaymentController:

// php
// Copy
// Edit
public function initiatePayment(Request $request)
{
    $request->validate([
        'phone' => 'required|digits:10', // Payer's phone number
        'amount' => 'required|numeric|min:1',
        'duration' => 'required|integer|min:1', // Duration of the payment in months
    ]);

    $referenceId = Str::uuid();
    $payerPhoneNumber = '250' . substr($request->phone, 1); // Payer's phone number
    $amount = $request->amount;
    $duration = $request->duration; // Duration (in months)
    $activeDays = $duration * 30; // Assuming 30 days in a month for simplicity
    $startDate = now(); // Current date as the start date
    $endDate = now()->addMonths($duration); // End date after the specified duration

    try {
        // Pass payer's phone number, amount, referenceId, etc. to the payment service
        $this->paymentService->requestToPay($payerPhoneNumber, $amount, $referenceId);

        // Store payment details in the database
        $payment = Payment::create([
            'user_id' => auth()->id(), // Get the authenticated user's ID
            'reference_id' => $referenceId,
            'phone' => $request->phone,
            'amount' => $amount,
            'duration' => $duration,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active_days' => $activeDays,
            'status' => 'PENDING',
        ]);

        return response()->json(['message' => 'Payment initiated successfully!', 'reference_id' => $referenceId]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

// 4. Manage User Access:
// To manage user access based on the payment duration and active days, you can create a method that checks if the user has valid access.

// Here’s an example of how to check if a user has active access:

// php
// Copy
// Edit
public function checkUserAccess()
{
    $user = auth()->user();

    // Retrieve the most recent payment record for the user
    $payment = Payment::where('user_id', $user->id)
                      ->orderBy('created_at', 'desc')
                      ->first();

    if ($payment) {
        $currentDate = now();
        if ($currentDate->between($payment->start_date, $payment->end_date)) {
            return response()->json(['message' => 'Access granted!']);
        } else {
            return response()->json(['message' => 'Access expired. Please renew payment.'], 403);
        }
    }

    return response()->json(['message' => 'No payment found. Please make a payment.'], 400);
}

// This method checks whether the current date is between the start_date and end_date of the most recent payment record for the user. If the payment is still valid, the user is granted access; otherwise, they are asked to renew their payment.

// 5. Payment Confirmation (Callback Handling):
// After the user makes the payment, MTN MoMo will send a callback. You can use the handleCallback method in the controller to update the payment status and finalize the payment.

// In this callback handler, you would update the status to PAID once the payment is confirmed and update the user's access accordingly.

// Conclusion:
// You don't need to create a new model for managing the payment details and user access. The existing Payment model is sufficient, as long as you add the necessary fields (such as user_id, duration, start_date, and end_date). This will allow you to manage the user’s access based on their payment status and duration effectively.

// Let me know if you need more help!4