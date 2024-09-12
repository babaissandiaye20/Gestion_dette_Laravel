<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Mail\FidelityCardMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Services\QRCodeService;
use App\Services\FidelityCardService;
use App\Services\PhotoStorageService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Facades\ClientRepositoryFacade;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ClientCreateRequest;
use  App\Services\UserService;
use App\Exceptions\ServiceException;
use App\Events\ClientFidelityEvent;
class ClientService implements ClientServiceInterface
{
    protected $qrCodeService;
    protected $fidelityCardService;
    protected $photoStorageService;
    protected $userService;
    public function __construct(QRCodeService $qrCodeService, FidelityCardService $fidelityCardService, PhotoStorageService $photoStorageService,UserService $userService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->fidelityCardService = $fidelityCardService;
        $this->photoStorageService = $photoStorageService;
        $this->userService = $userService;
    }

    public function registerUserForClient($request, $clientId)
    {
        $client = Client::find($clientId);
        if (!$client) {
            throw new ServiceException('Client not found', 404);
        }

        if ($client->user_id) {
            throw new \Exception('Ce client a déjà un compte utilisateur.');
        }

        $this->createUserForClient($request, $client);
        /*  event(new ClientFidelityEvent($client));  */
        return $client;
    }

    public function createClient($request)
{
    // Directly pass the request data to the ClientRepositoryFacade
    $client = ClientRepositoryFacade::create($request->all());

    // If user-related data is present, create a user for the client
    if ($request->has(['nom', 'prenom', 'login', 'password', 'password_confirmation'])) {
        $this->createUserForClient($request, $client);
    }
  /*   event(new ClientFidelityEvent($client));  */

    
    return $client;
}


    public function createUserForClient($request, $client)
    {
        $roleId = $request->input('role');
        $role = Role::find($roleId);

        if (!$role) {
            throw new ServiceException('Rôle not found', 404);
        }

        $userData = $request->only(['nom', 'prenom', 'login', 'password', 'password_confirmation']);
        $validator = Validator::make($userData, (new \App\Http\Requests\UserRequest())->rules(), (new \App\Http\Requests\UserRequest())->messages());

        if ($validator->fails()) {
            throw   new ServiceException(json_encode($validator->errors()));
        }

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            // Stocker la photo temporairement
            $photoPath = $photo->store('temp_photos');

        }
        $user = $this->userService->createUser($request->all());
        $client->user()->associate($user);
        $client->save();
    }

    protected function encodePhotoToBase64($photoUrl)
    {
        if ($photoUrl) {
            try {
                $imageData = file_get_contents($photoUrl);
                $imageExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                return 'data:image/' . $imageExtension . ';base64,' . base64_encode($imageData);
            } catch (  ServiceException $e) {
                return null;
            }
        }

        return null;
    }
    public function create(array $data)
    {
        // Directly pass the entire data array to the ClientRepositoryFacade
        $client = ClientRepositoryFacade::create($data);
    
        return response()->json([
            'statut' => 201,
            'message' => 'Client créé sans utilisateur avec succès.',
            'client' => $client
        ], 201);
    }
    
    public function getClientsByTelephones(array $telephones)
{
    $clients = ClientRepositoryFacade::getClientsByTelephones($telephones);

    // Parcourez chaque client et encodez la photo en base64 s'il y a un utilisateur avec une photo
    foreach ($clients as $client) {
        if ($client->user && $client->user->photo) {
            $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
        }
    }

    return ['statut' => 200, 'clients' => $clients,'message'=>'Success'];
}

    
public function getClientById($id)
{
    $client = ClientRepositoryFacade::getClientById($id);

    // Encodez la photo en base64 s'il y a un utilisateur avec une photo
    if ($client && $client->user && $client->user->photo) {
        $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
    }

    return ['statut' => 200, 'client' => $client,'message'=>'Sucess'];
}


public function getClientWithUser($id)
{
    $client = ClientRepositoryFacade::getClientWithUser($id);

    // Encodez la photo en base64 s'il y a un utilisateur avec une photo
    if ($client && $client->user && $client->user->photo) {
        $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
    }

    return ['statut' => 200, 'client' => $client,'message'=>'Sucess'];
}

    

    public function afficherDettes($clientId)
    {
        $dettes = ClientRepositoryFacade::afficherDettes($clientId);
        return ['statut' => 200, 'dettes' => $dettes,'message'=>'Succes'] ;
    }

    public function getClientsWithFilters(?string $comptes, ?string $etat): LengthAwarePaginator
    {
        $clients = ClientRepositoryFacade::getClientsWithFilters($comptes, $etat);
    
        // Parcourez chaque client et encodez la photo en base64 s'il y a un utilisateur avec une photo
        foreach ($clients as $client) {
            if ($client->user && $client->user->photo) {
                $client->user->photo_base64 = $this->encodePhotoToBase64($client->user->photo);
            }
        }
    
        return $clients;
    }
    
}