{{-- resources/views/admin/companies/show.blade.php   (REEMPLAZA al anterior)
    DETALLE del ADMIN: revisa los servicios que pidió el usuario, cambia su estado y sube los informes PDF.
    Los archivos que envió el usuario NO se ven aquí: están en Descargas. --}}
@extends('layouts.admin')
@section('title', 'Detalle de Solicitud')
@section('page-title', 'Solicitud ' . $solicitud->code)

@section('content')

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Revisa los datos:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · {{ $company->name }}</small>
    </div>
    <a href="{{ route('admin.companies.matrix', $company) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<form id="adminForm" method="POST" action="{{ route('admin.companies.requests.update', [$company, $solicitud]) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- 1. Solicitud y candidato (solo lectura) + estado general (editable) --}}
    <div class="edit-card mb-3">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">N° de solicitud</small>
                <strong>{{ $solicitud->code }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Fecha de solicitud</small>
                {{ $solicitud->created_at->format('d/m/Y H:i') }}
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Responsable</small>
                {{ $solicitud->user->name }}
                <small class="text-muted d-block">{{ $solicitud->user->email }}</small>
            </div>
            <div class="col-6 col-md-3">
                <label class="text-muted small d-block" for="general_status">Estado general</label>
                <select class="form-select form-select-sm" id="general_status" name="general_status">
                    @foreach (\App\Models\VerificationRequest::STATUSES as $st)
                        <option value="{{ $st }}" @selected($solicitud->status === $st)>{{ \App\Support\StatusLabel::general($st) }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Se actualiza solo según los servicios, salvo que lo cambies tú.</small>
            </div>

            <div class="col-6 col-md-3">
                <small class="text-muted d-block">DNI</small>
                {{ $solicitud->dni }}
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Nombres</small>
                {{ $solicitud->names }}
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Apellidos</small>
                {{ $solicitud->surnames }}
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Correo / Teléfono</small>
                {{ $solicitud->email }}<br>{{ $solicitud->phone }}
            </div>

            <div class="col-md-6">
                <small class="text-muted d-block">Dirección domiciliaria</small>
                {{ $solicitud->address ?: '—' }}
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Referencia domiciliaria</small>
                {{ $solicitud->reference ?: '—' }}
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Observaciones a tener en cuenta</small>
                {{ $solicitud->observations ?: 'Sin observaciones' }}
            </div>
        </div>
    </div>

    {{-- 2. Servicios: los pedidos se pueden gestionar; los no pedidos salen "No solicitado" --}}
    <div class="edit-card mb-3">
        <h6 class="fw-bold mb-3">Servicios</h6>

        @php $lastGroup = null; @endphp
        @foreach ($services as $key => $s)
            @php
                $item  = $solicitud->item($key);       // null = el usuario no lo pidió
                $group = $s['group'] ?? null;
            @endphp

            @if ($group && $group !== $lastGroup)
                <div class="small fw-semibold text-muted mt-3 mb-1">{{ $group }}</div>
            @endif
            @php $lastGroup = $group; @endphp

            @if ($item)
                @php $informe = $item->result; @endphp
                <div class="row g-2 align-items-center border-bottom py-2 admin-service-row"
                    data-has-informe="{{ $informe ? 1 : 0 }}">
                    <div class="col-md-4 fw-semibold">{{ $s['full'] }}</div>

                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="status[{{ $key }}]"
                                aria-label="Estado de {{ $s['full'] }}">
                            @foreach (\App\Models\VerificationRequest::STATUSES as $st)
                                <option value="{{ $st }}" @selected($item->status === $st)>{{ \App\Support\StatusLabel::service($st) }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger d-none informe-warn">Sube el informe PDF para marcar "Realizado".</small>
                    </div>

                    <div class="col-md-5">
                        @if ($informe)
                            <div class="small mb-1">
                                <i class="fas fa-file-pdf text-danger me-1"></i>Informe actual:
                                <a href="{{ route('admin.documents.download', [$informe, 'view' => 1]) }}" target="_blank" rel="noopener">{{ $informe->original_name }}</a>
                            </div>
                        @endif
                        <input type="file" class="form-control form-control-sm" name="informe[{{ $key }}]"
                            accept=".pdf" aria-label="{{ $informe ? 'Reemplazar' : 'Subir' }} informe de {{ $s['full'] }}">
                    </div>
                </div>
            @else
                <div class="row g-2 align-items-center border-bottom py-2 opacity-50">
                    <div class="col-md-4">{{ $s['full'] }}</div>
                    <div class="col-md-8 text-muted">No solicitado</div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('admin.companies.matrix', $company) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" id="btnSaveAdmin" class="btn btn-gestionar">
            <i class="fas fa-floppy-disk me-1"></i> Guardar cambios
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // Avisa si se elige "Realizado" sin informe (el servidor también lo valida)
    document.querySelectorAll('.admin-service-row').forEach(function (row) {
        var select = row.querySelector('select');
        var file = row.querySelector('input[type=file]');
        var warn = row.querySelector('.informe-warn');
        var hasInforme = row.dataset.hasInforme === '1';

        function check() {
            warn.classList.toggle('d-none', !(select.value === 'realizado' && !hasInforme && !file.files.length));
        }
        select.addEventListener('change', check);
        file.addEventListener('change', check);
        check();
    });

    // Evita el doble clic al guardar
    document.getElementById('adminForm').addEventListener('submit', function () {
        var btn = document.getElementById('btnSaveAdmin');
        btn.disabled = true;
        btn.innerHTML = 'Guardando...';
    });
</script>
@endpush