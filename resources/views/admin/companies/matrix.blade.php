{{-- resources/views/admin/companies/matrix.blade.php  (REEMPLAZA al anterior)
     MATRIZ = tabla RESUMEN de las solicitudes de la empresa. El detalle completo está en show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Matriz de Solicitudes')
@section('page-title', 'Matriz — ' . $company->name)

@section('content')

{{-- Buscador y filtro: funcionan por GET (?q=...&status=...), lo resuelve el controlador --}}
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

    <a href="{{ route('admin.companies.export', array_merge(['company' => $company], request()->query())) }}" class="btn btn-excel ms-auto">    <i class="fas fa-file-excel me-1"></i> Descargar Excel</a>
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
                    {{-- Botón azul: ver detalle (ahí también se gestionará) --}}
                    <a href="{{ route('admin.companies.requests.show', [$company, $s]) }}" class="btn-icon btn-icon-view" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </a>

                    {{-- Botón naranja: descargar los archivos que envió el usuario (.zip) --}}
                    @if ($s->attachments_count > 0)
                        <a href="{{ route('files.zip', [$s, 'requisito_cliente']) }}" class="btn-icon btn-icon-files" title="Descargar archivos del usuario (.zip)">
                            <i class="fas fa-download"></i>
                        </a>
                    @else
                        <span class="btn-icon btn-icon-files is-disabled" title="El usuario no envió archivos">
                            <i class="fas fa-download"></i>
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">No hay solicitudes para mostrar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="mt-3">{{ $solicitudes->links() }}</div>

@endsection