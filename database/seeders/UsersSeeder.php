<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Budi Fisikawan',
                'email' => 'budi@physics.edu',
                'password' => Hash::make('password123'),
                'avatar' => 'https://ui-avatars.com/api/?name=Budi+Fisikawan&background=6366f1&color=fff',
                'level' => 3,
                'total_xp' => 2450,
                'streak_days' => 12,
                'last_activity_date' => now()->toDateString(),
            ],
            [
                'name' => 'Sari Pembelajar',
                'email' => 'sari@physics.edu', 
                'password' => Hash::make('password123'),
                'avatar' => 'https://ui-avatars.com/api/?name=Sari+Pembelajar&background=8b5cf6&color=fff',
                'level' => 1,
                'total_xp' => 350,
                'streak_days' => 3,
                'last_activity_date' => now()->toDateString(),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Created 2 users successfully!');
    }
}
