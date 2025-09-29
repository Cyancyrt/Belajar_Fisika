<?php
// filepath: app/Models/Achievement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'xp_reward',
        'criteria',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'json',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }
}