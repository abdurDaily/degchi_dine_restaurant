<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use App\Services\SmsService;
use Illuminate\Http\Request;

/**
 * Example SMS Controller
 * Shows how to integrate SMS notifications in your application
 */
class SmsExampleController extends Controller
{
    /**
     * Example 1: Send SMS after member registration
     */
    public function sendRegistrationSms(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        // Format phone to international format
        $phone = format_phone($validated['phone']);

        // Send credentials SMS
        $response = send_credentials_sms(
            $phone,
            $validated['email'],
            $validated['password']
        );

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'SMS with credentials sent successfully!',
                'phone' => $phone
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error'] ?? 'Failed to send SMS'
            ], 400);
        }
    }

    /**
     * Example 2: Send SMS after payment confirmation
     */
    public function sendPaymentSms(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_id' => 'required|string'
        ]);

        $phone = format_phone($validated['phone']);
        $order = Order::find($validated['order_id']);

        // Send payment confirmation
        $response = send_payment_sms(
            $phone,
            $order->member->name ?? 'Customer',
            $validated['amount'],
            $validated['transaction_id']
        );

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Payment confirmation SMS sent!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error']
            ], 400);
        }
    }

    /**
     * Example 3: Send welcome SMS to new member
     */
    public function sendWelcomeSms(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $member = Member::find($validated['member_id']);

        $response = send_welcome_sms($member->phone, $member->name);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Welcome SMS sent to ' . $member->phone
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error']
            ], 400);
        }
    }

    /**
     * Example 4: Send custom SMS
     */
    public function sendCustomSms(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160'
        ]);

        $phone = format_phone($validated['phone']);
        $response = send_sms($phone, $validated['message']);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'SMS sent successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error']
            ], 400);
        }
    }

    /**
     * Example 5: Batch send SMS to multiple members
     */
    public function batchSendSms(Request $request)
    {
        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'message' => 'required|string'
        ]);

        $smsService = app(SmsService::class);
        $members = Member::whereIn('id', $validated['member_ids'])->get();
        $phones = $members->pluck('phone')->toArray();

        $results = $smsService->batchSendSms($phones, $validated['message']);

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $failCount = count($results) - $successCount;

        return response()->json([
            'success' => $failCount === 0,
            'total' => count($results),
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'results' => $results
        ]);
    }

    /**
     * Example 6: Test phone number formatting
     */
    public function testPhoneFormat(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string'
        ]);

        $formatted = format_phone($validated['phone']);

        return response()->json([
            'input' => $validated['phone'],
            'formatted' => $formatted,
            'is_valid' => preg_match('/^\+\d{10,15}$/', $formatted) === 1
        ]);
    }

    /**
     * Example 7: Send SMS with custom dashboard URL
     */
    public function sendCredentialsWithUrl(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'dashboard_url' => 'nullable|url'
        ]);

        $phone = format_phone($validated['phone']);
        $smsService = app(SmsService::class);

        $response = $smsService->sendCredentialsSms(
            $phone,
            $validated['email'],
            $validated['password'],
            $validated['dashboard_url'] ?? route('member-login')
        );

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Credentials sent via SMS!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error']
            ], 400);
        }
    }

    /**
     * Example 8: Integration with Member Card Registration
     */
    public function sendMembershipCardSms(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'name' => 'required|string',
            'card_number' => 'required|string',
            'card_details' => 'nullable|string'
        ]);

        $phone = format_phone($validated['phone']);
        $smsService = app(SmsService::class);

        $response = $smsService->sendMembershipCardSms(
            $phone,
            $validated['name'],
            $validated['card_number'],
            $validated['card_details']
        );

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Membership card details sent via SMS!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error']
            ], 400);
        }
    }

    /**
     * Example 9: Check SMS service status
     */
    public function checkSmsStatus()
    {
        try {
            $smsService = app(SmsService::class);
            
            return response()->json([
                'success' => true,
                'message' => 'SMS service is working',
                'status' => 'online',
                'provider' => 'SMSQ',
                'api_endpoint' => 'https://console.smsq.global/api/v2/SendSMS'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMS service error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Example 10: Send OTP SMS
     */
    public function sendOtpSms(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string'
        ]);

        $phone = format_phone($validated['phone']);
        $otp = rand(100000, 999999);

        // Store OTP in cache for 10 minutes
        cache()->put("otp_{$phone}", $otp, now()->addMinutes(10));

        // Send OTP
        $smsService = app(SmsService::class);
        $response = $smsService->sendOtpSms($phone, $otp);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp_expires_in' => '10 minutes'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response['error']
            ], 400);
        }
    }
}
