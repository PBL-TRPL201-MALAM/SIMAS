@php
  $routeNamePrefix = $routeContext === 'admin' ? 'admin.dasar-hukum' : 'super-admin.dasar-hukum';
  $indexRouteName = $routeContext === 'admin' ? 'admin.master-dasar-hukum' : 'super-admin.dasar-hukum';
  $profileRouteName = $routeContext . '.profil';
  $showCreateForm = $errors->any() && ! $editDasarHukum;
@endphp

    {{-- View ini hanya memakai data dari tabel dasar_hukum; tidak ada fallback data contoh statis. --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Master Dasar Hukum</h1>
          <p class="text-[11px] text-slate-400 font-light">Kelola referensi dasar hukum untuk bagian Mengingat pada SK.</p>
        </div>
        <a href="{{ route($profileRouteName) }}"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </a>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-4">
          @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
              {{ session('status') }}
            </div>
          @endif

          @if ($errors->any())
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3">
              <p class="text-xs font-semibold text-red-700">Data dasar hukum belum bisa disimpan:</p>
              <ul class="mt-1 space-y-1 text-[11px] text-red-600 font-light">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900">Daftar Dasar Hukum</h2>
              <p class="text-[11px] text-slate-400 font-light mt-0.5">Data aktif akan muncul pada form Buat Pengajuan SK.</p>
            </div>
            <button id="btn-tambah-dasar" type="button"
              class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
              Tambah Dasar Hukum
            </button>
          </div>

          <div id="form-tambah-dasar" class="{{ $showCreateForm ? '' : 'hidden' }} rounded-2xl bg-white border border-blue-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h3 class="text-sm font-bold text-slate-900">Tambah Dasar Hukum Baru</h3>
              <p class="text-xs text-slate-400 font-light mt-0.5">Simpan judul hukum dan keterangan ke tabel dasar_hukum.</p>
            </div>
            <form method="POST" action="{{ route($routeNamePrefix . '.store') }}" class="px-6 py-5 space-y-4">
              @csrf
              {{-- Field berikut sengaja disederhanakan agar master SK tidak memecah jenis, nomor, dan tahun peraturan. --}}
              @include('dasar-hukum.partials.form-fields', ['item' => null])
              <div class="flex items-center gap-3">
                <button type="submit"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                  Simpan
                </button>
                <button id="btn-batal-dasar" type="button"
                  class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  Batal
                </button>
              </div>
            </form>
          </div>

          @if ($editDasarHukum)
            <div class="rounded-2xl bg-white border border-amber-100 overflow-hidden">
              <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/50">
                <h3 class="text-sm font-bold text-slate-900">Edit Dasar Hukum</h3>
                <p class="text-xs text-slate-400 font-light mt-0.5">Perubahan ini akan dipakai untuk pilihan SK berikutnya.</p>
              </div>
              <form method="POST" action="{{ route($routeNamePrefix . '.update', $editDasarHukum) }}" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_to" value="{{ route($indexRouteName) }}">
                @include('dasar-hukum.partials.form-fields', ['item' => $editDasarHukum])
                <div class="flex items-center gap-3">
                  <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                    Simpan Perubahan
                  </button>
                  <a href="{{ route($indexRouteName) }}"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                    Batal Edit
                  </a>
                </div>
              </form>
            </div>
          @endif

          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Judul Hukum</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Keterangan</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  @forelse($dasarHukumList as $item)
                    <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                      <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800 max-w-[280px]">{{ $item->judul_hukum }}</p></td>
                      <td class="px-5 py-3.5"><p class="text-xs text-slate-600 max-w-[420px]">{{ $item->keterangan ?: '-' }}</p></td>
                      <td class="px-5 py-3.5">
                        @if ($item->is_active)
                          <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-emerald-500"></span>Aktif</span>
                        @else
                          <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full"><span class="w-1 h-1 rounded-full bg-slate-400"></span>Nonaktif</span>
                        @endif
                      </td>
                      <td class="px-5 py-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                          <a href="{{ route($indexRouteName, ['edit' => $item->dasar_hukum_id]) }}"
                            class="inline-flex items-center text-[11px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-all duration-200">
                            Edit
                          </a>
                          <form method="POST" action="{{ route($routeNamePrefix . '.toggle-status', $item) }}">
                            @csrf
                            @method('PATCH')
                            {{-- Tombol ini adalah soft delete fungsional: menonaktifkan data tanpa menghapus permanen. --}}
                            <button type="submit"
                              class="inline-flex items-center text-[11px] font-semibold {{ $item->is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100' }} px-2.5 py-1 rounded-lg transition-all duration-200">
                              {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-600">Belum ada dasar hukum.</p>
                        <p class="mt-1 text-[11px] font-light text-slate-400">Tambahkan data pertama agar pilihan Mengingat pada SK tersedia.</p>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </main>
    </div>
