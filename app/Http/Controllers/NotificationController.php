<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        // Show only 4 notifications unless show_all parameter is set
        $showAll = request('show_all', false);
        $limit = $showAll ? 50 : 4;
        
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return view('Notification.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // If notification has a URL and no specific detailed view logic is needed,
        // we could redirect. But for now, show a view with full details.

        return view('Notification.show', compact('notification'));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    public function unreadCount()
    {
        $count = auth()->user()->unreadNotifications->count();
        
        return response()->json(['count' => $count]);
    }

    public function recent()
    {
        // Get recent 3 notifications for dropdown
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notifikasi',
                    'message' => $notification->data['message'] ?? '',
                    'type' => $notification->type,
                    'created_at' => $notification->created_at->toISOString(),
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }
}
