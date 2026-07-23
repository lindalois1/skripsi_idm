<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataIDM;
use App\Models\Desa;
use App\Models\RiwayatUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Halaman Beranda
     */
    public function beranda()
    {
        $user = Auth::user();
        $desaId = $user->desa_id ?? null;
        $role = $user->role ?? 'desa';

        if ($role === 'super_admin') {
            return redirect()->route('super.beranda');
        }

        if ($role === 'kabupaten') {
            return $this->dashboardKabupaten();
        }

        if ($role === 'kecamatan') {
            return $this->dashboardKecamatan();
        }
        
        // ============================================
        // CEK APAKAH TABEL ADA
        // ============================================
        $tableDesaExists = Schema::hasTable('desas');
        $tableDataIdmExists = Schema::hasTable('data_idm_desa');
        $tableRiwayatExists = Schema::hasTable('riwayat_uploads');
        
        // ============================================
        // 1. STATISTIK TOTAL UPLOAD
        // ============================================
        $totalUpload = 0;
        $totalMenunggu = 0;
        $totalDisetujui = 0;
        $totalDitolak = 0;
        
        if ($tableRiwayatExists) {
            $totalUpload = RiwayatUpload::count();
            $totalMenunggu = RiwayatUpload::where('status', 'menunggu')->count();
            $totalDisetujui = RiwayatUpload::where('status', 'disetujui')->count();
            $totalDitolak = RiwayatUpload::where('status', 'revisi')->count();
            
            // Filter berdasarkan role
            if ($role == 'kecamatan' && $user->kecamatan_id) {
                $totalUpload = RiwayatUpload::whereHas('desa', function($q) use ($user) {
                    $q->where('kecamatan_id', $user->kecamatan_id);
                })->count();
            } elseif ($role == 'desa' && $desaId) {
                $totalUpload = RiwayatUpload::where('desa_id', $desaId)->count();
            }
        }
        
        // ============================================
        // 2. AMBIL DATA IDM (JIKA TABEL ADA)
        // ============================================
        $dataIdm = null;
        $tahunList = [];
        $skorData = [];
        $grafikData = ['tahun' => [], 'iks' => [], 'ike' => [], 'ikl' => []];
        $skorSekarang = 0;
        $statusSekarang = 'tertinggal';
        $statusBadge = 'badge-danger';
        $statusWarna = '#dc2626';
        $dimensiData = ['iks' => 0, 'ike' => 0, 'ikl' => 0];
        $trenData = [];
        $dataDesa = collect();
        $statistik = [
            'total_desa' => 0,
            'total_terverifikasi' => 0,
            'total_menunggu' => 0,
            'rata_rata_skor' => 0,
        ];
        
        // Hanya jalankan query jika tabel ada
        if ($tableDataIdmExists) {
            // Ambil data IDM berdasarkan desa_id atau nama_desa
            if ($desaId) {
                $dataIdmAll = DataIDM::where('desa_id', $desaId)
                                    ->orderBy('tahun', 'asc')
                                    ->get();
            } else {
                $dataIdmAll = DataIDM::where('user_id', $user->id)
                                    ->orWhere('nama_desa', 'LIKE', '%' . ($user->name ?? '') . '%')
                                    ->orderBy('tahun', 'asc')
                                    ->get();
            }
            
            // Proses data untuk grafik
            foreach ($dataIdmAll as $data) {
                $tahunList[] = $data->tahun;
                $skorData[] = [
                    'iks' => (float) $data->skor_iks,
                    'ike' => (float) $data->skor_ike,
                    'ikl' => (float) $data->skor_ikl,
                ];
            }
            
            // Data terbaru
            $dataIdm = $dataIdmAll->last();
            
            if ($dataIdm) {
                $skorSekarang = $dataIdm->skor_komposit ?? 0;
                $statusSekarang = $dataIdm->status ?? 'tertinggal';
                $dimensiData = [
                    'iks' => (float) $dataIdm->skor_iks,
                    'ike' => (float) $dataIdm->skor_ike,
                    'ikl' => (float) $dataIdm->skor_ikl,
                ];
            }
            
            // Status badge color
            $statusBadgeMap = [
                'mandiri' => 'badge-success',
                'maju' => 'badge-info',
                'berkembang' => 'badge-warning',
                'tertinggal' => 'badge-danger'
            ];
            $statusBadge = $statusBadgeMap[$statusSekarang] ?? 'badge-danger';
            
            $statusWarnaMap = [
                'mandiri' => '#059669',
                'maju' => '#2563eb',
                'berkembang' => '#f97316',
                'tertinggal' => '#dc2626'
            ];
            $statusWarna = $statusWarnaMap[$statusSekarang] ?? '#dc2626';
            
            // Data untuk grafik
            $grafikData = [
                'tahun' => $tahunList,
                'iks' => array_column($skorData, 'iks'),
                'ike' => array_column($skorData, 'ike'),
                'ikl' => array_column($skorData, 'ikl'),
            ];
            
            // Tren data
            $trenData = array_column($skorData, 'iks');
            
            // ============================================
            // 3. DATA DESA UNTUK TABEL
            // ============================================
            $dataDesa = DataIDM::with('desa')
                               ->when($role === 'desa' && $desaId, fn($q) => $q->where('desa_id', $desaId))
                               ->when($role === 'kecamatan' && $user->kecamatan_id, fn($q) => $q->whereHas('desa', fn($d) => $d->where('kecamatan_id', $user->kecamatan_id)))
                               ->where('tahun', 2025)
                               ->orderBy('created_at', 'desc')
                               ->limit(10)
                               ->get();
            
            if ($dataDesa->isEmpty() && $dataIdm) {
                $dataDesa = collect([$dataIdm]);
            }
            
            // ============================================
            // 4. STATISTIK
            // ============================================
            if ($tableDesaExists) {
                $statistik['total_desa'] = Desa::count();
            }
            $statistik['total_terverifikasi'] = DataIDM::where('verifikasi_status', 'verified')->count();
            $statistik['total_menunggu'] = DataIDM::where('verifikasi_status', 'menunggu')->count();
            $statistik['rata_rata_skor'] = DataIDM::avg('skor_komposit') ?? 0;
        }
        
        // ============================================
        // 5. RIWAYAT UPLOAD
        // ============================================
        $riwayat = collect();
        
        if ($tableRiwayatExists) {
            $riwayat = RiwayatUpload::with('desa')
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get();
            
            // Filter berdasarkan role
            if ($role == 'desa' && $desaId) {
                $riwayat = RiwayatUpload::where('desa_id', $desaId)
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)
                                       ->get();
            } elseif ($role == 'kecamatan' && $user->kecamatan_id) {
                $riwayat = RiwayatUpload::whereHas('desa', function($q) use ($user) {
                    $q->where('kecamatan_id', $user->kecamatan_id);
                })->orderBy('created_at', 'desc')->limit(5)->get();
            }
        }
        
        return view('dashboard.beranda', compact(
            'dataIdm',
            'dataDesa',
            'statistik',
            'riwayat',
            'grafikData',
            'skorSekarang',
            'statusSekarang',
            'statusBadge',
            'statusWarna',
            'dimensiData',
            'trenData',
            'tahunList',
            'totalUpload',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak',
            'role'
        ));
    }

    /**
     * Upload dan ekstrak file Excel
     */
    public function uploadExcel(Request $request)
    {
        $controller = new InputDataController();
        return $controller->uploadExcel($request);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $controller = new InputDataController();
        return $controller->downloadTemplate();
    }

    /**
     * Upload file (manual) - DIPERBAIKI dengan menyimpan file_path
     */
    public function uploadFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls|max:5120'
            ]);

            $user = Auth::user();
            
            // Simpan file ke storage
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('uploads/riwayat', $fileName, 'public');
            
            // Simpan ke database
            $riwayat = RiwayatUpload::create([
                'user_id' => $user->id,
                'desa_id' => $user->desa_id ?? null,
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'ukuran' => $file->getSize(),
                'tahun' => date('Y'),
                'status' => 'menunggu',
                'keterangan' => 'File berhasil diupload',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'riwayat_id' => $riwayat->id,
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error('Upload file error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Riwayat upload lengkap
     */
    public function riwayatLengkap()
    {
        $user = Auth::user();
        $riwayat = RiwayatUpload::where('user_id', $user->id)
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
        
        return view('dashboard.riwayat', compact('riwayat'));
    }

    /**
     * Dashboard Kabupaten
     */
    public function dashboardKabupaten()
    {
        $user = Auth::user();
        
        // Statistik kabupaten
        $totalDesa = Desa::count();
        $totalKecamatan = Desa::distinct('kecamatan')->count('kecamatan');
        
        $rataSkor = 0;
        $desaMandiri = 0;
        $desaMaju = 0;
        $desaBerkembang = 0;
        $desaTertinggal = 0;
        $tahunList = [];
        $trenData = [];
        $kecamatanLabels = [];
        $kecamatanValues = [];
        
        if (Schema::hasTable('data_idm_desa')) {
            $rataSkor = DataIDM::where('tahun', 2025)->avg('skor_komposit') ?? 0;
            $desaMandiri = DataIDM::where('tahun', 2025)->where('status', 'mandiri')->count();
            $desaMaju = DataIDM::where('tahun', 2025)->where('status', 'maju')->count();
            $desaBerkembang = DataIDM::where('tahun', 2025)->where('status', 'berkembang')->count();
            $desaTertinggal = DataIDM::where('tahun', 2025)->where('status', 'tertinggal')->count();

            // Grafik tren 5 tahun (2021-2025)
            foreach ([2021, 2022, 2023, 2024, 2025] as $tahun) {
                $tahunList[] = $tahun;
                $avgSkor = DataIDM::where('tahun', $tahun)->avg('skor_komposit');
                $trenData[] = round($avgSkor ?? 0, 4);
            }

            // Grafik skor per kecamatan
            $kecamatanData = DataIDM::with('desa')
                                    ->where('tahun', 2025)
                                    ->get()
                                    ->groupBy(function($item) {
                                        return $item->desa->kecamatan ?? 'Lainnya';
                                    })
                                    ->map(function($group) {
                                        return round($group->avg('skor_komposit'), 4);
                                    });

            $kecamatanLabels = $kecamatanData->keys()->toArray();
            $kecamatanValues = $kecamatanData->values()->toArray();
        }
        
        return view('dashboard.beranda_kabupaten', compact(
            'totalDesa', 'totalKecamatan', 'rataSkor',
            'desaMandiri', 'desaMaju', 'desaBerkembang', 'desaTertinggal',
            'tahunList', 'trenData', 'kecamatanLabels', 'kecamatanValues'
        ));
    }

    /**
     * Dashboard Kecamatan
     */
    public function dashboardKecamatan()
    {
        $user = Auth::user();
        
        $stats = [
            'total' => 0,
            'menunggu' => 0,
            'proses' => 0,
            'terverifikasi' => 0,
            'ditolak' => 0,
        ];
        
        if (Schema::hasTable('riwayat_uploads')) {
            $query = RiwayatUpload::query();
            if ($user->kecamatan_id) {
                $query->whereHas('desa', function($q) use ($user) {
                    $q->where('kecamatan_id', $user->kecamatan_id);
                });
            }
            
            $riwayatData = $query->get();
            $stats = [
                'total' => $riwayatData->count(),
                'menunggu' => $riwayatData->where('status', 'menunggu')->count(),
                'proses' => $riwayatData->where('status', 'proses')->count(),
                'terverifikasi' => $riwayatData->where('status', 'disetujui')->count(),
                'ditolak' => $riwayatData->where('status', 'revisi')->count(),
            ];
        }
        
        return view('dashboard.beranda_kecamatan', compact('stats'));
    }
}