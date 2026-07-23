<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelPreviewController extends Controller
{
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls|max:5120',
            ]);

            $file = $request->file('file');
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
            $previewRows = [];
            $limit = min(10, count($rows) - 1);

            for ($i = 1; $i <= $limit; $i++) {
                $row = $rows[$i];
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $previewRows[] = [
                    'nama_desa' => $this->getValue($header, $row, ['NAMA DESA', 'nama_desa', 'Desa', 'Nama Desa']),
                    'kecamatan' => $this->getValue($header, $row, ['NAMA KECAMATAN', 'kecamatan', 'Kecamatan', 'Nama Kecamatan']),
                    'tahun' => $this->getValue($header, $row, ['TAHUN', 'tahun', 'Tahun']),
                    'iks' => $this->getValue($header, $row, ['IKS', 'skor_iks', 'Skor IKS']),
                    'ike' => $this->getValue($header, $row, ['IKE', 'skor_ike', 'Skor IKE']),
                    'ikl' => $this->getValue($header, $row, ['IKL', 'skor_ikl', 'Skor IKL']),
                ];
            }

            return response()->json([
                'success' => true,
                'preview' => $previewRows,
                'total_rows' => count($rows) - 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mempreview file: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->every(fn($value) => trim((string) $value) === '');
    }

    private function getValue(array $header, array $row, array $names)
    {
        foreach ($header as $index => $value) {
            $cleanValue = trim(strtoupper((string) $value));
            foreach ($names as $name) {
                if ($cleanValue === strtoupper($name)) {
                    return $row[$index] ?? '-';
                }
            }
        }

        return '-';
    }
}
