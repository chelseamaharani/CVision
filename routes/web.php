<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\SettingsController;

// ===== Landing Page =====
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// ===== Auth sementara =====
Route::get('/login', fn() => redirect('/'))->name('login');
Route::get('/register', fn() => redirect('/'))->name('register');
Route::post('/logout', fn() => redirect('/'))->name('logout');

// ===== Dashboard =====
Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');

// ===== JOB =====
Route::get('/upload_job', [JobController::class, 'create'])->name('jobs.create');
Route::post('/upload_job', [JobController::class, 'store'])->name('jobs.store');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');

// ===== MATCHING =====
Route::get('/matching', [MatchingController::class, 'index'])->name('matching.index');
Route::get('/matching_results', [MatchingController::class, 'index'])->name('matching.results');

// ===== SETTINGS (FIXED) =====
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

// ===== LAINNYA =====
Route::get('/candidates', fn() => view('pages.candidates'))->name('candidates.index');