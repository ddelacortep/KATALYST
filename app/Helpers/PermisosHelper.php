<?php

namespace App\Helpers;

use App\Models\Participar;
use Illuminate\Support\Facades\Auth;

class PermisosHelper
{
    const ROL_ADMINISTRADOR = 1;
    const ROL_PARTICIPANTE = 2;

    /**
     * Obtener el rol del usuario autenticado en un proyecto (desde sesión)
     */
    public static function obtenerRolEnProyecto($idProyecto)
    {
        return session("user_projects.{$idProyecto}");
    }

    /**
     * Verificar si el usuario es administrador del proyecto
     */
    public static function esAdministrador($idProyecto)
    {
        return self::obtenerRolEnProyecto($idProyecto) === self::ROL_ADMINISTRADOR;
    }

    /**
     * Verificar si el usuario es participante del proyecto
     */
    public static function esParticipante($idProyecto)
    {
        return self::obtenerRolEnProyecto($idProyecto) === self::ROL_PARTICIPANTE;
    }

    /**
     * Verificar si el usuario puede crear tareas
     */
    public static function puedeCrearTareas($idProyecto)
    {
        return in_array(self::obtenerRolEnProyecto($idProyecto), [self::ROL_ADMINISTRADOR, self::ROL_PARTICIPANTE]);
    }

    /**
     * Verificar si el usuario puede editar una tarea
     */
    public static function puedeEditarTarea($tarea)
    {
        $rol = self::obtenerRolEnProyecto($tarea->id_proyecto);
        return $rol === self::ROL_ADMINISTRADOR || ($rol === self::ROL_PARTICIPANTE && $tarea->id_usuario == Auth::id());
    }

    /**
     * Verificar si el usuario puede eliminar una tarea
     */
    public static function puedeEliminarTarea($tarea)
    {
        $rol = self::obtenerRolEnProyecto($tarea->id_proyecto);
        return $rol === self::ROL_ADMINISTRADOR || ($rol === self::ROL_PARTICIPANTE && $tarea->id_usuario == Auth::id());
    }

    /**
     * Verificar si el usuario puede gestionar usuarios del proyecto
     */
    public static function puedeGestionarUsuarios($idProyecto)
    {
        return self::esAdministrador($idProyecto);
    }

    /**
     * Actualizar cache de permisos cuando cambian
     */
    public static function actualizarCache($idProyecto, $idUsuario = null)
    {
        $idUsuario = $idUsuario ?? Auth::id();
        
        $rol = Participar::where('id_proyecto', $idProyecto)
            ->where('id_usuario', $idUsuario)
            ->value('id_rols');
        
        session(["user_projects.{$idProyecto}" => $rol]);
    }

    /**
     * Limpiar cache de permisos
     */
    public static function limpiarCache()
    {
        session()->forget('user_projects');
    }

    /**
     * Obtener el nombre del rol
     */
    public static function obtenerNombreRol($idRol)
    {
        return [
            self::ROL_ADMINISTRADOR => 'Administrador',
            self::ROL_PARTICIPANTE => 'Participante'
        ][$idRol] ?? 'Desconocido';
    }

    /**
     * Obtener descripción de permisos del rol
     */
    public static function obtenerDescripcionRol($idRol)
    {
        return [
            self::ROL_ADMINISTRADOR => 'Creador del proyecto. Puede crear y asignar tareas a cualquier usuario, y eliminar cualquier tarea.',
            self::ROL_PARTICIPANTE => 'Puede ver todas las tareas y crear tareas asignadas a sí mismo. Solo puede eliminar sus propias tareas.'
        ][$idRol] ?? '';
    }
}
