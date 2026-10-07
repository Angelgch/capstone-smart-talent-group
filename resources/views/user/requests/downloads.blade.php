{{-- resources/views/user/requests/downloads.blade.php   (REEMPLAZA al anterior)
     DESCARGAS del USUARIO: los informes que le envió la consultora (admin), uno por servicio. --}}
@extends('layouts.user')
@section('title', 'Descargas')
@section('page-title', 'Descargas — ' . $solicitud->code)

@section('content')
@php $hayInformes = $solicitud->services->contains(fn ($s) => $s->result); @endphp

<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · Informes enviados por la consultora</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
        @if ($hayInformes)
            <a href="{{ route('user.requests.zip', [$solicitud, 'informe_admin']) }}" class="btn btn-excel btn-sm">
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
            $item    = $solicitud->item($key);   // null = no solicitado
            $informe = $item?->result;
            $group   = $s['group'] ?? null;
        @endphp

        @if ($group && $group !== $lastGroup)
            <div class="small fw-semibold text-muted mt-3 mb-1">{{ $group }}</div>
        @endif
        @php $lastGroup = $group; @endphp

        <div class="row g-2 align-items-center border-bottom py-2 {{ $item ? '' : 'opacity-50' }}">
            <div class="col-md-4 fw-semibold">{{ $s['full'] }}</div>

            <div class="col-md-3">
                @if ($item)
                    <x-request-status :status="$item->status" />
                @else
                    <span class="text-muted">No solicitado</span>
                @endif
            </div>

            <div class="col-md-5">
                @if ($informe)
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="small"><i class="fas fa-file-pdf text-danger me-1"></i>{{ $informe->original_name }}</span>
                        <a href="{{ route('user.documents.download', [$informe, 'view' => 1]) }}" target="_blank" rel="noopener"
                        class="btn-icon btn-icon-view" title="Visualizar" aria-label="Visualizar informe de {{ $s['full'] }}"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('user.documents.download', $informe) }}"
                        class="btn-icon btn-icon-files" title="Descargar" aria-label="Descargar informe de {{ $s['full'] }}"><i class="fas fa-download"></i></a>
                    </div>
                @elseif ($item)
                    <span class="text-muted small">Pendiente</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection