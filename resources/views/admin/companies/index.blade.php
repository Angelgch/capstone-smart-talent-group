@extends('layouts.admin')
@section('title', 'Gestión de Empresas')
@section('page-title', 'Gestión de Empresas')

@section('content')
<div class="row g-3">
    @foreach ($companies as $company)
    <div class="col-12">
        <div class="company-card">
            <div class="company-card-info">
                <h6>{{ $company['name'] }}</h6>
                <small>RUC: {{ $company['ruc'] }}</small>
            </div>
            <a href="{{ route('admin.companies.matrix', $company['id']) }}" class="company-card-arrow" title="Ver matriz">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection