<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ConfigController;

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

    Route::middleware('role:Admin')->group(function () {
        Route::get('/dashboard/admin', [ConfigController::class, 'adminDashboard'])->name('dashboard.admin');
        
        Route::post('/admin/config/update', [ConfigController::class, 'updateConfig'])->name('admin.config.update');
        Route::post('/admin/config/add-extension', [ConfigController::class, 'addBlockedExtension'])->name('admin.config.add-extension');
        Route::delete('/admin/config/remove-extension/{extension}', [ConfigController::class, 'removeBlockedExtension'])->name('admin.config.remove-extension');

        Route::resource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy'
        ]);

        Route::resource('admin/groups', GroupController::class)->names([
            'index' => 'admin.groups.index',
            'create' => 'admin.groups.create',
            'store' => 'admin.groups.store',
            'show' => 'admin.groups.show',
            'edit' => 'admin.groups.edit',
            'update' => 'admin.groups.update',
            'destroy' => 'admin.groups.destroy'
        ]);

        Route::post('/groups/{group}/add-user', [GroupController::class, 'addUser'])
            ->name('admin.groups.add-user');
            
        Route::delete('/groups/{group}/remove-user/{user}', [GroupController::class, 'removeUser'])
            ->name('admin.groups.remove-user');
    });

    Route::get('/dashboard/user', [FileController::class, 'index'])->name('dashboard.user');
});

Route::post('/files/upload', [FileController::class, 'store'])->name('files.store');