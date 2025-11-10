<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Participar;
use App\Models\Rols;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        
        $userId = session('user_id') ?? \Illuminate\Support\Facades\Auth::id();
        
        Participar::create([
            'id_usuario' => $userId,
            'id_proyecto' => $proyecto->id_proyecto,
            'id_rols' => $rolAdmin->id_rols
        ]);
        
        // Buscar colaborador solo por nom_usuario (o email si es numérico) para evitar error de conversión
        if ($request->usuario) {
            $colaborador = Usuario::where('nom_usuario', $request->usuario)
                ->orWhere('email', $request->usuario)
                ->first();
        } else {
            $colaborador = null;
        }
        
        if ($colaborador && $colaborador->id_usuario != $userId) {
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
        $proyecto->load(['tareas.usuario', 'participar.usuario', 'participar.rol']);
        
        $userId = session('user_id') ?? \Illuminate\Support\Facades\Auth::id();
        $participacion = $proyecto->participar->firstWhere('id_usuario', $userId);
        $esAdministrador = $participacion?->id_rols === 1;
        $esParticipante = $participacion?->id_rols === 2;
        
        return view('proyecto.show', [
            'proyecto' => $proyecto,
            'permisos' => [
                'puede_crear_tareas' => $esAdministrador || $esParticipante,
                'puede_gestionar_usuarios' => $esAdministrador,
                'es_administrador' => $esAdministrador,
                'es_participante' => $esParticipante,
                'rol_actual' => $participacion?->rol->nom_rols
            ],
            'todosUsuarios' => Usuario::all(),
            'roles' => Rols::all()
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
        $proyecto->delete();
        return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
    }
}