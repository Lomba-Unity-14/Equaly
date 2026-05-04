<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Livewire\Beranda;
use App\Livewire\DetailLowongan;
use App\Livewire\Edukasi;
use App\Livewire\Lowongan;
use App\Livewire\Onboarding;
use App\Livewire\Profil;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store']);
    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store']);
});

Route::post('/keluar', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'onboarding'])->group(function () {
    Route::get('/onboarding', Onboarding::class)->name('onboarding');

    Route::get('/profil', Profil::class)->name('profil');
    Route::get('/', Beranda::class)->name('beranda');
});

Route::get('/lowongan', Lowongan::class)->name('lowongan');
Route::get('/lowongan/{lowongan}', DetailLowongan::class)->name('lowongan.detail');
Route::get('/edukasi', Edukasi::class)->name('edukasi');
