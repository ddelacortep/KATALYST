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
    const ROL_EDITOR = 2;
    const ROL_VISUALIZADOR = 3;

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
     * Verificar si el usuario es editor del proyecto
     */
    public static function esEditor($idProyecto, $idUsuario = null)
    {
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);
        return $rol === self::ROL_EDITOR;
    }

    /**
     * Verificar si el usuario es visualizador del proyecto
     */
    public static function esVisualizador($idProyecto, $idUsuario = null)
    {
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);
        return $rol === self::ROL_VISUALIZADOR;
    }

    /**
     * Verificar si el usuario puede crear tareas
     * Administrador y Editor pueden crear
     */
    public static function puedeCrearTareas($idProyecto, $idUsuario = null)
    {
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);
        return in_array($rol, [self::ROL_ADMINISTRADOR, self::ROL_EDITOR]);
    }

    /**
     * Verificar si el usuario puede editar una tarea
     * Administrador puede editar todas
     * Editor puede editar solo las suyas
     */
    public static function puedeEditarTarea($tarea, $idUsuario = null)
    {
        $idUsuario = $idUsuario ?? Session::get('usuario_id');
        $idProyecto = $tarea->id_proyecto;
        $rol = self::obtenerRolEnProyecto($idProyecto, $idUsuario);

        if ($rol === self::ROL_ADMINISTRADOR) {
            return true;
        }

        if ($rol === self::ROL_EDITOR && $tarea->id_usuario == $idUsuario) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si el usuario puede eliminar una tarea
     * Solo el administrador puede eliminar
     */
    public static function puedeEliminarTarea($idProyecto, $idUsuario = null)
    {
        return self::esAdministrador($idProyecto, $idUsuario);
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
            self::ROL_EDITOR => 'Editor',
            self::ROL_VISUALIZADOR => 'Visualizador'
        ];

        return $nombres[$idRol] ?? 'Desconocido';
    }

    /**
     * Obtener descripción de permisos del rol
     */
    public static function obtenerDescripcionRol($idRol)
    {
        $descripciones = [
            self::ROL_ADMINISTRADOR => 'Puede crear, editar y eliminar tareas. Gestiona usuarios del proyecto.',
            self::ROL_EDITOR => 'Puede crear tareas y editarlas (solo las propias).',
            self::ROL_VISUALIZADOR => 'Solo puede ver el proyecto y sus tareas.'
        ];

        return $descripciones[$idRol] ?? '';
    }
}
