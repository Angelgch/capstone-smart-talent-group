<?php

// routes/auth.php — acceso general: login, registro y logout

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Raíz: login/registro. Si ya hay sesión, manda directo al panel según el rol.
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard');
    }

    return view('auth.login');
})->name('login');

// Login y registro reales (los llama login.js con fetch). Limitados para frenar intentos masivos.
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1')
    ->name('login.attempt');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1')
    ->name('register.store');

// Cerrar sesión (invalida la sesión y regenera el token CSRF)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');