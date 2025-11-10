<?php

namespace App\Models;

use App\Models\EstadoTarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tareas extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'id_tarea';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['id_tarea', 'nom_tarea', 'id_proyecto', 'id_usuario', 'id_estados'];

    // Relaciones
    public function estadoTarea(): BelongsTo
    {
        return $this->belongsTo(EstadoTarea::class, 'id_estados', 'id_estado');
    }
    
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    // Crear tarea con estado inicial
    public static function crearConEstado($nombre, $idProyecto, $idUsuario = null)
    {
        $idTarea = (self::max('id_tarea') ?? 0) + 1;
        $idEstado = (EstadoTarea::max('id_estado') ?? 0) + 1;
        
        // Si es participante (rol 2), se asigna a sí mismo, sino usa el id_usuario recibido
        $rol = session("user_projects.{$idProyecto}");
        $idUsuario = ($rol == 2) ? session('user_id') : ($idUsuario ?: session('user_id'));

        $tarea = self::create([
            'id_tarea' => $idTarea,
            'nom_tarea' => $nombre,
            'id_proyecto' => $idProyecto,
            'id_usuario' => $idUsuario,
            'id_estados' => $idEstado
        ]);

        EstadoTarea::create([
            'id_estado' => $idEstado,
            'nom_estat' => 'Pendiente',
            'id_tarea' => $idTarea
        ]);

        return $tarea->fresh(['estadoTarea', 'usuario']);
    }

    // Eliminar tarea con su estado
    public function eliminarConEstado()
    {
        $this->estadoTarea()->delete();
        return $this->delete();
    }

    // Verificar si el usuario puede editar
    public function puedeEditar()
    {
        $rol = session("user_projects.{$this->id_proyecto}");
        return $rol == 3 || $rol == 1 || $this->id_usuario == session('user_id');
    }

    // Verificar si el usuario puede eliminar
    public function puedeEliminar()
    {
        $rol = session("user_projects.{$this->id_proyecto}");
        return $rol == 3 || $rol == 1 || $this->id_usuario == session('user_id');
    }

    // Verificar si puede crear tareas en el proyecto
    public static function puedeCrear($idProyecto)
    {
        return session("user_projects.{$idProyecto}") !== null;
    }

    // Verificar si es administrador del proyecto
    public function esAdministrador()
    {
        $rol = session("user_projects.{$this->id_proyecto}");
        return $rol == 3 || $rol == 1;
    }

    // Verificar si es SuperAdmin del proyecto
    public function esSuperAdmin()
    {
        return session("user_projects.{$this->id_proyecto}") == 3;
    }
}
