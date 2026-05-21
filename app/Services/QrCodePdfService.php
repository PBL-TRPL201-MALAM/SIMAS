<?php

namespace App\Services;

use RuntimeException;

// Service ini khusus menangani pembuatan command QR di PDF, baik QR dummy preview maupun QR validasi final.
class QrCodePdfService
{
    // Parameter QR level L versi 1-5 cukup untuk URL validasi SIMAS berisi APP_URL + token 40 karakter.
    private const QR_LOW_ECC_PARAMS = [
        1 => ['data_codewords' => 19, 'ecc_codewords' => 7, 'remainder_bits' => 0, 'alignment' => []],
        2 => ['data_codewords' => 34, 'ecc_codewords' => 10, 'remainder_bits' => 7, 'alignment' => [6, 18]],
        3 => ['data_codewords' => 55, 'ecc_codewords' => 15, 'remainder_bits' => 7, 'alignment' => [6, 22]],
        4 => ['data_codewords' => 80, 'ecc_codewords' => 20, 'remainder_bits' => 7, 'alignment' => [6, 26]],
        5 => ['data_codewords' => 108, 'ecc_codewords' => 26, 'remainder_bits' => 7, 'alignment' => [6, 30]],
    ];

    // Method ini membuat placeholder QR/TTE merah untuk PREVIEW_VERIFIKASI_PDF.
    public function dummyRedCommands(float $x, float $y, float $width, float $height): string
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

    // Method ini menggambar QR asli hitam untuk FINAL_PDF berdasarkan URL validasi publik.
    public function validationBlackCommands(string $validationUrl, float $x, float $y, float $width, float $height): string
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

    // Method ini membuat QR validasi sebagai data URI SVG untuk template DomPDF SK.
    public function validationSvgDataUri(string $validationUrl, int $size = 140): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode($this->validationSvg($validationUrl, $size));
    }

    private function validationSvg(string $validationUrl, int $size): string
    {
        $matrix = $this->buildQrMatrix($validationUrl);
        $moduleCount = count($matrix);
        $quietZone = 4;
        $moduleAreaSize = $moduleCount + ($quietZone * 2);
        $cellSize = $size / $moduleAreaSize;
        $paths = [];

        foreach ($matrix as $row => $columns) {
            foreach ($columns as $col => $isDark) {
                if (! $isDark) {
                    continue;
                }

                // Koordinat dibuat dalam satuan piksel agar DomPDF tidak mengecilkan isi QR saat viewBox SVG diabaikan.
                $x = $this->formatSvgNumber(($col + $quietZone) * $cellSize);
                $y = $this->formatSvgNumber(($row + $quietZone) * $cellSize);
                $cell = $this->formatSvgNumber($cellSize);
                $paths[] = "M{$x} {$y}h{$cell}v{$cell}h-{$cell}z";
            }
        }

        $pathData = implode('', $paths);

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" shape-rendering="crispEdges">'
            . '<rect width="100%" height="100%" fill="#fff"/>'
            . '<path fill="#000" d="' . $pathData . '"/>'
            . '</svg>';
    }

    private function formatSvgNumber(float $number): string
    {
        return rtrim(rtrim(sprintf('%.4F', $number), '0'), '.');
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
}
