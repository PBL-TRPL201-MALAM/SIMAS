<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <title>Surat Keputusan {{ $sk->nomor_sk }}</title>
    <style>
      @page {
        size: A4 portrait;
        margin: 20mm 20mm 18mm 20mm;
      }

      * {
        box-sizing: border-box;
      }

      body {
        color: #242424;
        font-family: "Times New Roman", "DejaVu Serif", serif;
        font-size: 11.8px;
        line-height: 1.24;
      }

      .letterhead {
        border-bottom: 2px solid #4a4a4a;
        border-collapse: collapse;
        padding-bottom: 6px;
        width: 100%;
      }

      .letterhead td {
        padding: 0 0 6px;
        vertical-align: top;
      }

      .letterhead-logo {
        width: 130px;
      }

      .letterhead-text {
        padding-left: 8px;
      }

      .ministry {
        font-size: 13.2px;
        font-weight: bold;
        letter-spacing: .2px;
        line-height: 1.15;
        text-transform: uppercase;
      }

      .campus {
        font-size: 14.4px;
        font-weight: bold;
        line-height: 1.15;
        text-transform: uppercase;
      }

      .address {
        font-size: 10.3px;
        line-height: 1.28;
      }

      .title {
        margin-top: 18px;
        text-align: center;
      }

      .title .line {
        font-size: 13px;
        font-weight: bold;
        line-height: 1.25;
        text-transform: uppercase;
      }

      .title .about-label {
        margin-top: 18px;
      }

      .title .about-text {
        margin: 4px auto 0;
        max-width: 560px;
      }

      .opening {
        font-size: 12.5px;
        font-weight: bold;
        margin: 18px 0 10px;
        text-align: center;
        text-transform: uppercase;
      }

      table.clause {
        border-collapse: collapse;
        margin-top: 5px;
        width: 100%;
      }

      table.clause td {
        padding: 0 0 3px;
        vertical-align: top;
      }

      .clause-label {
        width: 92px;
      }

      .clause-colon {
        text-align: center;
        width: 16px;
      }

      .item-label {
        width: 28px;
      }

      .decision-label {
        font-weight: bold;
        width: 104px;
      }

      .decision-title {
        font-size: 13px;
        font-weight: bold;
        margin: 16px 0 10px;
        text-align: center;
        text-transform: uppercase;
      }

      .signature {
        margin-left: auto;
        margin-top: 12mm;
        width: 70mm;
      }

      .qr-tte {
        display: block;
        margin: 4mm 0 2mm;
      }

      .signer-name {
        font-weight: bold;
        text-decoration: underline;
      }

      .uppercase {
        text-transform: uppercase;
      }
    </style>
  </head>
  <body>
    {{-- Kop SK dirender langsung dari Blade agar PDF final tidak bergantung pada PDF upload. --}}
    <table class="letterhead">
      <tr>
        <td class="letterhead-logo">
          <img src="{{ public_path('images/logo-polibatam-kop.png') }}" width="120" style="width:120px;height:auto;">
        </td>
        <td class="letterhead-text">
          <div class="ministry">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
          <div class="campus">Politeknik Negeri Batam</div>
          <div class="address">
            Jalan Ahmad Yani, Batam Centre, Kecamatan Batam Kota, Batam 29461<br>
            Telepon +62 778 469856 - 469860, Faksimile +62 778 463620<br>
            Laman: www.polibatam.ac.id, Surel: info@polibatam.ac.id
          </div>
        </td>
      </tr>
    </table>

    <div class="title">
      <div class="line">Keputusan</div>
      <div class="line">Direktur Politeknik Negeri Batam</div>
      <div class="line">Nomor {{ $sk->nomor_sk }}</div>

      <div class="line about-label">Tentang</div>
      <div class="line about-text">{{ $tentangUpper }}</div>
    </div>

    <div class="opening">Direktur Politeknik Negeri Batam,</div>

    <table class="clause">
      @forelse ($menimbangItems as $item)
        <tr>
          @if ($loop->first)
            <td class="clause-label" rowspan="{{ max($menimbangItems->count(), 1) }}">Menimbang</td>
            <td class="clause-colon" rowspan="{{ max($menimbangItems->count(), 1) }}">:</td>
          @endif
          <td class="item-label">{{ chr(96 + $loop->iteration) }}.</td>
          <td>{{ $item }}</td>
        </tr>
      @empty
        <tr>
          <td class="clause-label">Menimbang</td>
          <td class="clause-colon">:</td>
          <td class="item-label">a.</td>
          <td>-</td>
        </tr>
      @endforelse
    </table>

    <table class="clause">
      @forelse ($mengingatItems as $item)
        <tr>
          @if ($loop->first)
            <td class="clause-label" rowspan="{{ max($mengingatItems->count(), 1) }}">Mengingat</td>
            <td class="clause-colon" rowspan="{{ max($mengingatItems->count(), 1) }}">:</td>
          @endif
          <td class="item-label">{{ $loop->iteration }}.</td>
          <td>{{ $item }}</td>
        </tr>
      @empty
        <tr>
          <td class="clause-label">Mengingat</td>
          <td class="clause-colon">:</td>
          <td class="item-label">1.</td>
          <td>-</td>
        </tr>
      @endforelse
    </table>

    <div class="decision-title">Memutuskan:</div>

    <table class="clause">
      <tr>
        <td class="decision-label">Menetapkan</td>
        <td class="clause-colon">:</td>
        <td class="uppercase">{{ $menetapkanText }}</td>
      </tr>
      @php
        $diktumLabels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
      @endphp
      @forelse ($memutuskanItems as $item)
        <tr>
          <td class="decision-label">{{ $diktumLabels[$loop->index] ?? 'KE-' . $loop->iteration }}</td>
          <td class="clause-colon">:</td>
          <td>{{ $item }}</td>
        </tr>
      @empty
        <tr>
          <td class="decision-label">KESATU</td>
          <td class="clause-colon">:</td>
          <td>-</td>
        </tr>
      @endforelse
    </table>

    <div class="signature">
      <div>Ditetapkan di {{ $sk->tempat_penetapan ?: 'Batam' }}</div>
      <div>pada tanggal {{ $tanggalSkLabel }}</div>
      <div>{{ $penandatangan->jabatan ?: 'Direktur' }},</div>

      {{-- QR berisi URL verifikasi publik /verifikasi/{verification_token}. --}}
      <img class="qr-tte" src="{{ $qrDataUri }}" width="95" height="95" style="width:28mm;height:28mm;" alt="QR Validasi Publik">

      <div class="signer-name">{{ $penandatangan->nama }}</div>
      <div>NIP {{ $penandatangan->nip_nik ?: '-' }}</div>
    </div>
  </body>
</html>
