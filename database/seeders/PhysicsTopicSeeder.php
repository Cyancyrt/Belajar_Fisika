<?php
// filepath: database/seeders/PhysicsTopicSeeder.php

namespace Database\Seeders;

use App\Models\PhysicsTopic;
use Illuminate\Database\Seeder;

class PhysicsTopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            [
                'name' => 'Hukum Newton II',
                'slug' => 'hukum-newton',
                'subtitle' => 'F = m × a',
                'description' => 'Mempelajari hubungan antara gaya, massa, dan percepatan dalam sistem dinamis dengan simulasi interaktif.',
                'difficulty' => 'beginner',
                'estimated_duration' => 15,
                'icon' => '⚡',
                'is_active' => true,
                'order_index' => 1,
            ],
            [
                'name' => 'Energi Kinetik',
                'slug' => 'energi-kinetik',
                'subtitle' => 'Ek = ½mv²',
                'description' => 'Memahami konsep energi kinetik, transformasi energi, dan hukum kekekalan energi.',
                'difficulty' => 'intermediate',
                'estimated_duration' => 20,
                'icon' => '🔋',
                'is_active' => true,
                'order_index' => 2,
            ],
            [
                'name' => 'Momentum',
                'slug' => 'momentum',
                'subtitle' => 'p = m × v',
                'description' => 'Mempelajari momentum linear, impuls, dan hukum kekekalan momentum dalam tumbukan.',
                'difficulty' => 'advanced',
                'estimated_duration' => 25,
                'icon' => '🎯',
                'is_active' => true,
                'order_index' => 3,
            ],
            [
                'name' => 'Gaya Gesek',
                'slug' => 'gaya-gesek',
                'subtitle' => 'f = μN',
                'description' => 'Memahami konsep gaya gesek statis dan kinetis pada berbagai permukaan dan bidang miring.',
                'difficulty' => 'beginner',
                'estimated_duration' => 18,
                'icon' => '🔥',
                'is_active' => true,
                'order_index' => 4,
            ],
        ];

        foreach ($topics as $topic) {
            PhysicsTopic::updateOrCreate($topic);
        }

        $this->command->info('✅ Created ' . count($topics) . ' physics topics successfully!');
    }
}