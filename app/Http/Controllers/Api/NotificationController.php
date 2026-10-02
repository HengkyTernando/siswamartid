<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        $notifications->through(fn ($notification) => $this->notificationData($notification));

        return $this->success($notifications, 'Daftar notifikasi berhasil diambil');
    }

    public function read(Request $request, string $notification)
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        return $this->success($this->notificationData($item->fresh()), 'Notifikasi ditandai sudah dibaca');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->success(null, 'Semua notifikasi ditandai sudah dibaca');
    }

    private function notificationData($notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ];
    }
}
