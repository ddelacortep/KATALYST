<?php

namespace App\Models;

use App\Models\EstadoTarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tareas extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'id_tarea';
    public $timestamps = false;
    public $incrementing = false; // SQL Server no tiene IDENTITY configurado
    protected $keyType = 'int';

    protected $fillable = [
        'id_tarea',
        'nom_tarea',
        'id_proyecto',
        'id_usuario',
        'id_estados'
    ];

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

