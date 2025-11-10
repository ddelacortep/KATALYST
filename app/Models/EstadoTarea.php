<?php

namespace App\Models;

use App\Models\Tareas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoTarea extends Model
{
    protected $table = 'estado_tarea';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['id_estado', 'nom_estat', 'id_tarea'];

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tareas::class, 'id_tarea', 'id_tarea');
    }
}