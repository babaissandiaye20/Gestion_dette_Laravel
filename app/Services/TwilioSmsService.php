<?php 
namespace App\Services;

namespace App\Services;

use Twilio\Rest\Client as TwilioClient; // Renommer Twilio Client en TwilioClient
use App\Models\Client as AppClient; // Renommer votre modèle Client en AppClient

class TwilioSmsService implements SmsServiceInterface
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new TwilioClient(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendSms($to, $message)
    {
        $this->twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message
        ]);
    }

    public function notifyClientsWithDebts()
    {
        // Récupérer les clients avec des dettes non soldées
        $clients = AppClient::with('dettes')->whereHas('dettes', function ($query) {
            $query->where('montant', '>', 0);
        })->get();

        foreach ($clients as $client) {
            $totalDebt = []; // Tableau pour regrouper les dettes par ID

            foreach ($client->dettes as $dette) {
                $remainingDebt = $dette->montant - $dette->total_paiements;

                if ($remainingDebt > 0) {
                    // Si l'ID de la dette existe déjà dans le tableau, on additionne
                    if (isset($totalDebt[$dette->id])) {
                        $totalDebt[$dette->id] += $remainingDebt;
                    } else {
                        // Sinon, on ajoute la dette à la liste avec son montant restant
                        $totalDebt[$dette->id] = $remainingDebt;
                    }
                }
            }

            // Une fois toutes les dettes regroupées, envoyer un SMS si nécessaire
            foreach ($totalDebt as $debtId => $sumDebt) {
                $user = $client->user;

                $message = "Bonjour {$user->prenom} {$user->nom}, vous avez une dette totale de {$sumDebt} FCFA pour la dette #{$debtId}. Merci de régulariser.";

                // Envoyer le SMS
                $this->sendSms($client->telephone, $message);
            }
        }
    }
}


