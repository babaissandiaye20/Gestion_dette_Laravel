<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Services\QRCodeService; // Assurez-vous que QRCodeService est importé
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Dette;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository implements ClientRepositoryInterface
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function registerUserForClient($request, $clientId)
    {
        $client = Client::find($clientId);
        if (!$client) {
            throw new \Exception('Client not found');
        }

        if ($client->user_id) {
            throw new \Exception('Ce client a déjà un compte utilisateur.');
        }

        $this->createUserForClient($request, $client);
        $this->generateQRCodeForClient($client); // Appel pour générer le QR code
        return $client;
    }

    public function createClient($request)
    {
        $client = new Client([
            'surnom' => $request->surnom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);
        $client->save();

        if ($request->has(['nom', 'prenom', 'login', 'password', 'password_confirmation'])) {
            $this->createUserForClient($request, $client);
        }

        $this->generateQRCodeForClient($client); // Appel pour générer le QR code
        return $client;
    }

    public function createUserForClient($request, $client)
    {
        $roleId = $request->input('role');
        $role = Role::find($roleId);

        if (!$role) {
            throw new \Exception('Role not found');
        }

        $userData = $request->only(['nom', 'prenom', 'login', 'password', 'password_confirmation']);
        $validator = Validator::make($userData, (new \App\Http\Requests\UserRequest())->rules(), (new \App\Http\Requests\UserRequest())->messages());

        if ($validator->fails()) {
            throw new \Exception(json_encode($validator->errors()));
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
            'photo' => $photoPath,
        ]);

        $client->user()->associate($user);
        $client->save();
    }

    public function create(array $data)
    {
        // Création du client sans utilisateur
        $client = new Client($data);
        $client->save();
        return $client;
    }

    public function getClientsByTelephones(array $telephones)
    {
        return Client::whereIn('telephone', $telephones)->get();
    }

    public function getClientById($id)
    {
        return Client::find($id);
    }

    public function getClientWithUser($id)
    {
        return Client::with('user')->find($id);
    }

    public function afficherDettes($clientId)
    {
        return Dette::where('client_id', $clientId)->with('articles', 'details')->get();
    }

    public function getClientsWithFilters(?string $comptes, ?string $etat): LengthAwarePaginator
    {
        $query = Client::query();

        // Ajouter des conditions en fonction des filtres fournis
        if ($comptes === 'oui') {
            $query->whereNotNull('user_id');
        } elseif ($comptes === 'non') {
            $query->whereNull('user_id');
        }

        if ($etat === 'oui') {
            $query->whereHas('user', function($q) {
                $q->where('etat', 'actif');
            });
        } elseif ($etat === 'non') {
            $query->whereHas('user', function($q) {
                $q->where('etat', 'inactif');
            });
        }

        // Charger les relations avec l'utilisateur uniquement si des filtres sont appliqués
        if ($comptes !== null || $etat !== null) {
            $query->with('user');
        }

        return $query->paginate(10);
    }

    protected function generateQRCodeForClient(Client $client)
    {
        $user = $client->user;
        $qrContent = "ID Client: " . $client->id . "\n" .   // Ajout de l'ID du client
                     "Nom: " . ($user->nom ?? 'N/A') . "\n" .
                     "Prénom: " . ($user->prenom ?? 'N/A') . "\n" .
                     "Téléphone: " . ($client->telephone ?? 'N/A') . "\n" .
                     "Surnom: " . ($client->surnom ?? 'N/A');
    
        // Définir le chemin du fichier QR code
        $qrCodePath = 'qrcodes/client_' . $client->id . '.png';
    
        // Appeler le service pour générer le QR code
        $this->qrCodeService->generateQRCode($qrContent, $qrCodePath);
    }
    
}
