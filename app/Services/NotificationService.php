<?php

namespace App\Services;

use App\Models\Notification;

/**
 * Class NotificationService.
 */
class NotificationService
{
    public function getNotifications()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->take(7)->get();

        return $notifications;
    }

    public function markAllAsRead()
    {
        $notification = Notification::where('is_read', 0)->update(['is_read' => 1]);

        return $notification;
    }
}
