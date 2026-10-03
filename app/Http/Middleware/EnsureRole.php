<?php

// app/Http/Middleware/EnsureRole.php
// Uso en rutas: ->middleware(['auth', 'role:admin'])  o  ->middleware(['auth', 'role:user'])
// Si el usuario no tiene el rol pedido => 403 Forbidden (RBAC del Prompt Maestro).

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== $role) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}