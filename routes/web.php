<?php

use Illuminate\Support\Facades\Route;

// Web Routes untuk frontend

// Beranda
Route::get('/', function () {
    return view('home');
})->name('home');

// Halaman login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Dashboard pemohon (sementara, tanpa auth)
Route::get('/dashboard/pemohon', function () {
    return view('dashboard-pemohon');
})->name('dashboard.pemohon');

// dashboard admin (sementara, tanpa auth)
Route::get('/dashboard/admin', function () {
    return view('dashboard-admin');
})->name('dashboard.admin');

// dashboard verifikator (sementara, tanpa auth)
Route::get('/dashboard/verifikator', function () {
    return view('dashboard-verifikator');
})->name('dashboard.verifikator');
