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
        $request->validate([
            'nombre_del_proyecto' => 'required|string|max:255',
            'usuario' => 'nullable|string|max:255',
        ]);

        $proyecto = Proyecto::create([
            'id_proyecto' => DB::table('proyecto')->max('id_proyecto') + 1,
            'nom_proyecto' => $request->nombre_del_proyecto,
            'slug' => Proyecto::generateSlug($request->nombre_del_proyecto),
        ]);
        
        // Asignar usuario autenticado como administrador
        Participar::create([
            'id_usuario' => Auth::id(),
            'id_proyecto' => $proyecto->id_proyecto,
            'id_rols' => 1, // Administrador
        ]);
        
        // Actualizar cache de sesión
        session(["user_projects.{$proyecto->id_proyecto}" => 1]);
        
        // Agregar colaborador adicional si existe
        if ($request->usuario) {
            $colaborador = Usuario::where('id_usuario', $request->usuario)
                                ->orWhere('nom_usuario', $request->usuario)
                                ->first();
            
            if ($colaborador && $colaborador->id_usuario != Auth::id()) {
                Participar::create([
                    'id_usuario' => $colaborador->id_usuario,
                    'id_proyecto' => $proyecto->id_proyecto,
                    'id_rols' => 2, // Participante
                ]);
            }
        }

        return redirect()->route('proyectos')->with('success', 'Proyecto creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proyecto $proyecto)
    {
        // Verificar acceso del usuario al proyecto
        if (!PermisosHelper::obtenerRolEnProyecto($proyecto->id_proyecto)) {
            return redirect()->route('proyectos')->with('error', 'No tienes acceso a este proyecto');
        }
        
        $proyecto->load(['tareas.usuario', 'participar.usuario', 'participar.rol']);
        
        $permisos = [
            'puede_crear_tareas' => PermisosHelper::puedeCrearTareas($proyecto->id_proyecto),
            'puede_gestionar_usuarios' => PermisosHelper::puedeGestionarUsuarios($proyecto->id_proyecto),
            'es_administrador' => PermisosHelper::esAdministrador($proyecto->id_proyecto),
            'es_participante' => PermisosHelper::esParticipante($proyecto->id_proyecto),
            'rol_actual' => PermisosHelper::obtenerRolEnProyecto($proyecto->id_proyecto)
        ];
        
        return view('proyecto.show', [
            'proyecto' => $proyecto,
            'todosUsuarios' => Usuario::all(),
            'roles' => Rols::all(),
            'permisos' => $permisos
        ]);
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
        if (!PermisosHelper::esAdministrador($proyecto->id_proyecto)) {
            return redirect()->route('proyectos')->with('error', 'Solo el administrador puede eliminar el proyecto');
        }

        $proyecto->delete();
        
        // Limpiar de sesión
        session()->forget("user_projects.{$proyecto->id_proyecto}");
        
        return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
    }
}