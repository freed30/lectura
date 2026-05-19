<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $dbNotification = $request->user()
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail();

        $dbNotification->markAsRead();

        return back()->with('notification_status', 'Notification marquee comme lue.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('notification_status', 'Toutes les notifications sont marquees comme lues.');
    }
}
