<?php
use function Laravel\Folio\name;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

name('register');

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<x-layouts.auth>
    @volt('pages.auth.register')
        <flux:card>
            <form wire:submit='register' class="space-y-6">
                <div>
                    <flux:heading size="lg">Register a new account</flux:heading>
                    <flux:subheading>Lets's get started</flux:subheading>
                </div>

                <div class="space-y-6">
                    <flux:input label="Name" type="text" placeholder="Your name" wire:model='name' />

                    <flux:input label="Email" type="email" placeholder="Your email address" wire:model='email' />

                    <flux:input label="Password" type="password" placeholder="Your password" wire:model='password' />

                    <flux:input label="Confirm Password" type="password" placeholder="Confirm your password"
                        wire:model='password_confirmation' />
                </div>

                <div class="space-y-2">
                    <flux:button variant="primary" class="w-full" type="submit">Register</flux:button>

                    <flux:button variant="ghost" class="w-full" href="{{ route('login') }}" wire:navigate>Already have an
                        account?
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endvolt
</x-layouts.auth>
