<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);

        return view('Notification.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // If the notification has a URL and no specific detailed view logic is needed,
        // we could redirect. But the user asked for "detail yang lebih detail lagi".
        // So we will show a view with full details.

        return view('Notification.show', compact('notification'));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
