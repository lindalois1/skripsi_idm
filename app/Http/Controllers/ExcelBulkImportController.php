<?php

namespace App\Http\Controllers;

use App\Models\DataIDM;
use App\Models\Desa;
use App\Models\RiwayatUpload;
use App\Services\StatusKlasifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelBulkImportController extends Controller
{
    private StatusKlasifikasiService $statusKlasifikasi;

    public function __construct()
    {
        $this->statusKlasifikasi = new StatusKlasifikasiService();
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls|max:5120',
            ]);

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $storedPath = $file->store('uploads/excel', 'public');

            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel kosong atau tidak ada data.'
                ], 400);
            }

            $header = $rows[0];
            $imported = 0;
            $skipped = 0;
            $errors = [];
            $firstRecord = null;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $data = $this->mapExcelData($header, $row);
                if (!$data || empty($data['nama_desa']) || $data['nama_desa'] === '-') {
                    $skipped++;
                    continue;
                }

                if ($firstRecord === null) {
                    $firstRecord = [
                        'nama_desa' => $data['nama_desa'],
                        'kecamatan' => $data['kecamatan'],
                        'tahun' => $data['tahun'],
                        'iks' => $data['iks'],
                        'ike' => $data['ike'],
                        'ikl' => $data['ikl'],
                    ];
                }

                $desa = $this->findDesa($data['nama_desa'], $data['kecamatan'], $data['kode_desa']);
                if (!$desa) {
                    $desa = Desa::create([
                        'nama_desa' => $data['nama_desa'],
                        'kecamatan' => $data['kecamatan'] ?? '-',
                        'kode_desa' => $data['kode_desa'] ?? '-',
                    ]);
                }

                $tahun = (int) ($data['tahun'] ?? date('Y'));
                $iks = (float) ($data['iks'] ?? 0);
                $ike = (float) ($data['ike'] ?? 0);
                $ikl = (float) ($data['ikl'] ?? 0);
                $komposit = round(($iks + $ike + $ikl) / 3, 4);
                $status = $this->statusKlasifikasi->tentukanStatus($komposit);
                $existing = DataIDM::where('desa_id', $desa->id)
                    ->where('tahun', $tahun)
                    ->first();

                if ($existing && $existing->verifikasi_status !== 'revisi') {
                    $skipped++;
                    $errors[] = "Data {$desa->nama_desa} tahun {$tahun} sudah ada.";
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
                        'skor_komposit' => $komposit,
                        'status' => $status,
                        'verifikasi_status' => 'menunggu',
                        'user_id' => Auth::id(),
                        'iks_detail' => json_encode($row),
                        'ike_detail' => json_encode($row),
                        'ikl_detail' => json_encode($row),
                    ]
                );

                $imported++;
            }

            if ($imported === 0) {
                if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                    Storage::disk('public')->delete($storedPath);
                }
                $errorMessage = !empty($errors) ? implode(' ', $errors) : 'Semua data desa dan tahun yang diupload sudah ada di database.';
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal upload: ' . $errorMessage
                ], 422);
            }

            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');
            $uploadYear = (int) ($firstRecord['tahun'] ?? $now->year);
            RiwayatUpload::create([
                'user_id' => $user?->id,
                'desa_id' => $user?->desa_id,
                'nama_file' => $originalName,
                'file_path' => $storedPath,
                'ukuran' => $file->getSize(),
                'tahun' => $uploadYear,
                'status' => 'menunggu',
                'keterangan' => 'File Excel diupload dan menunggu verifikasi kecamatan.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Import Excel selesai.',
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'detail' => $firstRecord,
                'download_url' => $storedPath ? asset('storage/' . $storedPath) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk Excel Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor file: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->every(fn($value) => trim((string) $value) === '');
    }

    private function mapExcelData($header, $dataRow)
    {
        $indices = [
            'nama_desa' => $this->findColumnIndex($header, ['NAMA DESA', 'nama_desa', 'Desa', 'Nama Desa']),
            'kode_desa' => $this->findColumnIndex($header, ['KODE DESA', 'kode_desa', 'Kode Desa']),
            'kecamatan' => $this->findColumnIndex($header, ['NAMA KECAMATAN', 'kecamatan', 'Kecamatan', 'Nama Kecamatan']),
            'tahun' => $this->findColumnIndex($header, ['TAHUN', 'tahun', 'Tahun']),
            'iks' => $this->findColumnIndex($header, ['IKS', 'skor_iks', 'Skor IKS']),
            'ike' => $this->findColumnIndex($header, ['IKE', 'skor_ike', 'Skor IKE']),
            'ikl' => $this->findColumnIndex($header, ['IKL', 'skor_ikl', 'Skor IKL']),
        ];

        return [
            'nama_desa' => isset($indices['nama_desa']) ? $this->cleanValue($dataRow[$indices['nama_desa']] ?? '-') : '-',
            'kode_desa' => isset($indices['kode_desa']) ? $this->cleanValue($dataRow[$indices['kode_desa']] ?? '-') : '-',
            'kecamatan' => isset($indices['kecamatan']) ? $this->cleanValue($dataRow[$indices['kecamatan']] ?? '-') : '-',
            'tahun' => isset($indices['tahun']) ? (int) $this->cleanValue($dataRow[$indices['tahun']] ?? date('Y')) : date('Y'),
            'iks' => isset($indices['iks']) ? $this->parseNumeric($dataRow[$indices['iks']] ?? 0) : 0,
            'ike' => isset($indices['ike']) ? $this->parseNumeric($dataRow[$indices['ike']] ?? 0) : 0,
            'ikl' => isset($indices['ikl']) ? $this->parseNumeric($dataRow[$indices['ikl']] ?? 0) : 0,
        ];
    }

    private function findColumnIndex($header, $names)
    {
        foreach ($header as $index => $value) {
            $cleanValue = trim(strtoupper((string) $value));
            foreach ($names as $name) {
                if ($cleanValue === strtoupper($name)) {
                    return $index;
                }
            }
        }

        return null;
    }

    private function cleanValue($value)
    {
        if (is_null($value)) {
            return '-';
        }

        $value = trim((string) $value);
        $value = str_replace("'", '', $value);
        $value = str_replace('"', '', $value);

        return $value;
    }

    private function parseNumeric($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $cleaned = str_replace("'", '', (string) $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    private function findDesa($namaDesa, $kecamatan, $kodeDesa)
    {
        if ($kodeDesa && $kodeDesa !== '-') {
            $desa = Desa::where('kode_desa', $kodeDesa)->first();
            if ($desa) {
                return $desa;
            }
        }

        if ($namaDesa && $namaDesa !== '-') {
            $query = Desa::where('nama_desa', 'LIKE', '%' . $namaDesa . '%');
            if ($kecamatan && $kecamatan !== '-') {
                $query->where('kecamatan', 'LIKE', '%' . $kecamatan . '%');
            }

            return $query->first();
        }

        return null;
    }
}
