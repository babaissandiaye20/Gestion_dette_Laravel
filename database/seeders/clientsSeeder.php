<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\clients;

class clientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création de trois clients qui ont un compte utilisateur
        clients::factory()->count(3)->create();

        // Création de trois clients qui n'ont pas de compte utilisateur
        clients::factory()->count(3)->withoutUser()->create();
    }
}
