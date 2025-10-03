<?php

namespace Database\Seeders;

use App\Models\PhysicsTopic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhysicsTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            [
                'name' => 'Hukum Newton II',
                'slug' => 'newton_second_law',
                'subtitle' => 'F = m × a',
                'description' => 'Mempelajari hubungan antara gaya, massa, dan percepatan dalam sistem dinamis.',
                'difficulty' => 'Mudah',
                'estimated_duration' => 10,
                'icon' => '⚡',
                'is_active' => true,
                'order_index' => 1,
            ],
            [
                'name' => 'Energi Kinetik',
                'slug' => 'kinetic_energy',
                'subtitle' => 'Ek = ½mv²',
                'description' => 'Memahami konsep energi kinetik dan faktor-faktor yang mempengaruhi besarnya energi gerak.',
                'difficulty' => 'Sedang',
                'estimated_duration' => 15,
                'icon' => '🔋',
                'is_active' => true,
                'order_index' => 2,
            ],
            [
                'name' => 'Momentum',
                'slug' => 'momentum',
                'subtitle' => 'p = m × v',
                'description' => 'Mempelajari momentum linear dan hukum kekekalan momentum dalam tumbukan.',
                'difficulty' => 'Sulit',
                'estimated_duration' => 20,
                'icon' => '🎯',
                'is_active' => true,
                'order_index' => 3,
            ],
        ];

        foreach ($topics as $topic) {
            PhysicsTopic::updateOrCreate($topic);
        }

        $this->command->info('✅ Created 3 physics topics successfully!');
    }
}
