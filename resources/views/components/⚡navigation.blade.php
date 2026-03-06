<?php

use App\Livewire\Actions\Logout;
use Livewire\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};
?>

<div>
    <flux:header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:brand href="/" logo="/img/logo.svg" name="Fission" class="max-lg:hidden dark:hidden" />
        <flux:brand href="/" logo="/img/logo-dark.svg" name="Fission" class="hidden max-lg:hidden! dark:flex" />

        <flux:navbar class="max-lg:hidden">
            <flux:navbar.item icon="home" href="/" wire:navigate>Home</flux:navbar.item>
            <flux:separator vertical variant="subtle" class="my-2" />
            <flux:navbar.item icon="face-smile" href="/playground" wire:navigate>Playground</flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <flux:dropdown position="bottom" align="end">
            <flux:button icon-trailing="chevron-down" variant="ghost">{{ auth()->user()->name }}</flux:button>

            <flux:navmenu>
                <flux:navmenu.item href="{{ route('profile.update') }}" wire:navigate icon="building-storefront">Profile</flux:navmenu.item>
                <flux:navmenu.item wire:click="logout" icon="arrow-right-start-on-rectangle">Logout</flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>
    </flux:header>

    <flux:sidebar sticky collapsible="mobile" class="border-r border-zinc-200 bg-zinc-50 lg:hidden dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <flux:sidebar.brand href="/" logo="/img/logo.svg" logo:dark="/img/logo-dark.svg" name="Fission" />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="/" wire:navigate>Home</flux:sidebar.item>
            <flux:sidebar.item icon="face-smile" href="/playground" wire:navigate>Playground</flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    <flux:main container>
        {{ $slot }}
    </flux:main>
</div>
