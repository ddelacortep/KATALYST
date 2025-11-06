<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rols extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rols';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false; 
    protected $fillable = ['id_rols', 'nom_rols'];

    // Relaciones
    public function participaciones()
    {
        return $this->hasMany(Participar::class, 'id_rols');
    }

    // Método helper para obtener roles desde caché
    public static function cached()
    {
        return session()->remember('roles_cache', fn() => self::all());
    }

    // Crear rol con siguiente ID automático
    public static function createWithNextId($nombre)
    {
        $rol = self::create([
            'id_rols' => self::max('id_rols') + 1,
            'nom_rols' => $nombre
        ]);
        
        self::clearCache();
        return $rol;
    }

    // Actualizar y limpiar caché
    public function updateAndClearCache(array $attributes)
    {
        $result = $this->update($attributes);
        self::clearCache();
        return $result;
    }

    // Eliminar y limpiar caché
    public function deleteAndClearCache()
    {
        $result = $this->delete();
        self::clearCache();
        return $result;
    }

    // Limpiar caché de roles
    public static function clearCache()
    {
        session()->forget('roles_cache');
    }
}
