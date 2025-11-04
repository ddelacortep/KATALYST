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
        
        // Generar slug a partir del nombre del proyecto
        $slug = Proyecto::generateSlug($request->input('nombre_del_proyecto'));
        
        // Crear el proyecto
        $proyecto = new Proyecto();
        $proyecto->id_proyecto = $nextId;
        $proyecto->nom_proyecto = $request->input('nombre_del_proyecto');
        $proyecto->slug = $slug;
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
    public function show(Proyecto $proyecto)
    {
        // Cargar relaciones
        $proyecto->load(['tareas.usuario', 'participar.usuario', 'participar.rol']);
        
        // Verificar si el usuario tiene acceso al proyecto
        $usuarioId = Session::get('usuario_id');
        $rolUsuario = PermisosHelper::obtenerRolEnProyecto($proyecto->id_proyecto, $usuarioId);
        
        if (!$rolUsuario) {
            return redirect()->route('proyectos')->with('error', 'No tienes acceso a este proyecto');
        }
        
        // Obtener todos los usuarios para el selector (solo si es administrador)
        $todosUsuarios = Usuario::all();
        
        // Obtener todos los roles disponibles
        $roles = Rols::all();
        
        // Pasar permisos a la vista
        $permisos = [
            'puede_crear_tareas' => PermisosHelper::puedeCrearTareas($proyecto->id_proyecto, $usuarioId),
            'puede_gestionar_usuarios' => PermisosHelper::puedeGestionarUsuarios($proyecto->id_proyecto, $usuarioId),
            'es_administrador' => PermisosHelper::esAdministrador($proyecto->id_proyecto, $usuarioId),
            'es_participante' => PermisosHelper::esParticipante($proyecto->id_proyecto, $usuarioId),
            'rol_actual' => $rolUsuario
        ];
        
        return view('proyecto.show', compact('proyecto', 'todosUsuarios', 'roles', 'permisos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proyecto $proyecto)
    {
        // Verificar que el usuario sea administrador del proyecto
        if (!PermisosHelper::esAdministrador($proyecto->id_proyecto)) {
            return redirect()->route('proyectos')->with('error', 'Solo el administrador puede eliminar el proyecto');
        }

        try {
            $proyecto->delete();
            return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('proyectos')->with('error', 'Error al eliminar el proyecto');
        }
    }
}