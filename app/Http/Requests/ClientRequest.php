<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SenegalPhoneNumber;
use App\Rules\PasswordStrength;


class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $clientIdProvided = $this->input('client_id') !== null;
        $userInfoProvided = $this->has(['nom', 'prenom', 'login', 'password']);
    
        return [
            'client_id' => 'nullable|exists:clients,id',
            'surnom' => $clientIdProvided ? 'nullable' : 'required|string|max:255|unique:clients,surnom',
            'telephone' => $clientIdProvided ? 'nullable' : ['required', 'string', 'unique:clients,telephone', new SenegalPhoneNumber()],
            'adresse' => $clientIdProvided ? 'nullable' : 'required|string|max:255',
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'login' => 'nullable|string|unique:users,login|max:255',
            'password' => [
                $clientIdProvided ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
                new PasswordStrength(),
            ],
            // Rendre le rôle facultatif si aucune information utilisateur n'est fournie
            'role' => $userInfoProvided ? 'required|exists:roles,id' : 'nullable|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'surnom.required' => 'Le surnom est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password_strength' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, et un caractère spécial.',
            'role.exists' => 'Le rôle sélectionné est invalide.',
            'client_id.exists' => 'Le client spécifié n\'existe pas.',
            'photo.mimes' => 'L\'image doit être de type jpeg, png, jpg ou gif.',
            'photo.max' => 'L\'image ne doit pas dépasser 2MB.',
        ];
    }
}
