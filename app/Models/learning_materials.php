<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class learning_materials extends Model
{
    protected $table = 'learning_materials';
    protected $fillable = [
        'title',
        'type',
        'content',
    ];

    protected $casts = [
        'type' => 'enum:text,simulasi',
    ];
}
