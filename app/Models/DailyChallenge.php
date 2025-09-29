<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChallenge extends Model
{
    protected $fillable = [
        'challenge_date', 'simulation_question_id', 'xp_multiplier', 'is_active'
    ];

    protected $casts = [
        'challenge_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(SimulationQuestion::class, 'simulation_question_id');
    }

    public function scopeToday($query)
    {
        return $query->where('challenge_date', today());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getUserAttemptsAttribute()
    {
        return SimulationAttempt::where('simulation_question_id', $this->simulation_question_id)
                                ->where('created_at', '>=', $this->challenge_date)
                                ->where('created_at', '<', $this->challenge_date->addDay())
                                ->get();
    }
}
