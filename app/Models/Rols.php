<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rols extends Model
{
    protected $table = 'roles';
    public $timestamps = false; 

     protected $fillable = ['nom:rols'];

      public function participaciones()
    {
        return $this->hasMany(Participar::class, 'id_rols');
    }
}
