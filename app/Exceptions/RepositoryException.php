<?php

namespace App\Exceptions;

use Exception;

class RepositoryException extends Exception
{
    // Messages pour le ClientRepository
    public static function clientNotFound(): self
    {
        return new self('Client non trouvé.');
    }

    public static function roleNotFound(): self
    {
        return new self('Rôle non trouvé.');
    }

    public static function clientAlreadyHasUserAccount(): self
    {
        return new self('Ce client a déjà un compte utilisateur.');
    }

    public static function photoUploadFailed(): self
    {
        return new self('Échec du téléchargement de la photo. Veuillez réessayer ou fournir une photo valide.');
    }

    // Messages pour le ProductRepository (ajoutez des méthodes similaires pour chaque repository)
    public static function productNotFound(): self
    {
        return new self('Produit non trouvé.');
    }

    public static function categoryNotFound(): self
    {
        return new self('Catégorie non trouvée.');
    }

    // Ajoutez d'autres messages d'erreur spécifiques pour d'autres repositories si nécessaire
}
