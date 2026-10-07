<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\User\RequestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Support\DemoData;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\User\AccountController as UserAccountController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;

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

// Ruta raíz: Login / Registro. Si ya hay sesión, manda directo al panel según el rol.
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

// Ruta POST para cerrar sesión de manera segura (Invalida sesión y regenera token CSRF)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Descarga de archivos: un archivo o todos en .zip (admin: cualquiera | user: solo los suyos)
Route::middleware('auth')->group(function () {
    Route::get('/files/{document}', [DocumentController::class, 'download'])->name('files.download');
    Route::get('/requests/{verificationRequest}/zip/{kind}', [DocumentController::class, 'zip'])->name('files.zip');
});

// Redirección por defecto si alguien entra manualmente a la URL base /admin
Route::redirect('/admin', '/admin/dashboard');


// ==========================================
// 2. PANEL DE USUARIO (CLIENTE / POSTULANTE)
//    Solo con sesión iniciada y role = user (otro rol => 403)
// ==========================================
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {

    Route::get('/dashboard', function () {
        $all = collect(DemoData::userRequests())->sortByDesc('date')->values();

        return view('user.dashboard', [
            'stats' => [
                'total'      => $all->count(),
                'proceso'    => $all->where('status', 'En Proceso')->count(),
                'realizadas' => $all->where('status', 'Realizado')->count(),
                'canceladas' => $all->where('status', 'Cancelado')->count(),
            ],
            'requests' => $all->take(5), // máximo 5
            'services' => DemoData::services(),   // TEMPORAL (dashboard aún con DemoData)
        ]);
    })->name('dashboard');

      // Perfil y configuración (provisionales: "Módulo en desarrollo")//ULTIMO AÑADIDO
    Route::view('/profile', 'user.profile')->name('profile');
    Route::view('/configuration', 'user.configuration')->name('configuration');

    // Solicitudes del usuario (User\RequestController)
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/export', [RequestController::class, 'export'])->name('requests.export');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{solicitud}', [RequestController::class, 'show'])->whereNumber('solicitud')->name('requests.show');
    Route::put('/requests/{solicitud}', [RequestController::class, 'update'])->whereNumber('solicitud')->name('requests.update');
    Route::delete('/requests/{solicitud}', [RequestController::class, 'destroy'])->whereNumber('solicitud')->name('requests.destroy');

    // TEMPORAL (siguen con DemoData hasta que el detalle sea editable): editar y documentos de demostración
    Route::get('/requests/{dni}/edit', function ($dni) {
        $c = DemoData::candidate($dni);

        // Separa nombre completo: últimos 2 = apellidos, el resto = nombres
        $parts = explode(' ', trim($c['name']));
        $c['surnames'] = implode(' ', array_slice($parts, -2));
        $c['names']    = implode(' ', array_slice($parts, 0, -2));
        $c['email']    = 'candidato@correo.com'; // demo
        $c['phone']    = '999 999 999';          // demo

        return view('user.requests.edit', ['candidate' => $c, 'services' => DemoData::services()]);
    })->name('requests.edit');

    Route::get('/requests/{dni}/downloads', function ($dni) {
        return view('user.requests.downloads', [
            'candidate' => DemoData::candidate($dni),
            'services'  => DemoData::services(),
        ]);
    })->name('requests.downloads');
});


// ==========================================
// 3. PANEL DE ADMINISTRADOR
//    Solo con sesión iniciada y role = admin (otro rol => 403)
// ==========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

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

        // Perfil y configuración (provisionales: "Módulo en desarrollo")//ULTIMO AÑADIDO
    Route::view('/profile', 'admin.profile')->name('profile');
    Route::view('/configuration', 'admin.configuration')->name('configuration');

    // Empresas (ver Admin\CompanyController): lista -> registrar -> matriz (resumen) -> detalle de la solicitud
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    //AÑADIDAS EN LA RAMA PRUEBA PARA LA FUNCION DE BUSQUEDA Y FILTRO DE SOLICITUDES
    Route::get('/companies/{company}/matrix', [AdminRequestController::class, 'matrix'])->name('companies.matrix');
    Route::get('/companies/{company}/matrix/export', [AdminRequestController::class, 'export'])->name('companies.export');
    Route::get('/companies/{company}/requests/{solicitud}', [AdminRequestController::class, 'show'])->whereNumber('solicitud')->name('companies.requests.show');
    Route::put('/companies/{company}/requests/{solicitud}', [AdminRequestController::class, 'update'])->whereNumber('solicitud')->name('companies.requests.update');
    
    /*Route::get('/companies/{company}/matrix', [CompanyController::class, 'matrix'])->name('companies.matrix');
    Route::get('/companies/{company}/requests/{verificationRequest}', [CompanyController::class, 'show'])->name('companies.requests.show');*/
});


// ==========================================
// RUTAS DE ADMINISTRADOR (Perfil y Config)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::put('/profile', [AdminAccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AdminAccountController::class, 'updatePassword'])->name('password.update');

    Route::put('/settings', function () {
        return back()->with('success', 'Configuración guardada.');
    })->name('settings.update');
});

// ==========================================
// RUTAS DE CLIENTE / USER (Perfil y Config)
// ==========================================
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::put('/profile', [UserAccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [UserAccountController::class, 'updatePassword'])->name('password.update');
    Route::delete('/account', [UserAccountController::class, 'destroy'])->name('account.destroy');

    Route::put('/settings', function () {
        return back()->with('success', 'Preferencias guardadas.');
    })->name('settings.update');
});