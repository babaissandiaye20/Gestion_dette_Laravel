<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SenegalPhoneNumber implements Rule
{
    public function passes($attribute, $value)
    {
        // Vérifie si le numéro commence par +221 et contient 9 chiffres après
        if (preg_match('/^\+2217[05678][0-9]{7}$/', $value)) {
            return true;
        }

        // Vérifie si le numéro commence par 77, 78, 76, 70 et contient 9 chiffres
        if (preg_match('/^7[05678][0-9]{7}$/', $value)) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return 'Le numéro de téléphone doit commencer par +221 suivi de 9 chiffres ou par l\'un des préfixes 77, 78, 76, 70,70 et contenir 9 chiffres.';
    }
}
