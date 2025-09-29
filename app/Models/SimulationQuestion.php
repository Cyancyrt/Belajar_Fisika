<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationQuestion extends Model
{
     protected $fillable = [
        'physics_topic_id', 'question_text', 'simulation_type', 
        'parameters', 'evaluation_criteria', 'hints', 'max_score', 'difficulty'
    ];

    protected $casts = [
        'parameters' => 'array',
        'evaluation_criteria' => 'array',
        'hints' => 'array',
    ];

    public function topic()
    {
        return $this->belongsTo(PhysicsTopic::class, 'physics_topic_id');
    }

    public function attempts()
    {
        return $this->hasMany(SimulationAttempt::class);
    }
}
