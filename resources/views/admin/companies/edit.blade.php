@extends('layouts.admin')
@section('title', 'Editar Solicitud')
@section('page-title', 'Editar — ' . $candidate['name'])

@section('content')

@php
    $extras = [
        'direccion'     => 'Dirección domiciliaria',
        'referencia'    => 'Referencia domiciliaria',
        'observaciones' => 'Observaciones a tener en cuenta',
    ];
    $statuses = ['No Solicitado', 'En Espera' , 'En Proceso', 'Realizado', 'Cancelado'];
@endphp

<div class="edit-header">
    <div>
        <h6>{{ $candidate['name'] }}</h6>
        <small>DNI: {{ $candidate['dni'] }} · {{ $company['name'] }}</small>
    </div>
    <a href="{{ route('admin.companies.matrix', $company['id']) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<form id="editForm" class="edit-card" data-return="{{ route('admin.companies.matrix', $company['id']) }}">

    {{-- Servicios --}}
    @foreach ($services as $key => $s)
        @php
            $current = $candidate['services'][$key] ?? 'No Solicitado';
            $file    = $candidate['files'][$key] ?? null;
        @endphp
        <div class="edit-row">
            <div class="edit-row-name">{{ $s['full'] }}</div>

            <select class="form-select" name="status[{{ $key }}]">
                @foreach ($statuses as $opt)
                    <option value="{{ $opt }}" @selected($current === $opt)>{{ $opt }}</option>
                @endforeach
            </select>

            <div>
                @if ($file)
                    <small class="text-muted d-block mb-1"><i class="fas fa-paperclip me-1"></i>{{ $file }}</small>
                @endif
                <input type="file" class="form-control form-control-sm" name="file[{{ $key }}]" accept=".pdf">
            </div>
        </div>
    @endforeach

    {{-- Extras: mismo formato + campo de texto --}}
    @foreach ($extras as $key => $label)
        @php
            $current = $candidate['extras_status'][$key] ?? 'No Solicitado';
            $file    = $candidate['files'][$key] ?? null;
        @endphp
        <div class="edit-row edit-row-extra">
            <div class="edit-row-name">{{ $label }}</div>

            <select class="form-select" name="status[{{ $key }}]">
                @foreach ($statuses as $opt)
                    <option value="{{ $opt }}" @selected($current === $opt)>{{ $opt }}</option>
                @endforeach
            </select>

            <div>
                @if ($file)
                    <small class="text-muted d-block mb-1"><i class="fas fa-paperclip me-1"></i>{{ $file }}</small>
                @endif
                <input type="file" class="form-control form-control-sm" name="file[{{ $key }}]" accept=".pdf">
            </div>

            <div class="edit-row-text">
                @if ($key === 'observaciones')
                    <textarea class="form-control" name="{{ $key }}" rows="2" placeholder="{{ $label }}">{{ $candidate[$key] }}</textarea>
                @else
                    <input type="text" class="form-control" name="{{ $key }}" value="{{ $candidate[$key] }}" placeholder="{{ $label }}">
                @endif
            </div>
        </div>
    @endforeach

    <div class="edit-actions">
        <a href="{{ route('admin.companies.matrix', $company['id']) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="button" id="btnSaveEdit" class="btn btn-gestionar">
            <i class="fas fa-floppy-disk me-1"></i> Guardar cambios
        </button>
    </div>
</form>

@endsection