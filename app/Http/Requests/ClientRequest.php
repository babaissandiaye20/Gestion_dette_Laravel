<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SenegalPhoneNumber;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $clientIdProvided = $this->input('client_id') !== null;
    
        return [
            'client_id' => 'nullable|exists:clients,id',
            'surnom' => $clientIdProvided ? 'nullable' : 'required|string|max:255|unique:clients,surnom',
            'telephone' => $clientIdProvided ? 'nullable' : ['required', 'string', 'unique:clients,telephone', new SenegalPhoneNumber()],
            'adresse' => $clientIdProvided ? 'nullable' : 'required|string|max:255',
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'login' => 'nullable|string|unique:users,login|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id', // Assurez-vous que la validation du rôle est correcte ici
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
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.exists' => 'Le rôle sélectionné est invalide.',
            'client_id.exists' => 'Le client spécifié n\'existe pas.',
        ];
    }
}
