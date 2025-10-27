<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['id_usuario', 'nom_usuario', 'email', 'password'];
    
    protected $hidden = ['password'];


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
