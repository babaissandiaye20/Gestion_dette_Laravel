<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dette;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DetteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Suppose you have already created some clients
        $clientIds = Client::pluck('id')->toArray();

        Dette::create([
            'date' => '2024-08-01',
            'montant' => 1000.00,
            'montantDu' => 500.00,
            'montantRestant' => 500.00,
            'client_id' => $clientIds[array_rand($clientIds)],
        ]);

        Dette::create([
            'date' => '2024-08-02',
            'montant' => 1500.00,
            'montantDu' => 800.00,
            'montantRestant' => 700.00,
            'client_id' => $clientIds[array_rand($clientIds)],
        ]);

        // Ajoutez autant de dettes que nécessaire
    }
}
