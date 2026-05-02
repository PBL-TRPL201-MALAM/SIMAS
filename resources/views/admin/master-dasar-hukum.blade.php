@include('template.header', ['pageTitle' => 'Master Dasar Hukum'])
@include('template.admin-sidebar')

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

      <!-- Topbar -->
      <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-slate-100/80 shrink-0">
        <button id="sidebar-toggle" type="button" class="xl:hidden -m-2 p-2 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-all duration-200 mr-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <div>
          <h1 class="text-sm font-bold text-slate-900">Master Dasar Hukum</h1>
          <p class="text-[11px] text-slate-400 font-light">Kelola referensi dasar hukum</p>
        </div>
        <button type="button"
          class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-4">

          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Master Dasar Hukum</h2>
            <button id="btn-tambah-dasar" type="button"
              class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
              Tambah Dasar Hukum
            </button>
          </div>

          <!-- Form tambah (toggle via JS) -->
          <div id="form-tambah-dasar" class="hidden rounded-2xl bg-white border border-blue-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/30">
              <h3 class="text-sm font-bold text-slate-900">Tambah Dasar Hukum Baru</h3>
            </div>
            <div class="px-6 py-5 space-y-4">
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Nama Peraturan <span class="text-blue-400">*</span></label>
                <input type="text" id="input-nama-dasar" placeholder="Contoh: UU No. 20 Tahun 2003"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
              </div>
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 tracking-wide">Tentang <span class="text-blue-400">*</span></label>
                <input type="text" id="input-tentang-dasar" placeholder="Contoh: tentang Sistem Pendidikan Nasional"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
              </div>
              <div class="flex items-center gap-3">
                <button id="btn-simpan-dasar" type="button"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                  Simpan
                </button>
                <button id="btn-batal-dasar" type="button"
                  class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-all duration-200">
                  Batal
                </button>
              </div>
            </div>
          </div>

          <!-- Tabel dasar hukum -->
          <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">No</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Nama Peraturan</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Tentang</th>
                    <th class="text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider px-5 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-dasar-hukum" class="divide-y divide-slate-50">

                  @foreach($dasarHukumList ?? [] as $index => $item)
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">{{ $index + 1 }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">{{ $item->nama_peraturan }}</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">{{ $item->tentang }}</p></td>
                    <td class="px-5 py-3.5">
                      <button type="button"
                        class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200"
                        data-id="{{ $item->id }}">Hapus</button>
                    </td>
                  </tr>
                  @endforeach

                  <!-- Fallback data statis -->
                  @if(empty($dasarHukumList) || count($dasarHukumList) === 0)
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">1</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">UU No. 20 Tahun 2003</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">tentang Sistem Pendidikan Nasional</p></td>
                    <td class="px-5 py-3.5"><button type="button" class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">2</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">PP No. 4 Tahun 2014</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">tentang Penyelenggaraan Pendidikan Tinggi</p></td>
                    <td class="px-5 py-3.5"><button type="button" class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button></td>
                  </tr>
                  <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-400 font-light">3</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-800">Peraturan Direktur Polibatam No. 01 Tahun 2023</p></td>
                    <td class="px-5 py-3.5"><p class="text-xs text-slate-500 font-light">tentang Tata Kelola Administrasi Polibatam</p></td>
                    <td class="px-5 py-3.5"><button type="button" class="text-[11px] font-medium text-slate-400 hover:text-red-500 transition-colors duration-200">Hapus</button></td>
                  </tr>
                  @endif

                </tbody>
              </table>
            </div>
          </div>

        </div>
      </main>
    </div>

@include('template.footer')

