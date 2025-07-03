<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public static function create($title, $message, $type = 'info', $userId = null, $data = [])
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data
        ]);
    }

    public static function createForAllUsers($title, $message, $type = 'info', $data = [])
    {
        $users = User::all();
        
        foreach ($users as $user) {
            self::create($title, $message, $type, $user->id, $data);
        }
    }

    public static function getUnreadCount($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public static function getRecentNotifications($userId = null, $limit = 30)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getUnreadNotifications($userId = null, $limit = 3)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function markAsRead($notificationId, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
            
        if ($notification) {
            $notification->markAsRead();
        }
        
        return $notification;
    }

    public static function markAllAsRead($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    // Métodos específicos para diferentes tipos de acciones
    public static function notifyCreate($modelName, $itemName, $userId = null)
    {
        $title = 'Creación Exitosa';
        $message = "Se ha creado exitosamente el {$modelName}: {$itemName}";
        
        return self::create($title, $message, 'success', $userId);
    }

    public static function notifyUpdate($modelName, $itemName, $userId = null)
    {
        $title = 'Actualización Exitosa';
        $message = "Se ha actualizado exitosamente el {$modelName}: {$itemName}";
        
        return self::create($title, $message, 'info', $userId);
    }

    public static function notifyDelete($modelName, $itemName, $userId = null)
    {
        $title = 'Eliminación Exitosa';
        $message = "Se ha eliminado exitosamente el {$modelName}: {$itemName}";
        
        return self::create($title, $message, 'warning', $userId);
    }

    public static function notifyLogin($userName, $userId = null)
    {
        $title = 'Inicio de Sesión';
        $message = "¡Bienvenido, {$userName}! Se ha conectado correctamente al sistema.";
        
        return self::create($title, $message, 'success', $userId);
    }
} 