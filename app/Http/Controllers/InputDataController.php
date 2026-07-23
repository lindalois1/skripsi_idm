<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataIDM;
use App\Models\Desa;
use App\Models\RiwayatUpload;
use App\Services\StatusKlasifikasiService;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InputDataController extends Controller
{
    private StatusKlasifikasiService $statusKlasifikasi;

    public function __construct()
    {
        $this->statusKlasifikasi = new StatusKlasifikasiService();
    }

    /**
     * Tampilan halaman input data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Jika user role desa, ambil desa_id dari relasi
        $desaId = null;
        if ($user->isDesa() && $user->desa_id) {
            $desaId = $user->desa_id;
        }
        
        $tahunFilter = $request->input('tahun', 'semua');

        // Cari data IDM tahun terpilih
        $dataIdm = null;
        if ($desaId && $tahunFilter !== 'semua') {
            $dataIdm = DataIDM::where('desa_id', $desaId)
                              ->where('tahun', (int) $tahunFilter)
                              ->first();
        }

        $pengajuanQuery = DataIDM::with('desa')
            ->when($desaId, fn ($q) => $q->where('desa_id', $desaId))
            ->orderBy('tahun', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $pengajuanQuery->where('tahun', (int) $request->tahun);
        }

        $dataPengajuan = $pengajuanQuery->paginate(10)->withQueryString();
        $tahunList = collect([2021, 2022, 2023, 2024, 2025]);
        
        // Decode JSON detail
        $iksData = $dataIdm ? ($dataIdm->iks_detail ?? []) : [];
        $ikeData = $dataIdm ? ($dataIdm->ike_detail ?? []) : [];
        $iklData = $dataIdm ? ($dataIdm->ikl_detail ?? []) : [];
        
        return view('dashboard.input', compact('dataIdm', 'iksData', 'ikeData', 'iklData', 'dataPengajuan', 'tahunList', 'tahunFilter'));
    }

    /**
     * Update data IDM (hanya boleh jika belum verified)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $data = DataIDM::findOrFail($id);

        if ($data->desa_id !== $user->desa_id) {
            return redirect()->route('input.data')->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak diizinkan mengedit data ini.'
            ]);
        }

        if (in_array($data->verifikasi_status, ['verified', 'disetujui'])) {
            return redirect()->route('input.data')->with('toast', [
                'type' => 'error',
                'message' => 'Data yang sudah terverifikasi tidak dapat diedit.'
            ]);
        }

        $request->validate([
            'tahun'    => 'required|integer|min:2000|max:2100',
            'skor_iks' => 'required|numeric|min:0|max:1',
            'skor_ike' => 'required|numeric|min:0|max:1',
            'skor_ikl' => 'required|numeric|min:0|max:1',
        ]);

        $skor_komposit = round(
            ((float)$request->skor_iks + (float)$request->skor_ike + (float)$request->skor_ikl) / 3, 4
        );

        $data->update([
            'tahun'             => $request->tahun,
            'skor_iks'          => $request->skor_iks,
            'skor_ike'          => $request->skor_ike,
            'skor_ikl'          => $request->skor_ikl,
            'skor_komposit'     => $skor_komposit,
            'status'            => $this->tentukanStatus($skor_komposit),
            'verifikasi_status' => 'menunggu',
        ]);

        return redirect()->route('input.data')->with('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui dan dikirim ulang untuk verifikasi.'
        ]);
    }

    /**
     * Hapus data IDM (hanya boleh jika belum verified)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $data = DataIDM::findOrFail($id);

        if ($data->desa_id !== $user->desa_id) {
            return redirect()->route('input.data')->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak diizinkan menghapus data ini.'
            ]);
        }

        if (in_array($data->verifikasi_status, ['verified', 'disetujui'])) {
            return redirect()->route('input.data')->with('toast', [
                'type' => 'error',
                'message' => 'Data yang sudah terverifikasi tidak dapat dihapus.'
            ]);
        }

        $data->delete();

        return redirect()->route('input.data')->with('toast', [
            'type' => 'success',
            'message' => 'Data IDM berhasil dihapus.'
        ]);
    }

    /**
     * Upload file Excel
     */
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls|max:5120'
            ]);

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel kosong atau tidak ada data'
                ], 400);
            }

            // Ambil header
            $header = $rows[0];
            $dataRow = $rows[1];

            // Mapping data
            $data = $this->mapExcelData($header, $dataRow);

            // Cek apakah data ditemukan
            if (!$data || !isset($data['nama_desa']) || $data['nama_desa'] == '-') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data desa tidak ditemukan dalam file. Pastikan file menggunakan template yang benar.'
                ], 400);
            }

            // Cari desa di database
            $desa = $this->findDesa($data['nama_desa'], $data['kecamatan'], $data['kode_desa']);

            return response()->json([
                'success' => true,
                'data' => $data,
                'desa' => $desa ? [
                    'id' => $desa->id,
                    'nama_desa' => $desa->nama_desa,
                    'kecamatan' => $desa->kecamatan ?? $data['kecamatan'],
                ] : null,
                'tahun' => $data['tahun'] ?? date('Y'),
                'message' => 'File Excel berhasil diekstrak'
            ]);

        } catch (\Exception $e) {
            Log::error('Upload Excel Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapping data dari Excel ke array
     */
    private function mapExcelData($header, $dataRow)
    {
        // Cari indeks kolom berdasarkan header
        $indices = [
            'nama_desa' => $this->findColumnIndex($header, ['NAMA DESA', 'nama_desa', 'Desa', 'Nama Desa']),
            'kode_desa' => $this->findColumnIndex($header, ['KODE DESA', 'kode_desa', 'Kode Desa']),
            'kecamatan' => $this->findColumnIndex($header, ['NAMA KECAMATAN', 'kecamatan', 'Kecamatan', 'Nama Kecamatan']),
            'tahun'     => $this->findColumnIndex($header, ['TAHUN', 'tahun', 'Tahun']),
            'iks'       => $this->findColumnIndex($header, ['IKS', 'skor_iks', 'Skor IKS']),
            'ike'       => $this->findColumnIndex($header, ['IKE', 'skor_ike', 'Skor IKE']),
            'ikl'       => $this->findColumnIndex($header, ['IKL', 'skor_ikl', 'Skor IKL']),
            'status'    => $this->findColumnIndex($header, ['STATUS IDM', 'status', 'Status IDM', 'STATUS']),
        ];

        // Ambil nilai dari setiap kolom
        $result = [
            'nama_desa' => isset($indices['nama_desa']) ? $this->cleanValue($dataRow[$indices['nama_desa']] ?? '-') : '-',
            'kode_desa' => isset($indices['kode_desa']) ? $this->cleanValue($dataRow[$indices['kode_desa']] ?? '-') : '-',
            'kecamatan' => isset($indices['kecamatan']) ? $this->cleanValue($dataRow[$indices['kecamatan']] ?? '-') : '-',
            'tahun'     => isset($indices['tahun']) ? (int) $this->cleanValue($dataRow[$indices['tahun']] ?? date('Y')) : date('Y'),
            'iks'       => isset($indices['iks']) ? $this->parseNumeric($dataRow[$indices['iks']] ?? 0) : 0,
            'ike'       => isset($indices['ike']) ? $this->parseNumeric($dataRow[$indices['ike']] ?? 0) : 0,
            'ikl'       => isset($indices['ikl']) ? $this->parseNumeric($dataRow[$indices['ikl']] ?? 0) : 0,
            'status'    => isset($indices['status']) ? strtolower($this->cleanValue($dataRow[$indices['status']] ?? 'tertinggal')) : 'tertinggal',
        ];

        return $result;
    }

    /**
     * Cari indeks kolom berdasarkan nama
     */
    private function findColumnIndex($header, $names)
    {
        foreach ($header as $index => $value) {
            $cleanValue = trim(strtoupper((string)$value));
            foreach ($names as $name) {
                if ($cleanValue == strtoupper($name)) {
                    return $index;
                }
            }
        }
        return null;
    }

    /**
     * Bersihkan nilai dari petik dan spasi
     */
    private function cleanValue($value)
    {
        if (is_null($value)) return '-';
        $value = trim((string)$value);
        $value = str_replace("'", "", $value);
        $value = str_replace('"', "", $value);
        return $value;
    }

    /**
     * Parse nilai numerik
     */
    private function parseNumeric($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        $cleaned = str_replace("'", "", (string)$value);
        $cleaned = str_replace(',', '.', $cleaned);
        if (is_numeric($cleaned)) {
            return (float) $cleaned;
        }
        return 0;
    }

    /**
     * Cari desa di database
     */
    private function findDesa($namaDesa, $kecamatan, $kodeDesa)
    {
        // Cari berdasarkan kode desa
        if ($kodeDesa && $kodeDesa != '-') {
            $desa = Desa::where('kode_desa', $kodeDesa)->first();
            if ($desa) return $desa;
        }

        // Cari berdasarkan nama dan kecamatan
        if ($namaDesa && $namaDesa != '-') {
            $query = Desa::where('nama_desa', 'LIKE', '%' . $namaDesa . '%');
            if ($kecamatan && $kecamatan != '-') {
                $query->where('kecamatan', 'LIKE', '%' . $kecamatan . '%');
            }
            return $query->first();
        }

        // Cari berdasarkan user yang login
        $user = Auth::user();
        if ($user && $user->desa_id) {
            return Desa::find($user->desa_id);
        }

        return null;
    }

    /**
     * Simpan data IDM (dari form atau dari upload Excel)
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validasi
            $request->validate([
                'iks'    => 'required|numeric|min:0|max:1',
                'ike'    => 'required|numeric|min:0|max:1',
                'ikl'    => 'required|numeric|min:0|max:1',
                'status' => 'required|string',
            ]);

            // Ambil data dari request
            $namaDesa  = $request->input('nama_desa', '');
            $kecamatan = $request->input('kecamatan', '');
            $tahun     = $request->input('tahun', date('Y'));
            $iks       = (float) $request->iks;
            $ike       = (float) $request->ike;
            $ikl       = (float) $request->ikl;
            $status    = strtolower($request->status);

            // Cari desa_id dengan PRIORITAS
            $desaId = null;
            
            // PRIORITAS 1: Dari user
            if ($user->desa_id) {
                $desaId = $user->desa_id;
                $desa = Desa::find($desaId);
                if ($desa) {
                    $namaDesa  = $desa->nama_desa;
                    $kecamatan = $desa->kecamatan;
                }
            }
            
            // PRIORITAS 2: Cari berdasarkan nama desa
            if (!$desaId && $namaDesa && $namaDesa != '-') {
                $desa = Desa::where('nama_desa', 'LIKE', '%' . $namaDesa . '%')->first();
                if ($desa) {
                    $desaId    = $desa->id;
                    $namaDesa  = $desa->nama_desa;
                    $kecamatan = $desa->kecamatan;
                }
            }
            
            // PRIORITAS 3: Cari berdasarkan user_id di tabel desas
            if (!$desaId) {
                $desa = Desa::where('user_id', $user->id)->first();
                if ($desa) {
                    $desaId    = $desa->id;
                    $namaDesa  = $desa->nama_desa;
                    $kecamatan = $desa->kecamatan;
                }
            }
            
            // PRIORITAS 4: Buat desa baru jika tidak ditemukan
            if (!$desaId) {
                $desa = Desa::create([
                    'nama_desa' => $namaDesa != '-' ? $namaDesa : 'Desa ' . $user->name,
                    'kecamatan' => $kecamatan != '-' ? $kecamatan : 'Belum Diisi',
                    'user_id'   => $user->id,
                ]);
                $desaId    = $desa->id;
                $namaDesa  = $desa->nama_desa;
                $kecamatan = $desa->kecamatan;
            }

            // Hitung skor komposit
            $skorKomposit = round(($iks + $ike + $ikl) / 3, 4);

            // Cari data existing
            $existing = DataIDM::where('desa_id', $desaId)
                               ->where('tahun', (int) $tahun)
                               ->first();

            if ($existing && $existing->verifikasi_status !== 'revisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data IDM tahun ' . $tahun . ' untuk desa ini sudah ada. Jika perlu perubahan, minta kecamatan memberi status revisi terlebih dahulu.'
                ], 422);
            }

            $dataToSave = [
                'desa_id'           => $desaId,
                'nama_desa'         => $namaDesa,
                'kecamatan'         => $kecamatan,
                'tahun'             => (int) $tahun,
                'skor_iks'          => $iks,
                'skor_ike'          => $ike,
                'skor_ikl'          => $ikl,
                'skor_komposit'     => $skorKomposit,
                'status'            => $status,
                'verifikasi_status' => 'menunggu',
                'user_id'           => $user->id,
            ];

            if ($existing) {
                $existing->update($dataToSave);
                $dataIdm = $existing;
            } else {
                $dataIdm = DataIDM::create($dataToSave);
            }

            // Simpan riwayat upload
            RiwayatUpload::create([
                'user_id'    => $user->id,
                'desa_id'    => $desaId,
                'nama_file'  => 'Upload IDM - ' . $namaDesa . ' ' . $tahun,
                'tahun'      => $tahun,
                'status'     => 'menunggu',
                'keterangan' => 'Data IDM ' . $namaDesa . ' tahun ' . $tahun . ' berhasil disimpan',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data IDM berhasil disimpan',
                'data'    => $dataIdm,
                'desa_id' => $desaId
            ]);

        } catch (\Exception $e) {
            Log::error('Error menyimpan data IDM: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header template (17 kolom sesuai yang diminta sistem)
            $headers = [
                'nama_desa', 'tahun', 'kode_desa', 'kecamatan', 
                'posyandu_aktif', 'tenaga_kesehatan', 'gotong_royong', 
                'paud_tk', 'keragaman_produksi', 'akses_pasar', 
                'umkm_binaan', 'kualitas_lingkungan', 'risiko_bencana', 
                'skor_iks', 'skor_ike', 'skor_ikl', 'status'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            // Contoh data
            $example = [
                'Desa Contoh', '2025', '3200000001', 'Kecamatan Contoh',
                '2', 'tersedia', '4', 'tersedia', 'tinggi', 'mudah',
                '15', 'baik', 'rendah', '0.7500', '0.6800', '0.7200', 'berkembang'
            ];

            $col = 'A';
            foreach ($example as $value) {
                $sheet->setCellValue($col . '2', $value);
                $col++;
            }

            // Bold header
            $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            return response()->stream(function() use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="template_idm.xlsx"',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate template: ' . $e->getMessage());
        }
    }

    /**
     * Method helper untuk menghitung IKS
     */
    private function hitungIKS($request)
    {
        $skor = 0;
        if ($request->posyandu_aktif > 0) $skor += 0.25;
        if ($request->tenaga_kesehatan == 'tersedia') $skor += 0.25;
        if ($request->gotong_royong >= 4) $skor += 0.25;
        if ($request->paud_tk == 'tersedia') $skor += 0.25;
        return round($skor, 4);
    }

    /**
     * Method helper untuk menghitung IKE
     */
    private function hitungIKE($request)
    {
        $skor = 0;
        if ($request->keragaman_produksi == 'tinggi') $skor += 0.4;
        elseif ($request->keragaman_produksi == 'sedang') $skor += 0.2;
        if ($request->akses_pasar == 'mudah') $skor += 0.3;
        if ($request->umkm_binaan >= 10) $skor += 0.3;
        elseif ($request->umkm_binaan >= 5) $skor += 0.15;
        return round($skor, 4);
    }

    /**
     * Method helper untuk menghitung IKL
     */
    private function hitungIKL($request)
    {
        $skor = 0.5;
        if ($request->kualitas_lingkungan == 'baik') $skor += 0.25;
        elseif ($request->kualitas_lingkungan == 'sedang') $skor += 0.1;
        if ($request->risiko_bencana == 'rendah') $skor += 0.25;
        return round($skor, 4);
    }

    /**
     * Method helper untuk menentukan status
     */
    private function tentukanStatus($skor)
    {
        return $this->statusKlasifikasi->tentukanStatus($skor);
    }
}
