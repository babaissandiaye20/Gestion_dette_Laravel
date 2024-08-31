<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\clients;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\users; //

class clientsFactory extends Factory
{
    protected $model = clients::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'telephone' => $this->faker->unique()->phoneNumber(),
            'surnom' => $this->faker->unique()->userName(),
            'adresse' => $this->faker->address(),
            'user_id' => users::factory(),// Création d'un utilisateur associé
        ];
    }

    /**
     * Indicate that the client is not a user (no associated user account).
     */
    public function withoutUser()
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }
}

