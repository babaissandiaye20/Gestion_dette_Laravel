<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SenegalPhoneNumber;


class ClientCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'surnom' => 'required|string|max:255|unique:clients,surnom',
            'telephone' => ['required', 'string', 'unique:clients,telephone', new SenegalPhoneNumber()],
            'adresse' => 'required|string|max:255',
            'category' => 'in:bronze,silver,gold',
            'max_debt_amount' => 'required_if:category,silver|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'surnom.required' => 'Le surnom est obligatoire.',
            'surnom.unique' => 'Ce surnom est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'max_debt_amount.required_if' => 'Le montant de dette maximale est requis pour la catégorie Silver.',
                    'max_debt_amount.numeric' => 'Le montant de dette maximale doit être un nombre.',
                    'max_debt_amount.min' => 'Le montant de dette maximale doit être supérieur à zéro.',
        ];
    }
}
