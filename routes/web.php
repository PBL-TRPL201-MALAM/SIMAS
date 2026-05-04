<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminProsesSuratController;
use App\Http\Controllers\AdminSemuaSuratController;
use App\Http\Controllers\AdminSuratMasukController;
use App\Http\Controllers\PemohonSuratController;
use App\Http\Controllers\PublicDokumenVerificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifikatorSuratController;
use Illuminate\Support\Facades\Route;

// File route di Laravel berfungsi seperti daftar "peta URL".
// Setiap URL diarahkan ke closure sederhana atau method controller yang menangani request.

// Route publik dasar untuk landing page SIMAS dan form login.
Route::get('/', function () {
    return view('home');
})->name('home');

// Route publik validasi dokumen dibuka tanpa middleware auth agar QR pada PDF bisa dipindai dari HP/laptop siapa pun.
// Token pada URL dicocokkan dengan kolom verification_token dokumen yang sudah PUBLISHED.
Route::get('/verifikasi/{token}', [PublicDokumenVerificationController::class, 'show'])
    ->name('verifikasi.public');

// Route guest hanya boleh diakses sebelum user login.
// Middleware guest adalah kebalikan dari auth: user yang sudah login akan diarahkan keluar dari halaman login.
Route::middleware('guest')->group(function () {
    // GET menampilkan form login, sedangkan POST memproses kredensial dari form tersebut.
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Route auth umum dipakai bersama oleh semua role setelah login berhasil.
// Middleware auth memastikan request memiliki session login sebelum boleh masuk ke route di dalam group ini.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Satu route dashboard umum ini bertugas meneruskan user ke dashboard sesuai role masing-masing.
        // Redirect berbasis route name membuat URL tujuan tetap mudah diganti tanpa mengubah logic controller.
        return redirect()->route(match ($user->role) {
            'SUPER_ADMIN' => 'super-admin.dashboard',
            'ADMIN_TU' => 'admin.dashboard',
            'PEMOHON' => 'pemohon.dashboard',
            'VERIFIKATOR' => 'verifikator.dashboard',
            default => 'home',
        });
    })->name('dashboard');
});

// Area Admin/TU untuk memproses pengajuan, mengatur verifikasi, dan melakukan publish dokumen.
// prefix('admin') membuat semua URL diawali /admin, name('admin.') membuat nama route diawali admin.
// Middleware role:ADMIN_TU membatasi seluruh route di group ini hanya untuk user role Admin/TU.
Route::middleware(['auth', 'role:ADMIN_TU'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Route pengajuan masuk dipakai Admin/TU untuk mengambil dokumen yang baru dikirim pemohon.
    Route::get('/pengajuan-masuk', [AdminSuratMasukController::class, 'index'])->name('pengajuan-masuk');
    Route::get('/pengajuan-masuk/{dokumen}/download-draft', [AdminSuratMasukController::class, 'downloadDraft'])->name('pengajuan-masuk.download-docx');

    // Route semua surat menampilkan arsip surat biasa, preview final, download final, dan aksi publish.
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

    // Route proses surat adalah wizard Admin/TU: simpan draft PDF, atur posisi elemen, lalu kirim ke verifikator.
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

// Area Pemohon untuk membuat pengajuan dan memantau status dokumen miliknya sendiri.
// Middleware role:PEMOHON menjaga agar pemohon hanya melihat fitur pengajuan dan dokumen miliknya.
Route::middleware(['auth', 'role:PEMOHON'])->prefix('pemohon')->name('pemohon.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pemohon'])->name('dashboard');

    // Route buat-surat memisahkan tampilan form (GET) dan proses penyimpanan pengajuan (POST).
    Route::get('/buat-surat', [PemohonSuratController::class, 'create'])->name('buat-surat');
    Route::post('/buat-surat', [PemohonSuratController::class, 'store'])->name('surat.store');

    Route::get('/buat-sk', function () {
        return view('pemohon.buat-sk');
    })->name('buat-sk');

    // Route surat-saya dan download membatasi akses dokumen pada pemohon yang sedang login melalui controller.
    Route::get('/surat-saya', [PemohonSuratController::class, 'index'])->name('surat-saya');
    Route::get('/surat/{dokumen}/download', [PemohonSuratController::class, 'download'])->name('surat.download');

    Route::get('/sk-saya', function () {
        return view('pemohon.sk-saya');
    })->name('sk-saya');

    Route::get('/profil', function () {
        return view('pemohon.profil');
    })->name('profil');
});

// Area Verifikator untuk memeriksa dokumen, memberi keputusan, dan melihat PDF preview hasil proses Admin/TU.
// Middleware role:VERIFIKATOR membatasi halaman pemeriksaan hanya untuk user yang ditugaskan sebagai verifikator.
Route::middleware(['auth', 'role:VERIFIKATOR'])->prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'verifikator'])->name('dashboard');

    // Route surat biasa untuk daftar menunggu, detail, arsip keputusan, preview/unduh PDF, dan proses approve/reject.
    Route::get('/surat-menunggu', [VerifikatorSuratController::class, 'menunggu'])->name('surat-menunggu');
    Route::get('/surat/{dokumen}/detail', [VerifikatorSuratController::class, 'detailSurat'])->name('surat.detail');
    Route::get('/surat-disetujui', [VerifikatorSuratController::class, 'disetujui'])->name('surat-disetujui');
    Route::get('/surat-ditolak', [VerifikatorSuratController::class, 'ditolak'])->name('surat-ditolak');
    Route::get('/surat/{dokumen}/preview-pdf', [VerifikatorSuratController::class, 'previewPdf'])->name('surat.preview-pdf');
    Route::get('/surat/{dokumen}/unduh-pdf', [VerifikatorSuratController::class, 'downloadPdf'])->name('surat.unduh-pdf');
    Route::post('/verifikasi/{verifikasi}/proses', [VerifikatorSuratController::class, 'proses'])->name('verifikasi.proses');

    Route::get('/surat-semua', [VerifikatorSuratController::class, 'semua'])->name('surat-semua');

    // Route SK disiapkan terpisah dari surat biasa karena struktur detail dan view-nya berbeda.
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

// Area Super Admin difokuskan untuk pengelolaan user dan monitoring ringkasan sistem.
// Middleware role:SUPER_ADMIN memisahkan fitur administrasi user dari role operasional lain.
Route::middleware(['auth', 'role:SUPER_ADMIN'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');

    // controller(UserController::class) membuat route di dalam group ini otomatis memanggil method UserController.
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::patch('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
    });

    // Route lama/alias diarahkan ke route resource-like agar link menu tetap kompatibel.
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
