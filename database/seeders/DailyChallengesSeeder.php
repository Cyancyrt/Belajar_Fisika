<?php

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

        // Create challenges for today and next 7 days
        for ($i = 0; $i < 7; $i++) {
            $challengeDate = now()->addDays($i)->toDateString();
            $randomQuestion = $questions->random();

            DailyChallenge::create([
                'challenge_date' => $challengeDate,
                'simulation_question_id' => $randomQuestion->id,
                'xp_multiplier' => rand(2, 5), // 2x to 5x XP
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Created daily challenges for 7 days!');
    }
}
