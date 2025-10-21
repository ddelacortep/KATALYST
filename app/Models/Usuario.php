<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Usuario extends Model
{
    protected $table = 'usuario';
    public $timestamps = false;
    protected $fillable = ['nom_usuario', 'email', 'password'];


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
        )->withPivot('id_rol');
    }
}
