<?php

namespace App\Exceptions;

use Exception;

class RepositoryException extends Exception
{
    // Propriété pour stocker des informations supplémentaires sur l'exception
    protected $context;
    protected $details;
    // Constructeur
    public function __construct($message = "", $code = 0, Exception $previous = null, $context = null,$details = null)
    {
        // Appeler le constructeur parent
        parent::__construct($message, $code, $previous);
        // Stocker le contexte supplémentaire, s'il y en a
        $this->context = $context;
        $this->details = $details;
    }

    // Méthode pour obtenir le contexte supplémentaire de l'exception
    public function getContext()
    {
        return $this->context;
    }
    public function getDetails()
    {
        return $this->details;
    }

    // Méthode pour déterminer si l'exception est liée à une erreur de base de données
    public function isDatabaseError()
    {
        // Vous pouvez ajouter des vérifications spécifiques ici, par exemple en fonction du code d'erreur
        return $this->code >= 500; // Par exemple, considérer les codes d'erreur 500+ comme erreurs de base de données
    }
}