<?php
namespace App\Broadcasting;


use Illuminate\Notifications\Notification;
use App\Services\InfobipSmsService;

class SmsNotificationChannel
{
    protected $InfoBipService;

    public function __construct(InfobipSmsService $InfoBipService)
    {
        $this->InfoBipService = $InfoBipService;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Assurez-vous que la notification a une méthode toSms
        if (method_exists($notification, 'toSms')) {
            $message = $notification->toSms($notifiable);
            $recipient = $notifiable->routeNotificationFor('sms');

            if ($recipient) {
                $this->InfoBipService->sendSms($recipient, $message);
            }
        }
    }
}
