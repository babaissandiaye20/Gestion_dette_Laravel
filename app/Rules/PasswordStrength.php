<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordStrength implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/[A-Z]/', $value) &&    // Au moins une majuscule
               preg_match('/[a-z]/', $value) &&    // Au moins une minuscule
               preg_match('/[0-9]/', $value) &&    // Au moins un chiffre
               preg_match('/[@$!%*?&#]/', $value); // Au moins un caractère spécial
    }

    public function message()
    {
        return 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, et un caractère spécial.';
    }
}
