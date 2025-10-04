<?php
// filepath: database/seeders/DailyChallengesSeeder.php

namespace Database\Seeders;

use App\Models\DailyChallenge;
use App\Models\SimulationQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailyChallengesSeeder extends Seeder
{
    public function run()
    {
        $questions = SimulationQuestion::all();

        // Validasi apakah ada questions
        if ($questions->count() == 0) {
            $this->command->error('❌ No simulation questions found! Please run SimulationQuestionSeeder first.');
            return;
        }

        // Create challenges for today and next 7 days
        for ($i = 0; $i < 7; $i++) {
            $challengeDate = now()->addDays($i)->toDateString();
            $randomQuestion = $questions->random();

            DailyChallenge::create([
                'challenge_date' => $challengeDate,           // Sesuai migration
                'simulation_question_id' => $randomQuestion->id, // Sesuai migration
                'xp_multiplier' => rand(2, 5),               // Sesuai migration (default: 2)
                'is_active' => true,                         // Sesuai migration
            ]);
        }

        $this->command->info('✅ Created daily challenges for 7 days!');
        $this->command->info('   - Total challenges: ' . DailyChallenge::count());
    }
}