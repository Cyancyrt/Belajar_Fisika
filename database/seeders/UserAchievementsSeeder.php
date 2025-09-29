<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAchievementsSeeder extends Seeder
{
    public function run()
    {
        $budi = User::where('email', 'budi@physics.edu')->first();
        $sari = User::where('email', 'sari@physics.edu')->first();

        // Budi earned several achievements
        $achievements = Achievement::whereIn('slug', [
            'first_correct_answer', 
            'seven_day_streak',
            'newton_master'
        ])->get();

        foreach ($achievements as $achievement) {
            UserAchievement::create([
                'user_id' => $budi->id,
                'achievement_id' => $achievement->id,
                'earned_at' => now()->subDays(rand(1, 10)),
            ]);
        }

        // Sari earned basic achievement
        $basicAchievement = Achievement::where('slug', 'first_correct_answer')->first();
        UserAchievement::create([
            'user_id' => $sari->id,
            'achievement_id' => $basicAchievement->id,
            'earned_at' => now()->subDays(1),
        ]);

        $this->command->info('✅ Created user achievements!');
    }
}
