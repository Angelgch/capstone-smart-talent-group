@extends('layouts.admin')
@section('title', 'Dashboard - Administrador')
@section('page-title', 'Dashboard')

@section('content')

@php
    $cards = [
        ['label' => 'Pendientes / Nuevas', 'value' => $stats['pendientes'],  'icon' => 'fa-inbox',        'color' => 'red'],
        ['label' => 'En Progreso',         'value' => $stats['progreso'],    'icon' => 'fa-spinner',      'color' => 'yellow'],
        ['label' => 'Completados',         'value' => $stats['completados'], 'icon' => 'fa-circle-check', 'color' => 'green'],
        ['label' => 'Cancelados',          'value' => $stats['cancelados'],  'icon' => 'fa-ban',          'color' => 'gray'],
    ];
    $badge = [
        'Pendiente'   => 'dash-badge-pendiente',
        'En Progreso' => 'dash-badge-progreso',
        'Completado'  => 'dash-badge-completado',
        'Cancelado'   => 'dash-badge-cancelado',
    ];
@endphp

{{-- 1. Tarjetas: 4 iguales --}}
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
    <input type="text" id="dashSearch" class="form-control" style="max-width:340px" placeholder="Buscar por RUC o Razón Social...">
    <select id="dashStatus" class="form-select" style="max-width:200px">
        <option value="">Todos los estados</option>
        <option>Pendiente</option>
        <option>En Progreso</option>
        <option>Completado</option>
        <option>Cancelado</option>
    </select>
</div>

{{-- 3. Solicitudes recientes (máx. 5) --}}
<div class="table-custom-container">
    <table class="table-simple">
        <thead>
            <tr>
                <th>RUC / Empresa</th>
                <th>Documentos solicitados</th>
                <th>Fecha de ingreso</th>
                <th>Estado</th>
                <th>Realizado por</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $r)
            <tr class="dash-row" data-search="{{ strtolower($r['ruc'].' '.$r['name']) }}" data-status="{{ $r['status'] }}">
                <td>
                    <div class="fw-semibold">{{ $r['name'] }}</div>
                    <small class="text-muted">RUC: {{ $r['ruc'] }}</small>
                </td>
                <td>
                    @foreach (array_slice($r['docs'], 0, 3) as $doc)
                        <span class="doc-chip">{{ $doc }}</span>
                    @endforeach
                    @if (count($r['docs']) > 3)
                        <span class="doc-chip doc-chip-more">+{{ count($r['docs']) - 3 }}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($r['date'])->format('d/m/Y') }}</td>
                <td><span class="badge-status {{ $badge[$r['status']] }}">{{ $r['status'] }}</span></td>
                <td>
                    @if ($r['assigned'])
                        {{ $r['assigned'] }}
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.companies.matrix', $r['company_id']) }}" class="btn-gestionar text-decoration-none">
                        <i class="fas fa-table me-1"></i> Ir a Matriz
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection