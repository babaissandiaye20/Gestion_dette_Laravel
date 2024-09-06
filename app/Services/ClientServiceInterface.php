<?php
namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClientServiceInterface
{
    public function registerUserForClient($request, $clientId);
    public function createClient($request);
    public function createUserForClient($request, $client);
    public function create(array $data);
    public function getClientsByTelephones(array $telephones);
    public function getClientById($id);
    public function getClientWithUser($id);
    public function afficherDettes($clientId);
    public function getClientsWithFilters(?string $comptes, ?string $etat): LengthAwarePaginator;
}
