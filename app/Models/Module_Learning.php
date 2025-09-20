<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module_Learning extends Model
{
    protected $table = 'module_learnings';
    protected $fillable = [
        'title',
        'content',
        'learning_materials_id',
    ];

    public function learningMaterial()
    {
        return $this->belongsTo(Learning_materials::class, 'learning_materials_id');
    }
}
