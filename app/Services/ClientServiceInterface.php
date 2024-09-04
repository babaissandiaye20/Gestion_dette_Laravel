<?php
namespace App\Services;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request; 
use Illuminate\Pagination\LengthAwarePaginator;
interface ClientServiceInterface
{
    public function register(ClientRequest $request);
    public function create(ClientCreateRequest $request);
    public function getClientsByTelephones(Request $request);
    public function getClientById($id);
    public function getClientWithUser(Request $request, $id);
    public function afficherDettes($clientId);
    public function getClientsWithFilters(?string $comptes, ?string $etat): LengthAwarePaginator;
}
