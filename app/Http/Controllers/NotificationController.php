<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = NotificationService::getUnreadCount();
        return response()->json(['count' => $count]);
    }

    public function getUnreadNotifications(): JsonResponse
    {
        $notifications = NotificationService::getUnreadNotifications();
        return response()->json(['notifications' => $notifications]);
    }

    public function getAllNotifications(): JsonResponse
    {
        $notifications = NotificationService::getRecentNotifications();
        return response()->json(['notifications' => $notifications]);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $notificationId = $request->input('notification_id');
        $notification = NotificationService::markAsRead($notificationId);
        
        if ($notification) {
            return response()->json(['success' => true, 'message' => 'Notificación marcada como leída']);
        }
        
        return response()->json(['success' => false, 'message' => 'Notificación no encontrada'], 404);
    }

    public function markAllAsRead(): JsonResponse
    {
        NotificationService::markAllAsRead();
        return response()->json(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
    }
} 