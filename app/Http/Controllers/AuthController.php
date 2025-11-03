<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) { 
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->role) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Tu cuenta no tiene un rol asignado. Contacta al administrador.');
            }

            if ($user->role->name === 'Admin') {
                return redirect()->route('dashboard.admin');
            }

            return redirect()->route('dashboard.user');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas, por favor intenta de nuevo.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}