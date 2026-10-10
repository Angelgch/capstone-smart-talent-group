<?php

// routes/user.php — portal del USUARIO. Solo con sesión iniciada y role = user (otro rol => 403)

use App\Http\Controllers\User\AccountController as UserAccountController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\DocumentController as UserDocumentController;
use App\Http\Controllers\User\RequestController as UserRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Perfil y configuración ("Tu cuenta")
    Route::view('/profile', 'user.profile')->name('profile');
    Route::view('/configuration', 'user.configuration')->name('configuration');
    Route::put('/profile', [UserAccountController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [UserAccountController::class, 'updatePassword'])->name('password.update');
    Route::delete('/account', [UserAccountController::class, 'destroy'])->name('account.destroy');
    Route::put('/settings', fn () => back()->with('success', 'Preferencias guardadas.'))->name('settings.update'); // provisional

    // Solicitudes: matriz, Excel, crear, ver/editar, eliminar
    // (las rutas fijas "export" y "create" van ANTES de las que llevan {solicitud})
    Route::get('/requests', [UserRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/export', [UserRequestController::class, 'export'])->name('requests.export');
    Route::get('/requests/create', [UserRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [UserRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{solicitud}', [UserRequestController::class, 'show'])->whereNumber('solicitud')->name('requests.show');
    Route::put('/requests/{solicitud}', [UserRequestController::class, 'update'])->whereNumber('solicitud')->name('requests.update');
    Route::delete('/requests/{solicitud}', [UserRequestController::class, 'destroy'])->whereNumber('solicitud')->name('requests.destroy');

    // Descargas: informes del admin (página), zip y archivo individual
    Route::get('/requests/{solicitud}/downloads', [UserDocumentController::class, 'index'])->whereNumber('solicitud')->name('requests.downloads');
    Route::get('/requests/{solicitud}/zip/{type}', [UserDocumentController::class, 'zip'])->whereNumber('solicitud')->name('requests.zip');
    Route::get('/documents/{document}', [UserDocumentController::class, 'download'])->whereNumber('document')->name('documents.download');
});