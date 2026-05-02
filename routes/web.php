<?php

use App\Livewire\Beranda;
use App\Livewire\DetailLowongan;
use App\Livewire\Edukasi;
use App\Livewire\Lowongan;
use App\Livewire\Profil;
use Illuminate\Support\Facades\Route;

Route::get('/', Beranda::class)->name('beranda');
Route::get('/lowongan', Lowongan::class)->name('lowongan');
Route::get('/lowongan/{lowongan}', DetailLowongan::class)->name('lowongan.detail');
Route::get('/edukasi', Edukasi::class)->name('edukasi');
Route::get('/profil', Profil::class)->name('profil');
