<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\PosisiElemenDokumen;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

// Service ini bertanggung jawab membuat PDF preview verifikasi dari PDF hasil pemeriksaan Admin/TU.
// Controller hanya memanggil service ini, sedangkan detail teknis manipulasi PDF disimpan di sini agar controller tetap fokus ke alur request.
class PreviewVerifikasiPdfGenerator
{
    // Nilai ini mengikuti kanvas preview admin agar koordinat klik bisa dipetakan ke ukuran PDF asli.
    private const PREVIEW_WIDTH = 980.0;
    private const PREVIEW_HEIGHT = 1386.0;
    private const PREVIEW_OVERLAY_TEXT_COLOR = '1 0 0';
    private const FINAL_OVERLAY_TEXT_COLOR = '0 0 0';

    // Parameter QR level L versi 1-5 cukup untuk URL validasi SIMAS berisi APP_URL + token 40 karakter.
    private const QR_LOW_ECC_PARAMS = [
        1 => ['data_codewords' => 19, 'ecc_codewords' => 7, 'remainder_bits' => 0, 'alignment' => []],
        2 => ['data_codewords' => 34, 'ecc_codewords' => 10, 'remainder_bits' => 7, 'alignment' => [6, 18]],
        3 => ['data_codewords' => 55, 'ecc_codewords' => 15, 'remainder_bits' => 7, 'alignment' => [6, 22]],
        4 => ['data_codewords' => 80, 'ecc_codewords' => 20, 'remainder_bits' => 7, 'alignment' => [6, 26]],
        5 => ['data_codewords' => 108, 'ecc_codewords' => 26, 'remainder_bits' => 7, 'alignment' => [6, 30]],
    ];

    // Method publik ini menjadi pintu masuk generator: ambil data dokumen, baca PDF sumber, tempel overlay, lalu simpan file preview.
    public function generate(Dokumen $dokumen, string $sourceRelativePath): string
    {
        // Relasi diload agar generator punya metadata surat, posisi elemen, dan daftar verifikator tanpa query manual di controller.
        $dokumen->loadMissing([
            'suratBiasa',
            'posisiElemenDokumen',
            'verifikasi.verifikator',
        ]);

        // Generator ini tidak merender HTML; ia menempelkan konten tambahan langsung ke PDF sumber yang sudah ada.
        $disk = Storage::disk('public');

        if (! $disk->exists($sourceRelativePath)) {
            throw new RuntimeException('File PDF sumber tidak ditemukan.');
        }

        $sourcePdf = $disk->get($sourceRelativePath);
        // Multi-page PDF: posisi dikelompokkan per nomor halaman agar overlay hanya ditempel ke halaman yang dipilih Admin/TU.
        // Data lama yang belum punya halaman tetap masuk halaman 1 supaya dokumen lama tidak rusak.
        $positions = $this->groupPositionsByPage($dokumen->posisiElemenDokumen);

        if ($positions->isEmpty()) {
            throw new RuntimeException('Posisi elemen dokumen belum tersedia.');
        }

        // Generator preview/final bekerja dari PDF hasil pemeriksaan yang sudah ada, lalu menempelkan elemen di koordinat simpanan admin.
        $pdfWithPreview = $this->appendPreviewOverlays($sourcePdf, $dokumen, $positions);
        $outputPath = 'dokumen/preview-verifikasi/' . pathinfo($sourceRelativePath, PATHINFO_FILENAME) . '-preview.pdf';

        $disk->put($outputPath, $pdfWithPreview);

        return $outputPath;
    }

    // Method ini khusus dipakai saat publish: QR/TTE dibuat dari URL validasi publik, bukan dummy seperti preview.
    public function generateFinal(Dokumen $dokumen, string $sourceRelativePath, string $validationUrl, string $outputPath): string
    {
        $dokumen->loadMissing([
            'suratBiasa',
            'posisiElemenDokumen',
            'verifikasi.verifikator',
        ]);

        $disk = Storage::disk('public');

        if (! $disk->exists($sourceRelativePath)) {
            throw new RuntimeException('File PDF sumber tidak ditemukan.');
        }

        $sourcePdf = $disk->get($sourceRelativePath);
        // FINAL_PDF memakai aturan multi-page yang sama dengan preview: semua halaman asli dipertahankan, overlay hanya pada halaman tersimpan.
        $positions = $this->groupPositionsByPage($dokumen->posisiElemenDokumen);

        if ($positions->isEmpty()) {
            throw new RuntimeException('Posisi elemen dokumen belum tersedia.');
        }

        // FINAL_PDF wajib memakai QR asli yang berisi URL route verifikasi.public dari APP_URL.
        $pdfWithFinalQr = $this->appendPreviewOverlays($sourcePdf, $dokumen, $positions, $validationUrl);

        $disk->put($outputPath, $pdfWithFinalQr);

        return $outputPath;
    }

    // Helper ini merapikan koleksi posisi menjadi group per halaman.
    // Nilai halaman kosong/0 dipaksa ke halaman 1 agar posisi lama dari sebelum dukungan multi-page tetap kompatibel.
    private function groupPositionsByPage(Collection $positions): Collection
    {
        return $positions
            ->groupBy(fn (PosisiElemenDokumen $position) => max(1, (int) ($position->halaman ?: 1)))
            ->filter(fn (Collection $items) => $items->isNotEmpty());
    }

    // Helper ini mencari urutan halaman dari page tree PDF.
    // Urutan page tree lebih aman untuk PDF multi-page dibanding hanya menebak dari urutan object di file.
    private function getPageObjectNumbers(array $objects): Collection
    {
        $catalogObjectNumber = collect($objects)
            ->filter(fn (array $object) => preg_match('/\/Type\s*\/Catalog\b/', $object['body']) === 1)
            ->keys()
            ->first();

        if ($catalogObjectNumber && preg_match('/\/Pages\s+(\d+)\s+\d+\s+R/', $objects[$catalogObjectNumber]['body'], $matches)) {
            $visited = [];
            $pageObjectNumbers = $this->walkPageTree((int) $matches[1], $objects, $visited);

            if ($pageObjectNumbers !== []) {
                return collect($pageObjectNumbers)->values();
            }
        }

        // Fallback untuk PDF sederhana: cari object bertipe Page, bukan Pages, lalu urutkan berdasarkan kemunculan.
        return collect($objects)
            ->filter(fn (array $object) => preg_match('/\/Type\s*\/Page\b/', $object['body']) === 1 && preg_match('/\/Type\s*\/Pages\b/', $object['body']) !== 1)
            ->sortBy('offset')
            ->keys()
            ->values();
    }

    // Rekursi ini mengikuti /Kids pada Pages tree sehingga halaman 1, 2, 3 sesuai struktur PDF asli.
    private function walkPageTree(int $objectNumber, array $objects, array &$visited): array
    {
        if (isset($visited[$objectNumber]) || ! isset($objects[$objectNumber])) {
            return [];
        }

        $visited[$objectNumber] = true;
        $body = $objects[$objectNumber]['body'];

        if (preg_match('/\/Type\s*\/Page\b/', $body) === 1 && preg_match('/\/Type\s*\/Pages\b/', $body) !== 1) {
            return [$objectNumber];
        }

        if (! preg_match('/\/Kids\s*\[(.*?)\]/s', $body, $kidsMatch)) {
            return [];
        }

        preg_match_all('/(\d+)\s+\d+\s+R/', $kidsMatch[1], $kidMatches);

        $pageObjectNumbers = [];

        foreach ($kidMatches[1] as $kidObjectNumber) {
            $pageObjectNumbers = array_merge(
                $pageObjectNumbers,
                $this->walkPageTree((int) $kidObjectNumber, $objects, $visited)
            );
        }

        return $pageObjectNumbers;
    }

    // Method ini membaca object PDF, mencari halaman, lalu menyiapkan object overlay baru untuk setiap halaman yang punya posisi elemen.
    private function appendPreviewOverlays(string $pdfContent, Dokumen $dokumen, Collection $positionsByPage, ?string $qrValidationUrl = null): string
    {
        // Regex ini mengambil object-object PDF mentah agar service bisa melakukan incremental update tanpa library eksternal.
        preg_match_all('/(\d+)\s+(\d+)\s+obj(.*?)endobj/s', $pdfContent, $matches, PREG_OFFSET_CAPTURE);

        $objects = [];
        $maxObjectNumber = 0;

        foreach ($matches[0] as $index => $fullMatch) {
            $objectNumber = (int) $matches[1][$index][0];
            $generation = (int) $matches[2][$index][0];
            $body = trim($matches[3][$index][0]);
            $offset = $fullMatch[1];

            $objects[$objectNumber] = [
                'generation' => $generation,
                'body' => $body,
                'offset' => $offset,
            ];

            $maxObjectNumber = max($maxObjectNumber, $objectNumber);
        }

        // Object halaman diambil sesuai page tree agar nomor halaman yang disimpan UI cocok dengan urutan halaman PDF asli.
        $pageObjectNumbers = $this->getPageObjectNumbers($objects);

        if ($pageObjectNumbers->isEmpty()) {
            throw new RuntimeException('Halaman PDF tidak dapat diproses.');
        }

        $fontObjectNumber = ++$maxObjectNumber;
        // Font khusus overlay didaftarkan sebagai object PDF baru agar teks nomor/tanggal/verifikasi bisa dirender.
        $updatedObjects = [
            $fontObjectNumber => "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
        ];

        foreach ($positionsByPage as $pageNumber => $positions) {
            $pageIndex = (int) $pageNumber - 1;
            $pageObjectNumber = $pageObjectNumbers->get($pageIndex);

            if (! $pageObjectNumber || ! isset($objects[$pageObjectNumber])) {
                continue;
            }

            // Setiap halaman PDF asli tetap ada; hanya halaman yang nomornya sama dengan posisi elemen yang diberi object overlay baru.
            [$pageWidth, $pageHeight] = $this->extractPageSize($objects[$pageObjectNumber]['body']);
            $contentObjectNumber = ++$maxObjectNumber;
            $contentStream = $this->buildOverlayStream($dokumen, $positions, $pageWidth, $pageHeight, $qrValidationUrl);

            $updatedObjects[$contentObjectNumber] = $this->wrapStream($contentStream);
            $updatedObjects[$pageObjectNumber] = $this->injectOverlayIntoPageObject(
                $objects[$pageObjectNumber]['body'],
                $contentObjectNumber,
                $fontObjectNumber
            );
        }

        if (count($updatedObjects) === 1) {
            throw new RuntimeException('Tidak ada halaman PDF yang cocok untuk ditempeli preview.');
        }

        return $this->appendIncrementalUpdate($pdfContent, $updatedObjects, $maxObjectNumber);
    }

    // Method ini membaca ukuran halaman dari MediaBox PDF; jika tidak ada, fallback ke ukuran A4 point.
    private function extractPageSize(string $pageBody): array
    {
        if (preg_match('/\/MediaBox\s*\[\s*([0-9.\-]+)\s+([0-9.\-]+)\s+([0-9.\-]+)\s+([0-9.\-]+)\s*\]/', $pageBody, $matches)) {
            return [
                (float) $matches[3] - (float) $matches[1],
                (float) $matches[4] - (float) $matches[2],
            ];
        }

        return [595.32, 841.92];
    }

    // Method ini membuat perintah gambar/teks PDF berdasarkan posisi elemen yang disimpan Admin/TU.
    private function buildOverlayStream(Dokumen $dokumen, Collection $positions, float $pageWidth, float $pageHeight, ?string $qrValidationUrl = null): string
    {
        $parts = [];
        $isFinalPdf = $qrValidationUrl !== null;
        $overlayTextColor = $isFinalPdf
            ? self::FINAL_OVERLAY_TEXT_COLOR
            : self::PREVIEW_OVERLAY_TEXT_COLOR;

        // Hanya verifikator yang sudah menyetujui yang dicantumkan pada preview/final.
        $approvedVerifierNames = $dokumen->verifikasi
            ->where('status_verifikasi', 'DISETUJUI')
            ->sortBy('level')
            ->map(fn ($verifikasi) => $verifikasi->verifikator?->nama)
            ->filter()
            ->values();

        /** @var PosisiElemenDokumen $position */
        foreach ($positions as $position) {
            // Koordinat dari kanvas preview dikonversi ke satuan point PDF asli agar posisi tetap proporsional.
            $width = (float) ($position->lebar ?? ($position->elemen === 'tte' ? 80 : 180));
            $height = (float) ($position->tinggi ?? ($position->elemen === 'tte' ? 80 : 34));

            $x = ((float) $position->posisi_x / self::PREVIEW_WIDTH) * $pageWidth;
            $yTop = ((float) $position->posisi_y / self::PREVIEW_HEIGHT) * $pageHeight;
            $heightPt = ($height / self::PREVIEW_HEIGHT) * $pageHeight;
            $widthPt = ($width / self::PREVIEW_WIDTH) * $pageWidth;
            $y = $pageHeight - $yTop - $heightPt;

            if ($position->elemen === 'nomor_surat') {
                $text = $dokumen->suratBiasa?->nomor_surat ?: 'Nomor Surat';
                $parts[] = $this->textCommand($text, $x, $y + max(10, $heightPt / 3), 11, $overlayTextColor);
                continue;
            }

            if ($position->elemen === 'tanggal_surat') {
                $text = $dokumen->suratBiasa?->tanggal_surat
                    ? Carbon::parse($dokumen->suratBiasa->tanggal_surat)->locale('id')->translatedFormat('d/F/Y')
                    : 'Tanggal Surat';
                $parts[] = $this->textCommand($text, $x, $y + max(10, $heightPt / 3), 11, $overlayTextColor);
                continue;
            }

            if (in_array($position->elemen, ['tujuan', 'kepada_tujuan'], true)) {
                $text = $dokumen->suratBiasa?->kepada_tujuan ?: 'Tujuan';
                $parts[] = $this->textCommand($text, $x, $y + max(10, $heightPt / 3), 11, $overlayTextColor);
                continue;
            }

            if ($position->elemen === 'tte') {
                // Preview verifikasi tetap memakai QR dummy; FINAL_PDF memakai QR asli berisi link validasi publik.
                $parts[] = $isFinalPdf
                    ? $this->qrValidationCommands($qrValidationUrl, $x, $y, $widthPt, $heightPt)
                    : $this->qrDummyCommands($x, $y, $widthPt, $heightPt);
            }
        }

        if ($approvedVerifierNames->isNotEmpty()) {
            // Teks verifikasi ditempatkan di area bawah halaman agar tidak bentrok dengan tanda tangan dan QR.
            $parts[] = $this->verificationTextCommands(
                $approvedVerifierNames->implode(', '),
                $pageHeight,
                $overlayTextColor
            );
        }

        return implode("\n", array_filter($parts));
    }

    // Method ini menyusun command PDF untuk menggambar teks pada koordinat tertentu.
    private function textCommand(string $text, float $x, float $y, float $fontSize, string $fillColor = self::FINAL_OVERLAY_TEXT_COLOR): string
    {
        $escaped = $this->escapePdfText($text);

        return implode("\n", [
            'q',
            'BT',
            sprintf('/FSIMASPREVIEW %.2F Tf', $fontSize),
            $fillColor . ' rg',
            sprintf('1 0 0 1 %.2F %.2F Tm', $x, $y),
            sprintf('(%s) Tj', $escaped),
            'ET',
            'Q',
        ]);
    }

    // Method ini membuat placeholder QR/TTE langsung sebagai command PDF berbentuk grid.
    private function qrDummyCommands(float $x, float $y, float $width, float $height): string
    {
        // Dummy QR digambar manual sebagai blok-blok persegi agar bisa ditaruh langsung ke PDF tanpa render HTML/browser.
        $gridSize = 7;
        $cellWidth = $width / $gridSize;
        $cellHeight = $height / $gridSize;
        $filledCells = [0,1,2,4,5,6,7,10,12,13,14,16,18,20,21,24,26,27,28,30,32,34,35,36,38,40,41,42,43,44,46,47,48];

        $commands = [
            'q',
            '1 0.92 0.92 rg',
            sprintf('%.2F %.2F %.2F %.2F re f', $x, $y, $width, $height),
        ];

        foreach ($filledCells as $cellIndex) {
            $col = $cellIndex % $gridSize;
            $row = intdiv($cellIndex, $gridSize);
            $cellX = $x + ($col * $cellWidth);
            $cellY = $y + (($gridSize - 1 - $row) * $cellHeight);
            $commands[] = '1 0 0 rg';
            $commands[] = sprintf('%.2F %.2F %.2F %.2F re f', $cellX, $cellY, $cellWidth - 1, $cellHeight - 1);
        }

        $commands[] = 'Q';

        return implode("\n", $commands);
    }

    // Method ini menggambar QR asli sebagai kumpulan kotak PDF berdasarkan URL validasi dokumen.
    private function qrValidationCommands(string $validationUrl, float $x, float $y, float $width, float $height): string
    {
        $matrix = $this->buildQrMatrix($validationUrl);
        $moduleCount = count($matrix);
        $quietZone = 4;
        $squareSize = min($width, $height);
        $cellSize = $squareSize / ($moduleCount + ($quietZone * 2));
        $originX = $x + (($width - $squareSize) / 2);
        $originY = $y + (($height - $squareSize) / 2);

        $commands = [
            'q',
            '1 1 1 rg',
            sprintf('%.2F %.2F %.2F %.2F re f', $originX, $originY, $squareSize, $squareSize),
            '0 0 0 rg',
        ];

        foreach ($matrix as $row => $columns) {
            foreach ($columns as $col => $isDark) {
                if (! $isDark) {
                    continue;
                }

                $moduleX = $originX + (($col + $quietZone) * $cellSize);
                $moduleY = $originY + $squareSize - (($row + $quietZone + 1) * $cellSize);
                $commands[] = sprintf('%.2F %.2F %.2F %.2F re f', $moduleX, $moduleY, $cellSize, $cellSize);
            }
        }

        $commands[] = 'Q';

        return implode("\n", $commands);
    }

    /**
     * @return array<int, array<int, bool>>
     */
    private function buildQrMatrix(string $text): array
    {
        // Method ini membangun matriks QR sederhana langsung di PHP agar FINAL_PDF tidak bergantung pada layanan/library QR eksternal.
        $bytes = array_values(unpack('C*', $text) ?: []);
        // Versi QR dipilih berdasarkan panjang URL validasi; makin panjang URL, makin besar matriks QR yang dibutuhkan.
        $version = $this->selectQrVersion(count($bytes));
        $params = self::QR_LOW_ECC_PARAMS[$version];
        // Data URL diubah menjadi codeword QR, lalu ditambah error correction Reed-Solomon supaya QR tetap bisa dipindai.
        $dataCodewords = $this->buildQrDataCodewords($bytes, $version, $params['data_codewords']);
        $eccCodewords = $this->reedSolomonRemainder($dataCodewords, $params['ecc_codewords']);
        $allCodewords = array_merge($dataCodewords, $eccCodewords);
        $size = 17 + (4 * $version);

        $baseModules = array_fill(0, $size, array_fill(0, $size, null));
        $isFunction = array_fill(0, $size, array_fill(0, $size, false));
        // Pola finder, timing, alignment, dan format adalah bagian tetap QR; data URL ditempatkan setelah pola ini siap.
        $this->drawQrFunctionPatterns($baseModules, $isFunction, $version);

        $bestMatrix = null;
        $bestPenalty = PHP_INT_MAX;

        for ($mask = 0; $mask < 8; $mask++) {
            $modules = $baseModules;
            $this->drawQrCodewords($modules, $isFunction, $allCodewords, $params['remainder_bits'], $mask);
            $this->drawQrFormatBits($modules, $isFunction, $mask);

            // QR dicoba dengan 8 mask standar; skor penalty paling kecil dipilih agar pola QR mudah dibaca scanner.
            $penalty = $this->qrPenaltyScore($modules);

            if ($penalty < $bestPenalty) {
                $bestPenalty = $penalty;
                $bestMatrix = $modules;
            }
        }

        return array_map(
            fn (array $row) => array_map(fn ($value) => (bool) $value, $row),
            $bestMatrix ?? $baseModules
        );
    }

    private function selectQrVersion(int $byteCount): int
    {
        // Versi dipilih dari kapasitas terkecil yang masih cukup agar QR final tetap ringkas di area TTE.
        foreach (self::QR_LOW_ECC_PARAMS as $version => $params) {
            $capacityBits = $params['data_codewords'] * 8;
            $requiredBits = 4 + 8 + ($byteCount * 8);

            if ($requiredBits <= $capacityBits) {
                return $version;
            }
        }

        throw new RuntimeException('URL validasi terlalu panjang untuk QR final SIMAS.');
    }

    /**
     * @param  array<int, int>  $bytes
     * @return array<int, int>
     */
    private function buildQrDataCodewords(array $bytes, int $version, int $dataCodewordCount): array
    {
        // QR memakai mode byte karena URL validasi berisi karakter teks umum, token, dan tanda baca URL.
        $bits = [];
        $this->appendQrBits($bits, 0b0100, 4);
        $this->appendQrBits($bits, count($bytes), $version <= 9 ? 8 : 16);

        foreach ($bytes as $byte) {
            $this->appendQrBits($bits, $byte, 8);
        }

        $capacityBits = $dataCodewordCount * 8;
        $terminatorBits = min(4, $capacityBits - count($bits));

        for ($i = 0; $i < $terminatorBits; $i++) {
            $bits[] = 0;
        }

        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        $codewords = [];

        foreach (array_chunk($bits, 8) as $chunk) {
            $value = 0;

            foreach ($chunk as $bit) {
                $value = ($value << 1) | $bit;
            }

            $codewords[] = $value;
        }

        for ($pad = 0; count($codewords) < $dataCodewordCount; $pad++) {
            // Padding 0xEC/0x11 adalah pola standar QR untuk mengisi sisa kapasitas data.
            $codewords[] = $pad % 2 === 0 ? 0xEC : 0x11;
        }

        return $codewords;
    }

    /**
     * @param  array<int, int>  $bits
     */
    private function appendQrBits(array &$bits, int $value, int $length): void
    {
        for ($i = $length - 1; $i >= 0; $i--) {
            $bits[] = ($value >> $i) & 1;
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     * @param  array<int, array<int, bool>>  $isFunction
     */
    private function drawQrFunctionPatterns(array &$modules, array &$isFunction, int $version): void
    {
        $size = count($modules);

        // Finder pattern adalah tiga kotak besar di sudut QR yang dipakai scanner untuk mengenali orientasi.
        $this->drawQrFinderPattern($modules, $isFunction, 0, 0);
        $this->drawQrFinderPattern($modules, $isFunction, $size - 7, 0);
        $this->drawQrFinderPattern($modules, $isFunction, 0, $size - 7);

        // Timing pattern membantu scanner membaca jarak modul QR secara konsisten.
        for ($i = 8; $i < $size - 8; $i++) {
            $this->setQrFunctionModule($modules, $isFunction, $i, 6, $i % 2 === 0);
            $this->setQrFunctionModule($modules, $isFunction, 6, $i, $i % 2 === 0);
        }

        // Alignment pattern muncul pada versi QR lebih besar agar pembacaan tetap stabil jika QR agak miring.
        foreach (self::QR_LOW_ECC_PARAMS[$version]['alignment'] as $centerY) {
            foreach (self::QR_LOW_ECC_PARAMS[$version]['alignment'] as $centerX) {
                if ($isFunction[$centerY][$centerX]) {
                    continue;
                }

                $this->drawQrAlignmentPattern($modules, $isFunction, $centerX, $centerY);
            }
        }

        $this->drawQrFormatBits($modules, $isFunction, 0);
        $this->setQrFunctionModule($modules, $isFunction, 8, $size - 8, true);
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     * @param  array<int, array<int, bool>>  $isFunction
     */
    private function drawQrFinderPattern(array &$modules, array &$isFunction, int $x, int $y): void
    {
        $size = count($modules);

        for ($dy = -1; $dy <= 7; $dy++) {
            for ($dx = -1; $dx <= 7; $dx++) {
                $xx = $x + $dx;
                $yy = $y + $dy;

                if ($xx < 0 || $xx >= $size || $yy < 0 || $yy >= $size) {
                    continue;
                }

                $isDark = $dx >= 0 && $dx <= 6 && $dy >= 0 && $dy <= 6
                    && ($dx === 0 || $dx === 6 || $dy === 0 || $dy === 6 || ($dx >= 2 && $dx <= 4 && $dy >= 2 && $dy <= 4));

                $this->setQrFunctionModule($modules, $isFunction, $xx, $yy, $isDark);
            }
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     * @param  array<int, array<int, bool>>  $isFunction
     */
    private function drawQrAlignmentPattern(array &$modules, array &$isFunction, int $centerX, int $centerY): void
    {
        for ($dy = -2; $dy <= 2; $dy++) {
            for ($dx = -2; $dx <= 2; $dx++) {
                $isDark = max(abs($dx), abs($dy)) === 2 || ($dx === 0 && $dy === 0);
                $this->setQrFunctionModule($modules, $isFunction, $centerX + $dx, $centerY + $dy, $isDark);
            }
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     * @param  array<int, array<int, bool>>  $isFunction
     */
    private function drawQrFormatBits(array &$modules, array &$isFunction, int $mask): void
    {
        $size = count($modules);
        $data = (0b01 << 3) | $mask;
        $bits = $data << 10;

        for ($i = 14; $i >= 10; $i--) {
            if ((($bits >> $i) & 1) !== 0) {
                $bits ^= 0x537 << ($i - 10);
            }
        }

        $formatBits = (($data << 10) | $bits) ^ 0x5412;

        for ($i = 0; $i <= 5; $i++) {
            $this->setQrFunctionModule($modules, $isFunction, 8, $i, (($formatBits >> $i) & 1) !== 0);
        }

        $this->setQrFunctionModule($modules, $isFunction, 8, 7, (($formatBits >> 6) & 1) !== 0);
        $this->setQrFunctionModule($modules, $isFunction, 8, 8, (($formatBits >> 7) & 1) !== 0);
        $this->setQrFunctionModule($modules, $isFunction, 7, 8, (($formatBits >> 8) & 1) !== 0);

        for ($i = 9; $i < 15; $i++) {
            $this->setQrFunctionModule($modules, $isFunction, 14 - $i, 8, (($formatBits >> $i) & 1) !== 0);
        }

        for ($i = 0; $i < 8; $i++) {
            $this->setQrFunctionModule($modules, $isFunction, $size - 1 - $i, 8, (($formatBits >> $i) & 1) !== 0);
        }

        for ($i = 8; $i < 15; $i++) {
            $this->setQrFunctionModule($modules, $isFunction, 8, $size - 15 + $i, (($formatBits >> $i) & 1) !== 0);
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     * @param  array<int, array<int, bool>>  $isFunction
     */
    private function setQrFunctionModule(array &$modules, array &$isFunction, int $x, int $y, bool $isDark): void
    {
        $modules[$y][$x] = $isDark;
        $isFunction[$y][$x] = true;
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     * @param  array<int, array<int, bool>>  $isFunction
     * @param  array<int, int>  $codewords
     */
    private function drawQrCodewords(array &$modules, array $isFunction, array $codewords, int $remainderBits, int $mask): void
    {
        $size = count($modules);
        $bits = [];

        foreach ($codewords as $codeword) {
            $this->appendQrBits($bits, $codeword, 8);
        }

        for ($i = 0; $i < $remainderBits; $i++) {
            $bits[] = 0;
        }

        $bitIndex = 0;

        // Data QR ditulis zig-zag dari kanan bawah ke kiri atas sesuai urutan penempatan modul QR.
        for ($right = $size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }

            for ($vertical = 0; $vertical < $size; $vertical++) {
                $upward = (($right + 1) & 2) === 0;
                $y = $upward ? $size - 1 - $vertical : $vertical;

                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;

                    if ($isFunction[$y][$x]) {
                        continue;
                    }

                    $isDark = ($bits[$bitIndex] ?? 0) === 1;
                    $bitIndex++;

                    if ($this->qrMaskApplies($mask, $x, $y)) {
                        $isDark = ! $isDark;
                    }

                    $modules[$y][$x] = $isDark;
                }
            }
        }
    }

    private function qrMaskApplies(int $mask, int $x, int $y): bool
    {
        // Mask QR membalik sebagian modul agar pola hitam/putih tidak membentuk blok besar yang sulit dipindai.
        return match ($mask) {
            0 => ($x + $y) % 2 === 0,
            1 => $y % 2 === 0,
            2 => $x % 3 === 0,
            3 => ($x + $y) % 3 === 0,
            4 => (intdiv($y, 2) + intdiv($x, 3)) % 2 === 0,
            5 => (($x * $y) % 2 + ($x * $y) % 3) === 0,
            6 => ((($x * $y) % 2 + ($x * $y) % 3) % 2) === 0,
            7 => ((($x + $y) % 2 + ($x * $y) % 3) % 2) === 0,
            default => false,
        };
    }

    /**
     * @param  array<int, array<int, bool|null>>  $modules
     */
    private function qrPenaltyScore(array $modules): int
    {
        // Skor penalty menilai pola QR: deret panjang, blok 2x2, pola mirip finder palsu, dan keseimbangan modul gelap.
        $size = count($modules);
        $penalty = 0;

        for ($y = 0; $y < $size; $y++) {
            $penalty += $this->qrRunPenalty($modules[$y]);
        }

        for ($x = 0; $x < $size; $x++) {
            $column = [];

            for ($y = 0; $y < $size; $y++) {
                $column[] = $modules[$y][$x];
            }

            $penalty += $this->qrRunPenalty($column);
        }

        for ($y = 0; $y < $size - 1; $y++) {
            for ($x = 0; $x < $size - 1; $x++) {
                $color = $modules[$y][$x];

                if ($color === $modules[$y][$x + 1] && $color === $modules[$y + 1][$x] && $color === $modules[$y + 1][$x + 1]) {
                    $penalty += 3;
                }
            }
        }

        $darkModules = 0;

        foreach ($modules as $row) {
            foreach ($row as $module) {
                if ($module) {
                    $darkModules++;
                }
            }
        }

        $totalModules = $size * $size;
        $penalty += (int) (floor(abs(($darkModules * 20) - ($totalModules * 10)) / $totalModules) * 10);

        return $penalty;
    }

    /**
     * @param  array<int, bool|null>  $modules
     */
    private function qrRunPenalty(array $modules): int
    {
        $penalty = 0;
        $runColor = $modules[0];
        $runLength = 1;
        $count = count($modules);

        for ($i = 1; $i < $count; $i++) {
            if ($modules[$i] === $runColor) {
                $runLength++;
                continue;
            }

            if ($runLength >= 5) {
                $penalty += 3 + ($runLength - 5);
            }

            $runColor = $modules[$i];
            $runLength = 1;
        }

        if ($runLength >= 5) {
            $penalty += 3 + ($runLength - 5);
        }

        for ($i = 0; $i <= $count - 11; $i++) {
            $pattern = array_slice($modules, $i, 11);
            $bits = implode('', array_map(fn ($module) => $module ? '1' : '0', $pattern));

            if ($bits === '10111010000' || $bits === '00001011101') {
                $penalty += 40;
            }
        }

        return $penalty;
    }

    /**
     * @param  array<int, int>  $data
     * @return array<int, int>
     */
    private function reedSolomonRemainder(array $data, int $degree): array
    {
        // Reed-Solomon menghasilkan error correction codeword agar QR masih terbaca walau ada sedikit noise/cacat cetak.
        $divisor = $this->reedSolomonDivisor($degree);
        $result = array_fill(0, $degree, 0);

        foreach ($data as $byte) {
            $factor = $byte ^ $result[0];
            array_shift($result);
            $result[] = 0;

            foreach ($divisor as $index => $coefficient) {
                $result[$index] ^= $this->reedSolomonMultiply($coefficient, $factor);
            }
        }

        return $result;
    }

    /**
     * @return array<int, int>
     */
    private function reedSolomonDivisor(int $degree): array
    {
        $result = array_fill(0, $degree, 0);
        $result[$degree - 1] = 1;
        $root = 1;

        for ($i = 0; $i < $degree; $i++) {
            for ($j = 0; $j < $degree; $j++) {
                $result[$j] = $this->reedSolomonMultiply($result[$j], $root);

                if ($j + 1 < $degree) {
                    $result[$j] ^= $result[$j + 1];
                }
            }

            $root = $this->reedSolomonMultiply($root, 0x02);
        }

        return $result;
    }

    private function reedSolomonMultiply(int $x, int $y): int
    {
        if ($x === 0 || $y === 0) {
            return 0;
        }

        [$expTable, $logTable] = $this->reedSolomonTables();

        return $expTable[($logTable[$x] + $logTable[$y]) % 255];
    }

    /**
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    private function reedSolomonTables(): array
    {
        static $expTable = null;
        static $logTable = null;

        if ($expTable !== null && $logTable !== null) {
            return [$expTable, $logTable];
        }

        $expTable = array_fill(0, 255, 0);
        $logTable = array_fill(0, 256, 0);
        $value = 1;

        for ($i = 0; $i < 255; $i++) {
            $expTable[$i] = $value;
            $logTable[$value] = $i;
            $value <<= 1;

            if (($value & 0x100) !== 0) {
                $value ^= 0x11D;
            }
        }

        return [$expTable, $logTable];
    }

    // Method ini menyusun teks "Terverifikasi oleh" dari daftar verifikator yang sudah menyetujui dokumen.
    private function verificationTextCommands(string $verifierNames, float $pageHeight, string $fillColor = self::FINAL_OVERLAY_TEXT_COLOR): string
    {
        $text = 'Terverifikasi oleh: ' . $verifierNames;

        // Potong jika terlalu panjang agar tidak turun/baris baru.
        if (strlen($text) > 110) {
            $text = substr($text, 0, 107) . '...';
        }

        $baseX = 40.0;
        $bottomMargin = 60.0;
        $fontSize = strlen($text) > 70 ? 7.0 : 8.0;

        return $this->textCommand(
            $text,
            $baseX,
            $bottomMargin,
            $fontSize,
            $fillColor
        );
    }

    // Helper ini memecah teks panjang menjadi beberapa baris; disiapkan jika layout verifikasi perlu dibuat multi-line.
    private function wrapVerificationText(string $text, int $maxChars): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
            }

            $current = $word;
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines === [] ? [$text] : $lines;
    }

    // Method ini menyisipkan object overlay ke Resources dan Contents halaman PDF.
    private function injectOverlayIntoPageObject(string $pageBody, int $contentObjectNumber, int $fontObjectNumber): string
    {
        $updated = preg_replace_callback(
            '/\/Font\s*<<(.+?)>>/s',
            fn (array $matches) => str_contains($matches[1], '/FSIMASPREVIEW')
                ? $matches[0]
                : '/Font <<' . trim($matches[1]) . sprintf(' /FSIMASPREVIEW %d 0 R', $fontObjectNumber) . '>>',
            $pageBody,
            1
        );

        if ($updated === $pageBody && preg_match('/\/Resources\s*<</', $pageBody)) {
            $updated = preg_replace(
                '/\/Resources\s*<</',
                sprintf('/Resources<</Font<</FSIMASPREVIEW %d 0 R>> ', $fontObjectNumber),
                $pageBody,
                1
            );
        }

        $updated = preg_replace_callback(
            '/\/Contents\s*(\[(.*?)\]|(\d+\s+\d+\s+R))/s',
            function (array $matches) use ($contentObjectNumber) {
                if (str_starts_with(trim($matches[1]), '[')) {
                    $existing = trim($matches[2]);
                    return '/Contents [' . $existing . sprintf(' %d 0 R]', $contentObjectNumber);
                }

                return sprintf('/Contents [%s %d 0 R]', trim($matches[1]), $contentObjectNumber);
            },
            $updated,
            1
        );

        return $updated;
    }

    // Method ini membungkus command overlay menjadi stream object sesuai format PDF.
    private function wrapStream(string $stream): string
    {
        return sprintf("<< /Length %d >>\nstream\n%s\nendstream", strlen($stream), $stream);
    }

    // Method ini menambahkan object baru di akhir PDF dan membuat xref/trailer baru tanpa menulis ulang PDF dari nol.
    private function appendIncrementalUpdate(string $originalPdf, array $updatedObjects, int $maxObjectNumber): string
    {
        // PDF diperbarui dengan incremental update supaya file sumber tidak perlu dibangun ulang dari nol.
        if (! preg_match_all('/trailer\s*<<(.*?)>>\s*startxref\s*(\d+)/s', $originalPdf, $trailerMatches, PREG_SET_ORDER)) {
            throw new RuntimeException('Trailer PDF tidak ditemukan.');
        }

        $lastTrailer = end($trailerMatches);
        $trailerBody = $lastTrailer[1];
        $previousXrefOffset = (int) $lastTrailer[2];

        preg_match('/\/Root\s+(\d+\s+\d+\s+R)/', $trailerBody, $rootMatch);
        preg_match('/\/Info\s+(\d+\s+\d+\s+R)/', $trailerBody, $infoMatch);
        preg_match('/\/ID\s*(\[[^\]]+\])/', $trailerBody, $idMatch);

        $pdf = rtrim($originalPdf) . "\n";
        $offsets = [];

        ksort($updatedObjects);

        foreach ($updatedObjects as $objectNumber => $body) {
            $offsets[$objectNumber] = strlen($pdf);
            $pdf .= sprintf("%d 0 obj\n%s\nendobj\n", $objectNumber, $body);
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n";

        $objectNumbers = array_keys($offsets);
        sort($objectNumbers);
        $ranges = [];

        foreach ($objectNumbers as $objectNumber) {
            if (empty($ranges) || $objectNumber !== ($ranges[array_key_last($ranges)]['end'] + 1)) {
                $ranges[] = ['start' => $objectNumber, 'end' => $objectNumber];
                continue;
            }

            $ranges[array_key_last($ranges)]['end'] = $objectNumber;
        }

        foreach ($ranges as $range) {
            $pdf .= sprintf("%d %d\n", $range['start'], ($range['end'] - $range['start']) + 1);
            for ($number = $range['start']; $number <= $range['end']; $number++) {
                $pdf .= sprintf("%010d %05d n \n", $offsets[$number], 0);
            }
        }

        $trailerLines = [
            sprintf('/Size %d', $maxObjectNumber + 1),
            sprintf('/Root %s', $rootMatch[1] ?? '1 0 R'),
        ];

        if (! empty($infoMatch[1])) {
            $trailerLines[] = sprintf('/Info %s', $infoMatch[1]);
        }

        if (! empty($idMatch[1])) {
            $trailerLines[] = sprintf('/ID %s', $idMatch[1]);
        }

        $trailerLines[] = sprintf('/Prev %d', $previousXrefOffset);

        $pdf .= "trailer\n<<\n" . implode("\n", $trailerLines) . "\n>>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }

    // Helper ini meng-escape karakter khusus agar teks aman dimasukkan ke literal string PDF.
    private function escapePdfText(string $text): string
    {
        return str_replace(
            ['\\', '(', ')', "\r", "\n"],
            ['\\\\', '\(', '\)', '', ' '],
            $text
        );
    }
}
