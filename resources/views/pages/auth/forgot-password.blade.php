<?php

use Illuminate\Support\Facades\Password;
use Livewire\Volt\Component;

use function Laravel\Folio\name;

name('password.request');

new class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        Flux::toast(__($status), variant: 'success');
    }
}; ?>

<x-layouts.auth>
    <flux:card>
        @volt('pages.auth.forgot-password')
            <form wire:submit="sendPasswordResetLink" class="space-y-6">
                <div>
                    <flux:heading size="lg">Reset your password</flux:heading>
                    <flux:subheading>Enter your email to receive a password reset link</flux:subheading>
                </div>

                <div class="space-y-6">
                    <flux:input wire:model="email" label="Email" type="email" placeholder="Your email address" required
                        autofocus />
                </div>

                <div class="space-y-2">
                    <flux:button variant="primary" class="w-full" type="submit">
                        {{ __('Email Password Reset Link') }}
                    </flux:button>

                    <flux:button variant="ghost" class="w-full" href="{{ route('login') }}" wire:navigate>
                        Back to login
                    </flux:button>
                </div>
            </form>
        @endvolt
    </flux:card>
</x-layouts.auth>
