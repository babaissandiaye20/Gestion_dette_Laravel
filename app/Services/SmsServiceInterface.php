<?php
namespace App\Services;

interface SmsServiceInterface
{
    public function sendSms($to, $message);
    public function notifyClientsWithDebts();
}
