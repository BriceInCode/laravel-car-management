<?php

namespace Database\Factories;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(RoleType::cases()), // Rôle aléatoire
            'phone' => $this->faker->phoneNumber, // Numéro de téléphone aléatoire
            'address' => $this->faker->address, // Adresse aléatoire
            'profile_image' => $this->faker->imageUrl(), // URL d'image aléatoire
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a specific role.
     */
    public function withRole(RoleType $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }
}
