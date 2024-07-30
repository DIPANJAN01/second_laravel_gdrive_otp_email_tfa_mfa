<?php

namespace Database\Seeders;

use App\Models\Tutor;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //comment this out because otherwise running seed will apply this again, and since these emails already exist in the users table (because we seeded them once before) and have the unique constraints. it'll give errors because laravel will try to push/seed these rows with the same emails again 
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        // User::factory()->create([
        //     'name' => 'Dipanjan',
        //     'email' => 'dipanjanghosal01@gmail.com',
        //     'password' => bcrypt('Password123$$$'),
        // ]);

        // Tutor::factory()->create([
        //     'name' => 'Sam',
        //     'age' => 21,
        //     'email' => 'sam@gmail.com',
        //     'number' => '11111'
        // ]);
        // Tutor::factory()->create([
        //     'name' => 'Jack',
        //     'age' => 21,
        //     'email' => 'jack@gmail.com',
        //     'number' => '22222'
        // ]);
        Tutor::factory()->create([
            'name' => 'Dip',
            'age' => 24,
            'email' => 'dipanjanghosal01@gmail.com',
            'number' => '33333'
        ]);
    }
}
