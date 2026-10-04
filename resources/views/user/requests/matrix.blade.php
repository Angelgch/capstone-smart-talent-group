{{-- resources/views/user/requests/matrix.blade.php   (antes index.blade.php)
     MATRIZ del usuario = tabla RESUMEN de sus solicitudes. El detalle completo está en show.blade.php --}}
@extends('layouts.user')
@section('title', 'Mis Solicitudes')
@section('page-title', 'Mis Solicitudes')

@section('content')

{{-- Aviso al crear una solicitud (viene de RequestController@store) --}}
@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

{{-- Buscador y filtro: funcionan por GET (?q=...&status=...) --}}
<form method="GET" action="{{ route('user.requests.index') }}" class="filter-section">
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
        <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary">Limpiar</a>
    @endif

    <div class="ms-auto d-flex gap-2">
        <a href="#" class="btn btn-excel" onclick="devAlert(event)"><i class="fas fa-file-excel me-1"></i> Descargar Excel</a>
        <a href="{{ route('user.requests.create') }}" class="btn-submit"><i class="fas fa-plus-circle"></i> Nueva solicitud</a>
    </div>
</form>

<div class="table-custom-container">
    <table class="table-matrix">
        <thead>
            <tr>
                <th>N° Solicitud</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>DNI</th>
                <th>Nombres</th>
                <th>Apellidos</th>
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
                <td>{{ $s->dni }}</td>
                <td>{{ $s->names }}</td>
                <td>{{ $s->surnames }}</td>
                <td><x-request-status :status="$s->status" kind="general" /></td>
                <td>
                    {{-- Botón azul: ver detalle --}}
                    <a href="{{ route('user.requests.show', $s) }}" class="btn-icon btn-icon-view" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </a>

                    {{-- Botón naranja: descargar los informes (.zip). Se activa cuando ya hay informes --}}
                    @if ($s->results_count > 0)
                        <a href="{{ route('files.zip', [$s, 'informe_admin']) }}" class="btn-icon btn-icon-files" title="Descargar informes (.zip)">
                            <i class="fas fa-download"></i>
                        </a>
                    @else
                        <span class="btn-icon btn-icon-files is-disabled" title="Aún no hay informes">
                            <i class="fas fa-download"></i>
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Todavía no tienes solicitudes.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection