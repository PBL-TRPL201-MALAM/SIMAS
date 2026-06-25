<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\RiwayatDokumen;
use App\Models\User;
use App\Models\Verifikasi;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

// Controller ini menyiapkan data ringkasan untuk dashboard tiap role.
// Polanya mirip controller di CodeIgniter: ambil data lewat model, susun statistik, lalu kirim ke view.
class DashboardController extends Controller
{
    // Method ini menampilkan dashboard Pemohon berdasarkan dokumen milik user yang sedang login.
    public function pemohon(Request $request): View
    {
        $userId = $request->user()->user_id;

        // Semua kartu statistik pemohon dibangun dari query dasar yang sama agar hanya menghitung dokumen milik user login.
        $baseQuery = Dokumen::query()
            ->where('pemohon_id', $userId);

        // Query ini mengambil 5 dokumen terbaru beserta relasi detailnya agar view tidak melakukan query tambahan.
        $latestDocuments = (clone $baseQuery)
            ->with(['suratBiasa', 'suratKeputusan'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Riwayat aktivitas difilter lewat whereHas agar hanya aktivitas dari dokumen milik pemohon ini yang muncul.
        $recentActivities = RiwayatDokumen::query()
            ->with(['actor', 'dokumen.suratBiasa', 'dokumen.suratKeputusan'])
            ->whereHas('dokumen', fn (Builder $query) => $query->where('pemohon_id', $userId))
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Data chart: distribusi status dokumen milik pemohon untuk Doughnut Chart.
        $statusCounts = (clone $baseQuery)
            ->selectRaw('status_dokumen, COUNT(*) as total')
            ->groupBy('status_dokumen')
            ->pluck('total', 'status_dokumen');

        $chartStatusDokumen = [
            'labels' => ['Diajukan', 'Diproses', 'Revisi / Ditolak', 'Published'],
            'data' => [
                (int) ($statusCounts['DIAJUKAN'] ?? 0),
                (int) (($statusCounts['DIPROSES'] ?? 0) + ($statusCounts['MENUNGGU_VERIFIKASI'] ?? 0) + ($statusCounts['SIAP_PUBLISH'] ?? 0)),
                (int) (($statusCounts['DITOLAK'] ?? 0) + ($statusCounts['PERLU_REVISI'] ?? 0)),
                (int) (($statusCounts['PUBLISHED'] ?? 0) + ($statusCounts['DISETUJUI'] ?? 0)),
            ],
        ];

        return view('pemohon.dashboard', [
            'stats' => [
                'total_dokumen' => (clone $baseQuery)->count(),
                'sedang_diproses' => (clone $baseQuery)->whereIn('status_dokumen', ['DIPROSES', 'MENUNGGU_VERIFIKASI', 'SIAP_PUBLISH'])->count(),
                'disetujui' => (clone $baseQuery)->whereIn('status_dokumen', ['PUBLISHED', 'DISETUJUI'])->count(),
                'ditolak_revisi' => (clone $baseQuery)->whereIn('status_dokumen', ['DITOLAK', 'PERLU_REVISI'])->count(),
            ],
            'latestDocuments' => $latestDocuments,
            'recentActivities' => $recentActivities,
            'chartStatusDokumen' => $chartStatusDokumen,
        ]);
    }

    // Method ini menampilkan dashboard Admin Surat untuk memantau antrean pengajuan dan status dokumen.
    public function admin(): View
    {
        // Dashboard Admin Surat menonjolkan antrean kerja paling depan, yaitu dokumen yang baru diajukan pemohon.
        $incomingQuery = Dokumen::query()
            ->where('status_dokumen', 'DIAJUKAN');

        // Eager loading dipakai agar data pemohon dan jenis surat sudah siap saat kartu daftar terbaru dirender.
        $latestIncoming = (clone $incomingQuery)
            ->with(['pemohon', 'suratBiasa', 'suratKeputusan'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Data chart: tren pengajuan per bulan dalam tahun berjalan untuk Bar Chart.
        $currentYear = now()->year;
        $monthlyTrend = Dokumen::query()
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan');

        $chartTrenBulanan = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => collect(range(1, 12))->map(fn ($m) => (int) ($monthlyTrend[$m] ?? 0))->values()->toArray(),
        ];

        // Data chart: perbandingan Surat Biasa vs Surat Keputusan untuk Doughnut Chart.
        $jenisCounts = Dokumen::query()
            ->selectRaw('jenis_dokumen, COUNT(*) as total')
            ->groupBy('jenis_dokumen')
            ->pluck('total', 'jenis_dokumen');

        $chartJenisSurat = [
            'labels' => ['Surat Biasa', 'Surat Keputusan'],
            'data' => [
                (int) ($jenisCounts['SURAT_BIASA'] ?? 0),
                (int) ($jenisCounts['SURAT_KEPUTUSAN'] ?? 0),
            ],
        ];

        return view('admin.dashboard', [
            'stats' => [
                'pengajuan_masuk' => (clone $incomingQuery)->count(),
                'sedang_diproses' => Dokumen::query()->whereIn('status_dokumen', ['DIPROSES', 'MENUNGGU_VERIFIKASI', 'SIAP_PUBLISH'])->count(),
                'published' => Dokumen::query()->where('status_dokumen', 'PUBLISHED')->count(),
                'total_dokumen' => Dokumen::query()->count(),
            ],
            'latestIncoming' => $latestIncoming,
            'chartTrenBulanan' => $chartTrenBulanan,
            'chartJenisSurat' => $chartJenisSurat,
        ]);
    }

    // Method ini menampilkan dashboard Verifikator berdasarkan level verifikasi yang sudah boleh diproses.
    public function verifikator(Request $request): View
    {
        $userId = $request->user()->user_id;

        // Dokumen yang tampil di dashboard verifikator hanya level yang benar-benar sudah aktif untuk diproses user login.
        $pendingQuery = $this->processableVerificationQuery($userId)
            ->where('status_verifikasi', 'MENUNGGU')
            ->whereHas('dokumen', fn (Builder $query) => $query->where('status_dokumen', 'MENUNGGU_VERIFIKASI'));

        // Dokumen pending terbaru ikut membawa relasi pemohon dan isi surat agar tabel dashboard tidak perlu query ulang.
        $latestPending = (clone $pendingQuery)
            ->with([
                'dokumen.pemohon',
                'dokumen.suratBiasa',
                'dokumen.suratKeputusan',
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Data chart: distribusi status verifikasi milik verifikator login untuk Doughnut Chart.
        $verifikasiCounts = Verifikasi::query()
            ->where('verifikator_id', $userId)
            ->selectRaw('status_verifikasi, COUNT(*) as total')
            ->groupBy('status_verifikasi')
            ->pluck('total', 'status_verifikasi');

        $chartVerifikasi = [
            'labels' => ['Menunggu', 'Disetujui', 'Ditolak'],
            'data' => [
                (int) ($verifikasiCounts['MENUNGGU'] ?? 0),
                (int) ($verifikasiCounts['DISETUJUI'] ?? 0),
                (int) ($verifikasiCounts['DITOLAK'] ?? 0),
            ],
        ];

        return view('verifikator.dashboard', [
            'stats' => [
                'menunggu' => (clone $pendingQuery)->count(),
                'disetujui' => Verifikasi::query()->where('verifikator_id', $userId)->where('status_verifikasi', 'DISETUJUI')->count(),
                'ditolak' => Verifikasi::query()->where('verifikator_id', $userId)->where('status_verifikasi', 'DITOLAK')->count(),
                'total_diverifikasi' => Verifikasi::query()->where('verifikator_id', $userId)->whereIn('status_verifikasi', ['DISETUJUI', 'DITOLAK'])->count(),
            ],
            'latestPending' => $latestPending,
            'chartVerifikasi' => $chartVerifikasi,
        ]);
    }

    // Method ini menampilkan dashboard Super Admin untuk melihat statistik global user dan dokumen.
    public function superAdmin(): View
    {
        // Distribusi role dipakai untuk memberi gambaran cepat komposisi user SIMAS pada dashboard Super Admin.
        $userCounts = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $roleDistribution = collect([
            'PEMOHON' => 'Pemohon',
            'ADMIN_SURAT' => 'Admin Surat',
            'VERIFIKATOR' => 'Verifikator',
            'PENANDATANGAN' => 'Penandatangan',
            'SUPER_ADMIN' => 'Super Admin',
        ])->map(function (string $label, string $role) use ($userCounts) {
            $total = (int) ($userCounts[$role] ?? 0);
            $allUsers = max(1, (int) $userCounts->sum());

            return [
                'label' => $label,
                'total' => $total,
                'percentage' => $allUsers > 0 ? (int) round(($total / $allUsers) * 100) : 0,
            ];
        })->values();

        // Aktivitas terakhir diambil lintas dokumen sebagai log ringkas aktivitas sistem.
        $recentActivities = RiwayatDokumen::query()
            ->with(['actor', 'dokumen.suratBiasa', 'dokumen.suratKeputusan'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Data chart: tren dokumen per bulan dalam tahun berjalan untuk Bar Chart Super Admin.
        $currentYear = now()->year;
        $monthlyDocs = Dokumen::query()
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan');

        $chartTrenDokumen = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => collect(range(1, 12))->map(fn ($m) => (int) ($monthlyDocs[$m] ?? 0))->values()->toArray(),
        ];

        return view('super-admin.dashboard', [
            'stats' => [
                'total_user' => User::query()->count(),
                'total_dokumen' => Dokumen::query()->count(),
                'published' => Dokumen::query()->where('status_dokumen', 'PUBLISHED')->count(),
                'pending' => Dokumen::query()->whereIn('status_dokumen', ['DIAJUKAN', 'DIPROSES', 'MENUNGGU_VERIFIKASI', 'SIAP_PUBLISH', 'PERLU_REVISI'])->count(),
            ],
            'roleDistribution' => $roleDistribution,
            'recentActivities' => $recentActivities,
            'chartTrenDokumen' => $chartTrenDokumen,
        ]);
    }

    // Helper query ini dipakai untuk memastikan verifikator hanya melihat level yang sudah aktif.
    protected function processableVerificationQuery(int $userId): Builder
    {
        // Level verifikasi baru dianggap aktif jika seluruh level sebelumnya pada dokumen yang sama sudah disetujui.
        return Verifikasi::query()
            ->where('verifikator_id', $userId)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('verifikasi as previous_levels')
                    ->whereColumn('previous_levels.dokumen_id', 'verifikasi.dokumen_id')
                    ->whereColumn('previous_levels.level', '<', 'verifikasi.level')
                    ->where('previous_levels.status_verifikasi', '!=', 'DISETUJUI');
            });
    }
}
