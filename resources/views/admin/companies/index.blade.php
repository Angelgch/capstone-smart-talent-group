{{-- resources/views/admin/companies/index.blade.php  (REEMPLAZA al anterior)
     Gestión de Empresas: lista + botón para registrar una nueva. El diseño es básico: cámbialo a tu gusto. --}}
@extends('layouts.admin')
@section('title', 'Gestión de Empresas')
@section('page-title', 'Gestión de Empresas')

@section('content')

{{-- Aviso al registrar una empresa (viene de CompanyController@store) --}}
@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.companies.create') }}" class="btn btn-gestionar text-decoration-none">
        <i class="fas fa-plus-circle me-1"></i> Nueva empresa
    </a>
</div>

<div class="row g-3">
    @forelse ($companies as $company)
    <div class="col-12">
        <div class="company-card">
            <div class="company-card-info">
                <h6>{{ $company->trade_name }}</h6>
                <small>{{ $company->legal_name }} · RUC: {{ $company->ruc }}</small>
            </div>
            
            {{-- AQUÍ ESTÁ EL CAMBIO: Los dos botones (tuerca + flecha) --}}
            <div class="d-flex gap-2">
                <a href="{{ route('admin.companies.edit', $company) }}" class="company-card-arrow" title="Configurar empresa">
                    <i class="fas fa-gear"></i>
                </a>
                <a href="{{ route('admin.companies.matrix', $company) }}" class="company-card-arrow" title="Ver matriz">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
    @empty
    <div class="col-12">
        <p class="text-center text-muted py-4">Todavía no hay empresas registradas.</p>
    </div>
    @endforelse
</div>
@endsection
