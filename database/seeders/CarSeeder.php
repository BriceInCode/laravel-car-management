<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Car::factory()->count(3)->create([
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        });
    }
}
