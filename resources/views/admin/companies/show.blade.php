{{-- resources/views/admin/companies/show.blade.php  (REEMPLAZA al anterior)
     Detalle de una solicitud para el ADMIN: encabezado + la ficha compartida requests/_detail --}}
@extends('layouts.admin')
@section('title', 'Detalle de Solicitud')
@section('page-title', 'Solicitud ' . $solicitud->code)

@section('content')

<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }} · {{ $company->name }}</small>
    </div>
    <a href="{{ route('admin.companies.matrix', $company) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

@include('requests._detail', ['showResponsable' => true])

@endsection
