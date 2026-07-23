<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelBulkImportTest extends TestCase
{
    public function test_bulk_import_marks_data_as_pending_until_kecamatan_verifies()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'desa-tester-' . uniqid(),
            'role' => 'desa',
            'is_active' => true,
        ]);
        $file = $this->createExcelFile();

        $response = $this->actingAs($user)
            ->postJson(route('upload.excel.bulk'), [
                'file' => $file,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('riwayat_uploads', [
            'user_id' => $user->id,
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('data_idm_desa', [
            'nama_desa' => 'Desa A',
            'verifikasi_status' => 'menunggu',
        ]);
    }

    private function createExcelFile(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['NAMA DESA', 'KECAMATAN', 'TAHUN', 'IKS', 'IKE', 'IKL'],
            ['Desa A', 'Cigugur', 2026, 0.8, 0.75, 0.7],
        ], null, 'A1');

        $tempPath = tempnam(sys_get_temp_dir(), 'excel');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return new UploadedFile(
            $tempPath,
            'sample.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }
}
