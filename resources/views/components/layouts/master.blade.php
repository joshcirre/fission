<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'My Laravel App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxStyles
</head>

<body class="dark:bg-zinc-800 min-h-screen bg-white font-sans antialiased">
    <!-- Navbar -->
    @section('navbar')
    @show

    <!-- Sidebar -->
    @section('sidebar')
    @show

    <flux:main container>
        <!-- Page Content -->
        {{ $slot }}
    </flux:main>

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts
</body>

</html>