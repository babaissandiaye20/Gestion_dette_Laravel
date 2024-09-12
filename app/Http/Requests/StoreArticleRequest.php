<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenez les règles de validation qui s'appliquent à la demande.
     */
    public function rules(): array
    {
        return [
            'libelle' => 'required|string|max:255|unique:articles,libelle', 
            'prix' => 'required|numeric|min:0',
            'qutestock' => 'required|integer|min:0',
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique'=>'le libellé est déja utiliée',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être un nombre positif.',
            'qutestock.required' => 'La quantité de stock est obligatoire.',
            'qutestock.integer' => 'La quantité de stock doit être un entier.',
            'qutestock.min' => 'La quantité de stock doit être un nombre positif.',

        ];
    }
}
