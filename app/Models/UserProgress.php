<?php
// filepath: app/Models/UserProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'physics_topic_id',
        'completed_questions',
        'total_questions',
        'best_score',
        'is_completed',
        'last_attempt_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'last_attempt_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(PhysicsTopic::class, 'physics_topic_id');
    }
}