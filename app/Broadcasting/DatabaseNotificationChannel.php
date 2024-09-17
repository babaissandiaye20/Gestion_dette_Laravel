<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use App\Models\NotificationLog;

class DatabaseNotificationChannel
{
    public function send($notifiable, Notification $notification)
    {
        // Récupérer les informations de la notification
        $message = $notification->toDatabase($notifiable);
        $userId = $notifiable->id; // ID de l'utilisateur notifiable

        // Enregistrer la notification dans la table NotificationLog
        NotificationLog::create([
            'user_id' => $userId,
            'message' => $message,
            'type' => get_class($notification),
            'created_at' => now(),
        ]);
    }
}
