<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rols extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rols';
    public $incrementing = false; // Cambiar a false si no es auto-incremental
    protected $keyType = 'int';
    public $timestamps = false; 

    protected $fillable = ['id_rols', 'nom_rols'];

    public function participaciones()
    {
        return $this->hasMany(Participar::class, 'id_rols');
    }
}
