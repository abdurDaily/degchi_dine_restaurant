<?php

namespace App\Support;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewOrder
{
    public static function send(Order $order): void
    {
        $admins = User::permission('orders-show')->get();

        if ($admins->isEmpty()) {
            return;
        }

        try {
            Notification::send($admins, new NewOrderNotification($order));
        } catch (\Throwable $e) {
            Log::error('Failed to notify admins of new order #'.$order->id.': '.$e->getMessage());
        }
    }
}
