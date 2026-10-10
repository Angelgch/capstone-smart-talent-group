<?php

// routes/web.php
// Solo lo base. Cada módulo tiene su propio archivo:
//   auth.php  -> inicio de sesión, registro y cierre de sesión
//   admin.php -> panel del administrador
//   user.php  -> portal del usuario

use Illuminate\Support\Facades\Route;

// Si alguien escribe /admin a mano, lo mandamos al dashboard
Route::redirect('/admin', '/admin/dashboard');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';