<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery')</title>

    {{-- Favicon Painting Mistery --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo-painting-mistery.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-painting-mistery.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-painting-mistery.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    @yield('content')
</body>
</html>
