<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;


Route::get('/', function () {
    return view('welcome');
});

Route::resource('roles', RoleController::class);
