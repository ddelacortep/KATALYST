<?php

namespace App\Models;

use App\Models\EstadoTarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tareas extends Model
{
    protected $table = 'tareas';
    public $timestamps = false;

 
    public function estadotarea(): BelongsTo
    {
        return $this->belongsTo(EstadoTarea::class, 'id_estado');
    }
    
    public function usuario(): belongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function proyecto(): belongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}

