<?php

namespace App\Services;

class NotificationService
{
    public static function create($title, $message, $type = 'info', $userId = null, $data = [])
    {
        // Notificaciones deshabilitadas por requerimiento de performance
        return true;
    }

    public static function createForAllUsers($title, $message, $type = 'info', $data = [])
    {
        return true;
    }

    public static function getUnreadCount($userId = null)
    {
        return 0;
    }

    public static function getRecentNotifications($userId = null, $limit = 30)
    {
        return collect();
    }

    public static function getUnreadNotifications($userId = null, $limit = 3)
    {
        return collect();
    }

    public static function markAsRead($notificationId, $userId = null)
    {
        return null;
    }

    public static function markAllAsRead($userId = null)
    {
        return 0;
    }

    // Métodos específicos para diferentes tipos de acciones
    public static function notifyCreate($modelName, $itemName, $userId = null)
    {
        return true;
    }

    public static function notifyUpdate($modelName, $itemName, $userId = null)
    {
        return true;
    }

    public static function notifyDelete($modelName, $itemName, $userId = null)
    {
        return true;
    }

    public static function notifyLogin($userName, $userId = null)
    {
        return true;
    }
} 