<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::resource('roles', RoleController::class)->middleware(['auth', 'role:Admin']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if (!$user->role) {
            return redirect()->route('login')->with('error', 'Tu usuario no tiene un rol asignado.');
        }

        return match ($user->role->name) {
            'Admin' => redirect()->route('dashboard.admin'),
            'User'  => redirect()->route('dashboard.user'),
            default => redirect()->route('login')->with('error', 'Rol desconocido.')
        };
    })->name('dashboard');

    Route::middleware('role:Admin')->get('/dashboard/admin', function () {
        return view('dashboard.admin');
    })->name('dashboard.admin');

    Route::middleware(['auth'])->get('/dashboard/user', function () {
        return view('dashboard.user');
    })->name('dashboard.user');
});
