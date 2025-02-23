<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(), // Expéditeur aléatoire
            'receiver_id' => User::factory(), // Destinataire aléatoire
            'content' => $this->faker->paragraph, // Contenu aléatoire
            'created_at' => now(), // Date de création
        ];
    }
}
