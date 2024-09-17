<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use App\Services\SmsServiceInterface;
use App\Notifications\InvoicePaid;
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
         * @param \App\Notifications\InvoicePaid $notification
         * @return void
         */
        public function send($notifiable, InvoicePaid $notification)
        {


            $message = $notification->toSms($notifiable);
            $recipient = $notifiable->routeNotificationFor('sms');

            if ($recipient) {
                $this->InfoBipService->sendSms($recipient, $message);
            }
        }
    }
