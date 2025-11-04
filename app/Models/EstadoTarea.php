<?php

namespace App\Models;


use App\Models\Tareas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoTarea extends Model
{
    protected $table = 'estado_tarea';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['id_estado', 'nom_estado'];

    public function tareas(): HasMany
    {
        return $this->hasMany(Tareas::class, 'id_estado');
    }
}