<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Article;

class UpdateArticlesQuantitiesRequest extends FormRequest
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
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => [
                'required',
                'integer',
                Rule::exists('articles', 'id')
            ],
            'articles.*.quantity' => 'required|integer|min:0',
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'articles.required' => 'Vous devez fournir au moins un article.',
            'articles.array' => 'Les articles doivent être sous forme de tableau.',
            'articles.*.articleId.required' => "L'ID de l'article est obligatoire.",
            'articles.*.articleId.integer' => "L'ID de l'article doit être un entier.",
            'articles.*.articleId.exists' => "L'article avec l'ID :input n'existe pas.",
            'articles.*.quantity.required' => 'La quantité est obligatoire.',
            'articles.*.quantity.integer' => 'La quantité doit être un entier.',
            'articles.*.quantity.min' => 'La quantité doit être un nombre positif.',
        ];
    }
}
