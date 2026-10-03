{{-- resources/views/admin/companies/show.blade.php
     DETALLE de una solicitud (solo lectura por ahora; en el siguiente paso se vuelve editable con un botón Guardar) --}}
@extends('layouts.admin')
@section('title', 'Detalle de Solicitud')
@section('page-title', 'Solicitud ' . $solicitud->code)

@section('content')

{{-- Encabezado --}}
<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · {{ $company->name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.companies.matrix', $company) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
        <a href="#" class="btn btn-excel btn-sm" onclick="devAlert(event)">
            <i class="fas fa-file-zipper me-1"></i> Descargar Todo (.zip)
        </a>
    </div>
</div>

{{-- 1. Datos de la solicitud y del candidato --}}
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
            <small class="text-muted d-block">Estado general</small>
            <x-request-status :status="$solicitud->status" kind="general" />
        </div>

        <div class="col-6 col-md-3">
            <small class="text-muted d-block">DNI</small>
            {{ $solicitud->dni }}
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Candidato</small>
            {{ $solicitud->full_name }}
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Correo</small>
            {{ $solicitud->email }}
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Teléfono</small>
            {{ $solicitud->phone }}
        </div>
    </div>
</div>

{{-- 2. Servicios: estado individual + documento del usuario + informe del admin --}}
<div class="edit-card mb-3">
    <h6 class="fw-bold mb-3">Servicios</h6>

    @php $lastGroup = null; @endphp
    @foreach ($services as $key => $s)
        @php
            $item  = $solicitud->item($key);   // null = no solicitado
            $group = $s['group'] ?? null;
        @endphp

        {{-- Título de grupo (ej. "Antecedentes Nacionales") cuando cambia --}}
        @if ($group && $group !== $lastGroup)
            <div class="small fw-semibold text-muted mt-3 mb-1">{{ $group }}</div>
        @endif
        @php $lastGroup = $group; @endphp

        <div class="doc-item {{ $item ? '' : 'doc-item-off' }}">
            <div class="doc-name">{{ $s['full'] }}</div>

            <div class="doc-file">
                @if ($item)
                    <x-request-status :status="$item->status" />

                    @if ($item->attachment)
                        <div class="small text-muted mt-1">
                            <i class="fas fa-paperclip me-1"></i>Enviado por el usuario: {{ $item->attachment->original_name }}
                        </div>
                    @endif

                    @if ($item->result)
                        <div class="small mt-1"><i class="fas fa-file-pdf text-danger me-1"></i>{{ $item->result->original_name }}</div>
                    @else
                        <div class="small text-muted mt-1">Informe pendiente</div>
                    @endif
                @else
                    <span class="text-muted">No solicitado</span>
                @endif
            </div>

            <div class="doc-actions">
                @if ($item && $item->result)
                    <a href="#" class="btn-icon btn-icon-view" title="Visualizar informe" onclick="devAlert(event)"><i class="fas fa-eye"></i></a>
                    <a href="#" class="btn-icon btn-icon-files" title="Descargar informe" onclick="devAlert(event)"><i class="fas fa-download"></i></a>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- 3. Domicilio (texto O pdf) y observaciones (solo texto) --}}
<div class="edit-card">
    <h6 class="fw-bold mb-3">Domicilio y observaciones</h6>

    @foreach ($extras as $key => $e)
        @php $item = $solicitud->item($key); @endphp
        <div class="mb-3">
            <small class="text-muted d-block">
                {{ $e['full'] }}
                @if ($item) <x-request-status :status="$item->status" /> @endif
            </small>

            @if ($item && $item->attachment)
                {{-- Eligió PDF --}}
                <i class="fas fa-file-pdf text-danger me-1"></i>{{ $item->attachment->original_name }}
                <a href="#" class="btn-icon btn-icon-view ms-2" title="Visualizar" onclick="devAlert(event)"><i class="fas fa-eye"></i></a>
                <a href="#" class="btn-icon btn-icon-files" title="Descargar" onclick="devAlert(event)"><i class="fas fa-download"></i></a>
            @elseif ($item && $item->text)
                {{-- Eligió texto --}}
                {{ $item->text }}
            @else
                <span class="text-muted">No indicada</span>
            @endif
        </div>
    @endforeach

    <div>
        <small class="text-muted d-block">Observaciones a tener en cuenta</small>
        @if ($solicitud->observations)
            {{ $solicitud->observations }}
        @else
            <span class="text-muted">Sin observaciones</span>
        @endif
    </div>
</div>

@endsection