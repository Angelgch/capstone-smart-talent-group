<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
| Aquí se registran todas las rutas de la aplicación web para usuarios,
| administradores y autenticación general.
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTAS DE AUTENTICACIÓN Y ACCESO GENERAL
// ==========================================

// Ruta raíz: Muestra la vista de inicio de sesión (Login)
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Ruta POST para cerrar sesión de manera segura (Invalida sesión y regenera token CSRF)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Redirección por defecto si alguien entra manualmente a la URL base /admin
Route::redirect('/admin', '/admin/dashboard');


// ==========================================
// 2. PANEL DE USUARIO (CLIENTE / POSTULANTE)
// ==========================================
Route::prefix('user')->name('user.')->group(function () {
    
    // Dashboard principal del usuario
    Route::get('/dashboard', function () {
        return view('user.dashboard'); 
    })->name('dashboard');

    Route::get('/index', function () {
        return view('user.index'); 
    })->name('index');

    // Aquí puedes añadir más rutas exclusivas para el rol de usuario en el futuro
});


// ==========================================
// 3. PANEL DE ADMINISTRADOR
// ==========================================
use App\Support\DemoData;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
    $all = collect(DemoData::requests())->sortByDesc('date')->values();

    $stats = [
        'pendientes'  => $all->where('status', 'Pendiente')->count(),
        'progreso'    => $all->where('status', 'En Progreso')->count(),
        'completados' => $all->where('status', 'Completado')->count(),
        'cancelados'  => $all->where('status', 'Cancelado')->count(),
    ];

    return view('admin.dashboard', [
        'stats'    => $stats,
        'requests' => $all->take(5), // máximo 5: al entrar una nueva, la más vieja sale
    ]);
})->name('dashboard');

    Route::get('/companies', function () {
        return view('admin.companies.index', ['companies' => DemoData::companies()]);
    })->name('companies.index');

    Route::get('/companies/{company}/matrix', function ($company) {
        return view('admin.companies.matrix', [
            'company'    => DemoData::company($company),
            'candidates' => DemoData::candidates(),
            'services'   => DemoData::services(),
        ]);
    })->name('companies.matrix');

    Route::get('/companies/{company}/candidates/{dni}/edit', function ($company, $dni) {
        return view('admin.companies.edit', [
            'company'   => DemoData::company($company),
            'candidate' => DemoData::candidate($dni),
            'services'  => DemoData::services(),
        ]);
    })->name('companies.edit');

    Route::get('/companies/{company}/candidates/{dni}/downloads', function ($company, $dni) {
        return view('admin.companies.downloads', [
            'company'   => DemoData::company($company),
            'candidate' => DemoData::candidate($dni),
            'services'  => DemoData::services(),
        ]);
    })->name('companies.downloads');
});