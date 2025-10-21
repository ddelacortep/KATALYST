<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    public $timestamps = false;

   
    public function tareas(): HasMany
    {
        return $this->hasMany(Tareas::class, 'id_proyecto');
    }


    public function participar(): HasMany
    {
        return $this->hasMany(Participar::class, 'id_proyecto');
    }

    protected $fillable = ['nom_proyecto'];

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'participar',
            'id_proyecto',
            'id_usuario'
        )->withPivot('id_rol');
    }
}
