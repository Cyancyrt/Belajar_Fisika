<?php
// filepath: database/seeders/SimulationAttemtsSeeder.php

namespace Database\Seeders;

use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SimulationAttemtsSeeder extends Seeder
{
    public function run()
    {
        // Ambil users yang ada atau buat jika tidak ada
        $users = User::take(2)->get();
        
        if ($users->count() < 2) {
            $users = collect([
                User::create([
                    'name' => 'Budi Santoso',
                    'email' => 'budi@physics.edu',
                    'password' => bcrypt('password123'),
                    'level' => 2,
                    'total_xp' => 150,
                    'streak_days' => 5,
                    'last_activity_date' => now()->subDay(),
                ]),
                User::create([
                    'name' => 'Sari Dewi',
                    'email' => 'sari@physics.edu',
                    'password' => bcrypt('password123'),
                    'level' => 1,
                    'total_xp' => 50,
                    'streak_days' => 2,
                    'last_activity_date' => now(),
                ])
            ]);
        }

        // Ambil questions yang ada
        $questions = SimulationQuestion::take(2)->get();
        
        if ($questions->count() < 2) {
            $this->command->error('❌ Not enough simulation questions found! Please run SimulationQuestionSeeder first.');
            return;
        }

        $budi = $users->first();
        $sari = $users->last();
        $question1 = $questions->first();
        $question2 = $questions->last();

        // Create attempts
        SimulationAttempt::create([
            'user_id' => $budi->id,
            'simulation_question_id' => $question1->id,
            'user_answer' => ['answer' => 'correct_value'],
            'correct_answer' => ['answer' => 'correct_value'],
            'is_correct' => true,
            'score_earned' => 100,
            'attempt_number' => 1,
            'time_taken' => 45.5,
            'simulation_data' => ['result' => 'success'],
            'created_at' => now()->subDays(10),
        ]);

        SimulationAttempt::create([
            'user_id' => $sari->id,
            'simulation_question_id' => $question1->id,
            'user_answer' => ['answer' => 'wrong_value'],
            'correct_answer' => ['answer' => 'correct_value'],
            'is_correct' => false,
            'score_earned' => 0,
            'attempt_number' => 1,
            'time_taken' => 125.3,
            'simulation_data' => ['result' => 'failed'],
            'created_at' => now()->subDays(3),
        ]);

        $this->command->info('✅ Created simulation attempts data!');
    }
}