<?php

use App\Livewire\Actions\Logout;
use function Livewire\Volt\{state};

state('routes');

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<flux:header container sticky
    class="dark:bg-zinc-900 dark:border-zinc-700 border-b border-zinc-200 bg-zinc-50 pt-2 lg:pt-0">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc."
        class="dark:hidden max-lg:hidden" />
    <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
        class="dark:flex hidden max-lg:!hidden" />

    @volt('layout.navigation.navbar.routes')
        <flux:navbar class="max-lg:hidden">
            @foreach ($routes as $route)
                <flux:navbar.item :icon="$route['icon']" :href="$route['to']" wire:navigate>
                    {{ $route['name'] }}
                </flux:navbar.item>
                <!-- If this is not the last route, add a separator -->
                @if (!$loop->last)
                    <flux:separator vertical variant="subtle" class="my-2" />
                @endif
            @endforeach
        </flux:navbar>
    @endvolt

    <flux:spacer />

    <flux:dropdown position="bottom" align="end">
        <flux:button variant="ghost" icon-trailing="chevron-down">
            {{ auth()->user()->name }}
        </flux:button>
        @volt('layout.navigation.profile.dropdown')
            <flux:menu>
                <flux:navmenu.item href="{{ route('profile.update') }}" wire:navigate icon="building-storefront">
                    Profile
                </flux:navmenu.item>
                <flux:menu.item wire:click="logout" icon="arrow-right-start-on-rectangle">
                    Logout
                </flux:menu.item>
            </flux:menu>
        @endvolt
    </flux:dropdown>
</flux:header>
