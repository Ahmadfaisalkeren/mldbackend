<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getNotifications();

        return response()->json([
            'status' => 200,
            'notifications' => $notifications,
            'message' => 'Notifications Fetched Successfully'
        ]);
    }

    public function markAllAsRead()
    {
        $notification = $this->notificationService->markAllAsRead();

        return response()->json([
            'status' => 200,
            'message' => 'Notifications Marked As Read Successfully',
            'notification' => $notification,
        ]);
    }
}
