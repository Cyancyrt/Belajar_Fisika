<?php

namespace Database\Seeders;

use App\Models\PhysicsTopic;
use App\Models\SimulationQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SimulationQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->seedNewtonQuestions();
        $this->seedEnergyQuestions();
        $this->seedMomentumQuestions();

        $this->command->info('✅ Created simulation questions for all topics!');
    }

    private function seedNewtonQuestions()
    {
        $newton = PhysicsTopic::where('slug', 'newton_second_law')->first();
        
        $questions = [
            [
                'physics_topic_id' => $newton->id,
                'question_text' => 'Sebuah balok bermassa 20 kg berada di atas lantai licin. Berapa besar gaya yang dibutuhkan untuk membuat balok tersebut bergerak dengan percepatan tepat 2.5 m/s²?',
                'simulation_type' => 'newton_second_law',
                'parameters' => [
                    'mass' => 20,
                    'friction' => 0,
                    'target_acceleration' => 2.5
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'force',
                    'target_value' => 50, // F = m × a = 20 × 2.5
                    'tolerance' => 2
                ],
                'hints' => [
                    'Gunakan rumus F = m × a',
                    'Massa balok = 20 kg',
                    'Percepatan target = 2.5 m/s²',
                    'Lantai licin berarti tidak ada gaya gesek'
                ],
                'max_score' => 100,
                'difficulty' => 'Mudah'
            ],
            [
                'physics_topic_id' => $newton->id,
                'question_text' => 'Sebuah balok bermassa 5 kg berada di atas lantai datar dengan koefisien gesek statis μs = 0.6 dan gesek kinetis μk = 0.4. Jika balok didorong dengan gaya 20 N, tentukan gaya gesek f yang bekerja pada balok!',
                'simulation_type' => 'friction_flat',
                'parameters' => [
                    'mass' => 5,
                    'mu_s' => 0.6,
                    'mu_k' => 0.4,
                    'appliedF' => 20,
                    'slopeDeg' => 0
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'friction',
                    // hitung manual:
                    // N = m*g = 5*9.8 = 49 N
                    // fs_max = μs*N = 0.6*49 = 29.4 N
                    // F = 20 N < fs_max → balok tidak bergerak
                    // f = 20 N
                    'target_value' => 20,
                    'tolerance' => 1
                ],
                'hints' => [
                    'Hitung gaya normal: N = m × g',
                    'Hitung gaya gesek maksimum: fs_max = μs × N',
                    'Bandingkan gaya dorong dengan fs_max',
                    'Jika F < fs_max maka f = F, jika F > fs_max maka f = fk'
                ],
                'max_score' => 100,
                'difficulty' => 'Sedang'
            ],
            [
                'physics_topic_id' => $newton->id,
                'question_text' => 'Sebuah mobil bermassa 1200 kg bergerak dari keadaan diam. Jika gaya dorong mesin adalah 30000 N dan gaya gesek 6000 N, berapakah percepatan mobil?',
                'simulation_type' => 'newton_second_law',
                'parameters' => [   
                    'mass' => 1200,
                    'applied_force' => 30000,
                    'friction_force' => 6000
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'acceleration',
                    'target_value' => 2.08, // a = (F_net)/m = (3000-500)/1200
                    'tolerance' => 0.1
                ],
                'hints' => [
                    'Hitung gaya netto terlebih dahulu',
                    'F_netto = F_dorong - F_gesek',
                    'Kemudian gunakan rumus a = F_netto / m'
                ],
                'max_score' => 120,
                'difficulty' => 'Sedang'
            ]
        ];

        foreach ($questions as $question) {
            SimulationQuestion::updateOrCreate($question);
        }
    }

    private function seedEnergyQuestions()
    {
        $energy = PhysicsTopic::where('slug', 'kinetic_energy')->first();
        
        $questions = [
            [
                'physics_topic_id' => $energy->id,
                'question_text' => 'Sebuah bola bermassa 5 kg bergerak dengan kecepatan 10 m/s. Hitunglah energi kinetik bola tersebut!',
                'simulation_type' => 'kinetic_energy',
                'parameters' => [
                    'mass' => 5,
                    'velocity' => 10
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'kinetic_energy',
                    'target_value' => 250, // Ek = ½mv² = ½ × 5 × 10²
                    'tolerance' => 5
                ],
                'hints' => [
                    'Gunakan rumus Ek = ½mv²',
                    'Massa bola = 5 kg',
                    'Kecepatan = 10 m/s',
                    'Jangan lupa ½ di depan rumus!'
                ],
                'max_score' => 150,
                'difficulty' => 'Mudah'
            ],
            [
                'physics_topic_id' => $energy->id,
                'question_text' => 'Dua buah benda memiliki massa yang sama yaitu 8 kg. Benda A bergerak dengan kecepatan 6 m/s dan benda B bergerak dengan kecepatan 12 m/s. Berapa perbandingan energi kinetik benda A dan B?',
                'simulation_type' => 'kinetic_energy_comparison',
                'parameters' => [
                    'mass_a' => 8,
                    'mass_b' => 8,
                    'velocity_a' => 6,
                    'velocity_b' => 12
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'energy_ratio',
                    'target_value' => 0.25, // Ek_A : Ek_B = (½×8×6²) : (½×8×12²) = 144:576 = 1:4
                    'tolerance' => 0.05
                ],
                'hints' => [
                    'Hitung energi kinetik masing-masing benda',
                    'Ek = ½mv²',
                    'Bandingkan Ek_A dengan Ek_B',
                    'Perhatikan bahwa energi kinetik berbanding lurus dengan kuadrat kecepatan'
                ],
                'max_score' => 180,
                'difficulty' => 'Sulit'
            ]
        ];

        foreach ($questions as $question) {
            SimulationQuestion::updateOrCreate($question);
        }
    }

    private function seedMomentumQuestions()
    {
        $momentum = PhysicsTopic::where('slug', 'momentum')->first();
        
        $questions = [
            [
                'physics_topic_id' => $momentum->id,
                'question_text' => 'Dua bola akan bertumbukan elastis. Bola A bermassa 3 kg bergerak ke kanan dengan kecepatan 5 m/s, bola B bermassa 2 kg bergerak ke kiri dengan kecepatan 3 m/s. Hitunglah momentum total sistem sebelum tumbukan!',
                'simulation_type' => 'momentum_collision',
                'parameters' => [
                    'mass_a' => 3,
                    'velocity_a' => 5,
                    'mass_b' => 2,
                    'velocity_b' => -3 // negatif karena berlawanan arah
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'total_momentum',
                    'target_value' => 9, // p = m1×v1 + m2×v2 = 3×5 + 2×(-3) = 15-6 = 9
                    'tolerance' => 1
                ],
                'hints' => [
                    'Momentum = massa × kecepatan',
                    'Perhatikan arah gerak (+ dan -)',
                    'Momentum total = p₁ + p₂',
                    'Kecepatan ke kanan (+), ke kiri (-)'
                ],
                'max_score' => 200,
                'difficulty' => 'Sedang'
            ],
            [
                'physics_topic_id' => $momentum->id,
                'question_text' => 'Sebuah peluru bermassa 0.02 kg ditembakkan dari senapan bermassa 4 kg. Jika kecepatan peluru 400 m/s, berapakah kecepatan mundur senapan?',
                'simulation_type' => 'momentum_conservation',
                'parameters' => [
                    'bullet_mass' => 0.02,
                    'gun_mass' => 4,
                    'bullet_velocity' => 400,
                    'initial_momentum' => 0
                ],
                'evaluation_criteria' => [
                    'target_variable' => 'recoil_velocity',
                    'target_value' => -2, // v_gun = -(m_bullet × v_bullet) / m_gun = -(0.02×400)/4
                    'tolerance' => 0.1
                ],
                'hints' => [
                    'Gunakan hukum kekekalan momentum',
                    'Momentum awal = momentum akhir',
                    'Momentum awal = 0 (sistem diam)',
                    'p_peluru + p_senapan = 0'
                ],
                'max_score' => 250,
                'difficulty' => 'Sulit'
            ]
        ];

        foreach ($questions as $question) {
            SimulationQuestion::updateOrCreate($question);
        }
    }
}
