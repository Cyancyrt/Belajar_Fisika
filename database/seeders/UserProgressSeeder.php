<?php

namespace Database\Seeders;

use App\Models\PhysicsTopic;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProgressSeeder extends Seeder
{
    public function run()
    {
        $budi = User::where('email', 'budi@physics.edu')->first();
        $sari = User::where('email', 'sari@physics.edu')->first();

        $newton = PhysicsTopic::where('slug', 'newton_second_law')->first();
        $energy = PhysicsTopic::where('slug', 'kinetic_energy')->first();
        $momentum = PhysicsTopic::where('slug', 'momentum')->first();

        // Budi's Progress (Advanced user)
        UserProgress::create([
            'user_id' => $budi->id,
            'physics_topic_id' => $newton->id,
            'completed_questions' => 2,
            'total_questions' => 2,
            'total_score' => 220,
            'best_score' => 120,
            'first_attempt_at' => now()->subDays(10),
            'last_attempt_at' => now()->subDays(2),
            'is_completed' => true,
        ]);

        UserProgress::create([
            'user_id' => $budi->id,
            'physics_topic_id' => $energy->id,
            'completed_questions' => 1,
            'total_questions' => 2,
            'total_score' => 150,
            'best_score' => 150,
            'first_attempt_at' => now()->subDays(5),
            'last_attempt_at' => now()->subDays(1),
            'is_completed' => false,
        ]);

        // Sari's Progress (Beginner user)
        UserProgress::create([
            'user_id' => $sari->id,
            'physics_topic_id' => $newton->id,
            'completed_questions' => 1,
            'total_questions' => 2,
            'total_score' => 100,
            'best_score' => 100,
            'first_attempt_at' => now()->subDays(3),
            'last_attempt_at' => now()->subDays(1),
            'is_completed' => false,
        ]);

        $this->command->info('✅ Created user progress data!');
    }
}
