{{-- resources/views/user/requests/show.blade.php   (REEMPLAZA al anterior)
     DETALLE del USUARIO con el MISMO diseño que el detalle del admin (tarjetas edit-card y filas separadas),
     pero con sus datos y documentos editables. El estado general es solo lectura. --}}
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

<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · Registrada el {{ $solicitud->created_at->format('d/m/Y') }}</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        {{-- Solo lo que ÉL envió (los informes están en Descargas) --}}
        @if ($solicitud->services->contains(fn ($s) => $s->attachment))
            <a href="{{ route('user.requests.zip', [$solicitud, 'requisito_cliente']) }}" class="btn btn-excel btn-sm">
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

        {{-- 1. Solicitud y candidato (el DNI no se puede cambiar) --}}
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
                    <small class="text-muted d-block">Estado general</small>
                    <x-request-status :status="$solicitud->status" kind="general" />
                </div>
                <div class="col-6 col-md-3">
                    <label class="text-muted small d-block" for="dni">DNI</label>
                    <input type="text" class="form-control form-control-sm" id="dni" value="{{ $solicitud->dni }}" readonly>
                </div>

                <div class="col-md-3">
                    <label class="text-muted small d-block" for="names">Nombres</label>
                    <input type="text" class="form-control form-control-sm" id="names" name="names" required
                           value="{{ old('names', $solicitud->names) }}">
                </div>
                <div class="col-md-3">
                    <label class="text-muted small d-block" for="surnames">Apellidos</label>
                    <input type="text" class="form-control form-control-sm" id="surnames" name="surnames" required
                           value="{{ old('surnames', $solicitud->surnames) }}">
                </div>
                <div class="col-md-3">
                    <label class="text-muted small d-block" for="email">Correo</label>
                    <input type="email" class="form-control form-control-sm" id="email" name="email" required
                           value="{{ old('email', $solicitud->email) }}">
                </div>
                <div class="col-md-3">
                    <label class="text-muted small d-block" for="phone">Teléfono (máx. 9 dígitos)</label>
                    <input type="tel" class="form-control form-control-sm" id="phone" name="phone" required maxlength="9"
                           inputmode="numeric" value="{{ old('phone', $solicitud->phone) }}">
                    <small class="field-error d-none" id="phoneError" role="alert"></small>
                </div>
            </div>
        </div>

        {{-- 2. Servicios con sus documentos --}}
        <div class="edit-card mb-3">
            <h6 class="fw-bold mb-1">Servicios</h6>
            <p class="text-muted small mb-2">
                Puedes agregar servicios, cancelar los que aún no empezaron y enviar, cambiar o quitar sus documentos.
                Los servicios con candado ya están en trámite.
            </p>

            @include('user.requests.serviceRows', ['plain' => true])

            <div id="formError" class="form-error d-none" role="alert"></div>
        </div>

        {{-- 3. Dirección, referencia y observaciones (solo texto, opcionales) --}}
        <div class="edit-card mb-3">
            <h6 class="fw-bold mb-3">Dirección, referencia y observaciones <small class="text-muted fw-normal">(opcional)</small></h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted small d-block" for="address">Dirección domiciliaria</label>
                    <input type="text" class="form-control" id="address" name="address" maxlength="500"
                           value="{{ old('address', $solicitud->address) }}">
                </div>
                <div class="col-md-6">
                    <label class="text-muted small d-block" for="reference">Referencia domiciliaria</label>
                    <input type="text" class="form-control" id="reference" name="reference" maxlength="500"
                           value="{{ old('reference', $solicitud->reference) }}">
                </div>
                <div class="col-12">
                    <label class="text-muted small d-block" for="observations">Observaciones a tener en cuenta</label>
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