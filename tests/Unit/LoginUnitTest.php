<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('password is hashed automatically via cast', function () {
    $user = User::factory()->create([
        'password' => 'secret123',
    ]);

    expect($user->password)->not->toBe('secret123')
        ->and(Hash::check('secret123', $user->password))->toBeTrue();
});

it('wrong password fails verification', function () {
    $user = User::factory()->create([
        'password' => 'secret123',
    ]);

    expect(Hash::check('wrongpass', $user->password))->toBeFalse();
});

it('role defaults to pelamar when not specified', function () {
    $user = User::factory()->create();
    $user->refresh();

    expect($user->role)->toBe('pelamar');
});

it('user fillable attributes match expected fields', function () {
    $user = new User();

    expect($user->getFillable())->toEqual(['name', 'email', 'password', 'role', 'google_id']);
});

it('password and remember_token are hidden from array/json', function () {
    $user = User::factory()->create(['password' => 'secret123']);

    $array = $user->toArray();

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});

it('admin can access dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk();
});

it('pelamar is forbidden from accessing dashboard', function () {
    $pelamar = User::factory()->create(['role' => 'pelamar']);

    $this->actingAs($pelamar)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('guest is redirected to login when accessing dashboard', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});