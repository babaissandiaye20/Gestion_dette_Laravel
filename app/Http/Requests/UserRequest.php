<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    return [
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'login' => 'required|string|unique:users,login|max:255',
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'role' => 'required|exists:roles,id', // Assurez-vous que le rôle existe
    ];
}
    public function messages()
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.exists' => 'Le rôle sélectionné est invalide. Veuillez sélectionner un rôle valide.',
        ];
    }
}
