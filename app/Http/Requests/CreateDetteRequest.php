<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDetteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
            'articles' => 'required|array',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qustock' => 'required|integer|min:1',  // Remplacez qtestock par qustock
           'articles.*.prix' => 'required|numeric',
            'paiement.montant' => 'nullable|numeric|min:0'
        ];
    }
}
