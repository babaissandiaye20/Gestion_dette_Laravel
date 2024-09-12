<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dette_id' => 'required|exists:dettes,id',
            'montant' => 'required|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return [
            'dette_id.required' => 'The debt ID is required.',
            'dette_id.exists' => 'The provided debt ID does not exist.',
            'montant.required' => 'The amount is required.',
            'montant.numeric' => 'The amount must be a number.',
            'montant.min' => 'The amount must be at least 0.01.',
        ];
    }
}
