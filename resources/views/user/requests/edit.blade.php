@extends('layouts.user')
@section('title', 'Editar Solicitud')
@section('page-title', 'Editar — ' . $candidate['name'])

@section('content')
    @include('user.requests.formCreate', [
        'candidate' => $candidate,
        'return'    => route('user.requests.index'),
        'message'   => '✅ Cambios guardados (borrador, aún sin base de datos)',
    ])
@endsection