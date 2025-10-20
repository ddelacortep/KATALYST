<?php

namespace App\Models;

use App\Models\EstadoTarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tareas extends Model
{
    protected $table = 'tareas';
    public $timestamps = false;

 
    public function estadotarea(): BelongsTo
    {
        return $this->belongsTo(EstadoTarea::class, 'id_estado');
    }

    
}
