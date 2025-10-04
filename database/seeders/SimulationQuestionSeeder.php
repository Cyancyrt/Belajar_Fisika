<?php
// filepath: database/seeders/SimulationQuestionSeeder.php

namespace Database\Seeders;

use App\Models\PhysicsTopic;
use App\Models\SimulationQuestion;
use Illuminate\Database\Seeder;

class SimulationQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $topics = PhysicsTopic::all();
        
        foreach ($topics as $topic) {
            $questions = $this->getQuestionsForTopic($topic);
            
            foreach ($questions as $questionData) {
                SimulationQuestion::create([
                    'physics_topic_id' => $topic->id,
                    'question_text' => $questionData['question_text'],
                    'simulation_type' => $questionData['simulation_type'],
                    'parameters' => $questionData['parameters'],
                    'evaluation_criteria' => $questionData['evaluation_criteria'],
                    'hints' => $questionData['hints'],
                    'max_score' => 100,
                    'difficulty' => $questionData['difficulty'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✅ Created simulation questions successfully!');
    }

    private function getQuestionsForTopic($topic)
    {
        switch ($topic->slug) {
            case 'gaya-gesek':
            return [
                [
                'question_text' => 'Kamu mencoba mendorong kotak di lantai, tapi kotaknya belum bergerak. Berapa besar gaya gesek yang menahannya?',
                'simulation_type' => 'friction_static',
                'parameters' => [
                    'mass' => ['min' => 1, 'max' => 10, 'default' => 5, 'unit' => 'kg'],
                    'friction_coefficient' => ['min' => 0.1, 'max' => 0.8, 'default' => 0.3, 'unit' => ''],
                    'applied_force' => ['min' => 1, 'max' => 25, 'default' => 10, 'unit' => 'N'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 10.0,
                    'tolerance' => 2.0,
                    'unit' => 'N',
                    'calculation_type' => 'friction_force',
                ],
                'hints' => [
                    'f_max = μ × N, di mana N = m × g',
                    'Jika gaya dorong lebih kecil dari f_max, benda tidak bergerak.',
                    'Semakin kasar permukaan, semakin besar gaya geseknya!',
                ],
                'difficulty' => 'beginner',
                ],
                [
                'question_text' => 'Sebuah benda meluncur di bidang miring. Dengan mengetahui sudut dan gaya geseknya, bisakah kamu mencari percepatan benda tersebut?',
                'simulation_type' => 'inclined_plane',
                'parameters' => [
                    'mass' => ['min' => 2, 'max' => 8, 'default' => 5, 'unit' => 'kg'],
                    'angle' => ['min' => 15, 'max' => 45, 'default' => 30, 'unit' => 'degrees'],
                    'friction_coefficient' => ['min' => 0.1, 'max' => 0.5, 'default' => 0.2, 'unit' => ''],
                ],
                'evaluation_criteria' => [
                    'target_value' => 3.2,
                    'tolerance' => 0.5,
                    'unit' => 'm/s²',
                    'calculation_type' => 'acceleration',
                ],
                'hints' => [
                    'Gunakan rumus: a = g(sin θ - μ cos θ)',
                    'Ingat, gaya gesek menghambat gerakan.',
                    'θ = sudut bidang miring terhadap tanah.',
                ],
                'difficulty' => 'intermediate',
                ],
            ];

            case 'momentum':
            return [
                [
                'question_text' => 'Sebuah bola sedang bergerak. Dengan mengetahui massanya dan kecepatannya, bisakah kamu menghitung momentum bola tersebut?',
                'simulation_type' => 'momentum_linear',
                'parameters' => [
                    'mass' => ['min' => 1, 'max' => 10, 'default' => 5, 'unit' => 'kg'],
                    'velocity' => ['min' => 1, 'max' => 20, 'default' => 6, 'unit' => 'm/s'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 30.0,
                    'tolerance' => 5.0,
                    'unit' => 'kg⋅m/s',
                    'calculation_type' => 'momentum',
                ],
                'hints' => [
                    'Gunakan rumus p = m × v',
                    'Momentum menunjukkan "dorongan" benda yang bergerak.',
                    'Satuan momentum adalah kg⋅m/s',
                ],
                'difficulty' => 'beginner',
                ],
                [
                'question_text' => 'Dua bola bertumbukan di lintasan. Dengan mengetahui massa dan kecepatan keduanya, bisakah kamu menghitung apakah momentum totalnya tetap sama?',
                'simulation_type' => 'collision',
                'parameters' => [
                    'mass1' => ['min' => 2, 'max' => 8, 'default' => 4, 'unit' => 'kg'],
                    'mass2' => ['min' => 2, 'max' => 8, 'default' => 6, 'unit' => 'kg'],
                    'velocity1' => ['min' => 5, 'max' => 15, 'default' => 10, 'unit' => 'm/s'],
                    'velocity2' => ['min' => 0, 'max' => 10, 'default' => 0, 'unit' => 'm/s'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 40.0,
                    'tolerance' => 8.0,
                    'unit' => 'kg⋅m/s',
                    'calculation_type' => 'total_momentum',
                ],
                'hints' => [
                    'Gunakan hukum kekekalan momentum: p_sebelum = p_sesudah',
                    'Momentum tiap benda: p = m × v',
                    'Jumlahkan semua momentum untuk mendapat totalnya.',
                ],
                'difficulty' => 'advanced',
                ],
            ];

            case 'energi-kinetik':
            return [
                [
                'question_text' => 'Sebuah bola sedang bergulir di tanah. Jika kamu tahu berat bola dan kecepatannya, bisakah kamu menghitung energi geraknya (energi kinetik)?',
                'simulation_type' => 'kinetic_basic',
                'parameters' => [
                    'mass' => ['min' => 1, 'max' => 10, 'default' => 5, 'unit' => 'kg'],
                    'velocity' => ['min' => 1, 'max' => 20, 'default' => 10, 'unit' => 'm/s'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 250.0,
                    'tolerance' => 50.0,
                    'unit' => 'J',
                    'calculation_type' => 'kinetic_energy',
                ],
                'hints' => [
                    'Gunakan rumus Ek = ½ × m × v²',
                    'Semakin cepat benda bergerak, makin besar energi kinetiknya!',
                    'Energi diukur dalam Joule (J)',
                ],
                'difficulty' => 'beginner',
                ],
                [
                'question_text' => 'Sebuah bola dijatuhkan dari ketinggian tertentu. Saat jatuh, energi potensialnya berubah menjadi energi kinetik. Bisakah kamu menghitung total energinya?',
                'simulation_type' => 'energy_transformation',
                'parameters' => [
                    'mass' => ['min' => 2, 'max' => 8, 'default' => 5, 'unit' => 'kg'],
                    'height' => ['min' => 1, 'max' => 10, 'default' => 5, 'unit' => 'm'],
                    'velocity_initial' => ['min' => 0, 'max' => 15, 'default' => 0, 'unit' => 'm/s'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 245.0,
                    'tolerance' => 25.0,
                    'unit' => 'J',
                    'calculation_type' => 'total_energy',
                ],
                'hints' => [
                    'Gunakan konsep: Energi total = Energi kinetik + Energi potensial',
                    'Ep = m × g × h',
                    'Ingat hukum kekekalan energi!',
                ],
                'difficulty' => 'intermediate',
                ],
            ];

            case 'hukum-newton':
            return [
                [
                'question_text' => 'Kamu sedang mendorong sebuah kotak di lantai. Jika kamu tahu gaya dorong dan massanya, bisakah kamu menghitung seberapa cepat kotak itu bertambah cepat (percepatannya)?',
                'simulation_type' => 'newton_basic',
                'parameters' => [
                    'mass' => ['min' => 1, 'max' => 10, 'default' => 5, 'unit' => 'kg'],
                    'force' => ['min' => 5, 'max' => 50, 'default' => 20, 'unit' => 'N'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 4.0,
                    'tolerance' => 0.5,
                    'unit' => 'm/s²',
                    'calculation_type' => 'acceleration',
                ],
                'hints' => [
                    'Gunakan rumus F = m × a',
                    'Untuk mencari a, ubah jadi a = F / m',
                    'Semakin besar gaya, semakin besar percepatan!',
                ],
                'difficulty' => 'beginner',
                ],
                [
                'question_text' => 'Dua orang mendorong kotak dari arah yang berbeda. Dengan mengetahui besar gaya dan sudut antar gaya, bisakah kamu mencari percepatan kotak tersebut?',
                'simulation_type' => 'newton_resultant',
                'parameters' => [
                    'mass' => ['min' => 2, 'max' => 8, 'default' => 4, 'unit' => 'kg'],
                    'force1' => ['min' => 10, 'max' => 30, 'default' => 20, 'unit' => 'N'],
                    'force2' => ['min' => 5, 'max' => 15, 'default' => 8, 'unit' => 'N'],
                    'angle' => ['min' => 0, 'max' => 180, 'default' => 90, 'unit' => 'degrees'],
                ],
                'evaluation_criteria' => [
                    'target_value' => 3.0,
                    'tolerance' => 0.8,
                    'unit' => 'm/s²',
                    'calculation_type' => 'resultant_acceleration',
                ],
                'hints' => [
                    'Hitung dulu gaya total (resultan) dari dua gaya yang berbeda arah.',
                    'Gunakan rumus: F_resultan = √(F1² + F2² + 2 × F1 × F2 × cos(θ))',
                    'Lalu cari percepatan: a = F_resultan / m',
                ],
                'difficulty' => 'intermediate',
                ],
            ];

            default:
            return [
                [
                'question_text' => "Eksperimen {$topic->name} - Gunakan parameter yang tersedia untuk memahami konsep fisikanya!",
                'simulation_type' => str_replace('-', '_', $topic->slug),
                'parameters' => [],
                'evaluation_criteria' => [
                    'target_value' => 100,
                    'tolerance' => 10,
                    'unit' => 'points',
                    'calculation_type' => 'completion',
                ],
                'hints' => [
                    'Ikuti petunjuk simulasi dengan seksama.',
                    'Amati bagaimana perubahan parameter memengaruhi hasil.',
                ],
                'difficulty' => 'beginner',
                ],
            ];
        }
    }
}
