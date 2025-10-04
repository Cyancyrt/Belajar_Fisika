<?php
// filepath: app/Models/DailyChallenge.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChallenge extends Model
{
    protected $fillable = [
        'challenge_date', 
        'simulation_question_id', 
        'xp_multiplier', 
        'is_active'
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
        return \App\Models\SimulationAttempt::where('simulation_question_id', $this->simulation_question_id)
                                ->whereDate('created_at', $this->challenge_date)
                                ->get();
    }

    // Helper methods untuk data yang tidak ada di migration
    public function getTitleAttribute()
    {
        return 'Daily Challenge - ' . $this->challenge_date->format('M d, Y');
    }

    public function getDescriptionAttribute()
    {
        return 'Complete this physics simulation to earn ' . $this->xp_multiplier . 'x XP bonus!';
    }

    public function getXpRewardAttribute()
    {
        // Kalkulasi XP reward berdasarkan question dan multiplier
        return ($this->question->max_score ?? 100) * $this->xp_multiplier;
    }

    public function getDifficultyAttribute()
    {
        return $this->question->difficulty ?? 'Mudah';
    }
}