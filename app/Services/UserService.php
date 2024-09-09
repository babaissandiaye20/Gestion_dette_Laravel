<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    public function createUser(array $userData)
    {
        $validator = Validator::make($userData, (new \App\Http\Requests\UserRequest())->rules(), (new \App\Http\Requests\UserRequest())->messages());
    
        if ($validator->fails()) {
            throw new Exception(json_encode($validator->errors()));
        }
    
        $roleId = $userData['role'];
        $role = Role::find($roleId);
    
        if (!$role) {
            throw new Exception('Role not found');
        }
    
        // Créer l'utilisateur sans attendre l'upload de la photo
        $user = User::create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
        ]);
    
        if (isset($userData['photo'])) {
            $photoPath = $userData['photo']->store('photos/temp', 'public'); // Utilisation du disque public
            Log::info('Photo path saved: ' . $photoPath); // Log pour vérifier le chemin
            event(new UserCreated($user, $photoPath)); // Passer le chemin de la photo
        }
        
        return $user;
    }
}

