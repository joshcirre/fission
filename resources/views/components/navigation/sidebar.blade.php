<?php
use function Livewire\Volt\{state};

state('routes');

?>

<nav>
    <flux:sidebar stashable sticky
        class="dark:bg-zinc-900 dark:border-zinc-700 border-r border-zinc-200 bg-zinc-50 lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
        @volt('layout.navigation.sidebar.routes')
            <flux:navlist variant="outline">
                @foreach ($routes as $route)
                    <flux:navlist.item :icon="$route['icon']" :href="$route['to']">
                        {{ $route['name'] }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist>
        @endvolt
    </flux:sidebar>
</nav>
