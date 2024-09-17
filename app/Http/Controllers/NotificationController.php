<?php

namespace App\Http\Controllers;

use App\Services\SmsServiceInterface;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Routing\Controller;
class NotificationController extends Controller
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Endpoint pour envoyer un SMS
     */
    public function sendSms(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $to = $request->input('to');
        $message = $request->input('message');

        // Appeler la méthode sendSms
        $this->smsService->sendSms($to, $message);

        return response()->json(['message' => 'SMS envoyé avec succès']);
    }

    /**
     * Endpoint pour notifier tous les clients avec des dettes
     */
    public function notifyClientsWithDebts()
    {
        // Appeler la méthode notifyClientsWithDebts
        $this->smsService->notifyClientsWithDebts();

        return response()->json(['message' => 'Notifications envoyées aux clients avec des dettes']);
    }

    /**
     * Endpoint pour récupérer toutes les notifications
     */
    public function getAllNotifications()
    {
        $notifications = Notification::all();

        return response()->json($notifications);
    }

    /**
     * Endpoint pour récupérer uniquement les notifications non lues
     */
    public function getUnreadNotifications()
    {
        $unreadNotifications = Notification::where('is_read', false)->get();

        return response()->json($unreadNotifications);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification non trouvée'], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marquée comme lue']);
    }
}
