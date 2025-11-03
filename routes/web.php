<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FileController;


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

Route::get('/dashboard/user', [FileController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.user');

Route::post('/files/upload', [FileController::class, 'store'])->name('files.store');

Route::get('/php-limits', function() {
    return [
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'memory_limit' => ini_get('memory_limit'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'max_execution_time' => ini_get('max_execution_time'),
    ];
});