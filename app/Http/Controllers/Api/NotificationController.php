<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::with(['user', 'request'])
            ->whereHas('request', function ($query) {
                $query->where('coordinator_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'user_name' => $notification->user->first_name . ' ' . $notification->user->last_name,
                    'user_image' => $notification->user->profile_picture,
                    'type' => $notification->request->type,
                    'date' => $notification->request->date,
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            });

        $unread_count = Notification::where('read', false)
            ->whereHas('request', function ($query) {
                $query->where('coordinator_id', auth()->id());
            })
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ]);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $notification = Notification::findOrFail($request->notification_id);
        $notification->update(['read' => true]);

        return response()->json(['success' => true]);
    }
}
