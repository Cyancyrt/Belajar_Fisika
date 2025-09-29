<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementsSeeder extends Seeder
{
     public function run()
    {
        $achievements = [
            [
                'name' => 'Fisikawan Pemula',
                'slug' => 'first_correct_answer',
                'description' => 'Menjawab soal pertama dengan benar',
                'icon' => '🌟',
                'criteria' => [
                    'correct_answers' => 1
                ],
                'xp_reward' => 50,
                'rarity' => 'common',
                'is_active' => true,
            ],
            [
                'name' => 'Streak Master',
                'slug' => 'seven_day_streak',
                'description' => 'Belajar selama 7 hari berturut-turut',
                'icon' => '🔥',
                'criteria' => [
                    'streak_days' => 7
                ],
                'xp_reward' => 200,
                'rarity' => 'rare',
                'is_active' => true,
            ],
            [
                'name' => 'Newton Expert',
                'slug' => 'newton_master',
                'description' => 'Menyelesaikan semua soal Hukum Newton II',
                'icon' => '⚡',
                'criteria' => [
                    'completed_topics' => ['newton_second_law']
                ],
                'xp_reward' => 300,
                'rarity' => 'epic',
                'is_active' => true,
            ],
            [
                'name' => 'Energy Guardian',
                'slug' => 'energy_master',
                'description' => 'Menguasai konsep energi kinetik',
                'icon' => '🔋',
                'criteria' => [
                    'completed_topics' => ['kinetic_energy']
                ],
                'xp_reward' => 350,
                'rarity' => 'epic',
                'is_active' => true,
            ],
            [
                'name' => 'Momentum Legend',
                'slug' => 'momentum_legend',
                'description' => 'Master momentum dan tumbukan',
                'icon' => '🎯',
                'criteria' => [
                    'completed_topics' => ['momentum']
                ],
                'xp_reward' => 400,
                'rarity' => 'legendary',
                'is_active' => true,
            ],
            [
                'name' => 'Physics Genius',
                'slug' => 'all_topics_completed',
                'description' => 'Menyelesaikan semua topik fisika',
                'icon' => '🏆',
                'criteria' => [
                    'completed_topics' => 3
                ],
                'xp_reward' => 1000,
                'rarity' => 'legendary',
                'is_active' => true,
            ],
            [
                'name' => 'Speed Learner',
                'slug' => 'fast_completion',
                'description' => 'Menyelesaikan 10 soal dalam waktu rata-rata <30 detik',
                'icon' => '⚡',
                'criteria' => [
                    'fast_answers' => 10,
                    'max_time' => 30
                ],
                'xp_reward' => 250,
                'rarity' => 'rare',
                'is_active' => true,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }

        $this->command->info('✅ Created 7 achievements successfully!');
    }
}
