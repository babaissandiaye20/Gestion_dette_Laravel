<?php

namespace App\Notifications;

use App\Broadcasting\SmsNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Contracts\SmsService;
use App\Services\InfobipSmsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $recipient;

    /**
     * Create a new notification instance.
     *
     * @param string $recipient
     * @param string $message
     */
    public function __construct($recipient, $message)
    {
        $this->recipient = $recipient;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsNotificationChannel::class,'database'];
    }

    /**
     * Send SMS via the specified service.
     *
     * @param mixed $notifiable
     * @return void
     */
    public function toSms($notifiable)
    {
        // Assuming SmsService is injected using Laravel's Service Container
        app(InfobipSmsService::class)->sendSms($this->recipient, $this->message);

    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'recipient' => $this->recipient,
        ];
    }
}
