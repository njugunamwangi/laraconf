<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Speaker;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::create([
            'name' => 'Ndachi',
            'email' => 'info@ndachi.dev',
            'password' => bcrypt('password')
        ]);

        Venue::factory(100)->create();

        Speaker::factory(27)->withTalks(1)->create();
    }
}
