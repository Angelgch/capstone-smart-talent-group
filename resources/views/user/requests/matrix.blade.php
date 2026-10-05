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

{{-- Errores (para cuando la contraseña de eliminar es incorrecta) --}}
@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
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
        <a href="{{ route('user.requests.export', request()->query()) }}" class="btn btn-excel"><i class="fas fa-file-excel me-1"></i> Descargar Excel</a>
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
                    <button type="button" class="btn-icon btn-icon-delete" title="Eliminar solicitud"
                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-action="{{ route('user.requests.destroy', $s) }}" data-code="{{ $s->code }}">
                        <i class="fas fa-trash"></i>
                    </button>
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

{{-- Paginación --}}
<div class="mt-3">{{ $solicitudes->links() }}</div>

{{-- Confirmar eliminación con la contraseña del usuario --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Eliminar solicitud <span id="deleteCode"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Se borrará la solicitud con todos sus documentos. Esta acción no se puede deshacer.</p>
                <label class="form-label fw-semibold" for="deletePassword">Ingresa tu contraseña para confirmar</label>
                <input type="password" class="form-control" id="deletePassword" name="password"
                       autocomplete="current-password" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Pasa la solicitud elegida al formulario del modal
    document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
        document.getElementById('deleteForm').action = e.relatedTarget.dataset.action;
        document.getElementById('deleteCode').textContent = e.relatedTarget.dataset.code;
        document.getElementById('deletePassword').value = '';
    });
</script>
@endpush

@endsection