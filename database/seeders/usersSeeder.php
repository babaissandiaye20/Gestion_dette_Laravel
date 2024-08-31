<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\users;

class usersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création d'un admin
        users::factory()->admin()->create();

        // Création d'un boutiquier
        users::factory()->boutiquier()->create();

        // Création d'un client (qui a un compte)
        users::factory()->client()->create();
    }
}
