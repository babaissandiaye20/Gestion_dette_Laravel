<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Broadcasting\SmsNotificationChannel;

class DemandeNotification extends Notification
{
    use Queueable;

    protected $demandeId;
    protected $clientId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($demandeId, $clientId)
    {
        $this->demandeId = $demandeId;
        $this->clientId = $clientId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Adding SMS channel along with database and optionally email
        return ['database', SmsNotificationChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line("Une nouvelle demande de dette (ID: {$this->demandeId}) a été créée pour le client ID: {$this->clientId}.")
                    ->action('Confirmer la demande', url("/demandes/{$this->demandeId}/confirmer"))
                    ->line('Merci de traiter cette demande.');
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        // Customize the message to be sent via SMS
        return "Nouvelle demande de dette ID: {$this->demandeId} pour le client ID: {$this->clientId}. Connectez-vous pour confirmer.";
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demandeId,
            'client_id' => $this->clientId,
            'message' => "Une nouvelle demande de dette a été créée pour le client ID: {$this->clientId}."
        ];
    }
}
