<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::factory()->count(4)->create(); // Crée 4 rôles (par exemple, Admin, User, Moderator, Guest)
    }
}
