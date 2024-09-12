<?php
namespace App\Services;

interface ArticleService
{
    public function all();
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function updateQuantities(array $articles);
    public function findByLibelle($libelle);
    public function findByEtat($disponible);
}
