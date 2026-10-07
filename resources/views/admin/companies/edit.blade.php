{{-- resources/views/admin/companies/edit.blade.php   (REEMPLAZA al edit.blade.php viejo de candidatos: ya no se usa)
     La "tuerquita" de cada empresa: editar sus datos y, abajo, eliminarla con la contraseña del admin. --}}
@extends('layouts.admin')
@section('title', 'Configurar Empresa')
@section('page-title', 'Configurar — ' . $company->name)

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 1. Datos de la empresa --}}
<form method="POST" action="{{ route('admin.companies.update', $company) }}" class="edit-card mb-4" style="max-width:720px">
    @csrf
    @method('PUT')

    <h6 class="fw-bold mb-3">Datos de la empresa</h6>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold" for="ruc">RUC (11 dígitos)</label>
            <input type="text" class="form-control" id="ruc" name="ruc" maxlength="11" inputmode="numeric"
                   autocomplete="off" required value="{{ old('ruc', $company->ruc) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold" for="phone">Teléfono (opcional)</label>
            <input type="tel" class="form-control" id="phone" name="phone" maxlength="15" inputmode="numeric"
                   autocomplete="off" value="{{ old('phone', $company->phone) }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="legal_name">Razón social</label>
            <input type="text" class="form-control" id="legal_name" name="legal_name" required
                   value="{{ old('legal_name', $company->legal_name) }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="trade_name">Nombre comercial</label>
            <input type="text" class="form-control" id="trade_name" name="trade_name" required
                   value="{{ old('trade_name', $company->trade_name) }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="address">Dirección (opcional)</label>
            <input type="text" class="form-control" id="address" name="address"
                   value="{{ old('address', $company->address) }}">
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">Volver</a>
        <button type="submit" class="btn btn-gestionar"><i class="fas fa-floppy-disk me-1"></i> Guardar cambios</button>
    </div>
</form>

{{-- 2. Zona de peligro: eliminar --}}
<div class="edit-card border border-danger-subtle" style="max-width:720px">
    <h6 class="fw-bold text-danger mb-2">Eliminar empresa</h6>
    <p class="text-muted small mb-3">
        Esta empresa tiene <strong>{{ $usersCount }}</strong> usuario(s) y <strong>{{ $requestsCount }}</strong> solicitud(es).
        Al eliminarla se borran también sus usuarios, sus solicitudes y todos sus documentos.
        <strong>No se puede deshacer.</strong>
    </p>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCompanyModal">
        <i class="fas fa-trash me-1"></i> Eliminar empresa
    </button>
</div>

{{-- Confirmación con la contraseña del admin --}}
<div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">¿Eliminar {{ $company->name }}?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Se eliminarán la empresa, sus {{ $usersCount }} usuario(s) y sus {{ $requestsCount }} solicitud(es) con todos sus documentos.</p>
                <label class="form-label fw-semibold" for="deleteCompanyPassword">Ingresa tu contraseña de administrador para confirmar</label>
                <input type="password" class="form-control" id="deleteCompanyPassword" name="password"
                       autocomplete="current-password" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Sí, eliminar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // RUC y teléfono: solo números (el servidor igual los valida)
    ['ruc', 'phone'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    });
</script>
@endpush