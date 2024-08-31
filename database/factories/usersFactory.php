<?php

namespace Database\Factories;

use App\Models\users;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class usersFactory extends Factory
{
    protected $model = users::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'login' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'), // Hashed password
            'role' => $this->faker->randomElement(['admin', 'boutiquier', 'client']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a boutiquier.
     */
    public function boutiquier()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'boutiquier',
        ]);
    }

    /**
     * Indicate that the user is a client.
     */
    public function client()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
        ]);
    }
}
