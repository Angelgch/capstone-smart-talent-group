{{-- resources/views/admin/companies/matrix.blade.php
     MATRIZ = tabla RESUMEN de las solicitudes de la empresa. El detalle completo está en show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Matriz de Solicitudes')
@section('page-title', 'Matriz — ' . $company->name)

@section('content')

{{-- Buscador y filtro: ahora SÍ filtran (GET ?q=...&status=...), lo resuelve el controlador --}}
<form method="GET" action="{{ route('admin.companies.matrix', $company) }}" class="filter-section">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" style="max-width:320px"
           placeholder="Buscar por DNI, nombre o apellido...">

    <select name="status" class="form-select" style="max-width:200px">
        <option value="">Todos los estados</option>
        @foreach (['en_espera', 'en_progreso', 'realizado', 'cancelado'] as $st)
            <option value="{{ $st }}" @selected(request('status') === $st)>{{ \App\Support\StatusLabel::general($st) }}</option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-gestionar"><i class="fas fa-search me-1"></i> Filtrar</button>

    @if (request()->filled('q') || request()->filled('status'))
        <a href="{{ route('admin.companies.matrix', $company) }}" class="btn btn-outline-secondary">Limpiar</a>
    @endif

    <a href="#" class="btn btn-excel ms-auto" onclick="devAlert(event)"><i class="fas fa-file-excel me-1"></i> Descargar Excel</a>
</form>

<div class="table-custom-container">
    <table class="table-matrix">
        <thead>
            <tr>
                <th>N° Solicitud</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>Candidato</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($solicitudes as $s)
            <tr>
                <td class="fw-semibold">{{ $s->code }}</td>
                <td>{{ $s->created_at->format('d/m/Y') }}</td>
                <td>{{ $s->user->name }}</td>
                <td>
                    <div class="fw-semibold">{{ $s->full_name }}</div>
                    <small class="text-muted">DNI: {{ $s->dni }}</small>
                </td>
                <td><x-request-status :status="$s->status" kind="general" /></td>
                <td>
                    {{-- Ver detalle (ahí también se editará) --}}
                    <a href="{{ route('admin.companies.requests.show', [$company, $s]) }}" class="btn-icon btn-icon-view" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="#" class="btn-icon btn-icon-files" title="Descargar informes" onclick="devAlert(event)">
                        <i class="fas fa-download"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No hay solicitudes para mostrar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection