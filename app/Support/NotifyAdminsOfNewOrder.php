<?php

namespace App\Support;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewOrder
{
    public static function send(Order $order): void
    {
        $admins = User::permission('orders-show')->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new NewOrderNotification($order));
    }
}
