<?php

namespace App\Http\Controllers;

use App\Models\Rols;
use Illuminate\Http\Request;

class RolsController extends Controller
{
    public function index()
    {
        return request()->expectsJson() 
            ? response()->json(Rols::cached())
            : view('roles.index', ['roles' => Rols::cached()]);
    }

    public function store(Request $request)
    {
        $request->validate(['nom_rols' => 'required|string|max:255']);
        $rol = Rols::createWithNextId($request->nom_rols);

        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Rol creado', 'rol' => $rol], 201)
            : back()->with('success', 'Rol creado');
    }

    public function show($id)
    {
        return response()->json(Rols::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nom_rols' => 'required|string|max:255']);
        Rols::findOrFail($id)->updateAndClearCache(['nom_rols' => $request->nom_rols]);

        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Rol actualizado'])
            : back()->with('success', 'Rol actualizado');
    }

    public function destroy($id)
    {
        Rols::findOrFail($id)->deleteAndClearCache();

        return request()->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Rol eliminado'])
            : back()->with('success', 'Rol eliminado');
    }
}
