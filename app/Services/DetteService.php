<?php

namespace App\Services;

interface DetteService
{
    public function getArticlesByDetteId(int $id);
    public function  getAllDettes($isSolde);
    public function getPaiementsByDetteId(int $id);
    public function getClientDettes(int $clientId);
    public function getDetteById($id);
    public function createDette(array $data);
    public function addArticlesToDette(array $articlesData, int $detteId);
}
