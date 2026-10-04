{{-- resources/views/user/requests/show.blade.php
     Detalle de una solicitud para el USUARIO: encabezado + la ficha compartida requests/_detail --}}
@extends('layouts.user')
@section('title', 'Detalle de Solicitud')
@section('page-title', 'Solicitud ' . $solicitud->code)

@section('content')

<div class="edit-header">
    <div>
        <h6>{{ $solicitud->full_name }}</h6>
        <small>{{ $solicitud->code }}</small>
    </div>
    <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

@include('requests._detail')

@endsection
