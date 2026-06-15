<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected string $apiUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->storeId = (string) config('sslcommerz.store_id');
        $this->storePassword = (string) config('sslcommerz.store_password');
        $this->sandbox = (bool) config('sslcommerz.sandbox');
        $this->apiUrl = $this->sandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Initiate SSLCommerz payment session.
     */
    public function initiatePayment(array $data): array
    {
        if ($this->storeId === '' || $this->storePassword === '') {
            Log::error('SSLCommerz credentials missing');
            return [
                'success' => false,
                'message' => 'Payment gateway is not configured. Please contact support.',
            ];
        }

        $postData = [
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $data['total_amount'],
            'currency'     => 'BDT',
            'tran_id'      => $data['tran_id'],
            'success_url'  => $data['success_url'] ?? route('payment.success'),
            'fail_url'     => $data['fail_url'] ?? route('payment.fail'),
            'cancel_url'   => $data['cancel_url'] ?? route('payment.cancel'),
            'ipn_url'      => $data['ipn_url'] ?? route('payment.ipn'),

            'cus_name'    => $data['cus_name'],
            'cus_email'   => $data['cus_email'] ?? 'customer@example.com',
            'cus_add1'    => $data['cus_add1'],
            'cus_city'    => $data['cus_city'] ?? 'Chittagong',
            'cus_country' => 'Bangladesh',
            'cus_phone'   => $data['cus_phone'],

            'shipping_method'  => 'NO',
            'num_of_item'      => $data['num_of_item'] ?? 1,
            'product_name'     => $data['product_name'] ?? 'Food Order',
            'product_category' => 'Food',
            'product_profile'  => 'general',
        ];

        try {
            $response = Http::timeout(60)->asForm()->post($this->apiUrl . '/gwprocess/v4/api.php', $postData);
            $result = $response->json();

            if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                return [
                    'success'     => true,
                    'gateway_url' => $result['GatewayPageURL'],
                    'session_key' => $result['sessionkey'] ?? null,
                ];
            }

            Log::error('SSLCommerz initiation failed', ['response' => $result]);
            return [
                'success' => false,
                'message' => $result['failedreason'] ?? 'Payment initiation failed.',
            ];
        } catch (\Exception $e) {
            Log::error('SSLCommerz exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Could not connect to payment gateway.',
            ];
        }
    }

    /**
     * Validate a transaction by val_id.
     * Must use GET — POST returns SOAP XML, not JSON.
     */
    public function validateTransaction(string $valId): array
    {
        if ($valId === '' || $this->storeId === '' || $this->storePassword === '') {
            return [];
        }

        try {
            $response = Http::timeout(60)->get($this->apiUrl . '/validator/api/validationserverAPI.php', [
                'val_id'       => $valId,
                'store_id'     => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format'       => 'json',
                'v'            => 1,
            ]);

            $result = $response->json();

            if (! is_array($result) || empty($result)) {
                Log::error('SSLCommerz validation returned non-JSON response', [
                    'val_id' => $valId,
                    'http_status' => $response->status(),
                    'body_preview' => mb_substr($response->body(), 0, 300),
                ]);

                return [];
            }

            Log::info('SSLCommerz validation result', [
                'val_id' => $valId,
                'status' => $result['status'] ?? null,
                'tran_id' => $result['tran_id'] ?? null,
                'amount' => $result['amount'] ?? null,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('SSLCommerz validation error', [
                'val_id' => $valId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function isSuccessfulValidation(array $validation, string $expectedTranId, float $expectedAmount): bool
    {
        $status = strtoupper((string) ($validation['status'] ?? ''));

        if (! in_array($status, ['VALID', 'VALIDATED'], true)) {
            return false;
        }

        if (($validation['tran_id'] ?? '') !== $expectedTranId) {
            Log::warning('SSLCommerz tran_id mismatch', [
                'expected' => $expectedTranId,
                'received' => $validation['tran_id'] ?? null,
            ]);

            return false;
        }

        if (isset($validation['amount']) && $validation['amount'] !== '') {
            $validatedAmount = (float) $validation['amount'];
            if (abs($validatedAmount - $expectedAmount) > 0.01) {
                Log::warning('SSLCommerz amount mismatch', [
                    'expected' => $expectedAmount,
                    'received' => $validatedAmount,
                ]);

                return false;
            }
        }

        return true;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
