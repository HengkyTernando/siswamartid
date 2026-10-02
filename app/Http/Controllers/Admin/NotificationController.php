<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function poll(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'notifications' => $user->notifications()->latest()->limit(8)->get()->map(fn ($notification) => [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at?->diffForHumans(),
            ]),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(DatabaseNotification $notification): RedirectResponse
    {
        abort_unless((int) $notification->notifiable_id === (int) auth()->id(), 403);

        $notification->markAsRead();
        $url = $notification->data['url'] ?? route('admin.dashboard');

        return redirect()->to($url);
    }

    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
