<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

test('profile page requires authentication', function () {
    get('/profile')->assertRedirect('/auth/login');
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->call('updateProfileInformation');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('email verification status is cleared when email is changed', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('name', $user->name)
        ->set('email', 'newemail@example.com')
        ->call('updateProfileInformation');

    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('user name is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('name', '')
        ->set('email', $user->email)
        ->call('updateProfileInformation')
        ->assertHasErrors(['name' => 'required']);
});

test('user email is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('name', $user->name)
        ->set('email', '')
        ->call('updateProfileInformation')
        ->assertHasErrors(['email' => 'required']);
});

test('user email must be valid', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('name', $user->name)
        ->set('email', 'not-an-email')
        ->call('updateProfileInformation')
        ->assertHasErrors(['email']);
});

test('user email must be unique', function () {
    $otherUser = User::factory()->create(['email' => 'other@example.com']);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('name', $user->name)
        ->set('email', 'other@example.com')
        ->call('updateProfileInformation')
        ->assertHasErrors(['email' => 'unique']);
});

test('password can be updated', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);
});

test('new password must be confirmed', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'different-password')
        ->call('updatePassword')
        ->assertHasErrors(['password' => 'confirmed']);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('delete_password', 'password')
        ->call('deleteUser')
        ->assertRedirect('/');

    expect(User::find($user->id))->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::profile.index')
        ->set('delete_password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['delete_password']);

    expect(User::find($user->id))->not->toBeNull();
});
