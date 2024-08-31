<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }
    
    public function messages()
    {
        return [
            'login.required' => 'Le login est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}
