<?php

namespace App\Helpers;

use App\Models\Participar;
use Illuminate\Support\Facades\Session;

class PermisosHelper
{
    /**
     * IDs de roles predefinidos
     */
    const ROL_ADMINISTRADOR = 1;
    const ROL_PARTICIPANTE = 2;

    /**
     * Obtener el rol del usuario en un proyecto
     */
    public static function obtenerRolEnProyecto($idProyecto, $idUsuario = null)
    {
        $idUsuario = $idUsuario ?? Session::get('usuario_id');
        
        if (!$idUsuario) {
            return null;
        }

        $participacion = Participar::where('id_proyecto', $idProyecto)
            ->where('id_usuario', $idUsuario)
            ->first();

        return $participacion ? $participacion->id_rols : null;
    }

    /**
     * Verificar si el usuario es administrador del proyecto
     */
    public static function esAdministrador($idProyecto, $idUsuario = null)
    {
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);
        return $rol === self::ROL_ADMINISTRADOR;
    }

    /**
     * Verificar si el usuario es participante del proyecto
     */
    public static function esParticipante($idProyecto, $idUsuario = null)
    {
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);
        return $rol === self::ROL_PARTICIPANTE;
    }

    /**
     * Verificar si el usuario puede crear tareas
     * Tanto Administrador como Participante pueden crear tareas
     */
    public static function puedeCrearTareas($idProyecto, $idUsuario = null)
    {
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);
        return in_array($rol, [self::ROL_ADMINISTRADOR, self::ROL_PARTICIPANTE]);
    }

    /**
     * Verificar si el usuario puede editar una tarea
     * Administrador puede editar todas
     * Participante puede editar solo las suyas
     */
    public static function puedeEditarTarea($tarea, $idUsuario = null)
    {
        $idUsuario = $idUsuario ?? Session::get('usuario_id');
        $idProyecto = $tarea->id_proyecto;
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);

        if ($rol === self::ROL_ADMINISTRADOR) {
            return true;
        }

        if ($rol === self::ROL_PARTICIPANTE && $tarea->id_usuario == $idUsuario) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si el usuario puede eliminar una tarea
     * Administrador puede eliminar todas las tareas
     * Participante puede eliminar solo las suyas
     */
    public static function puedeEliminarTarea($tarea, $idUsuario = null)
    {
        $idUsuario = $idUsuario ?? Session::get('usuario_id');
        $idProyecto = $tarea->id_proyecto;
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);

        if ($rol === self::ROL_ADMINISTRADOR) {
            return true;
        }

        if ($rol === self::ROL_PARTICIPANTE && $tarea->id_usuario == $idUsuario) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si el usuario puede gestionar usuarios del proyecto
     * Solo el administrador puede
     */
    public static function puedeGestionarUsuarios($idProyecto, $idUsuario = null)
    {
        return self::esAdministrador($idProyecto, $idUsuario);
    }

    /**
     * Obtener el nombre del rol
     */
    public static function obtenerNombreRol($idRol)
    {
        $nombres = [
            self::ROL_ADMINISTRADOR => 'Administrador',
            self::ROL_PARTICIPANTE => 'Participante'
        ];

        return $nombres[$idRol] ?? 'Desconocido';
    }

    /**
     * Obtener descripción de permisos del rol
     */
    public static function obtenerDescripcionRol($idRol)
    {
        $descripciones = [
            self::ROL_ADMINISTRADOR => 'Creador del proyecto. Puede crear y asignar tareas a cualquier usuario, y eliminar cualquier tarea.',
            self::ROL_PARTICIPANTE => 'Puede ver todas las tareas y crear tareas asignadas a sí mismo. Solo puede eliminar sus propias tareas.'
        ];

        return $descripciones[$idRol] ?? '';
    }
}
