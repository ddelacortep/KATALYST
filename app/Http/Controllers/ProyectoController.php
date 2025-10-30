<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Participar;
use App\Models\Rols;
use App\Models\Usuario;
use App\Helpers\PermisosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proyectos = Proyecto::all();
        return view('proyectos', compact('proyectos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Session::has('usuario_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para crear un proyecto.');
        }

        // Validar datos
        $request->validate([
            'nombre_del_proyecto' => 'required|string|max:255',
            'usuario' => 'nullable|string|max:255',
        ]);

        // Obtener el ID del usuario autenticado
        $usuarioAutenticadoId = Session::get('usuario_id');

        // Obtener el siguiente ID disponible para el proyecto
        $nextId = DB::table('proyecto')->max('id_proyecto') + 1;
        
        // Crear el proyecto
        $proyecto = new Proyecto();
        $proyecto->id_proyecto = $nextId;
        $proyecto->nom_proyecto = $request->input('nombre_del_proyecto');
        $proyecto->save();
        
        // Obtener o crear rol por defecto
        $rol = Rols::firstOrCreate(
            ['id_rols' => 1],
            ['nom_rols' => 'Administrador']
        );
        
        // Asignar automáticamente el usuario autenticado como creador del proyecto
        $participar = new Participar();
        $participar->id_usuario = $usuarioAutenticadoId;
        $participar->id_proyecto = $proyecto->id_proyecto;
        $participar->id_rols = $rol->id_rols;
        $participar->save();
        
        // Agregar colaboradores adicionales si se proporcionó
        $usuarioInput = $request->input('usuario');
        
        if (!empty($usuarioInput) && $usuarioInput != $usuarioAutenticadoId) {
            // Buscar el usuario colaborador
            $usuario = Usuario::where('id_usuario', $usuarioInput)
                             ->orWhere('nom_usuario', $usuarioInput)
                             ->first();
            
            // Si existe el usuario, agregarlo como colaborador
            if ($usuario) {
                $participarColaborador = new Participar();
                $participarColaborador->id_usuario = $usuario->id_usuario;
                $participarColaborador->id_proyecto = $proyecto->id_proyecto;
                $participarColaborador->id_rols = $rol->id_rols;
                $participarColaborador->save();
            }
        }

        return redirect()->route('proyectos')->with('success', 'Proyecto creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $proyecto = Proyecto::with(['tareas.usuario', 'participar.usuario', 'participar.rol'])
            ->findOrFail($id);
        
        // Verificar si el usuario tiene acceso al proyecto
        $usuarioId = Session::get('usuario_id');
        $rolUsuario = PermisosHelper::obtenerRolEnProyecto($id, $usuarioId);
        
        if (!$rolUsuario) {
            return redirect()->route('proyectos')->with('error', 'No tienes acceso a este proyecto');
        }
        
        // Obtener todos los usuarios para el selector (solo si es administrador)
        $todosUsuarios = Usuario::all();
        
        // Obtener todos los roles disponibles
        $roles = Rols::all();
        
        // Pasar permisos a la vista
        $permisos = [
            'puede_crear_tareas' => PermisosHelper::puedeCrearTareas($id, $usuarioId),
            'puede_eliminar_tareas' => PermisosHelper::puedeEliminarTarea($id, $usuarioId),
            'puede_gestionar_usuarios' => PermisosHelper::puedeGestionarUsuarios($id, $usuarioId),
            'es_administrador' => PermisosHelper::esAdministrador($id, $usuarioId),
            'es_editor' => PermisosHelper::esEditor($id, $usuarioId),
            'es_visualizador' => PermisosHelper::esVisualizador($id, $usuarioId),
            'rol_actual' => $rolUsuario
        ];
        
        return view('proyecto.show', compact('proyecto', 'todosUsuarios', 'roles', 'permisos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proyecto $proyecto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Primero eliminar las relaciones en la tabla participar
            DB::table('participar')->where('id_proyecto', $id)->delete();
            
            // Luego eliminar las tareas asociadas al proyecto (si existen)
            DB::table('tareas')->where('id_proyecto', $id)->delete();
            
            // Finalmente eliminar el proyecto
            DB::table('proyecto')->where('id_proyecto', $id)->delete();
            
            return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('proyectos')->with('error', 'Error al eliminar el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Agregar un usuario al proyecto
     */
    public function agregarUsuario(Request $request, $id)
    {
        // Verificar permisos
        if (!PermisosHelper::puedeGestionarUsuarios($id)) {
            return redirect()->back()->with('error', 'No tienes permisos para gestionar usuarios');
        }

        $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        // Verificar si el usuario ya está en el proyecto
        $existe = Participar::where('id_usuario', $request->id_usuario)
            ->where('id_proyecto', $id)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'El usuario ya participa en este proyecto');
        }

        $participar = new Participar();
        $participar->id_usuario = $request->id_usuario;
        $participar->id_proyecto = $id;
        $participar->id_rols = $request->id_rol;
        $participar->save();

        return redirect()->back()->with('success', 'Usuario agregado al proyecto correctamente');
    }

    /**
     * Eliminar un usuario del proyecto
     */
    public function eliminarUsuario($proyectoId, $usuarioId)
    {
        // Verificar permisos
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'No tienes permisos para gestionar usuarios');
        }

        // Verificar que el usuario a eliminar no sea administrador
        $participacion = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->first();

        if ($participacion && $participacion->id_rols == PermisosHelper::ROL_ADMINISTRADOR) {
            return redirect()->back()->with('error', 'No se puede eliminar al administrador del proyecto');
        }

        try {
            DB::table('participar')
                ->where('id_proyecto', $proyectoId)
                ->where('id_usuario', $usuarioId)
                ->delete();

            return redirect()->back()->with('success', 'Usuario eliminado del proyecto');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el usuario del proyecto');
        }
    }

    /**
     * Actualizar el rol de un usuario en el proyecto
     */
    public function actualizarRolUsuario(Request $request, $proyectoId, $usuarioId)
    {
        // Verificar permisos
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'No tienes permisos para gestionar usuarios');
        }

        $request->validate([
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        // Verificar que no se intente cambiar el rol del administrador
        $participacion = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->first();

        if ($participacion && $participacion->id_rols == PermisosHelper::ROL_ADMINISTRADOR) {
            return redirect()->back()->with('error', 'No se puede cambiar el rol del administrador del proyecto');
        }

        try {
            DB::table('participar')
                ->where('id_proyecto', $proyectoId)
                ->where('id_usuario', $usuarioId)
                ->update(['id_rols' => $request->id_rol]);

            return redirect()->back()->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el rol');
        }
    }
}
