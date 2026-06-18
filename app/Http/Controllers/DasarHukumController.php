<?php

namespace App\Http\Controllers;

use App\Models\DasarHukum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Controller ini mengelola master dasar hukum yang dipakai sebagai referensi bagian Mengingat pada pengajuan SK.
// Aksesnya diberikan lewat route Admin Surat dan Super Admin, sehingga view tidak perlu data contoh statis.
class DasarHukumController extends Controller
{
    // Halaman master untuk Admin Surat.
    public function adminIndex(Request $request): View
    {
        return $this->renderIndex($request, 'admin.master-dasar-hukum', 'admin');
    }

    // Halaman master untuk Super Admin.
    public function superAdminIndex(Request $request): View
    {
        return $this->renderIndex($request, 'super-admin.dasar-hukum', 'super-admin');
    }

    // Method ini mengambil data asli dari tabel dasar_hukum dan menyiapkan item yang sedang diedit jika ada query ?edit=.
    private function renderIndex(Request $request, string $viewName, string $routeContext): View
    {
        $dasarHukumList = DasarHukum::query()
            ->orderByDesc('is_active')
            ->orderBy('judul_hukum')
            ->get();

        $editDasarHukum = $request->filled('edit')
            ? DasarHukum::query()->find($request->integer('edit'))
            : null;

        return view($viewName, [
            'dasarHukumList' => $dasarHukumList,
            'editDasarHukum' => $editDasarHukum,
            'routeContext' => $routeContext,
        ]);
    }

    // Method ini menyimpan dasar hukum baru dari form master ke tabel dasar_hukum.
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateDasarHukum($request);

        DasarHukum::query()->create($validated);

        return back()->with('status', 'Dasar hukum berhasil ditambahkan.');
    }

    // Method ini memperbarui dasar hukum tanpa mengubah relasi SK yang mungkin sudah memakai referensi tersebut.
    public function update(Request $request, DasarHukum $dasarHukum): RedirectResponse
    {
        $validated = $this->validateDasarHukum($request);

        $dasarHukum->update($validated);

        $redirectTo = $request->input('redirect_to', url()->previous());

        // Validasi agar terhindar dari Open Redirect (hanya izinkan path relatif atau domain lokal)
        if (! \Illuminate\Support\Str::startsWith($redirectTo, '/') && ! \Illuminate\Support\Str::startsWith($redirectTo, config('app.url'))) {
            $redirectTo = route('admin.master-dasar-hukum');
        }

        return redirect()
            ->to($redirectTo)
            ->with('status', 'Dasar hukum berhasil diperbarui.');
    }

    // Method ini menjadi pengganti hapus permanen: data tetap ada, tetapi tidak tampil sebagai pilihan aktif pada form SK.
    public function toggleStatus(DasarHukum $dasarHukum): RedirectResponse
    {
        $dasarHukum->update([
            'is_active' => ! $dasarHukum->is_active,
        ]);

        return back()->with(
            'status',
            $dasarHukum->is_active
                ? 'Dasar hukum berhasil diaktifkan.'
                : 'Dasar hukum berhasil dinonaktifkan.'
        );
    }

    // Validasi dipusatkan agar aturan tambah dan edit selalu sama.
    private function validateDasarHukum(Request $request): array
    {
        return $request->validate([
            'judul_hukum' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ], [
            'judul_hukum.required' => 'Judul hukum wajib diisi.',
            'is_active.required' => 'Status dasar hukum wajib dipilih.',
        ]);
    }
}
