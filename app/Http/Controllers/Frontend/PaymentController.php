<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SSLCommerzService;
use App\Support\NotifyAdminsOfNewOrder;
use App\Support\OrderRedirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * SSLCommerz Success callback.
     */
    public function success(Request $request)
    {
        Log::info('SSLCommerz success callback received', $request->all());

        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');

        $order = Order::where('transaction_id', $tranId)->first();

        if (! $order) {
            Log::warning('SSLCommerz success: order not found', ['tran_id' => $tranId]);

            return redirect()->route('frontend.home')->with('error', 'Order not found.');
        }

        if ($order->payment_status === 'paid') {
            return OrderRedirect::respond(
                $request,
                $order,
                'Payment successful! Order #' . $order->id . ' confirmed.',
                true
            );
        }

        if (! $valId) {
            $order->update([
                'payment_status'  => 'failed',
                'payment_details' => json_encode($request->all()),
            ]);

            return $this->redirectToCheckoutWithStatus(
                'fail',
                'Payment validation failed (missing val_id). Please contact support.'
            );
        }

        $sslcommerz = new SSLCommerzService();
        $validation = $sslcommerz->validateTransaction($valId);

        $payload = array_merge($request->all(), $validation);
        $isValid = $sslcommerz->isSuccessfulValidation(
            $validation,
            (string) $order->transaction_id,
            (float) $order->final_amount
        );

        if ($isValid) {
            $this->markOrderAsPaid($order, $payload);

            return OrderRedirect::respond(
                $request,
                $order,
                'Payment successful! Order #' . $order->id . ' confirmed.',
                true
            );
        }

        Log::warning('SSLCommerz validation failed on success callback', [
            'order_id' => $order->id,
            'tran_id' => $tranId,
            'val_id' => $valId,
            'callback_status' => $request->input('status'),
            'validation' => $validation,
        ]);

        $order->update([
            'payment_status'  => 'failed',
            'payment_details' => json_encode($payload),
        ]);

        return $this->redirectToCheckoutWithStatus(
            'fail',
            'Payment was not completed. Please try again.'
        );
    }

    /**
     * SSLCommerz Fail callback.
     */
    public function fail(Request $request)
    {
        Log::info('SSLCommerz fail callback received', $request->all());

        $tranId = $request->input('tran_id');
        $order = Order::where('transaction_id', $tranId)->first();

        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status'  => 'failed',
                'status'          => 'pending',
                'payment_details' => json_encode($request->all()),
            ]);
        }

        return $this->redirectToCheckoutWithStatus(
            'fail',
            'Payment failed. Please try again or choose a different method.'
        );
    }

    /**
     * SSLCommerz Cancel callback.
     */
    public function cancel(Request $request)
    {
        Log::info('SSLCommerz cancel callback received', $request->all());

        $tranId = $request->input('tran_id');
        $order = Order::where('transaction_id', $tranId)->first();

        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status'  => 'cancelled',
                'status'          => 'canceled',
                'payment_details' => json_encode($request->all()),
            ]);
        }

        return $this->redirectToCheckoutWithStatus(
            'cancel',
            'Payment was cancelled.'
        );
    }

    /**
     * SSLCommerz IPN (Instant Payment Notification) - server-to-server.
     */
    public function ipn(Request $request)
    {
        Log::info('SSLCommerz IPN received', $request->all());

        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');

        $order = Order::where('transaction_id', $tranId)->first();

        if (! $order) {
            return response()->json(['status' => 'order_not_found'], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['status' => 'already_paid']);
        }

        if (! $valId) {
            return response()->json(['status' => 'missing_val_id'], 422);
        }

        $sslcommerz = new SSLCommerzService();
        $validation = $sslcommerz->validateTransaction($valId);

        if ($sslcommerz->isSuccessfulValidation(
            $validation,
            (string) $order->transaction_id,
            (float) $order->final_amount
        )) {
            $this->markOrderAsPaid($order, array_merge($request->all(), $validation));
        }

        return response()->json(['status' => 'ok']);
    }

    private function redirectToCheckoutWithStatus(string $status, string $message, bool $clearCart = false)
    {
        $query = [
            'payment_result' => $status,
            'payment_message' => $message,
        ];

        if ($clearCart) {
            $query['clear_cart'] = '1';
        }

        return redirect()->route('frontend.checkout', $query);
    }

    private function markOrderAsPaid(Order $order, array $details): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status'  => 'paid',
            'payment_date'    => now(),
            'status'          => 'confirmed',
            'payment_details' => json_encode($details),
        ]);

        $order->creditMemberPurchase();

        NotifyAdminsOfNewOrder::send($order);

        $this->sendPaymentConfirmationSms($order);
    }

    /**
     * Send payment confirmation SMS to customer
     */
    private function sendPaymentConfirmationSms(Order $order)
    {
        try {
            $member = $order->member;

            Log::info('Attempting to send payment SMS', [
                'order_id' => $order->id,
                'member_id' => $member?->id,
                'member_phone' => $member?->phone ?? 'NULL',
                'order_customer_phone' => $order->customer_phone ?? 'NULL',
            ]);

            if (! $member || ! $member->phone) {
                Log::warning('Cannot send payment SMS - member or phone not found', [
                    'order_id' => $order->id,
                    'member_id' => $member?->id,
                ]);

                return ['success' => false];
            }

            $phone = format_phone($member->phone);

            $response = send_payment_sms(
                $phone,
                $member->name,
                $order->final_amount,
                $order->transaction_id
            );

            if ($response['success']) {
                Log::info('Payment confirmation SMS sent successfully', [
                    'order_id' => $order->id,
                    'member_id' => $member->id,
                    'phone' => $phone,
                ]);
            } else {
                Log::warning('Failed to send payment confirmation SMS', [
                    'order_id' => $order->id,
                    'error' => $response['error'] ?? 'Unknown error',
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Exception while sending payment confirmation SMS', [
                'order_id' => $order->id,
                'exception' => $e->getMessage(),
            ]);

            return ['success' => false];
        }
    }
}
