<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminActivityNotification;

class AdminNotificationService
{
    public function notifyAdmins(array $payload, ?int $exceptUserId = null): void
    {
        User::where('role', 'admin')
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->get()
            ->each(fn (User $admin) => $admin->notify(new AdminActivityNotification($payload)));
    }
}
