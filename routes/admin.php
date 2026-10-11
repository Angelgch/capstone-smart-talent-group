<?php

// routes/admin.php — panel del ADMINISTRADOR. Solo con sesión iniciada y role = admin (otro rol => 403)

use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Perfil y configuración ("Tu cuenta"; el admin no puede eliminar su cuenta)
    Route::view('/profile', 'admin.profile')->name('profile');
    Route::view('/configuration', 'admin.configuration')->name('configuration');
    Route::put('/profile', [AdminAccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AdminAccountController::class, 'updatePassword'])->name('password.update');
    Route::put('/settings', fn () => back()->with('success', 'Configuración guardada.'))->name('settings.update'); // provisional

    // Empresas: lista, registrar, editar (tuerquita) y eliminar
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    // Solicitudes de una empresa: matriz, Excel, detalle y guardar
    Route::get('/companies/{company}/matrix', [AdminRequestController::class, 'matrix'])->name('companies.matrix');
    Route::get('/companies/{company}/matrix/export', [AdminRequestController::class, 'export'])->name('companies.export');
    Route::get('/companies/{company}/requests/{solicitud}', [AdminRequestController::class, 'show'])->whereNumber('solicitud')->name('companies.requests.show');
    Route::put('/companies/{company}/requests/{solicitud}', [AdminRequestController::class, 'update'])->whereNumber('solicitud')->name('companies.requests.update');

    // Descargas: archivos del usuario (página), zip y archivo individual
    Route::get('/companies/{company}/requests/{solicitud}/downloads', [AdminDocumentController::class, 'index'])->whereNumber('solicitud')->name('companies.requests.downloads');
    Route::get('/companies/{company}/requests/{solicitud}/zip/{type}', [AdminDocumentController::class, 'zip'])->whereNumber('solicitud')->name('companies.requests.zip');
    Route::get('/documents/{document}', [AdminDocumentController::class, 'download'])->whereNumber('document')->name('documents.download');
});