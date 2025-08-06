<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetch()
    {
        $user = Auth::user();
        return response()->json([
            'unread' => $user->unreadNotifications,
            'read' => $user->readNotifications()->limit(5)->get(),
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->update(['read' => true]);
        
        return response()->json(['success' => true]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'string',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $userIds = $request->input('user_ids');
        $title = $request->input('title');
        $message = $request->input('message');
        $notificationsData = [];
        $now = now();

        if (in_array('all', $userIds)) {
            $targetUserIds = User::where('role', '!=', 'admin')->pluck('id');
        } else {
            $targetUserIds = User::whereIn('id', $userIds)->pluck('id');
        }

        foreach ($targetUserIds as $userId) {
            $notificationsData[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($notificationsData)) {
            Notification::insert($notificationsData);
        }
        
        return response()->json(['message' => 'Notification sent successfully!']);
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $notification->update($request->only('title', 'message'));

        return response()->json([
            'message' => 'Notification updated successfully!',
            'notification' => $notification
        ]);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully!']);
    }
}
