@extends('layouts.user')
@section('title', 'Dashboard - Portal Cliente')
@section('page-title', 'Dashboard')

@section('content')

@php
    $cards = [
        ['label' => 'Total enviadas', 'value' => $stats['total'],      'icon' => 'fa-paper-plane',    'color' => 'teal'],
        ['label' => 'En Proceso',     'value' => $stats['proceso'],    'icon' => 'fa-spinner',        'color' => 'orange'],
        ['label' => 'Realizadas',     'value' => $stats['realizadas'], 'icon' => 'fa-circle-check',   'color' => 'green'],
        ['label' => 'Canceladas',     'value' => $stats['canceladas'], 'icon' => 'fa-ban',            'color' => 'red'],
    ];
@endphp

<div class="welcome-banner">
    <div>
        <h4>¡Bienvenido al Portal!</h4>
        <p>Gestiona tus solicitudes de verificación y descarga tus documentos.</p>
    </div>
    <a href="{{ route('user.requests.create') }}" class="btn-new-request">
        <i class="fas fa-plus-circle me-1"></i> Nueva solicitud
    </a>
</div>

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

<div class="filter-section">
    <input type="text" id="dashSearch" class="form-control" style="max-width:340px" placeholder="Buscar por DNI o nombre...">
    <select id="dashStatus" class="form-select" style="max-width:200px">
        <option value="">Todos los estados</option>
        <option>En Proceso</option>
        <option>Realizado</option>
        <option>Cancelado</option>
    </select>
    <a href="{{ route('user.requests.index') }}" class="btn btn-gestionar ms-auto text-decoration-none">
        <i class="fas fa-table me-1"></i> Ver todas
    </a>
</div>

<div class="table-custom-container">
    <table class="table-simple">
        <thead>
            <tr>
                <th>Candidato</th>
                <th>Servicios solicitados</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $r)
            <tr class="dash-row" data-search="{{ strtolower($r['dni'].' '.$r['name']) }}" data-status="{{ $r['status'] }}">
                <td>
                    <div class="fw-semibold">{{ $r['name'] }}</div>
                    <small class="text-muted">DNI: {{ $r['dni'] }}</small>
                </td>
                <td>
                    @php $keys = array_keys($r['services']); @endphp
                    @foreach (array_slice($keys, 0, 3) as $k)
                        <span class="doc-chip">{{ $services[$k]['short'] }}</span>
                    @endforeach
                    @if (count($keys) > 3)
                        <span class="doc-chip doc-chip-more">+{{ count($keys) - 3 }}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($r['date'])->format('d/m/Y') }}</td>
                <td><x-status-badge :status="$r['status']" /></td>
                <td>
                    <a href="{{ route('user.requests.downloads', $r['dni']) }}" class="btn-icon btn-icon-files" title="Documentos">
                        <i class="fas fa-warehouse"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection