<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NotificationEvent;

class NotificationController extends Controller
{
    public function getUnreadNotifications()
    {
        $user = auth()->user();
        $unreadNotifications = $user->unreadNotifications;

        return response()->json([
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        // Notify the frontend about the update
        event(new NotificationEvent(['message' => 'All notifications marked as read', 'user_id' => $user->id]));

        return response()->noContent();
    }
}

