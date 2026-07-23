<?php

namespace App\Imports;

use App\Models\DataIDM;
use App\Models\Desa;
use App\Services\StatusKlasifikasiService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;

class IDMImport implements ToModel, WithHeadingRow, WithStartRow
{
    protected $desaId;
    private StatusKlasifikasiService $statusKlasifikasi;
    
    public function __construct($desaId = null)
    {
        $this->desaId = $desaId;
        $this->statusKlasifikasi = new StatusKlasifikasiService();
    }
    
    public function startRow(): int
    {
        return 3;
    }
    
    public function model(array $row)
    {
        // Mapping kolom Excel
        $namaDesa = $row['nama_desa'] ?? $row['NAMA DESA'] ?? null;
        $tahun = $row['tahun'] ?? $row['TAHUN'] ?? date('Y');
        
        // Cari atau buat desa
        $desa = null;
        if ($namaDesa) {
            $desa = Desa::firstOrCreate(
                ['nama_desa' => $namaDesa],
                [
                    'kode_desa' => $row['kode_desa'] ?? $row['KODE DESA'] ?? '-',
                    'kecamatan' => $row['nama_kecamatan'] ?? $row['NAMA KECAMATAN'] ?? '-',
                    'kabupaten' => $row['nama_kabupaten'] ?? $row['NAMA KABUPATEN'] ?? 'INDRAMAYU',
                    'provinsi' => $row['nama_provinsi'] ?? $row['NAMA PROVINSI'] ?? 'JAWA BARAT',
                ]
            );
        } elseif ($this->desaId) {
            $desa = Desa::find($this->desaId);
        }
        
        if (!$desa) {
            return null;
        }
        
        // Ambil skor dari Excel atau hitung
        $iks = $this->getValue($row, ['iks', 'skor_iks', 'IKS', 'IKS 2022']);
        $ike = $this->getValue($row, ['ike', 'skor_ike', 'IKE', 'IKE 2022']);
        $ikl = $this->getValue($row, ['ikl', 'skor_ikl', 'IKL', 'IKL 2022']);
        $komposit = $this->getValue($row, ['idm', 'skor_komposit', 'NILAI IDM', 'IDM']);
        
        // Jika skor tidak ada, hitung dari item-item
        if (!$iks) $iks = $this->hitungIKS($row);
        if (!$ike) $ike = $this->hitungIKE($row);
        if (!$ikl) $ikl = $this->hitungIKL($row);
        if (!$komposit) $komposit = round(($iks + $ike + $ikl) / 3, 4);
        
        $status = $this->getValue($row, ['status', 'status_idm', 'STATUS IDM', 'STATUS']);
        if (!$status) $status = $this->tentukanStatus($komposit);
        
        return DataIDM::updateOrCreate(
            ['desa_id' => $desa->id, 'tahun' => $tahun],
            [
                'skor_iks' => $iks,
                'skor_ike' => $ike,
                'skor_ikl' => $ikl,
                'skor_komposit' => $komposit,
                'status' => strtolower($status),
                'verifikasi_status' => 'disetujui',
                'iks_detail' => json_encode($row),
                'ike_detail' => json_encode($row),
                'ikl_detail' => json_encode($row),
            ]
        );
    }
    
    private function getValue($row, $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && !empty($row[$key])) {
                $value = $row[$key];
                if (is_numeric($value)) {
                    return floatval($value);
                }
                return $value;
            }
        }
        return null;
    }
    
    private function hitungIKS($row)
    {
        $skor = 0;
        $kesehatan = $this->getValue($row, ['kesehatan', 'KESEHATAN']) ?? 0;
        $pendidikan = $this->getValue($row, ['pendidikan', 'PENDIDIKAN']) ?? 0;
        $modalSosial = $this->getValue($row, ['modal_sosial', 'MODAL SOSIAL']) ?? 0;
        
        if (is_numeric($kesehatan)) $skor += $kesehatan / 100;
        if (is_numeric($pendidikan)) $skor += $pendidikan / 100;
        if (is_numeric($modalSosial)) $skor += $modalSosial / 100;
        
        return round(min($skor, 1), 4);
    }
    
    private function hitungIKE($row)
    {
        $skor = 0;
        $keragaman = $this->getValue($row, ['keragaman_produksi', 'KERAGAMAN PRODUKSI']) ?? 0;
        $perdagangan = $this->getValue($row, ['perdagangan', 'PERDAGANGAN']) ?? 0;
        $akses = $this->getValue($row, ['akses_distribusi', 'AKSES DISTRIBUSI']) ?? 0;
        
        if (is_numeric($keragaman)) $skor += $keragaman / 100;
        if (is_numeric($perdagangan)) $skor += $perdagangan / 100;
        if (is_numeric($akses)) $skor += $akses / 100;
        
        return round(min($skor, 1), 4);
    }
    
    private function hitungIKL($row)
    {
        $skor = 0.5;
        $kualitas = $this->getValue($row, ['kualitas_lingkungan', 'KUALITAS LINGKUNGAN']) ?? 0;
        $bencana = $this->getValue($row, ['potensi_bencana', 'POTENSI BENCANA']) ?? 0;
        
        if (is_numeric($kualitas)) $skor += $kualitas / 100;
        if (is_numeric($bencana)) $skor += $bencana / 100;
        
        return round(min($skor, 1), 4);
    }
    
    private function tentukanStatus($skor)
    {
        return $this->statusKlasifikasi->tentukanStatus($skor);
    }
}