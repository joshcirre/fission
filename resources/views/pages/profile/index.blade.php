<?php

use App\Models\User;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

use function Laravel\Folio\{middleware, name};

middleware(['auth']);

name('profile.update');

new class extends Component {
    public string $name = '';
    public string $email = '';

    // Update Password Properties
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Delete User Property
    public string $delete_password = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast('Profile updated successfully.', variant: 'success');
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast('Verification link sent!', variant: 'success');
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast('Password updated successfully.', variant: 'success');
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'delete_password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        Flux::toast('Account deleted successfully.', variant: 'success');

        $this->redirect('/', navigate: true);
    }
}; ?>

<x-layouts.app>
    @volt('pages.profile.update')
        <div class="space-y-6">
            <flux:card>
                <form wire:submit="updateProfileInformation" class="space-y-6">
                    <div>
                        <flux:heading size="lg">Profile Information</flux:heading>
                        <flux:subheading>Update your account's profile information and email address.</flux:subheading>
                    </div>

                    <div class="space-y-6">
                        <flux:input wire:model="name" label="Name" type="text" placeholder="Your name" required
                            autofocus />

                        <flux:input wire:model="email" label="Email" type="email" placeholder="Your email address"
                            required />

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                            <div>
                                <p class="text-sm text-gray-800">
                                    Your email address is unverified.

                                    <flux:button wire:click.prevent="sendVerification" variant="link">
                                        Click here to re-send the verification email.
                                    </flux:button>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-4 justify-end items-center">
                        <flux:button type="submit" variant="primary">Save</flux:button>
                    </div>
                </form>
            </flux:card>

            <flux:card>
                <form wire:submit="updatePassword" class="space-y-6">
                    <div>
                        <flux:heading size="lg">Update Password</flux:heading>
                        <flux:subheading>Ensure your account is using a long, random password to stay secure.
                        </flux:subheading>
                    </div>

                    <div class="space-y-6">
                        <flux:input wire:model="current_password" label="Current Password" type="password" required />
                        <flux:input wire:model="password" label="New Password" type="password" required />
                        <flux:input wire:model="password_confirmation" label="Confirm Password" type="password" required />
                    </div>

                    <div class="flex gap-4 justify-end items-center">
                        <flux:button type="submit" variant="primary">Update Password</flux:button>
                    </div>
                </form>
            </flux:card>
            <flux:card>
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Delete Account</flux:heading>
                        <flux:subheading>Once your account is deleted, all of its resources and data will be permanently
                            deleted.</flux:subheading>
                    </div>

                    <flux:modal.trigger name="delete-profile">
                        <flux:button variant="danger" class="mt-4">Delete Account</flux:button>
                    </flux:modal.trigger>

                    <flux:modal name="delete-profile" class="min-w-[22rem] space-y-6">
                        <form wire:submit="deleteUser">
                            <div>
                                <flux:heading size="lg">Are you sure you want to delete your account?</flux:heading>

                                <flux:subheading>
                                    Once your account is deleted, all of its resources and data will be permanently
                                    deleted.
                                </flux:subheading>
                            </div>

                            <div class="mt-6">
                                <flux:input wire:model="delete_password" label="Password" type="password"
                                    placeholder="Password" required />
                            </div>

                            <div class="flex gap-2 mt-6">
                                <flux:spacer />

                                <flux:modal.close>
                                    <flux:button variant="ghost">Cancel</flux:button>
                                </flux:modal.close>

                                <flux:button type="submit" variant="danger">Delete Account</flux:button>
                            </div>
                        </form>
                    </flux:modal>
                </div>
            </flux:card>
        </div>
    @endvolt
</x-layouts.app>
