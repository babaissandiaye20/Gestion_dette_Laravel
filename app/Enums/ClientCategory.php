<?php
namespace App\Enums;


class ClientCategory
{
    const BRONZE = 'bronze';
    const SILVER = 'silver';
    const GOLD = 'gold';

    public static function getCategories()
    {
        return [
            self::BRONZE,
            self::SILVER,
            self::GOLD,
        ];
    }

    // Méthode pour convertir une valeur en constante lisible
    public static function from(string $value)
    {
        switch ($value) {
            case self::BRONZE:
                return 'Bronze';
            case self::SILVER:
                return 'Silver';
            case self::GOLD:
                return 'Gold';
            default:
                throw new \InvalidArgumentException("Valeur de catégorie non valide : $value");
        }
    }

    // Méthode pour convertir la constante lisible en valeur pour la base de données
    public static function toValue(string $label)
    {
        switch (strtolower($label)) {
            case 'bronze':
                return self::BRONZE;
            case 'silver':
                return self::SILVER;
            case 'gold':
                return self::GOLD;
            default:
                throw new \InvalidArgumentException("Label de catégorie non valide : $label");
        }
    }
}
