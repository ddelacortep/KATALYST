<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Participar;
use App\Models\Rols;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        return view('proyectos', ['proyectos' => Proyecto::all()]);
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_del_proyecto' => 'required|string|max:255',
            'usuario' => 'nullable|string|max:255',
        ]);

        $proyecto = Proyecto::create([
            'id_proyecto' => Proyecto::max('id_proyecto') + 1,
            'nom_proyecto' => $request->nombre_del_proyecto,
            'slug' => Proyecto::generateSlug($request->nombre_del_proyecto)
        ]);
        
        $rolAdmin = Rols::firstOrCreate(['id_rols' => 1], ['nom_rols' => 'Administrador']);
        
        Participar::create([
            'id_usuario' => session('user_id'),
            'id_proyecto' => $proyecto->id_proyecto,
            'id_rols' => $rolAdmin->id_rols
        ]);
        
        // Actualizar sesión con el nuevo proyecto
        session(["user_projects.{$proyecto->id_proyecto}" => 1]);
        session()->push('accessible_projects', $proyecto->id_proyecto);
        
        $colaborador = Usuario::where('id_usuario', $request->usuario)->orWhere('nom_usuario', $request->usuario)->first();
        
        if ($colaborador && $colaborador->id_usuario != session('user_id')) {
            Participar::create([
                'id_usuario' => $colaborador->id_usuario,
                'id_proyecto' => $proyecto->id_proyecto,
                'id_rols' => $rolAdmin->id_rols
            ]);
        }

        return redirect()->route('proyectos')->with('success', 'Proyecto creado correctamente');
    }

    public function show(Proyecto $proyecto)
    {
        // Verificar acceso usando sesión
        if (!in_array($proyecto->id_proyecto, session('accessible_projects', []))) {
            return redirect()->route('proyectos')->with('error', 'No tienes acceso a este proyecto');
        }

        $proyecto->load(['tareas.usuario', 'participar.usuario', 'participar.rol']);
        
        $rolUsuario = session("user_projects.{$proyecto->id_proyecto}");
        
        return view('proyecto.show', [
            'proyecto' => $proyecto,
            'permisos' => [
                'puede_crear_tareas' => $rolUsuario !== null,
                'puede_gestionar_usuarios' => $rolUsuario == 1,
                'es_administrador' => $rolUsuario == 1,
                'es_participante' => $rolUsuario == 2,
                'rol_actual' => $rolUsuario
            ],
            'todosUsuarios' => Usuario::all(),
            'roles' => Rols::cached()
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Proyecto $proyecto)
    {
        if (session("user_projects.{$proyecto->id_proyecto}") != 1) {
            return redirect()->route('proyectos')->with('error', 'Solo el administrador puede eliminar el proyecto');
        }

        $proyecto->delete();
        
        // Limpiar de sesión
        session()->forget("user_projects.{$proyecto->id_proyecto}");
        $accessible = session('accessible_projects', []);
        session(['accessible_projects' => array_values(array_diff($accessible, [$proyecto->id_proyecto]))]);
        
        return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
    }
}