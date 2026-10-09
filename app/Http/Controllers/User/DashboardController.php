<?php

// app/Http/Controllers/User/DashboardController.php
// Dashboard del USUARIO con datos reales: SOLO sus solicitudes.
//   - Tarjetas: cuántas tiene en cada estado general.
//   - Actividad reciente: las 5 solicitudes que cambiaron más recientemente (por ejemplo cuando el admin
//     actualiza un estado o sube un informe). La que cambió último aparece primera.

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Solo SUS solicitudes (nunca las de otros usuarios)
        $counts = VerificationRequest::where('user_id', $userId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'pendientes'  => $counts['en_espera']   ?? 0,
            'progreso'    => $counts['en_progreso'] ?? 0,
            'completados' => $counts['realizado']   ?? 0,
            'cancelados'  => $counts['cancelado']   ?? 0,
        ];

        // Mismas etiquetas del dashboard del admin
        $label = [
            'en_espera'   => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'realizado'   => 'Completado',
            'cancelado'   => 'Cancelado',
        ];

        $requests = VerificationRequest::where('user_id', $userId)
            ->with('services.result')
            ->latest('updated_at')        // la que cambió último, primero
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'id'       => $r->id,
                'code'     => $r->code,
                'dni'      => $r->dni,
                'name'     => $r->full_name,
                'status'   => $label[$r->status] ?? $r->status,
                'total'    => $r->services->count(),
                'done'     => $r->services->where('status', 'realizado')->count(),
                'informes' => $r->services->filter(fn ($s) => $s->result)->count(),
                'updated'  => $r->updated_at,
            ]);

        return view('user.dashboard', compact('stats', 'requests'));
    }
}