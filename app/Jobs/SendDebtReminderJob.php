<?php

namespace App\Jobs;

use App\Models\Dette;
use App\Models\User;
use App\Notifications\DebtReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\InvoicePaid;

class SendDebtReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        // Parcours toutes les dettes et récupère les clients avec leur dette totale
        $dettes = Dette::with('client')->get();

    foreach ($dettes as $dette) {
        $client = $dette->client; // Récupère le client associé
        $montant = $dette->montant; // Montant de la dette
        //$user = $client->load(user); // Missing semicolon added here

        // Crée le message personnalisé
        $message = "Bonjour {$client->surnom}, vous avez une dette de {$montant} FCFA. Merci de régulariser.";

        // Envoie la notification au client
        $user = User::find(253);
        $user->notify(new InvoicePaid('+221755263051',$message));
    }

    }
}
