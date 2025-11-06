<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Usuario extends Authenticatable
{

    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['id_usuario', 'nom_usuario', 'email', 'password'];
    
    protected $hidden = ['password'];

    /**
     * Obtener el nombre de usuario para autenticación
     */
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    /**
     * Obtener el valor del identificador único del usuario.
     */
    public function getAuthIdentifier()
    {
        return $this->id_usuario;
    }

    /**
     * Obtener la contraseña del usuario para autenticación
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tareas::class, 'id_usuario');
    }

    public function proyectos()
    {
        return $this->belongsToMany(
            Proyecto::class,
            'participar',
            'id_usuario',
            'id_proyecto'
        )->withPivot('id_rols');
    }

    /**
     * Verificar si el usuario tiene proyectos asignados
     */
    public function tieneProyectos(): bool
    {
        return $this->proyectos()->exists();
    }

    /**
     * Obtener información básica del usuario
     */
    public function informacionBasica(): array
    {
        return [
            'id_usuario' => $this->id_usuario,
            'nom_usuario' => $this->nom_usuario,
            'email' => $this->email
        ];
    }

    /**
     * Scope para obtener solo información básica
     */
    public function scopeInformacionBasica($query)
    {
        return $query->select('id_usuario', 'nom_usuario', 'email');
    }
}
