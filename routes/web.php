<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminProsesSuratController;
use App\Http\Controllers\AdminSemuaSuratController;
use App\Http\Controllers\AdminSuratMasukController;
use App\Http\Controllers\PemohonSuratController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifikatorSuratController;
use Illuminate\Support\Facades\Route;

// home & login
Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return redirect()->route(match ($user->role) {
            'SUPER_ADMIN' => 'super-admin.dashboard',
            'ADMIN_TU' => 'admin.dashboard',
            'PEMOHON' => 'pemohon.dashboard',
            'VERIFIKATOR' => 'verifikator.dashboard',
            default => 'home',
        });
    })->name('dashboard');
});

// admin
Route::middleware(['auth', 'role:ADMIN_TU'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::get('/pengajuan-masuk', [AdminSuratMasukController::class, 'index'])->name('pengajuan-masuk');
    Route::get('/pengajuan-masuk/{dokumen}/download-draft', [AdminSuratMasukController::class, 'downloadDraft'])->name('pengajuan-masuk.download-docx');

    Route::get('/semua-surat', [AdminSemuaSuratController::class, 'index'])->name('semua-surat');
    Route::get('/semua-surat/{dokumen}/preview-final', [AdminSemuaSuratController::class, 'previewFinal'])->name('semua-surat.preview-final');
    Route::get('/semua-surat/{dokumen}/download-final', [AdminSemuaSuratController::class, 'downloadFinal'])->name('semua-surat.download-final');
    Route::post('/surat/{dokumen}/publish', [AdminSemuaSuratController::class, 'publish'])->name('surat.publish');

    Route::get('/pengajuan-sk', function () {
        return view('admin.pengajuan-sk');
    })->name('pengajuan-sk');

    Route::get('/semua-sk', function () {
        return view('admin.semua-sk');
    })->name('semua-sk');

    Route::get('/master-dasar-hukum', function () {
        return view('admin.master-dasar-hukum');
    })->name('master-dasar-hukum');

    Route::get('/proses-surat', [AdminProsesSuratController::class, 'show'])->name('proses-surat');
    Route::post('/proses-surat/{dokumen}/simpan', [AdminProsesSuratController::class, 'storeDraft'])->name('proses-surat.store');
    Route::get('/proses-surat/{dokumen}/preview-pdf', [AdminProsesSuratController::class, 'previewPdf'])->name('proses-surat.preview-pdf');
    Route::post('/proses-surat/{dokumen}/posisi-elemen', [AdminProsesSuratController::class, 'storePosisiElemen'])->name('proses-surat.posisi-elemen');
    Route::post('/proses-surat/{dokumen}/kirim-verifikasi', [AdminProsesSuratController::class, 'storeVerifikasi'])->name('proses-surat.kirim-verifikasi');

    Route::get('/proses-sk', function () {
        return view('admin.proses-sk');
    })->name('proses-sk');

    Route::get('/profil', function () {
        return view('admin.profil');
    })->name('profil');
});

// pemohon
Route::middleware(['auth', 'role:PEMOHON'])->prefix('pemohon')->name('pemohon.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pemohon'])->name('dashboard');

    Route::get('/buat-surat', [PemohonSuratController::class, 'create'])->name('buat-surat');
    Route::post('/buat-surat', [PemohonSuratController::class, 'store'])->name('surat.store');

    Route::get('/buat-sk', function () {
        return view('pemohon.buat-sk');
    })->name('buat-sk');

    Route::get('/surat-saya', [PemohonSuratController::class, 'index'])->name('surat-saya');
    Route::get('/surat/{dokumen}/download', [PemohonSuratController::class, 'download'])->name('surat.download');

    Route::get('/sk-saya', function () {
        return view('pemohon.sk-saya');
    })->name('sk-saya');

    Route::get('/profil', function () {
        return view('pemohon.profil');
    })->name('profil');
});

// verifikator
Route::middleware(['auth', 'role:VERIFIKATOR'])->prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'verifikator'])->name('dashboard');

    Route::get('/surat-menunggu', [VerifikatorSuratController::class, 'menunggu'])->name('surat-menunggu');
    Route::get('/surat/{dokumen}/detail', [VerifikatorSuratController::class, 'detailSurat'])->name('surat.detail');
    Route::get('/surat-disetujui', [VerifikatorSuratController::class, 'disetujui'])->name('surat-disetujui');
    Route::get('/surat-ditolak', [VerifikatorSuratController::class, 'ditolak'])->name('surat-ditolak');
    Route::get('/surat/{dokumen}/preview-pdf', [VerifikatorSuratController::class, 'previewPdf'])->name('surat.preview-pdf');
    Route::get('/surat/{dokumen}/unduh-pdf', [VerifikatorSuratController::class, 'downloadPdf'])->name('surat.unduh-pdf');
    Route::post('/verifikasi/{verifikasi}/proses', [VerifikatorSuratController::class, 'proses'])->name('verifikasi.proses');

    Route::get('/surat-semua', function () {
        return view('verifikator.surat-semua');
    })->name('surat-semua');

    Route::get('/sk-menunggu', [VerifikatorSuratController::class, 'skMenunggu'])->name('sk-menunggu');
    Route::get('/sk/{dokumen}/detail', [VerifikatorSuratController::class, 'detailSk'])->name('sk.detail');
    Route::get('/sk/{dokumen}/preview-pdf', [VerifikatorSuratController::class, 'previewPdf'])->name('sk.preview-pdf');
    Route::get('/sk/{dokumen}/unduh-pdf', [VerifikatorSuratController::class, 'downloadPdf'])->name('sk.unduh-pdf');

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
Route::middleware(['auth', 'role:SUPER_ADMIN'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::patch('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
    });

    Route::get('/semua-user', function () {
        return redirect()->route('super-admin.users.index');
    })->name('semua-user');

    Route::get('/tambah-user', function () {
        return redirect()->route('super-admin.users.create');
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

    Route::get('/profil', function (UserController $controller) {
        return $controller->edit(auth()->user());
    })->name('profil');
});
