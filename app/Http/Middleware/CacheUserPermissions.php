<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participar;

class CacheUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !session()->has('user_projects')) {
            // Cachear todos los proyectos del usuario con sus roles
            $proyectos = Participar::where('id_usuario', Auth::id())
                ->pluck('id_rols', 'id_proyecto')
                ->toArray();
            
            session(['user_projects' => $proyectos]);
        }

        return $next($request);
    }
}
