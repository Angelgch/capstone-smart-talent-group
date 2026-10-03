@extends('layouts.user')
@section('title', 'Nueva Solicitud')
@section('page-title', 'Nueva Solicitud')

@section('content')
    @include('user.requests.formCreate', [
        'candidate' => null,
        'return'    => route('user.requests.index'),
        'message'   => '✅ Solicitud enviada (borrador, aún sin base de datos)',
    ])
@endsection