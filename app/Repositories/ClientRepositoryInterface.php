<?php
namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface
{
    public function create(array $data);
    public function findClientById($id);
    public function findClientWithUserById($id);
    public function getClientsByTelephones(array $telephones);
    public function getClientsWithFilters(?string $comptes, ?string $etat): LengthAwarePaginator;
    public function afficherDettes($clientId);
}
