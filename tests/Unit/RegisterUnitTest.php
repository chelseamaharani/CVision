<?php

use App\Models\User;

it('registers a new user successfully and logs them in', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('landing'));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'budi@example.com',
        'role' => 'pelamar',
    ]);
});

it('fails registration when name is missing', function () {
    $response = $this->post('/register', [
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

it('fails registration when email is invalid', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'bukan-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('fails registration when name already exists', function () {
    User::factory()->create(['name' => 'Budi Santoso']);

    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'baru@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('name');
});

it('fails registration when email already exists', function () {
    User::factory()->create(['email' => 'budi@example.com']);

    $response = $this->post('/register', [
        'name' => 'Nama Lain',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('fails registration when password is less than 8 characters', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
});

it('fails registration when password confirmation does not match', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'beda-password',
    ]);

    $response->assertSessionHasErrors('password');
});

it('always assigns role pelamar regardless of input', function () {
    $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'admin', // coba nyelundup jadi admin
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'budi@example.com',
        'role' => 'pelamar', // tetep pelamar, karena role di-hardcode di controller
    ]);
});