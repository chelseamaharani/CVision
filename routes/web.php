<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Sementara agar tidak error saat klik tombol Daftar/Login
// Nanti ganti dengan controller auth kamu sendiri
Route::get('/login', fn() => redirect('/'))->name('login');
Route::get('/register', fn() => redirect('/'))->name('register');
