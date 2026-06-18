@include('template.layouts.header', ['pageTitle' => 'Role & Akses'])
@include('template.sidebar.super-admin')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Role &amp; Akses</h1>
          <p class="text-[11px] text-slate-400 font-light">Ringkasan hak akses per role utama</p>
        </div>
        <a href="{{ route('super-admin.semua-user') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800 transition-all duration-200">Lihat User</a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
              <h2 class="text-sm font-semibold text-slate-800">Matriks Role</h2>
              <p class="text-[11px] text-slate-400 font-light mt-0.5">Acuan cepat untuk pengaturan akses di backend nanti.</p>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Role</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Akses Utama</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Keterangan</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs">
                  <tr>
                    <td class="px-5 py-4 font-semibold text-slate-800">SUPER_ADMIN</td>
                    <td class="px-5 py-4 text-slate-600">Kelola user, role, monitoring, pengaturan</td>
                    <td class="px-5 py-4 text-slate-500">Akses penuh tingkat sistem</td>
                  </tr>
                  <tr>
                    <td class="px-5 py-4 font-semibold text-slate-800">ADMIN_SURAT</td>
                    <td class="px-5 py-4 text-slate-600">Proses surat, SK, master dasar hukum</td>
                    <td class="px-5 py-4 text-slate-500">Operasional dokumen</td>
                  </tr>
                  <tr>
                    <td class="px-5 py-4 font-semibold text-slate-800">VERIFIKATOR</td>
                    <td class="px-5 py-4 text-slate-600">Verifikasi, setujui, tolak dokumen</td>
                    <td class="px-5 py-4 text-slate-500">Validasi alur persetujuan</td>
                  </tr>
                  <tr>
                    <td class="px-5 py-4 font-semibold text-slate-800">PENANDATANGAN</td>
                    <td class="px-5 py-4 text-slate-600">Verifikasi final, tanda tangan dokumen</td>
                    <td class="px-5 py-4 text-slate-500">Otorisasi akhir & pengesahan</td>
                  </tr>
                  <tr>
                    <td class="px-5 py-4 font-semibold text-slate-800">PEMOHON</td>
                    <td class="px-5 py-4 text-slate-600">Buat surat, buat SK, pantau status</td>
                    <td class="px-5 py-4 text-slate-500">Pengguna pengajuan</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-100 p-5">
              <h3 class="text-sm font-semibold text-slate-800">Hak Akses Prioritas</h3>
              <div class="mt-4 space-y-3">
                <div class="rounded-xl border border-slate-100 p-4">
                  <p class="text-xs font-semibold text-slate-700">SUPER_ADMIN</p>
                  <p class="text-xs text-slate-500 font-light mt-1">Memiliki kontrol penuh atas sistem. Bertanggung jawab menambah, mengedit, dan mengelola status akun seluruh user. Dapat memantau seluruh dokumen dan aktivitas di dalam sistem.</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                  <p class="text-xs font-semibold text-slate-700">ADMIN_SURAT</p>
                  <p class="text-xs text-slate-500 font-light mt-1">Bertugas memproses pengajuan surat dan SK dari pemohon. Melengkapi metadata resmi dokumen, mengatur posisi elemen pada PDF, memilih jalur verifikasi, dan melakukan publish dokumen final.</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                  <p class="text-xs font-semibold text-slate-700">PEMOHON</p>
                  <p class="text-xs text-slate-500 font-light mt-1">Membuat pengajuan surat biasa dan surat keputusan melalui form pengajuan. Dapat memantau status dokumen, melakukan revisi jika diminta, serta mengunduh dokumen yang sudah dipublish.</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                  <p class="text-xs font-semibold text-slate-700">VERIFIKATOR</p>
                  <p class="text-xs text-slate-500 font-light mt-1">Memeriksa dan memvalidasi dokumen pada tahap verifikasi bertingkat. Dapat menyetujui atau menolak dokumen disertai catatan, sesuai level verifikasi yang ditugaskan oleh Admin Surat.</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                  <p class="text-xs font-semibold text-slate-700">PENANDATANGAN</p>
                  <p class="text-xs text-slate-500 font-light mt-1">Pejabat berwenang yang melakukan pengesahan akhir dokumen. Bertindak sebagai level verifikasi terakhir dalam alur persetujuan. Hanya user dengan jabatan pejabat tertentu (Direktur, Wakil Direktur, Kepala Jurusan) yang dapat memegang role ini.</p>
                </div>
              </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-5">
              <h3 class="text-sm font-semibold text-slate-800">Alur perubahan role</h3>
              <ol class="mt-4 space-y-2 text-xs text-slate-500 font-light list-decimal pl-4">
                <li>Pilih user dari halaman semua user.</li>
                <li>Ubah role sesuai kebutuhan operasional.</li>
                <li>Simpan perubahan dan catat alasan perubahan.</li>
              </ol>
            </div>
          </div>
        </div>
      </main>
    </div>

@include('template.layouts.footer')

