<?php

namespace App\Traits;

use App\Enums\Statues;

trait StatuesTrait
{
    public function response(Statues $statues, array $data = null): array
    {
        return [
            'statut' => $statues->code(),
            'message' => $statues->message(),
            'data' => $data,
        ];
    }
}
