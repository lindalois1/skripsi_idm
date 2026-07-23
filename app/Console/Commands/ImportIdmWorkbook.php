<?php

namespace App\Console\Commands;

use App\Models\DataIDM;
use App\Models\Desa;
use App\Services\StatusKlasifikasiService;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class ImportIdmWorkbook extends Command
{
    protected $signature = 'idm:import-workbook {file} {--year=} {--verify : Tandai data langsung verified}';

    protected $description = 'Import data IDM dari workbook Excel resmi dengan format rekap berbeda-beda.';

    public function handle(StatusKlasifikasiService $statusKlasifikasi): int
    {
        $file = $this->argument('file');
        if (!is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'docx') {
            return $this->importDocx($file, $statusKlasifikasi);
        }

        $spreadsheet = IOFactory::load($file);
        $imported = 0;
        $skipped = 0;
        $sheetProcessed = 0;
        $verified = $this->option('verify');

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $highestRow = $sheet->getHighestDataRow();
            $highestColumn = $sheet->getHighestDataColumn();
            $headerRow = $this->detectHeaderRow($sheet, $highestRow, $highestColumn);

            if (!$headerRow) {
                continue;
            }

            $headers = $sheet->rangeToArray("A{$headerRow}:{$highestColumn}{$headerRow}", null, true, false)[0];
            $columns = $this->mapColumns($headers);

            if (!isset($columns['nama_desa']) || (!isset($columns['idm']) && !isset($columns['skor']))) {
                continue;
            }

            $sheetProcessed++;

            for ($rowNumber = $headerRow + 1; $rowNumber <= $highestRow; $rowNumber++) {
                $row = $sheet->rangeToArray("A{$rowNumber}:{$highestColumn}{$rowNumber}", null, true, false)[0];
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $namaDesa = $this->clean($this->value($row, $columns['nama_desa'] ?? null));
                $kecamatan = $this->clean($this->value($row, $columns['kecamatan'] ?? null));
                $kodeDesa = $this->clean($this->value($row, $columns['kode_desa'] ?? null));

                if ($namaDesa === '' || is_numeric($namaDesa)) {
                    $skipped++;
                    continue;
                }

                $tahun = (int) ($this->option('year') ?: $this->parseNumeric($this->value($row, $columns['tahun'] ?? null)) ?: date('Y'));
                $skorKomposit = $this->parseScore($this->value($row, $columns['idm'] ?? ($columns['skor'] ?? null)));

                if ($skorKomposit <= 0) {
                    $skipped++;
                    continue;
                }

                $iks = $this->parseScore($this->value($row, $columns['iks'] ?? ($columns['sosial'] ?? null))) ?: $skorKomposit;
                $ike = $this->parseScore($this->value($row, $columns['ike'] ?? ($columns['ekonomi'] ?? null))) ?: $skorKomposit;
                $ikl = $this->parseScore($this->value($row, $columns['ikl'] ?? ($columns['lingkungan'] ?? null))) ?: $skorKomposit;

                $status = strtolower($this->clean($this->value($row, $columns['status'] ?? null)));
                if ($status === '' || is_numeric($status)) {
                    $status = $statusKlasifikasi->tentukanStatus($skorKomposit);
                } else {
                    $status = $this->normalizeStatus($status, $statusKlasifikasi->tentukanStatus($skorKomposit));
                }

                $desa = $this->findDesa($namaDesa, $kecamatan, $kodeDesa);
                if (!$desa) {
                    $skipped++;
                    continue;
                }

                DataIDM::updateOrCreate(
                    ['desa_id' => $desa->id, 'tahun' => $tahun],
                    [
                        'nama_desa' => $desa->nama_desa,
                        'kecamatan' => $desa->kecamatan,
                        'tahun' => $tahun,
                        'skor_iks' => $iks,
                        'skor_ike' => $ike,
                        'skor_ikl' => $ikl,
                        'skor_komposit' => $skorKomposit,
                        'status' => $status,
                        'verifikasi_status' => $verified ? 'verified' : 'menunggu',
                        'iks_detail' => $row,
                        'ike_detail' => $row,
                        'ikl_detail' => $row,
                    ]
                );

                $imported++;
            }
        }

        if ($sheetProcessed === 0) {
            $this->error('Header tidak ditemukan. Pastikan file berisi kolom NAMA DESA/DESA dan skor IDM.');
            return self::FAILURE;
        }

        $this->info("Import selesai. Sheet: {$sheetProcessed}, masuk: {$imported}, dilewati: {$skipped}.");
        return self::SUCCESS;
    }

    private function importDocx(string $file, StatusKlasifikasiService $statusKlasifikasi): int
    {
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            $this->error("Gagal membuka DOCX: {$file}");
            return self::FAILURE;
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) {
            $this->error('Isi DOCX tidak ditemukan.');
            return self::FAILURE;
        }

        $text = strip_tags(str_replace(['</w:tr>', '</w:p>'], [PHP_EOL, PHP_EOL], $xml));
        $cells = array_values(array_filter(array_map('trim', explode(PHP_EOL, $text)), fn ($value) => $value !== ''));

        $statusIndex = null;
        foreach ($cells as $index => $cell) {
            if (preg_match('/STATUS\s+IDM\s+(\d{4})/i', $cell, $matches)) {
                $statusIndex = $index;
                $yearFromHeader = (int) $matches[1];
                break;
            }
        }

        if ($statusIndex === null) {
            $this->error('Header STATUS IDM tahun tidak ditemukan di DOCX.');
            return self::FAILURE;
        }

        $tahun = (int) ($this->option('year') ?: ($yearFromHeader ?? date('Y')));
        $imported = 0;
        $skipped = 0;
        $verified = $this->option('verify');

        for ($index = $statusIndex + 1; $index + 12 < count($cells); $index += 13) {
            $chunk = array_slice($cells, $index, 13);
            if (!isset($chunk[0], $chunk[2], $chunk[4], $chunk[6], $chunk[7], $chunk[8], $chunk[9], $chunk[10], $chunk[11], $chunk[12])) {
                $skipped++;
                continue;
            }

            if (!is_numeric($chunk[0]) || !is_numeric($chunk[2]) || !is_numeric($chunk[4]) || !is_numeric($chunk[6])) {
                $skipped++;
                continue;
            }

            $kecamatan = $this->clean($chunk[5]);
            $kodeDesa = $this->clean($chunk[6]);
            $namaDesa = $this->clean($chunk[7]);
            $iks = $this->parseScore($chunk[8]);
            $ike = $this->parseScore($chunk[9]);
            $ikl = $this->parseScore($chunk[10]);
            $skorKomposit = $this->parseScore($chunk[11]);

            if ($namaDesa === '' || $skorKomposit <= 0) {
                $skipped++;
                continue;
            }

            $desa = $this->findDesa($namaDesa, $kecamatan, $kodeDesa);
            if (!$desa) {
                $skipped++;
                continue;
            }

            $fallbackStatus = $statusKlasifikasi->tentukanStatus($skorKomposit);
            $status = $this->normalizeStatus($this->clean($chunk[12]), $fallbackStatus);

            DataIDM::updateOrCreate(
                ['desa_id' => $desa->id, 'tahun' => $tahun],
                [
                    'nama_desa' => $desa->nama_desa,
                    'kecamatan' => $desa->kecamatan,
                    'tahun' => $tahun,
                    'skor_iks' => $iks,
                    'skor_ike' => $ike,
                    'skor_ikl' => $ikl,
                    'skor_komposit' => $skorKomposit,
                    'status' => $status,
                    'verifikasi_status' => $verified ? 'verified' : 'menunggu',
                    'iks_detail' => $chunk,
                    'ike_detail' => $chunk,
                    'ikl_detail' => $chunk,
                ]
            );

            $imported++;
        }

        $this->info("Import DOCX selesai. Masuk: {$imported}, dilewati: {$skipped}.");
        return self::SUCCESS;
    }

    private function detectHeaderRow($sheet, int $highestRow, string $highestColumn): ?int
    {
        for ($rowNumber = 1; $rowNumber <= min($highestRow, 15); $rowNumber++) {
            $row = $sheet->rangeToArray("A{$rowNumber}:{$highestColumn}{$rowNumber}", null, true, false)[0];
            $text = strtoupper(implode(' ', array_map(fn ($value) => trim((string) $value), $row)));
            if ((str_contains($text, 'NAMA DESA') || preg_match('/\bDESA\b/', $text)) && (str_contains($text, 'IDM') || str_contains($text, 'SKOR'))) {
                return $rowNumber;
            }

            if (str_contains($text, 'NO') && str_contains($text, 'KECAMATAN') && str_contains($text, 'DIMENSI')) {
                return $rowNumber;
            }
        }

        return null;
    }

    private function mapColumns(array $headers): array
    {
        $columns = [];
        foreach ($headers as $index => $header) {
            $key = strtoupper(preg_replace('/\s+/', ' ', trim((string) $header)));

            if ($key === 'NAMA DESA' || $key === 'DESA') {
                $columns['nama_desa'] = $index;
            } elseif ($key === 'NAMA KECAMATAN' || $key === 'KECAMATAN') {
                $columns['kecamatan'] = $index;
            } elseif ($key === 'KODE DESA') {
                $columns['kode_desa'] = $index;
            } elseif ($key === 'TAHUN') {
                $columns['tahun'] = $index;
            } elseif ($key === 'IKS') {
                $columns['iks'] = $index;
            } elseif ($key === 'IKE') {
                $columns['ike'] = $index;
            } elseif ($key === 'IKL') {
                $columns['ikl'] = $index;
            } elseif ($key === 'IDM') {
                $columns['idm'] = $index;
            } elseif ($key === 'SKOR') {
                $columns['skor'] = $index;
            } elseif (str_contains($key, 'STATUS') && (str_contains($key, 'IDM') || str_contains($key, 'INDEKS'))) {
                $columns['status'] = $index;
            } elseif (str_contains($key, 'SOSIAL')) {
                $columns['sosial'] = $index;
            } elseif (str_contains($key, 'EKONOMI')) {
                $columns['ekonomi'] = $index;
            } elseif (str_contains($key, 'LINGKUNGAN')) {
                $columns['lingkungan'] = $index;
            }
        }

        $normalized = array_map(fn ($header) => strtoupper(preg_replace('/\s+/', ' ', trim((string) $header))), $headers);
        if (!isset($columns['nama_desa']) && (($normalized[0] ?? '') === 'NO') && (($normalized[1] ?? '') === 'KECAMATAN')) {
            $columns['kecamatan'] = 1;
            $columns['nama_desa'] = 2;
            $columns['sosial'] = 4;
            $columns['ekonomi'] = 5;
            $columns['lingkungan'] = 6;
            $columns['skor'] = 9;
            $columns['status'] = 10;
        }

        return $columns;
    }

    private function findDesa(string $namaDesa, string $kecamatan, string $kodeDesa): ?Desa
    {
        if ($kodeDesa !== '') {
            $desa = Desa::where('kode_desa', $kodeDesa)->first();
            if ($desa) {
                return $desa;
            }
        }

        $query = Desa::where('nama_desa', 'LIKE', '%' . $namaDesa . '%');
        if ($kecamatan !== '') {
            $query->where('kecamatan', 'LIKE', '%' . $kecamatan . '%');
        }

        return $query->first() ?: Desa::where('nama_desa', 'LIKE', '%' . $namaDesa . '%')->first();
    }

    private function parseScore($value): float
    {
        $number = $this->parseNumeric($value);
        if ($number > 1) {
            $number = $number / 100;
        }

        return round(max(0, min(1, $number)), 4);
    }

    private function parseNumeric($value): float
    {
        $cleaned = str_replace(["'", ','], ['', '.'], (string) $value);
        $cleaned = preg_replace('/[^0-9.\-]/', '', $cleaned);

        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    private function normalizeStatus(string $status, string $fallback): string
    {
        $status = str_replace(['!', '1'], ['i', 'i'], strtolower($status));
        if (str_contains($status, 'mandir')) {
            return 'mandiri';
        }
        if (str_contains($status, 'maju')) {
            return 'maju';
        }
        if (str_contains($status, 'berkembang')) {
            return 'berkembang';
        }
        if (str_contains($status, 'tertinggal')) {
            return 'tertinggal';
        }

        return $fallback;
    }

    private function value(array $row, ?int $index)
    {
        return $index === null ? null : ($row[$index] ?? null);
    }

    private function clean($value): string
    {
        return trim(str_replace(["'", '"'], '', (string) $value));
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
    }
}
