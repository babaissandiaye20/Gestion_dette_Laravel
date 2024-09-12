<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $userData)
    {
        $validator = Validator::make($userData, (new \App\Http\Requests\UserRequest())->rules(), (new \App\Http\Requests\UserRequest())->messages());
    
        if ($validator->fails()) {
            throw new Exception(json_encode($validator->errors()));
        }
    
        $role = $this->userRepository->findRoleById($userData['role']);
    
        if (!$role) {
            throw new Exception('Role not found');
        }
    
        $user = $this->userRepository->create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
        ]);
    
        if (isset($userData['photo'])) {
            $photoPath = $userData['photo']->store('photos/temp', 'public');
            Log::info('Photo path saved: ' . $photoPath);
            event(new UserCreated($user, $photoPath));
        }
        
        return $user;
    }
}
