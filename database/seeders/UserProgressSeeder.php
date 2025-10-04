<?php
// filepath: database/seeders/UserProgressSeeder.php

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
        // Ambil semua users yang ada
        $users = User::take(2)->get();
        
        if ($users->count() < 2) {
            // Buat users jika tidak ada
            $budi = User::create([
                'name' => 'Budi Santoso',
                'email' => 'budi@physics.edu',
                'password' => bcrypt('password123'),
                'level' => 2,
                'total_xp' => 150,
                'streak_days' => 5,
                'last_activity_date' => now()->subDay(),
            ]);

            $sari = User::create([
                'name' => 'Sari Dewi',
                'email' => 'sari@physics.edu',
                'password' => bcrypt('password123'),
                'level' => 1,
                'total_xp' => 50,
                'streak_days' => 2,
                'last_activity_date' => now(),
            ]);
        } else {
            $budi = $users->first();
            $sari = $users->skip(1)->first();
        }

        // Ambil physics topics yang ada
        $topics = PhysicsTopic::take(3)->get();
        
        if ($topics->count() < 3) {
            $this->command->error('❌ Not enough physics topics found! Please run PhysicsTopicSeeder first.');
            return;
        }

        $newton = $topics->get(0);
        $energy = $topics->get(1);
        $momentum = $topics->get(2);

        // Create progress data
        $progressData = [
            [
                'user_id' => $budi->id,
                'physics_topic_id' => $newton->id,
                'completed_questions' => 2,
                'total_questions' => 2,
                'total_score' => 220,
                'best_score' => 120,
                'first_attempt_at' => now()->subDays(10),
                'last_attempt_at' => now()->subDays(2),
                'is_completed' => true,
            ],
            [
                'user_id' => $budi->id,
                'physics_topic_id' => $energy->id,
                'completed_questions' => 1,
                'total_questions' => 2,
                'total_score' => 150,
                'best_score' => 150,
                'first_attempt_at' => now()->subDays(5),
                'last_attempt_at' => now()->subDays(1),
                'is_completed' => false,
            ],
            [
                'user_id' => $sari->id,
                'physics_topic_id' => $newton->id,
                'completed_questions' => 1,
                'total_questions' => 2,
                'total_score' => 100,
                'best_score' => 100,
                'first_attempt_at' => now()->subDays(3),
                'last_attempt_at' => now()->subDays(1),
                'is_completed' => false,
            ],
        ];

        foreach ($progressData as $progress) {
            UserProgress::updateOrCreate(
                [
                    'user_id' => $progress['user_id'],
                    'physics_topic_id' => $progress['physics_topic_id'],
                ],
                $progress
            );
        }

        $this->command->info('✅ Created user progress data!');
        $this->command->info('   - Total progress records: ' . count($progressData));
    }
}