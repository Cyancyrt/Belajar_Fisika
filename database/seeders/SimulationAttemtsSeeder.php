<?php

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
        $budi = User::where('email', 'budi@physics.edu')->first();
        $sari = User::where('email', 'sari@physics.edu')->first();

        // Get questions
        $newtonQ1 = SimulationQuestion::whereHas('topic', function($query) {
            $query->where('slug', 'newton_second_law');
        })->first();

        $energyQ1 = SimulationQuestion::whereHas('topic', function($query) {
            $query->where('slug', 'kinetic_energy');
        })->first();

        // Budi's attempts (experienced)
        SimulationAttempt::create([
            'user_id' => $budi->id,
            'simulation_question_id' => $newtonQ1->id,
            'user_answer' => [
                'applied_force' => 50,
                'calculated_acceleration' => 2.5
            ],
            'correct_answer' => $newtonQ1->evaluation_criteria,
            'is_correct' => true,
            'score_earned' => 100,
            'attempt_number' => 1,
            'time_taken' => 45.5,
            'simulation_data' => [
                'final_velocity' => 12.5,
                'distance_traveled' => 31.25
            ],
            'created_at' => now()->subDays(10),
        ]);

        SimulationAttempt::create([
            'user_id' => $budi->id,
            'simulation_question_id' => $energyQ1->id,
            'user_answer' => [
                'kinetic_energy' => 250
            ],
            'correct_answer' => $energyQ1->evaluation_criteria,
            'is_correct' => true,
            'score_earned' => 150,
            'attempt_number' => 1,
            'time_taken' => 32.8,
            'simulation_data' => [
                'velocity_final' => 10,
                'energy_calculated' => 250
            ],
            'created_at' => now()->subDays(5),
        ]);

        // Sari's attempts (beginner)
        SimulationAttempt::create([
            'user_id' => $sari->id,
            'simulation_question_id' => $newtonQ1->id,
            'user_answer' => [
                'applied_force' => 45,
                'calculated_acceleration' => 2.25
            ],
            'correct_answer' => $newtonQ1->evaluation_criteria,
            'is_correct' => false,
            'score_earned' => 0,
            'attempt_number' => 1,
            'time_taken' => 125.3,
            'simulation_data' => [
                'final_velocity' => 11.25
            ],
            'created_at' => now()->subDays(3),
        ]);

        SimulationAttempt::create([
            'user_id' => $sari->id,
            'simulation_question_id' => $newtonQ1->id,
            'user_answer' => [
                'applied_force' => 50,
                'calculated_acceleration' => 2.5
            ],
            'correct_answer' => $newtonQ1->evaluation_criteria,
            'is_correct' => true,
            'score_earned' => 100,
            'attempt_number' => 2,
            'time_taken' => 87.2,
            'simulation_data' => [
                'final_velocity' => 12.5,
                'distance_traveled' => 31.25
            ],
            'created_at' => now()->subDays(1),
        ]);

        $this->command->info('✅ Created simulation attempts data!');
    }
}
