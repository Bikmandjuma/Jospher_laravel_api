<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MTNPaymentService
{
    private $userId;  // MTN User ID (X-Reference-Id)
    private $apiKey;  // MTN API Key

    public function __construct()
    {
        // Dynamically generate or retrieve stored user ID and API Key
        $this->userId = $this->registerUser();
        $this->apiKey = $this->generateApiKey($this->userId);
    }

    /**
     * Register a new API user (only needed if not already registered).
     *
     * @return string
     * @throws \Exception
     */
    private function registerUser(): string
    {
        $referenceId = (string) Str::uuid(); // Generate a new UUID

        $response = Http::withHeaders([
            'X-Reference-Id' => $referenceId,
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        ])->post(env('MTN_BASE_URL') . 'apiuser', [
            'providerCallbackHost' => env('MTN_CALLBACK_HOST'),
        ]);

        if (!$response->ok()) {
            throw new \Exception('Unable to register user: ' . $response->body());
        }

        return $referenceId;
    }

    /**
     * Generate an API Key for the registered user.
     *
     * @param string $referenceId
     * @return string
     * @throws \Exception
     */
    private function generateApiKey(string $referenceId): string
    {
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        ])->post(env('MTN_BASE_URL') . "apiuser/{$referenceId}/apikey");

        if (!$response->ok()) {
            throw new \Exception('Unable to generate API key: ' . $response->body());
        }

        return $response->json('apiKey');
    }

    /**
     * Get the access token from MTN API.
     *
     * @return string
     * @throws \Exception
     */
    public function getAccessToken(): string
    {
        $credentials = base64_encode($this->userId . ':' . $this->apiKey);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Ocp-Apim-Subscription-Key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        ])->post(env('MTN_BASE_URL') . 'token/');

        if (!$response->ok()) {
            throw new \Exception('Unable to fetch access token: ' . $response->body());
        }

        return $response->json('access_token');
    }

    /**
     * Initiate a payment request.
     *
     * @param string $payerPhoneNumber
     * @param float $amount
     * @return array
     * @throws \Exception
     */
    public function requestToPay(string $payerPhoneNumber, float $amount): array
    {
        $accessToken = $this->getAccessToken();
        $referenceId = (string) Str::uuid();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => env('MTN_ENV'),
            'Ocp-Apim-Subscription-Key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        ])->post(env('MTN_BASE_URL') . 'requesttopay', [
            'amount' => $amount,
            'currency' => 'RWF',
            'externalId' => $referenceId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $payerPhoneNumber,
            ],
            'payerMessage' => 'Payment Request',
            'payeeNote' => 'Please confirm payment',
            'callbackUrl' => env('MTN_CALLBACK_URL'),
            'receiver' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $receiverPhoneNumber,
            ]
        ]);

        if (!$response->ok()) {
            throw new \Exception('Unable to initiate payment: ' . $response->body());
        }

        return [
            'status' => $response->status(),
            'reference_id' => $referenceId,
            'body' => $response->json(),
        ];
    }

    /**
     * Check the status of a payment request.
     *
     * @param string $referenceId
     * @return array
     * @throws \Exception
     */
    public function getTransactionStatus(string $referenceId): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'X-Target-Environment' => env('MTN_ENV'),
            'Ocp-Apim-Subscription-Key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        ])->get(env('MTN_BASE_URL') . "requesttopay/{$referenceId}");

        if (!$response->ok()) {
            throw new \Exception('Unable to fetch transaction status: ' . $response->body());
        }

        return $response->json();
    }
}
