<?php
use function Laravel\Folio\name;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

name('login');

new class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
};
?>

<x-layouts.auth>
    @volt('pages.auth.login')
        <flux:card>
            <form wire:submit='login' class="space-y-6">
                <div>
                    <flux:heading size="lg">Log in to your account</flux:heading>
                    <flux:subheading>Welcome back!</flux:subheading>
                </div>

                <div class="space-y-6">
                    <flux:input wire:model='form.email' label="Email" type="email" placeholder="Your email address" />

                    <flux:field>
                        <flux:label class="flex justify-between">
                            Password

                            <flux:link href="{{ route('password.request') }}" wire:navigate variant="subtle">Forgot password?
                            </flux:link>
                        </flux:label>

                        <flux:input wire:model='form.password' type="password" placeholder="Your password" />

                        <flux:error name="form.password" />
                    </flux:field>

                    <flux:checkbox wire:model="form.remember" label="Remember me" />
                </div>

                <div class="space-y-2">
                    <flux:button variant="primary" class="w-full" type="submit">Log in</flux:button>

                    <flux:button variant="ghost" class="w-full" href="{{ route('register') }}" wire:navigate>Sign up for a
                        new
                        account
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endvolt
</x-layouts.auth>
