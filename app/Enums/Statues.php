<?php
namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self SUCCESS()
 * @method static self BAD_REQUEST()
 * @method static self SERVER_ERROR()
 * @method static self ECHEC()
 */
class Statues extends Enum
{
    // Définit le code HTTP associé à chaque statut
    public function code(): int
    {
        return match ($this->value) {
            self::SUCCESS()->value => 200,
            self::BAD_REQUEST()->value => 400,
            self::SERVER_ERROR()->value => 500,
            self::ECHEC()->value => 403, // Code HTTP pour ECHEC (e.g., 403 Forbidden)
        };
    }

    // Définit le message associé à chaque statut
    public function message(): string
    {
        return match ($this->value) {
            self::SUCCESS()->value => 'L\'opération a réussi.',
            self::BAD_REQUEST()->value => 'Requête incorrecte.',
            self::SERVER_ERROR()->value => 'Erreur interne du serveur.',
            self::ECHEC()->value => 'Vous n\'êtes pas autorisé à effectuer cette action.', // Message pour ECHEC
        };
    }

    // Retourne la date actuelle
    public function date(): string
    {
        return date('Y-m-d H:i:s');
    }
}
