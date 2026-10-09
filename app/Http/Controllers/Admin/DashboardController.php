<?php

// app/Http/Controllers/Admin/DashboardController.php
// Dashboard del ADMIN con datos reales: totales por estado general + las 5 solicitudes más recientes de TODAS las empresas.
// Cada vez que se carga la página aparece primero la solicitud más nueva.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Support\ServiceCatalog;

class DashboardController extends Controller
{
    public function index()
    {
        // Cuántas solicitudes hay en cada estado general (las tarjetas de arriba)
        $counts = VerificationRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'pendientes'  => $counts['en_espera']   ?? 0,
            'progreso'    => $counts['en_progreso'] ?? 0,
            'completados' => $counts['realizado']   ?? 0,
            'cancelados'  => $counts['cancelado']   ?? 0,
        ];

        // Etiquetas que ya usa tu vista del dashboard (así el blade actual no cambia)
        $label = [
            'en_espera'   => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'realizado'   => 'Completado',
            'cancelado'   => 'Cancelado',
        ];
        $catalog = ServiceCatalog::all();

        // Las 5 más recientes, sin importar la empresa (una fila = una solicitud)
        $requests = VerificationRequest::with(['user.company', 'services'])
            ->latest()
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'company_id' => $r->user?->company?->id,
                'ruc'        => $r->user?->company?->ruc,
                'name'       => $r->user?->company?->name,
                'docs'       => $r->services->map(fn ($s) => $catalog[$s->service]['short'] ?? $s->service)->all(),
                'date'       => $r->created_at->toDateString(),
                'status'     => $label[$r->status] ?? $r->status,
                'assigned'   => $r->user?->name,   // la columna ahora es el RESPONSABLE
            ]);

        return view('admin.dashboard', compact('stats', 'requests'));
    }
}