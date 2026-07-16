<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        abort(404);
    }

    public function markAsRead(string $id)
    {
        abort_unless(auth()->user()->can('orders-show'), 403);

        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        abort_unless(auth()->user()->can('orders-show'), 403);

        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
