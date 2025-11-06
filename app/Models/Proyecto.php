<?php

namespace App\Models;

use App\Models\Participar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyecto';
    public $incrementing = false; // Cambiado a false porque SQL Server no tiene IDENTITY configurado
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['id_proyecto', 'nom_proyecto', 'slug', 'descripcion'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generar slug automáticamente a partir del nombre
     */
    public static function generateSlug($nombre)
    {
        $slug = Str::slug($nombre);
        $count = 1;
        
        // Verificar si el slug ya existe y agregar número si es necesario
        while (static::where('slug', $slug)->exists()) {
            $slug = Str::slug($nombre) . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tareas::class, 'id_proyecto');
    }


    public function participar(): HasMany
    {
        return $this->hasMany(Participar::class, 'id_proyecto');
    }

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'participar',
            'id_proyecto',
            'id_usuario'
        )->withPivot('id_rol');
    }

    /**
     * Verificar si un usuario es administrador del proyecto
     */
    public function esAdministrador($usuarioId): bool
    {
        return $this->participar()
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', 1)
            ->exists();
    }

    /**
     * Obtener la participación de un usuario en el proyecto
     */
    public function participacionDeUsuario($usuarioId)
    {
        return $this->participar()
            ->where('id_usuario', $usuarioId)
            ->first();
    }

    /**
     * Agregar un participante al proyecto
     */
    public function agregarParticipante($usuarioId, $rolId = 1)
    {
        return $this->participar()->create([
            'id_usuario' => $usuarioId,
            'id_proyecto' => $this->id_proyecto,
            'id_rols' => $rolId
        ]);
    }
}
