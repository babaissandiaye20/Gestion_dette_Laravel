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
use App\Services\FidelityCardService;
use App\Mail\FidelityCardMail;
use Illuminate\Support\Facades\Mail;
use Cloudinary\Cloudinary;
use App\Services\PhotoStorageService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;



class ClientRepository implements ClientRepositoryInterface
{
    protected $qrCodeService;
    protected $fidelityCardService;
     protected $photoStorageService;
    public function __construct(QRCodeService $qrCodeService,FidelityCardService $fidelityCardService,PhotoStorageService $photoStorageService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->fidelityCardService = $fidelityCardService;
        $this->photoStorageService = $photoStorageService;
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
        $qrCodePath = $this->generateQRCodeForClient($client);

        // Pass the $qrCodePath to generateFidelityCardForClient
        $fidelityCardPath = $this->generateFidelityCardForClient($client, $qrCodePath);
        Mail::to($client->user->login)->send(new FidelityCardMail($client, $fidelityCardPath));
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

        
        $qrCodePath = $this->generateQRCodeForClient($client);
        $fidelityCardPath = $this->generateFidelityCardForClient($client, $qrCodePath);
        Mail::to($client->user->login)->send(new FidelityCardMail($client, $fidelityCardPath));
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

     
        $photoUrl = null;  // Initialize $photoUrl


        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
         
            // Use PhotoStorageService to handle photo upload
            $photoUrl = $this->photoStorageService->uploadPhoto($photo);
            
           
        }

    // Vérification si $photoUrl est null après la tentative de téléchargement
    if (is_null($photoUrl)) {
        throw new \Exception('Échec du téléchargement de la photo. Veuillez réessayer ou fournir une photo valide.');
    }
    

        $user = User::create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
            'photo' => $photoUrl,
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
        $clients = Client::whereIn('telephone', $telephones)->with('user')->get();
    
        foreach ($clients as $client) {
            if ($client->user && $client->user->photo) {
                $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
            }
        }
    
        return $clients;
    }
    

    public function getClientById($id)
    {
        $client = Client::with('user')->find($id);
    
        if ($client && $client->user && $client->user->photo) {
            $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
        }
    
        return $client;
    }
    

    public function getClientWithUser($id)
    {
        $client = Client::with('user')->find($id);
    
        if ($client && $client->user && $client->user->photo) {
            $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
        }
    
        return $client;
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

    $clients = $query->paginate(10);

    // Encoder les photos en base64 pour chaque client
    foreach ($clients as $client) {
        if ($client->user && $client->user->photo) {
            $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
        }
    }

    return $clients;
}


    protected function generateQRCodeForClient(Client $client): string
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
    
    return $qrCodePath; // Retourner le chemin du QR code
}


protected function generateFidelityCardForClient(Client $client, string $qrCodePath): string
{
    // Utilisateur lié au client
    $user = $client->user;

    // Récupération de l'URL de la photo de l'utilisateur
    $photoUrl = $user->photo; // URL Cloudinary ou autre URL stockée dans la base de données
    
    // Encoder la photo en base64 si nécessaire
    $encodedPhoto = $this->encodePhotoToBase64($photoUrl);

    // Dossier où la carte de fidélité sera enregistrée
    $fidelityCardDir = storage_path('app/public/fidelity_cards');
    
    // Vérifier si le dossier existe, sinon le créer
    if (!is_dir($fidelityCardDir)) {
        mkdir($fidelityCardDir, 0755, true); // Création du dossier avec les permissions appropriées
    }

    // Générer la carte de fidélité (en utilisant le service)
    $fidelityCardPath = $this->fidelityCardService->generateFidelityCard($client, $qrCodePath, $encodedPhoto);

    return $fidelityCardPath;
}

protected function encodePhotoToBase64($photoUrl)
{
    // Vérifier que l'URL est valide
    if ($photoUrl) {
        try {
            // Récupérer le contenu de l'image depuis l'URL
            $imageData = file_get_contents($photoUrl);
            
            // Obtenir l'extension de l'image à partir de l'URL
            $imageExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            
            // Encoder l'image en base64
            return 'data:image/' . $imageExtension . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            // Gérer les erreurs (e.g. URL invalide ou inaccessible)
            return null;
        }
    }
    
    return null;
}

    
}
