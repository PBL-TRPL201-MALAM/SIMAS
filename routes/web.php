<?php

use Illuminate\Support\Facades\Route;

// home & login
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

// admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/pengajuan-masuk', function () {
        return view('admin.pengajuan-masuk');
    })->name('pengajuan-masuk');

    Route::get('/semua-surat', function () {
        return view('admin.semua-surat');
    })->name('semua-surat');

    Route::get('/pengajuan-sk', function () {
        return view('admin.pengajuan-sk');
    })->name('pengajuan-sk');

    Route::get('/semua-sk', function () {
        return view('admin.semua-sk');
    })->name('semua-sk');

    Route::get('/master-dasar-hukum', function () {
        return view('admin.master-dasar-hukum');
    })->name('master-dasar-hukum');

    Route::get('/proses-surat', function () {
        return view('admin.proses-surat');
    })->name('proses-surat');

    Route::get('/proses-sk', function () {
        return view('admin.proses-sk');
    })->name('proses-sk');

    Route::get('/profil', function () {
        return view('admin.profil');
    })->name('profil');
});

// pemohon
Route::prefix('pemohon')->name('pemohon.')->group(function () {
    Route::get('/dashboard', function () {
        return view('pemohon.dashboard');
    })->name('dashboard');

    Route::get('/buat-surat', function () {
        return view('pemohon.buat-surat');
    })->name('buat-surat');

    Route::get('/buat-sk', function () {
        return view('pemohon.buat-sk');
    })->name('buat-sk');

    Route::get('/surat-saya', function () {
        return view('pemohon.surat-saya');
    })->name('surat-saya');

    Route::get('/sk-saya', function () {
        return view('pemohon.sk-saya');
    })->name('sk-saya');

    Route::get('/profil', function () {
        return view('pemohon.profil');
    })->name('profil');
});

// verifikator
Route::prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('/dashboard', function () {
        return view('verifikator.dashboard');
    })->name('dashboard');

    Route::get('/surat-menunggu', function () {
        return view('verifikator.surat-menunggu');
    })->name('surat-menunggu');

    Route::get('/surat-disetujui', function () {
        return view('verifikator.surat-disetujui');
    })->name('surat-disetujui');

    Route::get('/surat-ditolak', function () {
        return view('verifikator.surat-ditolak');
    })->name('surat-ditolak');

    Route::get('/surat-semua', function () {
        return view('verifikator.surat-semua');
    })->name('surat-semua');

    Route::get('/sk-menunggu', function () {
        return view('verifikator.sk-menunggu');
    })->name('sk-menunggu');

    Route::get('/sk-disetujui', function () {
        return view('verifikator.sk-disetujui');
    })->name('sk-disetujui');

    Route::get('/sk-ditolak', function () {
        return view('verifikator.sk-ditolak');
    })->name('sk-ditolak');

    Route::get('/sk-semua', function () {
        return view('verifikator.sk-semua');
    })->name('sk-semua');

    Route::get('/profil', function () {
        return view('verifikator.profil');
    })->name('profil');
});

// super admin
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('super-admin.dashboard');
    })->name('dashboard');

    Route::get('/semua-user', function () {
        return view('super-admin.semua-user');
    })->name('semua-user');

    Route::get('/tambah-user', function () {
        return view('super-admin.tambah-user');
    })->name('tambah-user');

    Route::get('/role-akses', function () {
        return view('super-admin.role-akses');
    })->name('role-akses');

    Route::get('/dasar-hukum', function () {
        return view('super-admin.dasar-hukum');
    })->name('dasar-hukum');

    Route::get('/unit-kerja', function () {
        return view('super-admin.unit-kerja');
    })->name('unit-kerja');

    Route::get('/semua-dokumen', function () {
        return view('super-admin.semua-dokumen');
    })->name('semua-dokumen');

    Route::get('/log-aktivitas', function () {
        return view('super-admin.log-aktivitas');
    })->name('log-aktivitas');

    Route::get('/profil', function () {
        return view('super-admin.profil');
    })->name('profil');
});

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
