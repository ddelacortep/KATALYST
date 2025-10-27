<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participar extends Model
{
    protected $table = 'participar';
    protected $primaryKey = ['id_usuario', 'id_proyecto'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['id_usuario', 'id_proyecto', 'id_rols'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function rol()
    {
        return $this->belongsTo(Rols::class, 'id_rols');
    }
}
    

