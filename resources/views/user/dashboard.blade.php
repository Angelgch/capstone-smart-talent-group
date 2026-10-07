{{-- resources/views/user/dashboard.blade.php   (REEMPLAZA al anterior)
     Usa las mismas clases del dashboard del admin (stat-card, table-simple, dash-badge-*).
     El buscador y el filtro los resuelve user.js con los ids dashSearch / dashStatus y la clase dash-row. --}}
@extends('layouts.user')
@section('title', 'Dashboard - Portal Cliente')
@section('page-title', 'Dashboard')

@section('content')

@php
    $cards = [
        ['label' => 'Pendientes',  'value' => $stats['pendientes'],  'icon' => 'fa-inbox',        'color' => 'red'],
        ['label' => 'En Progreso', 'value' => $stats['progreso'],    'icon' => 'fa-spinner',      'color' => 'yellow'],
        ['label' => 'Completadas', 'value' => $stats['completados'], 'icon' => 'fa-circle-check', 'color' => 'green'],
        ['label' => 'Canceladas',  'value' => $stats['cancelados'],  'icon' => 'fa-ban',          'color' => 'gray'],
    ];
    $badge = [
        'Pendiente'   => 'dash-badge-pendiente',
        'En Progreso' => 'dash-badge-progreso',
        'Completado'  => 'dash-badge-completado',
        'Cancelado'   => 'dash-badge-cancelado',
    ];
@endphp

{{-- 1. Tarjetas: mis solicitudes por estado --}}
<div class="row g-3 mb-3">
    @foreach ($cards as $card)
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon {{ $card['color'] }}"><i class="fas {{ $card['icon'] }}"></i></div>
            <div>
                <div class="stat-number">{{ $card['value'] }}</div>
                <div class="stat-label">{{ $card['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- 2. Buscador y filtro --}}
<div class="filter-section">
    <input type="text" id="dashSearch" class="form-control" style="max-width:340px" placeholder="Buscar por N°, DNI o candidato...">
    <select id="dashStatus" class="form-select" style="max-width:200px">
        <option value="">Todos los estados</option>
        <option>Pendiente</option>
        <option>En Progreso</option>
        <option>Completado</option>
        <option>Cancelado</option>
    </select>
    <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary ms-auto">Ver todas mis solicitudes</a>
</div>

{{-- 3. Actividad reciente: las 5 que cambiaron último (ej. el admin actualizó un estado o subió un informe) --}}
<div class="table-custom-container">
    <table class="table-simple">
        <thead>
            <tr>
                <th>N° Solicitud</th>
                <th>Candidato</th>
                <th>Avance</th>
                <th>Informes</th>
                <th>Estado</th>
                <th>Última actualización</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $r)
            <tr class="dash-row" data-search="{{ strtolower($r['code'] . ' ' . $r['dni'] . ' ' . $r['name']) }}" data-status="{{ $r['status'] }}">
                <td class="fw-semibold">{{ $r['code'] }}</td>
                <td>
                    <div class="fw-semibold">{{ $r['name'] }}</div>
                    <small class="text-muted">DNI: {{ $r['dni'] }}</small>
                </td>
                <td>{{ $r['done'] }} de {{ $r['total'] }} servicios completados</td>
                <td>
                    @if ($r['informes'] > 0)
                        <span class="doc-chip"><i class="fas fa-file-pdf text-danger me-1"></i>{{ $r['informes'] }} informe(s)</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td><span class="badge-status {{ $badge[$r['status']] ?? '' }}">{{ $r['status'] }}</span></td>
                <td>{{ $r['updated']->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('user.requests.show', $r['id']) }}" class="btn-gestionar text-decoration-none">
                        <i class="fas fa-eye me-1"></i> Ver detalle
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Todavía no tienes solicitudes.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection