<?php

namespace Database\Factories;

use App\Enums\PermissionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(PermissionType::cases()), // Nom de permission aléatoire
            'description' => $this->faker->sentence, // Description aléatoire
        ];
    }
}
