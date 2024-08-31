<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SenegalPhoneNumber implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^7[0-9]{8}$/', $value);
    }

    public function message()
    {
        return 'Le numéro de téléphone doit commencer par 7 et contenir 9 chiffres.';
    }
}
