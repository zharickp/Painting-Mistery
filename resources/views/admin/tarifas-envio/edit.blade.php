@extends('layouts.app')
@section('title', 'Editar tarifa de envío')
@section('content')
    @include('admin.tarifas-envio._form', ['action' => route('admin.tarifas-envio.update', $tarifa), 'tarifa' => $tarifa, 'method' => 'PUT', 'titulo' => 'Editar tarifa de envío'])
@endsection
