<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\PosisiElemenDokumen;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

// Service ini berisi operasi umum overlay PDF: baca struktur PDF, multi-page, koordinat, teks, dan incremental update.
class PdfOverlayService
{
    // Nilai ini mengikuti kanvas preview admin agar koordinat klik bisa dipetakan ke ukuran PDF asli.
    private const PREVIEW_WIDTH = 980.0;
    private const PREVIEW_HEIGHT = 1386.0;
    private const DEFAULT_TEXT_COLOR = '0 0 0';

    // Helper ini merapikan koleksi posisi menjadi group per halaman.
    // Nilai halaman kosong/0 dipaksa ke halaman 1 agar posisi lama dari sebelum dukungan multi-page tetap kompatibel.
    public function groupPositionsByPage(Collection $positions): Collection
    {
        return $positions
            ->groupBy(fn (PosisiElemenDokumen $position) => max(1, (int) ($position->halaman ?: 1)))
            ->filter(fn (Collection $items) => $items->isNotEmpty());
    }

    // Method ini membaca object PDF, mencari halaman, lalu menyiapkan object overlay baru untuk setiap halaman yang punya posisi elemen.
    public function appendSuratBiasaOverlays(
        string $pdfContent,
        Dokumen $dokumen,
        Collection $positionsByPage,
        string $overlayTextColor,
        callable $tteCommandBuilder
    ): string {
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
            $contentStream = $this->buildSuratBiasaOverlayStream(
                $dokumen,
                $positions,
                $pageWidth,
                $pageHeight,
                $overlayTextColor,
                $tteCommandBuilder
            );

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

    // Method ini membuat perintah gambar/teks PDF berdasarkan posisi elemen yang disimpan Admin Surat.
    private function buildSuratBiasaOverlayStream(
        Dokumen $dokumen,
        Collection $positions,
        float $pageWidth,
        float $pageHeight,
        string $overlayTextColor,
        callable $tteCommandBuilder
    ): string {
        $parts = [];

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
                // Generator preview/final menentukan sendiri apakah TTE berupa QR dummy merah atau QR validasi asli.
                $parts[] = $tteCommandBuilder($x, $y, $widthPt, $heightPt);
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
    public function textCommand(string $text, float $x, float $y, float $fontSize, string $fillColor = self::DEFAULT_TEXT_COLOR): string
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

    // Method ini menyusun teks "Terverifikasi oleh" dari daftar verifikator yang sudah menyetujui dokumen.
    private function verificationTextCommands(string $verifierNames, float $pageHeight, string $fillColor = self::DEFAULT_TEXT_COLOR): string
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
            ['\\\\', '\\(', '\\)', '', ' '],
            $text
        );
    }
}
