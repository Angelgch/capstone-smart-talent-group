{{-- resources/views/admin/companies/downloads.blade.php   (REEMPLAZA al anterior)
     DESCARGAS del ADMIN: los archivos que envió el usuario, uno por servicio. --}}
@extends('layouts.admin')
@section('title', 'Descargas')
@section('page-title', 'Descargas — ' . $solicitud->code)

@section('content')
@php $hayArchivos = $solicitud->services->contains(fn ($s) => $s->attachment); @endphp

<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · {{ $company->name }} · Archivos enviados por {{ $solicitud->user->name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.companies.matrix', $company) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
        @if ($hayArchivos)
            <a href="{{ route('admin.companies.requests.zip', [$company, $solicitud, 'requisito_cliente']) }}" class="btn btn-excel btn-sm">
                <i class="fas fa-file-zipper me-1"></i> Descargar todo (.zip)
            </a>
        @else
            <button type="button" class="btn btn-excel btn-sm" disabled>
                <i class="fas fa-file-zipper me-1"></i> Descargar todo (.zip)
            </button>
        @endif
    </div>
</div>

<div class="edit-card">
    @php $lastGroup = null; @endphp
    @foreach ($services as $key => $s)
        @php
            $item  = $solicitud->item($key);   // null = el usuario no lo pidió
            $doc   = $item?->attachment;
            $group = $s['group'] ?? null;
        @endphp

        @if ($group && $group !== $lastGroup)
            <div class="small fw-semibold text-muted mt-3 mb-1">{{ $group }}</div>
        @endif
        @php $lastGroup = $group; @endphp

        <div class="row g-2 align-items-center border-bottom py-2 {{ $item ? '' : 'opacity-50' }}">
            <div class="col-md-4 fw-semibold">{{ $s['full'] }}</div>

            <div class="col-md-8">
                @if (! $item)
                    <span class="text-muted">No solicitado</span>
                @elseif ($doc)
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="small"><i class="fas fa-paperclip me-1"></i>{{ $doc->original_name }}</span>
                        <a href="{{ route('admin.documents.download', [$doc, 'view' => 1]) }}" target="_blank" rel="noopener"
                           class="btn-icon btn-icon-view" title="Visualizar" aria-label="Visualizar archivo de {{ $s['full'] }}"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.documents.download', $doc) }}"
                           class="btn-icon btn-icon-files" title="Descargar" aria-label="Descargar archivo de {{ $s['full'] }}"><i class="fas fa-download"></i></a>
                    </div>
                @else
                    <span class="text-muted small">El usuario no envió documento</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection