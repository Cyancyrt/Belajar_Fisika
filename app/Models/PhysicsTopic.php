<?php
// filepath: app/Models/PhysicsTopic.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicsTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'description',
        'difficulty',
        'estimated_duration',
        'icon',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function questions()
    {
        return $this->hasMany(SimulationQuestion::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }
}