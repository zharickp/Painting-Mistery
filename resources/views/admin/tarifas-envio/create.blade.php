@extends('layouts.app')
@section('title', 'Nueva tarifa de envío')
@section('content')
    @include('admin.tarifas-envio._form', ['action' => route('admin.tarifas-envio.store'), 'tarifa' => null, 'method' => 'POST', 'titulo' => 'Nueva tarifa de envío'])
@endsection
