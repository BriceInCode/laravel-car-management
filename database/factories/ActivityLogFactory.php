<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Associe un utilisateur aléatoire
            'action' => $this->faker->word, // Une action aléatoire
            'description' => $this->faker->sentence, // Une description aléatoire
            'created_at' => now(), // Date de création
        ];
    }
}
