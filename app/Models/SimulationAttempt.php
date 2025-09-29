<?php
// filepath: app/Models/SimulationAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_question_id',
        'user_answer',
        'correct_answer',
        'is_correct',
        'score_earned',
        'attempt_number',
        'time_taken',
        'simulation_data',
    ];

    protected $casts = [
        'user_answer' => 'json',
        'correct_answer' => 'json',
        'simulation_data' => 'json',
        'is_correct' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(SimulationQuestion::class, 'simulation_question_id');
    }
}