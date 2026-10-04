@extends('layouts.user')
@section('title', 'Nueva Solicitud')
@section('page-title', 'Nueva Solicitud')

@section('content')
    {{-- $services y $extras llegan desde User\RequestController@create --}}
    @include('user.requests.formCreate')
@endsection
