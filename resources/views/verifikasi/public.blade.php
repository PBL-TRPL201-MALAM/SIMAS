<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Validasi Dokumen - SIMAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      * { font-family: 'Poppins', sans-serif; }
    </style>
  </head>
  <body class="min-h-screen bg-slate-50 antialiased">
    <!-- Halaman ini dibuka dari QR publik dan tidak membutuhkan session login. -->
    <main class="min-h-screen px-5 py-10">
      <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center gap-3">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SIMAS" class="h-9 w-auto object-contain" />
          <div>
            <p class="text-sm font-bold text-slate-900">SIMAS</p>
            <p class="text-[11px] font-light text-slate-400">Validasi Dokumen Publik</p>
          </div>
        </div>

        @if ($isValid && $dokumen)
          <!-- Data dokumen valid berasal dari token QR yang cocok dengan dokumen berstatus PUBLISHED. -->
          <section class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">
            <div class="border-b border-emerald-100 bg-emerald-50 px-6 py-5">
              <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Dokumen Valid</p>
              <h1 class="mt-1 text-xl font-bold text-slate-900">Dokumen ini terdaftar dan sudah dipublish di SIMAS.</h1>
            </div>

            <div class="divide-y divide-slate-100 px-6">
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Surat</p>
                <p class="text-sm font-medium text-slate-800">{{ $dokumen->suratBiasa?->nomor_surat ?: '-' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Surat</p>
                <p class="text-sm font-medium text-slate-800">{{ optional($dokumen->suratBiasa?->tanggal_surat)->translatedFormat('d F Y') ?: '-' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jenis Surat</p>
                <p class="text-sm font-medium text-slate-800">{{ $dokumen->suratBiasa?->jenis_surat ?: 'Surat Biasa' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Perihal</p>
                <p class="text-sm font-medium text-slate-800">{{ $dokumen->suratBiasa?->hal ?: '-' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pemohon</p>
                <p class="text-sm font-medium text-slate-800">{{ $dokumen->pemohon?->nama ?: '-' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Penandatangan</p>
                <p class="text-sm font-medium text-slate-800">{{ $dokumen->suratBiasa?->penandatangan ?: '-' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Terverifikasi oleh</p>
                <p class="text-sm font-medium text-slate-800">{{ $verifiedBy->isNotEmpty() ? $verifiedBy->implode(', ') : '-' }}</p>
              </div>
              <div class="grid gap-1 py-4 sm:grid-cols-[180px,1fr]">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Publish</p>
                <p class="text-sm font-medium text-slate-800">{{ optional($dokumen->published_at)->translatedFormat('d F Y H:i') ?: '-' }}</p>
              </div>
            </div>
          </section>
        @else
          <!-- Pesan invalid sengaja dibuat umum agar token yang salah atau dokumen belum published tidak membocorkan data. -->
          <section class="rounded-2xl border border-red-100 bg-white px-6 py-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50">
              <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
              </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Dokumen tidak valid atau belum dipublish.</h1>
            <p class="mt-2 text-sm font-light text-slate-500">Pastikan QR berasal dari PDF final SIMAS yang sudah resmi diterbitkan.</p>
          </section>
        @endif
      </div>
    </main>
  </body>
</html>
