<?php
use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;

middleware(['auth', 'verified']);
name('playground');

new class extends Component {
    public function mount()
    {
        info('this component has been loaded');
    }
};
?>

<x-layouts.app>
    @volt('pages.playground')
        <div>
            <flux:button data-pan="playground-button">Hi everyone</flux:button>
        </div>
    @endvolt
</x-layouts.app>
