<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $usuari = Usuario::where('email', $request->input('email'))->first();

        if ($usuari && Hash::check($request->input('password'), $usuari->)) {
            return redirect()->back()->withErrors(['email' => 'El correo electrónico ya está en uso.'])->withInput();
        }
    }
}
