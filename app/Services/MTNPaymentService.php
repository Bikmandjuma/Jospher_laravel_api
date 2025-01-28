<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MTNPaymentService
{
    private $userId;
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('MTN_ENV') === 'production'
            ? 'https://momodeveloper.mtn.com/v1_0/'
            : 'https://sandbox.momodeveloper.mtn.com/v1_0/';

        $this->userId = $this->registerUser();
        $this->apiKey = $this->generateApiKey($this->userId);
    }

    /**
     * Register a new API user.
     *
     * @return string
     * @throws \Exception
     */
    private function registerUser(): string
    {
        $referenceId = $this->generateReferenceId();

        $response = $this->makeHttpRequest('POST', 'apiuser', [
            'providerCallbackHost' => $this->getEnv('MTN_CALLBACK_HOST'),
        ], [
            'X-Reference-Id' => $referenceId,
            'Content-Type' => 'application/json',
        ]);

        if (!$response->ok()) {
            $this->logError('Register User', $response);
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
        $response = $this->makeHttpRequest('POST', "apiuser/{$referenceId}/apikey");

        if (!$response->ok()) {
            $this->logError('Generate API Key', $response);
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
        $credentials = $this->generateAuthCredentials();

        $response = $this->makeHttpRequest('POST', 'token/', [], [
            'Authorization' => 'Basic ' . $credentials,
        ]);

        if (!$response->ok()) {
            $this->logError('Get Access Token', $response);
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
        $referenceId = $this->generateReferenceId();

        $response = $this->makeHttpRequest('POST', 'requesttopay', [
            'amount' => $amount,
            'currency' => 'RWF',
            'externalId' => $referenceId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $payerPhoneNumber,
            ],
            'payerMessage' => 'Payment Request',
            'payeeNote' => 'Please confirm payment',
            'callbackUrl' => $this->getEnv('MTN_CALLBACK_URL'),
        ], [
            'Authorization' => 'Bearer ' . $accessToken,
            'X-Reference-Id' => $referenceId,
        ]);

        if (!$response->ok()) {
            $this->logError('Request to Pay', $response);
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

        $response = $this->makeHttpRequest('GET', "requesttopay/{$referenceId}", [], [
            'Authorization' => 'Bearer ' . $accessToken,
        ]);

        if (!$response->ok()) {
            $this->logError('Get Transaction Status', $response);
            throw new \Exception('Unable to fetch transaction status: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Generate a unique reference ID.
     *
     * @return string
     */
    private function generateReferenceId(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Generate Basic Authentication credentials.
     *
     * @return string
     */
    private function generateAuthCredentials(): string
    {
        return base64_encode($this->userId . ':' . $this->apiKey);
    }

    /**
     * Helper to get environment variables.
     *
     * @param string $key
     * @return string
     */
    private function getEnv(string $key): string
    {
        return env($key);
    }

    /**
     * Log error messages for debugging.
     *
     * @param string $action
     * @param \Illuminate\Http\Client\Response $response
     */
    private function logError(string $action, $response): void
    {
        Log::error("MTN API Error - {$action}: ", [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }

    /**
     * Make an HTTP request.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $body
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     */
    private function makeHttpRequest(string $method, string $endpoint, array $body = [], array $headers = []): \Illuminate\Http\Client\Response
    {
        $baseHeaders = [
            'Ocp-Apim-Subscription-Key' => $this->getEnv('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        ];

        $mergedHeaders = array_merge($baseHeaders, $headers);

        return Http::timeout(10)
            ->withHeaders($mergedHeaders)
            ->$method($this->baseUrl . $endpoint, $body);
    }
}
