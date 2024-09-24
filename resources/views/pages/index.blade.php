<?php
use function Laravel\Folio\{middleware, name};
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

middleware(['auth', 'verified']);
name('dashboard');

new class extends Component {
    // dashboard logic
};
?>

<x-layouts.app>
    @volt('pages.dashboard')
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">Welcome to your new dashboard</flux:heading>
                <flux:subheading>Let's get started in the <flux:link href="/playground">playground.</flux:link>
                </flux:subheading>
            </div>
        </flux:card>
    @endvolt
</x-layouts.app>
