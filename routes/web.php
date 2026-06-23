<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPagePelamarController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\CandidateResumeController;
use App\Http\Controllers\SettingsController;

// ===== AUTH =====
Route::get('/login',    [LoginController::class, 'index'])->name('login');
Route::post('/login',   [LoginController::class, 'login']);
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register',[RegisterController::class, 'register']);

Route::get('/auth/google',     fn() => redirect('/login'))->name('auth.google');
Route::get('/forgot-password', fn() => redirect('/login'))->name('password.request');

// ===== ROOT (/) - Landing page pelamar, BISA diakses tanpa login =====
Route::get('/', [LandingPagePelamarController::class, 'index'])->name('landing');

// ===== AKSI PELAMAR YANG WAJIB LOGIN (submit CV, hapus CV) =====
Route::middleware(['auth'])->group(function () {
    Route::post('/upload-cv', [LandingPagePelamarController::class, 'store'])->name('cv.store');
    Route::delete('/upload-cv/{id}', [LandingPagePelamarController::class, 'destroy'])->name('cv.destroy');
});

// ===== DASHBOARD HRD & FITUR (admin only, perlu login) =====
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Job Listing
    Route::get('/job_listing/create',  [JobListingController::class, 'create'])->name('job_listing.create');
    Route::post('/job_listing',        [JobListingController::class, 'store'])->name('job_listing.store');
    Route::get('/job_listing',         [JobListingController::class, 'index'])->name('job_listing.index');
    Route::post('/job_listing/{jobId}/screen', [JobListingController::class, 'screen'])->name('job_listing.screen');

    // Matching (History + Results detail + Candidate detail)
    Route::get('/matching',          [MatchingController::class, 'index'])->name('matching.index');
    Route::get('/matching_results',  [MatchingController::class, 'results'])->name('matching.results');
    Route::get('/candidate/{id}',    [CandidateResumeController::class, 'show'])->name('candidate.resume');

    // Candidates
    Route::get('/candidates', [CandidatesController::class, 'index'])->name('candidates.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

});