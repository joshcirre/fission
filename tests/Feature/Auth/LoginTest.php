<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;

test('users can authenticate using the login form', function () {
    $user = User::factory()->create();

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create();

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('form.email');

    expect(auth()->check())->toBeFalse();
});

test('email field is required', function () {
    Livewire::test('pages::auth.login')
        ->set('form.email', '')
        ->set('form.password', 'password')
        ->call('login')
        ->assertHasErrors(['form.email' => 'required']);
});

test('password field is required', function () {
    Livewire::test('pages::auth.login')
        ->set('form.email', 'test@example.com')
        ->set('form.password', '')
        ->call('login')
        ->assertHasErrors(['form.password' => 'required']);
});

test('remember me functionality works', function () {
    $user = User::factory()->create();

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password')
        ->set('form.remember', true)
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
    // Note: viaRemember() only works after a subsequent request with the cookie
});
