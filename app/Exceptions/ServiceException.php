<?php

namespace App\Exceptions;

use Exception;

class ServiceException extends Exception
{
    // Propriété pour stocker des données supplémentaires sur l'exception
    protected $details;

    // Constructeur
    public function __construct($message = "", $code = 0, Exception $previous = null, $details = null)
    {
        // Appeler le constructeur parent
        parent::__construct($message, $code, $previous);

        // Stocker les détails supplémentaires, s'il y en a
        $this->details = $details;
    }

    // Méthode pour obtenir les détails supplémentaires de l'exception
    public function getDetails()
    {
        return $this->details;
    }

    // Méthode pour déterminer si l'exception est considérée comme une erreur grave
    public function isCritical()
    {
        return $this->code >= 500; // Par exemple, les codes d'erreur 500+ peuvent être considérés comme critiques
    }
}