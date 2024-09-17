<?php

namespace App\Services;

interface ArchivageInterface
{
    public function store($data);
    public function recuper($referenceOrFilter = null);
}
