<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\User\RequestController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\User\AccountController as UserAccountController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DocumentController as UserDocumentController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;

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



// Redirección por defecto si alguien entra manualmente a la URL base /admin
Route::redirect('/admin', '/admin/dashboard');


// ==========================================
// 2. PANEL DE USUARIO (CLIENTE / POSTULANTE)
//    Solo con sesión iniciada y role = user (otro rol => 403)
// ==========================================
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {

    // Dashboard del usuario
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Perfil y configuración
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

    // Documentos y descargas (User\DocumentController)
    Route::get('/requests/{solicitud}/downloads', [UserDocumentController::class, 'index'])->whereNumber('solicitud')->name('requests.downloads');
    Route::get('/requests/{solicitud}/zip/{type}', [UserDocumentController::class, 'zip'])->whereNumber('solicitud')->name('requests.zip');
    Route::get('/documents/{document}', [UserDocumentController::class, 'download'])->whereNumber('document')->name('documents.download');

});


// ==========================================
// 3. PANEL DE ADMINISTRADOR
//    Solo con sesión iniciada y role = admin (otro rol => 403)
// ==========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

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
    
    Route::get('/companies/{company}/requests/{solicitud}/downloads', [AdminDocumentController::class, 'index'])->whereNumber('solicitud')->name('companies.requests.downloads');
    Route::get('/companies/{company}/requests/{solicitud}/zip/{type}', [AdminDocumentController::class, 'zip'])->whereNumber('solicitud')->name('companies.requests.zip');
    Route::get('/documents/{document}', [AdminDocumentController::class, 'download'])->whereNumber('document')->name('documents.download');
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