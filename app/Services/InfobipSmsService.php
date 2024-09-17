<?php
namespace App\Services;

use App\Models\Client as AppClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class InfobipSmsService implements SmsServiceInterface
{
    protected $infobipClient;


    protected $apiKey;
    protected $baseUri;

    public function __construct()
    {
        $this->apiKey = env('INFOBIP_API_KEY');
        $this->baseUri = 'https://api.infobip.com/sms/1/text/single'; // URL de l'API Infobip
    }

    public function sendSms($to, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUri, [
                'from' => env('INFOBIP_PHONE_NUMBER'),
                'to' => $to,
                'text' => $message
            ]);

            if ($response->failed()) {
                throw new \Exception('Error sending SMS: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }
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
