@php
  $isActiveValue = old('is_active', $item?->is_active ?? true) ? '1' : '0';
@endphp

<div class="grid gap-4 md:grid-cols-3">
  <div class="space-y-1.5 md:col-span-2">
    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Judul Hukum <span class="text-blue-400">*</span></label>
    <input type="text" name="judul_hukum" value="{{ old('judul_hukum', $item?->judul_hukum) }}" placeholder="Contoh: Undang-Undang Nomor 20 Tahun 2003"
      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" />
  </div>
  <div class="space-y-1.5">
    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Status <span class="text-blue-400">*</span></label>
    {{-- Status aktif menentukan apakah dasar hukum muncul pada bagian Mengingat di form pengajuan SK. --}}
    <select name="is_active"
      class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
      <option value="1" @selected($isActiveValue === '1')>Aktif</option>
      <option value="0" @selected($isActiveValue === '0')>Nonaktif</option>
    </select>
  </div>
</div>

<div class="space-y-1.5">
  <label class="block text-xs font-semibold text-slate-700 tracking-wide">Keterangan</label>
  {{-- Keterangan melengkapi Judul Hukum dan akan digabung sebagai label pilihan Mengingat. --}}
  <textarea name="keterangan" rows="3" placeholder="Contoh: tentang Sistem Pendidikan Nasional"
    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 font-light outline-none transition-all duration-200 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none">{{ old('keterangan', $item?->keterangan) }}</textarea>
</div>
