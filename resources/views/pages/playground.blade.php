<?php
use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;

middleware(['auth', 'verified']);
name('playground');

new class extends Component {
    public string $message;

    public function sayHi()
    {
        sleep(3);
        $this->message = 'Hello there!';
    }
};
?>

<x-layouts.app>
    @volt('pages.playground')
        <div>
            <form wire:submit='sayHi'>
                <flux:button type="submit" loading="sayHi">Hi there</flux:button>
            </form>
            {{ $message }}
        </div>
    @endvolt
</x-layouts.app>
