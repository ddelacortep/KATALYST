<?php

namespace App\Models;


use App\Models\Tareas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoTarea extends Model
{
    protected $table = 'estadotarea';
    public $timestamps = false;



    public function tareas(): HasMany
    {
        return $this->hasMany(Tareas::class, 'estado_id');
    }
}