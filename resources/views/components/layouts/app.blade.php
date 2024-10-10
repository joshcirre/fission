<?php
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};
?>

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

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container
        class="pt-2 border-b bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 lg:pt-0">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />

        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc."
            class="max-lg:hidden dark:hidden" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
            class="max-lg:!hidden hidden dark:flex" />

        <flux:navbar class="max-lg:hidden">
            <flux:navbar.item icon="home" href="/" wire:navigate>Home</flux:navbar.item>
            <flux:separator vertical variant="subtle" class="my-2" />
            <flux:navbar.item icon="face-smile" href="/playground" wire:navigate>Playground</flux:navbar.item>

        </flux:navbar>

        <flux:spacer />


        <flux:dropdown position="bottom" align="end">
            <flux:button icon-trailing="chevron-down" variant="ghost">{{ auth()->user()->name }}</flux:button>

            @volt('layout.navigation.profile.dropdown')
                <flux:navmenu>
                    <flux:navmenu.item href="{{ route('profile.update') }}" wire:navigate icon="building-storefront">Profile
                    </flux:navmenu.item>
                    <flux:navmenu.item wire:click='logout' icon="arrow-right-start-on-rectangle">Logout</flux:navmenu.item>
                </flux:navmenu>
            @endvolt
        </flux:dropdown>
    </flux:header>

    <flux:sidebar stashable sticky
        class="border-r lg:hidden bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:brand href="/" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc."
            class="px-2 dark:hidden" />
        <flux:brand href="/" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
            class="hidden px-2 dark:flex" />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="/">Home</flux:navlist.item>
            <flux:navlist.item icon="face-smile" href="/playground">Playground</flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    <flux:main container>
        <div class="self-stretch flex-1 max-md:pt-6">
            {{ $slot }}
        </div>
    </flux:main>
    @persist('toast')
        <flux:toast />
    @endpersist
    @fluxScripts()
</body>

</html>
