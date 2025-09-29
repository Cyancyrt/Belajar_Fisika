<?php

namespace Database\Seeders;

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

        $this->call([
            PhysicsTopicSeeder::class,
            SimulationQuestionSeeder::class,
            AchievementsSeeder::class,
            UsersSeeder::class,
            UserProgressSeeder::class,
            UserAchievementsSeeder::class,
            SimulationAttemtsSeeder::class,
            DailyChallengesSeeder::class,
        ]);
    }
}
