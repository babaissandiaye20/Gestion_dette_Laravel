<?php

namespace App\Repositories;

use App\Models\Dette;

interface DetteRepositories
{
    public function findArticlesByDetteId(int $id);
    public function findPaiementsByDetteId(int $id);
    public function getDettesByClientId(int $clientId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findById(int $id);
    public function findByClient(int $clientId);
    public function getAllDettes($isSolde);
}
