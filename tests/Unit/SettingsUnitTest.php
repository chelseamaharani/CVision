<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('admin can view settings page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('settings.index'));

    $response->assertOk();
});

it('admin can update name and email without changing password', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Nama Lama',
        'email' => 'lama@example.com',
        'password' => 'password123',
    ]);
    $originalPassword = $admin->password;

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => 'Nama Baru',
        'email' => 'baru@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $admin->refresh();

    expect($admin->name)->toBe('Nama Baru')
        ->and($admin->email)->toBe('baru@example.com')
        ->and($admin->password)->toBe($originalPassword);
});

it('admin can change password when old password is correct', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'oldpassword123',
    ]);

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => $admin->name,
        'email' => $admin->email,
        'old_password' => 'oldpassword123',
        'new_password' => 'newpassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $admin->refresh();

    expect(Hash::check('newpassword123', $admin->password))->toBeTrue();
});

it('fails to change password when old password is incorrect', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'oldpassword123',
    ]);
    $originalPassword = $admin->password;

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => $admin->name,
        'email' => $admin->email,
        'old_password' => 'passwordsalah',
        'new_password' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('old_password');

    $admin->refresh();

    expect($admin->password)->toBe($originalPassword);
});

it('does not change password when only new_password is filled without old_password', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'oldpassword123',
    ]);
    $originalPassword = $admin->password;

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => $admin->name,
        'email' => $admin->email,
        'new_password' => 'newpassword123',
    ]);

    $response->assertRedirect();

    $admin->refresh();

    expect($admin->password)->toBe($originalPassword);
});

it('fails to update when email already used by another user', function () {
    User::factory()->create(['email' => 'dipakai@example.com']);
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => $admin->name,
        'email' => 'dipakai@example.com',
    ]);

    $response->assertSessionHasErrors('email');
});

it('allows keeping the same email when updating own profile', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => 'Nama Update',
        'email' => 'admin@example.com', // email sendiri, harusnya lolos
    ]);

    $response->assertSessionDoesntHaveErrors('email');
});

it('fails to update when new_password is less than 8 characters', function () {
    $admin = User::factory()->create(['role' => 'admin', 'password' => 'oldpassword123']);

    $response = $this->actingAs($admin)->put(route('settings.update'), [
        'name' => $admin->name,
        'email' => $admin->email,
        'old_password' => 'oldpassword123',
        'new_password' => 'short',
    ]);

    $response->assertSessionHasErrors('new_password');
});

it('pelamar cannot access settings page', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $response = $this->actingAs($pelamar)->get(route('settings.index'));

    $response->assertForbidden();
});

it('guest is redirected to login when accessing settings', function () {
    $response = $this->get(route('settings.index'));

    $response->assertRedirect(route('login'));
});