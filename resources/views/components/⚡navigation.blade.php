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
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 pt-2 lg:pt-0 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />

        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="max-lg:hidden dark:hidden" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="hidden max-lg:!hidden dark:flex" />

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

    <flux:sidebar stashable sticky class="border-r border-zinc-200 bg-zinc-50 lg:hidden dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:brand href="/" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden" />
        <flux:brand href="/" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="hidden px-2 dark:flex" />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="/">Home</flux:navlist.item>
            <flux:navlist.item icon="face-smile" href="/playground">Playground</flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>
</div>
