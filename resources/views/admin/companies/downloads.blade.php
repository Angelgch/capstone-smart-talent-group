@extends('layouts.admin')
@section('title', 'Documentos')
@section('page-title', 'Documentos — ' . $candidate['name'])

@section('content')

<div class="edit-header">
    <div>
        <h6>{{ $candidate['name'] }}</h6>
        <small>DNI: {{ $candidate['dni'] }} · {{ $company['name'] }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.companies.matrix', $company['id']) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
        @if (count($candidate['files']))
            <a href="#" class="btn btn-excel btn-sm" onclick="devAlert(event)">
                <i class="fas fa-file-zipper me-1"></i> Descargar Todo (.zip)
            </a>
        @endif
    </div>
</div>

<div class="edit-card">
    @foreach ($services as $key => $s)
        @php
            $status = $candidate['services'][$key] ?? null;
            $file   = $candidate['files'][$key] ?? null;
        @endphp
        <div class="doc-item {{ $status ? '' : 'doc-item-off' }}">
            <div class="doc-name">{{ $loop->iteration }}. {{ $s['full'] }}</div>

            <div class="doc-file">
                @if ($file)
                    <i class="fas fa-file-pdf text-danger me-1"></i>{{ $file }}
                @elseif ($status)
                    <span class="text-muted">Pendiente de subir</span>
                @else
                    <x-status-badge />
                @endif
            </div>

            <div class="doc-actions">
                @if ($file)
                    <a href="#" class="btn-icon btn-icon-view" title="Visualizar" onclick="devAlert(event)"><i class="fas fa-eye"></i></a>
                    <a href="#" class="btn-icon btn-icon-files" title="Descargar" onclick="devAlert(event)"><i class="fas fa-download"></i></a>
                @endif
            </div>
        </div>
    @endforeach
</div>

@endsection