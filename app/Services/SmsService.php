<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $apiUrl;
    private $apiKey;
    private $clientId;
    private $senderId;

    public function __construct()
    {
        $this->apiUrl = 'https://console.smsq.global/api/v2/SendSMS';
        $this->apiKey = 'X0haHzffFZo6V69T16mBZ+T/WLiuikBqgGMORDpTQuE=';
        $this->clientId = 'aeff5028-d333-4762-91c9-0d53d81394e7';
        $this->senderId = '8809617611892'; // APPROVED
    }

    /**
     * Send credentials SMS
     * @param string $phoneNumber - Phone number with country code (e.g., +8801234567890)
     * @param string $email - User email
     * @param string $password - User password (temporary/generated)
     * @param string $dashboardUrl - Dashboard login URL
     * @return array
     */
    public function sendCredentialsSms($phoneNumber, $email, $password, $dashboardUrl = null)
    {
        $message = "Your account credentials:\nEmail: {$email}\nPassword: {$password}\n\nThank you for joining our community!";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send welcome SMS after registration
     * @param string $phoneNumber
     * @param string $userName
     * @return array
     */
    public function sendWelcomeSms($phoneNumber, $userName)
    {
        $message = "Welcome {$userName}! Your membership account has been created successfully. Enjoy exclusive dining benefits! 🎉";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send payment confirmation SMS
     * @param string $phoneNumber
     * @param string $userName
     * @param string $amount
     * @param string $transactionId
     * @return array
     */
    public function sendPaymentConfirmationSms($phoneNumber, $userName, $amount, $transactionId)
    {
        $message = "Dear {$userName}, your payment of {$amount}৳ has been confirmed. Transaction ID: {$transactionId}. Thank you for your order! 🙏";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send OTP SMS
     * @param string $phoneNumber
     * @param string $otp
     * @return array
     */
    public function sendOtpSms($phoneNumber, $otp)
    {
        $message = "Your OTP is: {$otp}. This code is valid for 10 minutes.";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send membership card details
     * @param string $phoneNumber
     * @param string $userName
     * @param string $cardNumber
     * @param string $cardDetails - Additional card info
     * @return array
     */
    public function sendMembershipCardSms($phoneNumber, $userName, $cardNumber, $cardDetails = null)
    {
        $message = "Dear {$userName}, your membership card has been issued. Card Number: {$cardNumber}.";
        if ($cardDetails) {
            $message .= " Details: {$cardDetails}";
        }
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send generic SMS
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendSms($phoneNumber, $message)
    {
        try {
            // Validate phone number
            if (!$this->isValidPhoneNumber($phoneNumber)) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format',
                    'message' => 'Phone number must be in international format (e.g., +8801234567890)'
                ];
            }

            // Prepare request payload
            $payload = [
                'ClientId' => $this->clientId,
                'ApiKey' => $this->apiKey,
                'SenderId' => $this->senderId,
                'Message' => $message,
                'MobileNumbers' => [$phoneNumber]
            ];

            // Send SMS via API
            $response = Http::timeout(30)
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $data
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $data
                ];
            } else {
                $errorMsg = $response->json()['message'] ?? 'Unknown error';
                
                Log::error('SMS sending failed', [
                    'phone' => $phoneNumber,
                    'status' => $response->status(),
                    'error' => $errorMsg
                ]);

                return [
                    'success' => false,
                    'error' => $errorMsg,
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS service exception', [
                'phone' => $phoneNumber,
                'exception' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send SMS',
                'exception' => $e->getMessage()
            ];
        }
    }

    
    /**
     * Validate phone number format
     * @param string $phoneNumber
     * @return bool
     */
    private function isValidPhoneNumber($phoneNumber)
    {
        // Check if phone number starts with + and contains only digits
        return preg_match('/^\+\d{10,15}$/', $phoneNumber) === 1;
    }

    /**
     * Format phone number to international format
     * @param string $phoneNumber
     * @param string $countryCode - Default: +880 (Bangladesh)
     * @return string
     */
    public static function formatPhoneNumber($phoneNumber, $countryCode = '+880')
    {
        // Remove any non-digit characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
        
        // Remove leading 0 if exists
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        
        // Add country code if not present
        if (strpos($phoneNumber, '+') === false) {
            $phoneNumber = $countryCode . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Batch send SMS to multiple numbers
     * @param array $phoneNumbers
     * @param string $message
     * @return array
     */
    public function batchSendSms(array $phoneNumbers, $message)
    {
        $results = [];
        
        foreach ($phoneNumbers as $phone) {
            $results[$phone] = $this->sendSms($phone, $message);
        }
        
        return $results;
    }
}
