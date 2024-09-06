<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\PhotoStorageService;
use Exception;

class UserService
{
    protected $photoStorageService;

    public function __construct(PhotoStorageService $photoStorageService)
    {
        $this->photoStorageService = $photoStorageService;
    }

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

        $photoUrl = null;

        if (isset($userData['photo'])) {
            $photo = $userData['photo'];
            $photoUrl = $this->photoStorageService->uploadPhoto($photo);
        }

      /*   if (is_null($photoUrl)) {
            throw new Exception('Photo upload failed.');
        }
 */
        $user = User::create([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'login' => $userData['login'],
            'password' => Hash::make($userData['password']),
            'role_id' => $role->id,
            'photo' => $photoUrl,
        ]);

        return $user;
    }
}
