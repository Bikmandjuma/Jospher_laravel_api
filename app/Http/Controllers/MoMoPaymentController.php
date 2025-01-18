<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MoMoPaymentController extends Controller
{
    private $subscriptionKey = 'YOUR_SUBSCRIPTION_KEY';
    private $consumerKey = 'YOUR_CONSUMER_KEY';
    private $consumerSecret = 'YOUR_CONSUMER_SECRET';

    public function initiatePayment(Request $request)
    {
        $amount = $request->input('amount');
        $phoneNumber = $request->input('phone_number');
        $externalId = '25JSR' . time();
        
        // Request access token
        $tokenResponse = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->post('https://sandbox.momodeveloper.mtn.com/collection/token/', [
                'grant_type' => 'client_credentials'
            ]);

        $accessToken = $tokenResponse->json()['access_token'];

        // Send payment request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ])->post('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay', [
            'amount' => $amount,
            'currency' => 'RWF',
            'externalId' => $externalId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $phoneNumber,
            ],
            'payeeNote' => 'Payment for Job Sphere Rwanda services',
            'payerMessage' => 'Thank you for your payment',
            'payee' => [
                'partyIdType' => 'MSISDN',
                'partyId' => '0785389000',
            ],
        ]);

        return response()->json($response->json());
    }

    public function checkPaymentStatus(Request $request)
    {
        $externalId = $request->input('external_id');

        // Request access token
        $tokenResponse = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->post('https://sandbox.momodeveloper.mtn.com/collection/token/', [
                'grant_type' => 'client_credentials'
            ]);

        $accessToken = $tokenResponse->json()['access_token'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ])->get('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/' . $externalId);

        return response()->json($response->json());
    }
}

