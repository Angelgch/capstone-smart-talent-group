{{-- resources/views/user/requests/show.blade.php   (REEMPLAZA al anterior)
     DETALLE del USUARIO = su formulario de crear, con sus datos y documentos ya guardados.
     El estado general es solo lectura (lo maneja el admin). Los informes del admin NO se ven aquí: están en Descargas. --}}
@extends('layouts.user')
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

{{-- Encabezado: candidato, fecha, estado general (solo lectura) y volver --}}
<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · Registrada el {{ $solicitud->created_at->format('d/m/Y') }}</small>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="text-muted small">Estado general</span>
        <x-request-status :status="$solicitud->status" kind="general" />

        {{-- Solo lo que ÉL envió (los informes están en Descargas) --}}
        @if ($solicitud->services->contains(fn ($s) => $s->attachment))
            <a href="{{ route('files.zip', [$solicitud, 'requisito_cliente']) }}" class="btn btn-excel btn-sm">
                <i class="fas fa-file-zipper me-1"></i> Archivos enviados (.zip)
            </a>
        @endif
        <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

@unless ($canEdit)
    <div class="alert alert-secondary">
        Esta solicitud ya no puede editarse porque está
        <strong>{{ strtolower(\App\Support\StatusLabel::general($solicitud->status)) }}</strong>.
    </div>
@endunless

<form id="requestForm" method="POST" action="{{ route('user.requests.update', $solicitud) }}"
      enctype="multipart/form-data" data-mode="edit">
    @csrf
    @method('PUT')

    {{-- Si no se puede editar, todo queda bloqueado --}}
    <fieldset @disabled(! $canEdit)>

        {{-- 1. CANDIDATO (el DNI no se puede cambiar) --}}
        <div class="section-card">
            <h5 class="section-title">
                <span class="icon-circle teal"><i class="fas fa-user-plus"></i></span>
                Datos del candidato
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="dni">DNI</label>
                    <input type="text" class="form-control" id="dni" value="{{ $solicitud->dni }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="email">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="{{ old('email', $solicitud->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="names">Nombres</label>
                    <input type="text" class="form-control" id="names" name="names" required
                           value="{{ old('names', $solicitud->names) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="surnames">Apellidos</label>
                    <input type="text" class="form-control" id="surnames" name="surnames" required
                           value="{{ old('surnames', $solicitud->surnames) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phone">Teléfono (máx. 9 dígitos)</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required maxlength="9"
                           inputmode="numeric" value="{{ old('phone', $solicitud->phone) }}">
                    <small class="field-error d-none" id="phoneError" role="alert"></small>
                </div>
            </div>
        </div>

        {{-- 2. SERVICIOS con sus documentos --}}
        <div class="section-card">
            <h5 class="section-title">
                <span class="icon-circle orange"><i class="fas fa-concierge-bell"></i></span>
                Servicios solicitados
            </h5>
            <p class="text-muted small mb-3">
                Puedes agregar servicios, cancelar los que aún no empezaron y enviar, cambiar o quitar sus documentos.
                Los servicios con candado ya están en trámite.
            </p>

            @include('user.requests.serviceRows')

            <div id="formError" class="form-error d-none" role="alert"></div>
        </div>

        {{-- 3. DIRECCIÓN, REFERENCIA Y OBSERVACIONES (solo texto, opcionales) --}}
        <div class="section-card">
            <h5 class="section-title">
                <span class="icon-circle pink"><i class="fas fa-location-dot"></i></span>
                Dirección, referencia y observaciones
                <small class="text-muted fw-normal" style="font-size:.8rem">(opcional)</small>
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="address">Dirección domiciliaria</label>
                    <input type="text" class="form-control" id="address" name="address" maxlength="500"
                           value="{{ old('address', $solicitud->address) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="reference">Referencia domiciliaria</label>
                    <input type="text" class="form-control" id="reference" name="reference" maxlength="500"
                           value="{{ old('reference', $solicitud->reference) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" for="observations">Observaciones a tener en cuenta</label>
                    <textarea class="form-control" id="observations" name="observations" rows="3"
                              maxlength="2000">{{ old('observations', $solicitud->observations) }}</textarea>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary">
            {{ $canEdit ? 'Cancelar' : 'Volver' }}
        </a>
        @if ($canEdit)
            <button type="submit" id="btnSubmitRequest" class="btn-submit">
                <i class="fas fa-floppy-disk"></i> Guardar cambios
            </button>
        @endif
    </div>
</form>

@endsection