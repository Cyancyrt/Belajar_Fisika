<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $table = 'simulations';
    protected $fillable = ['learning_materials_id', 'simulation_name', 'gravity','object', 'ground'];

    public function learning_material()
    {
        return $this->belongsTo(Learning_materials::class, 'learning_materials_id');
    }
}
