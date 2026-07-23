<?php

use App\Models\User;

it('registers a new user successfully and logs them in', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'pass123',
        'password_confirmation' => 'pass123',
    ]);

    $response->assertRedirect(route('landing'));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'budisantoso.test@gmail.com',
        'role' => 'pelamar',
    ]);
});

it('fails registration when name is missing', function () {
    $response = $this->post('/register', [
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'pass123',
        'password_confirmation' => 'pass123',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

it('fails registration when email is invalid', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'bukan-email',
        'password' => 'pass123',
        'password_confirmation' => 'pass123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('fails registration when name already exists', function () {
    User::factory()->create(['name' => 'Budi Santoso']);

    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'baru.test@gmail.com',
        'password' => 'pass123',
        'password_confirmation' => 'pass123',
    ]);

    $response->assertSessionHasErrors('name');
});

it('fails registration when email already exists', function () {
    User::factory()->create(['email' => 'budisantoso.test@gmail.com']);

    $response = $this->post('/register', [
        'name' => 'Nama Lain',
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'pass123',
        'password_confirmation' => 'pass123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('fails registration when password is less than 6 characters', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'pas1',
        'password_confirmation' => 'pas1',
    ]);

    $response->assertSessionHasErrors('password');
});

it('fails registration when password is more than 8 characters', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('password');
});

it('fails registration when password confirmation does not match', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'pass123',
        'password_confirmation' => 'beda1234',
    ]);

    $response->assertSessionHasErrors('password');
});

it('always assigns role pelamar regardless of input', function () {
    $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budisantoso.test@gmail.com',
        'password' => 'pass123',
        'password_confirmation' => 'pass123',
        'role' => 'admin', // coba nyelundup jadi admin
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'budisantoso.test@gmail.com',
        'role' => 'pelamar', // tetep pelamar, karena role di-hardcode di controller
    ]);
});