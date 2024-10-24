<?php
$routes = [
    [
        'name' => 'Home',
        'icon' => 'home',
        'to' => route('dashboard'),
    ],
    [
        'name' => 'Playground',
        'icon' => 'face-smile',
        'to' => route('playground'),
    ],
];

?>

<x-layouts.master>
    @section('navbar')
        <x-navigation.navbar :routes="$routes" />
    @endsection

    @section('sidebar')
        <x-navigation.sidebar :routes="$routes" />
    @endsection

    {{ $slot }}
</x-layouts.master>