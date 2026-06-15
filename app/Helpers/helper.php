<?php

if (!function_exists('app_name')) {
    function app_name()
    {
        return config('app.name');
    }
}


if (!function_exists('send_sms')) {
    /**
     * Send SMS helper function
     * @param string $phone - Phone number
     * @param string $message - Message to send
     * @return array
     */
    function send_sms($phone, $message)
    {
        $smsService = app(\App\Services\SmsService::class);
        return $smsService->sendSms($phone, $message);
    }
}

if (!function_exists('send_credentials_sms')) {
    /**
     * Send credentials SMS
     * @param string $phone
     * @param string $email
     * @param string $password
     * @return array
     */
    function send_credentials_sms($phone, $email, $password)
    {
        $smsService = app(\App\Services\SmsService::class);
        return $smsService->sendCredentialsSms($phone, $email, $password);
    }
}

if (!function_exists('send_welcome_sms')) {
    /**
     * Send welcome SMS
     * @param string $phone
     * @param string $userName
     * @return array
     */
    function send_welcome_sms($phone, $userName)
    {
        $smsService = app(\App\Services\SmsService::class);
        return $smsService->sendWelcomeSms($phone, $userName);
    }
}

if (!function_exists('send_payment_sms')) {
    /**
     * Send payment confirmation SMS
     * @param string $phone
     * @param string $userName
     * @param string $amount
     * @param string $transactionId
     * @return array
     */
    function send_payment_sms($phone, $userName, $amount, $transactionId)
    {
        $smsService = app(\App\Services\SmsService::class);
        $message = "Dear {$userName}, your payment of {$amount}৳ has been confirmed. Transaction ID: {$transactionId}. Thank you for your order! 🙏";
        return $smsService->sendSms($phone, $message);
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format phone number to international format
     * @param string $phone
     * @param string $countryCode
     * @return string
     */
    function format_phone($phone, $countryCode = '+880')
    {
        return \App\Services\SmsService::formatPhoneNumber($phone, $countryCode);
    }
}
