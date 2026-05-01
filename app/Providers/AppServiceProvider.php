<?php

namespace App\Providers;

use App\Models\Dokumen;
use App\Models\User;
use App\Models\Verifikasi;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('template.pemohon-sidebar', function ($view): void {
            $user = Auth::user();

            if (! $user || $user->role !== 'PEMOHON') {
                $view->with('sidebarStats', ['surat_count' => 0, 'sk_count' => 0]);
                return;
            }

            // Badge sidebar pemohon selalu dihitung real-time dari database agar jumlah surat dan SK tidak hardcode.
            $view->with('sidebarStats', [
                'surat_count' => Dokumen::query()->where('pemohon_id', $user->user_id)->where('jenis_dokumen', 'SURAT_BIASA')->count(),
                'sk_count' => Dokumen::query()->where('pemohon_id', $user->user_id)->where('jenis_dokumen', 'SURAT_KEPUTUSAN')->count(),
            ]);
        });

        View::composer('template.admin-sidebar', function ($view): void {
            // Sidebar Admin/TU menampilkan jumlah pengajuan yang benar-benar masih menunggu diproses dari masing-masing jenis dokumen.
            $view->with('sidebarStats', [
                'pengajuan_surat_count' => Dokumen::query()->where('jenis_dokumen', 'SURAT_BIASA')->where('status_dokumen', 'DIAJUKAN')->count(),
                'pengajuan_sk_count' => Dokumen::query()->where('jenis_dokumen', 'SURAT_KEPUTUSAN')->where('status_dokumen', 'DIAJUKAN')->count(),
            ]);
        });

        View::composer('template.verifikator-sidebar', function ($view): void {
            $user = Auth::user();

            if (! $user || $user->role !== 'VERIFIKATOR') {
                $view->with('sidebarStats', ['surat_menunggu_count' => 0, 'sk_menunggu_count' => 0]);
                return;
            }

            $baseQuery = Verifikasi::query()
                ->where('verifikator_id', $user->user_id)
                ->where('status_verifikasi', 'MENUNGGU')
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('verifikasi as previous_levels')
                        ->whereColumn('previous_levels.dokumen_id', 'verifikasi.dokumen_id')
                        ->whereColumn('previous_levels.level', '<', 'verifikasi.level')
                        ->where('previous_levels.status_verifikasi', '!=', 'DISETUJUI');
                });

            // Badge verifikator hanya menghitung level yang sudah terbuka untuk diproses, bukan semua record verifikasi.
            $view->with('sidebarStats', [
                'surat_menunggu_count' => (clone $baseQuery)->whereHas('dokumen', fn (Builder $query) => $query
                    ->where('jenis_dokumen', 'SURAT_BIASA')
                    ->where('status_dokumen', 'MENUNGGU_VERIFIKASI'))->count(),
                'sk_menunggu_count' => (clone $baseQuery)->whereHas('dokumen', fn (Builder $query) => $query
                    ->where('jenis_dokumen', 'SURAT_KEPUTUSAN')
                    ->where('status_dokumen', 'MENUNGGU_VERIFIKASI'))->count(),
            ]);
        });

        View::composer('template.super-admin-sidebar', function ($view): void {
            // Sidebar Super Admin cukup memakai total user sebagai ringkasan cepat kondisi sistem.
            $view->with('sidebarStats', [
                'user_count' => User::query()->count(),
            ]);
        });
    }
}
