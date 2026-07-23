<?php

namespace App\Http\Controllers;

use App\Models\DataIDM;
use App\Models\Desa;
use App\Models\RiwayatUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    /**
     * Halaman Laporan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $tahun = (int) $request->input('tahun', 2025);
        
        // Jika role desa: tampilkan laporan desanya sendiri
        if ($role == 'desa') {
            $desaId = $user->desa_id;
            $dataIdm = DataIDM::where('desa_id', $desaId)
                              ->where('tahun', $tahun)
                              ->first();
            $desa = Desa::find($desaId);
            return view('dashboard.laporan_detail', compact('dataIdm', 'desa'));
        }
        
        // Query untuk semua desa dengan data IDM dan riwayat upload
        $desaList = Desa::with(['dataIdm' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }, 'riwayatUpload' => function($q) {
            $q->orderBy('created_at', 'desc');
        }]);
        
        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $desaList->where('nama_desa', 'like', "%{$search}%");
        }
        
        // Filter kecamatan
        if ($request->filled('kecamatan')) {
            $desaList->where('kecamatan', $request->kecamatan);
        }
        
        // Filter berdasarkan role kecamatan
        if ($role == 'kecamatan' && $user->kecamatan_id) {
            $desaList->where('kecamatan_id', $user->kecamatan_id);
        }
        
        $statsDesaList = (clone $desaList)->get();
        $desaList = $desaList->paginate(10)->withQueryString();
        
        // Hitung statistik dari data real
        $totalDesa = $statsDesaList->count();
        $rataSkor = $statsDesaList->avg(function($desa) {
            $dataIdm = $desa->dataIdm->first();
            return $dataIdm ? $dataIdm->skor_komposit : 0;
        });
        
        $desaMandiri = $statsDesaList->filter(function($desa) {
            $dataIdm = $desa->dataIdm->first();
            return $dataIdm && $dataIdm->status == 'mandiri';
        })->count();
        
        $terverifikasi = $statsDesaList->filter(function($desa) {
            $dataIdm = $desa->dataIdm->first();
            return $dataIdm && $dataIdm->verifikasi_status == 'verified';
        })->count();
        
        // Daftar kecamatan untuk filter
        $daftarKecamatan = Desa::distinct()->pluck('kecamatan')->filter()->values();
        $tahunList = collect([2021, 2022, 2023, 2024, 2025]);
        
        return view('dashboard.laporan', compact(
            'desaList', 
            'totalDesa', 
            'rataSkor', 
            'desaMandiri', 
            'terverifikasi', 
            'daftarKecamatan',
            'role',
            'tahun',
            'tahunList'
        ));
    }

    /**
     * Tampilan Cetak PDF Rekapitulasi Laporan IDM
     */
    public function downloadPdf(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $tahun = (int) $request->input('tahun', 2025);
        
        $desaList = Desa::with(['dataIdm' => function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        }]);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $desaList->where('nama_desa', 'like', "%{$search}%");
        }
        
        if ($request->filled('kecamatan')) {
            $desaList->where('kecamatan', $request->kecamatan);
        }
        
        if ($role == 'kecamatan' && $user->kecamatan_id) {
            $desaList->where('kecamatan_id', $user->kecamatan_id);
        }
        
        $desaList = $desaList->get();
        
        return view('dashboard.laporan_pdf', compact('desaList', 'tahun'));
    }
    
    /**
     * Detail Laporan Desa
     */
    public function detail($desaId)
    {
        $user = Auth::user();
        $desa = Desa::findOrFail($desaId);
        $tahun = DataIDM::max('tahun') ?? date('Y');

        if ($user->role === 'desa' && $user->desa_id != $desaId) {
            abort(403, 'Akses tidak diizinkan. Anda hanya dapat melihat laporan desa Anda sendiri.');
        }

        if ($user->role === 'kecamatan' && $user->kecamatan_id && $desa->kecamatan_id != $user->kecamatan_id) {
            abort(403, 'Akses tidak diizinkan. Anda hanya dapat melihat laporan desa di kecamatan Anda.');
        }

        $dataIdm = DataIDM::where('desa_id', $desaId)
                          ->where('tahun', $tahun)
                          ->first();
        
        // Ambil riwayat upload terbaru
        $riwayat = RiwayatUpload::where('desa_id', $desaId)
                                ->orderBy('created_at', 'desc')
                                ->first();
        
        $dataIdmLastYear = DataIDM::where('desa_id', $desaId)
                                  ->where('tahun', $tahun - 1)
                                  ->first();
        
        $dimensiData = [
            'iks' => [
                'nama' => 'Dimensi Sosial (IKS)',
                'keterangan' => 'Kesehatan, Pendidikan, Modal Sosial',
                'bobot' => 0.333,
                'nilai_sekarang' => $dataIdm->skor_iks ?? 0,
                'nilai_lalu' => $dataIdmLastYear->skor_iks ?? 0,
                'status' => ($dataIdm->skor_iks ?? 0) >= 0.8 ? 'OPTIMAL' : (($dataIdm->skor_iks ?? 0) >= 0.6 ? 'PENINGKATAN' : 'TERENDAH'),
                'progress' => round(($dataIdm->skor_iks ?? 0) / 1 * 100, 1)
            ],
            'ike' => [
                'nama' => 'Dimensi Ekonomi (IKE)',
                'keterangan' => 'Keragaman Produksi, Akses Pasar',
                'bobot' => 0.333,
                'nilai_sekarang' => $dataIdm->skor_ike ?? 0,
                'nilai_lalu' => $dataIdmLastYear->skor_ike ?? 0,
                'status' => ($dataIdm->skor_ike ?? 0) >= 0.8 ? 'OPTIMAL' : (($dataIdm->skor_ike ?? 0) >= 0.6 ? 'PENINGKATAN' : 'TERENDAH'),
                'progress' => round(($dataIdm->skor_ike ?? 0) / 1 * 100, 1)
            ],
            'ikl' => [
                'nama' => 'Dimensi Lingkungan (IKL)',
                'keterangan' => 'Kualitas Lingkungan, Bencana Alam',
                'bobot' => 0.333,
                'nilai_sekarang' => $dataIdm->skor_ikl ?? 0,
                'nilai_lalu' => $dataIdmLastYear->skor_ikl ?? 0,
                'status' => ($dataIdm->skor_ikl ?? 0) >= 0.8 ? 'OPTIMAL' : (($dataIdm->skor_ikl ?? 0) >= 0.6 ? 'PENINGKATAN' : 'TERENDAH'),
                'progress' => round(($dataIdm->skor_ikl ?? 0) / 1 * 100, 1)
            ]
        ];
        
        return view('dashboard.laporan_detail', compact('desa', 'dataIdm', 'dimensiData', 'riwayat'));
    }
    
    /**
     * Download File Excel yang diupload Desa
     */
    public function downloadFile($id)
    {
        try {
            $riwayat = RiwayatUpload::findOrFail($id);
            $user = Auth::user();
            
            // Cek akses: hanya kecamatan, kabupaten, super_admin yang bisa download
            if (!in_array($user->role, ['kecamatan', 'kabupaten', 'super_admin'])) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mendownload file ini.'
                ]);
            }
            
            // Jika role kecamatan, cek apakah desa berada di kecamatannya
            if ($user->role == 'kecamatan' && $user->kecamatan_id) {
                $desa = Desa::find($riwayat->desa_id);
                if ($desa && $desa->kecamatan_id != $user->kecamatan_id) {
                    return redirect()->back()->with('toast', [
                        'type' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk mendownload file desa di luar kecamatan Anda.'
                    ]);
                }
            }
            
            // Cek apakah file ada
            if (!$riwayat->file_path || !Storage::disk('public')->exists($riwayat->file_path)) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'File tidak ditemukan!'
                ]);
            }
            
            // Download file
            return Storage::disk('public')->download($riwayat->file_path, $riwayat->nama_file);
            
        } catch (\Exception $e) {
            Log::error('Download file error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal mendownload file: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Export Laporan ke Excel
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            $tahun = DataIDM::max('tahun') ?? date('Y');
            
            // Query data
            $query = DataIDM::with('desa')->where('tahun', $tahun);
            
            // Filter berdasarkan role
            if ($user->role == 'kecamatan' && $user->kecamatan_id) {
                $query->whereHas('desa', function($q) use ($user) {
                    $q->where('kecamatan_id', $user->kecamatan_id);
                });
            }
            
            // Filter kecamatan
            if ($request->filled('kecamatan')) {
                $query->whereHas('desa', function($q) use ($request) {
                    $q->where('kecamatan', $request->kecamatan);
                });
            }
            
            $data = $query->get();
            
            // Gunakan library Excel (Maatwebsite)
            // Atau export manual CSV
            
            // Contoh export CSV sederhana
            $filename = 'laporan_idm_' . date('Ymd') . '.csv';

            // Set header untuk download CSV
            return response()->stream(
                function() use ($data) {
                    $handle = fopen('php://output', 'w');
                    fputcsv($handle, ['No', 'Desa', 'Kecamatan', 'IKS', 'IKE', 'IKL', 'Skor IDM', 'Status', 'Verifikasi']);
                    foreach ($data as $key => $item) {
                        fputcsv($handle, [
                            $key + 1,
                            $item->desa->nama_desa ?? $item->nama_desa ?? '-',
                            $item->desa->kecamatan ?? $item->kecamatan ?? '-',
                            number_format($item->skor_iks ?? 0, 4),
                            number_format($item->skor_ike ?? 0, 4),
                            number_format($item->skor_ikl ?? 0, 4),
                            number_format($item->skor_komposit ?? 0, 4),
                            $item->status ?? 'Tertinggal',
                            $item->verifikasi_status ?? 'Menunggu',
                        ]);
                    }
                    fclose($handle);
                },
                200,
                [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update data IDM dari Laporan (hanya kabupaten/super_admin)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kabupaten', 'super_admin'])) {
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melakukan tindakan ini.'
            ]);
        }

        $data = DataIDM::findOrFail($id);

        $request->validate([
            'tahun'    => 'required|integer|min:2000|max:2100',
            'skor_iks' => 'required|numeric|min:0|max:1',
            'skor_ike' => 'required|numeric|min:0|max:1',
            'skor_ikl' => 'required|numeric|min:0|max:1',
        ]);

        $skor_komposit = round(
            ((float)$request->skor_iks + (float)$request->skor_ike + (float)$request->skor_ikl) / 3, 4
        );

        $statusKlasifikasi = new \App\Services\StatusKlasifikasiService();
        $status = $statusKlasifikasi->tentukanStatus($skor_komposit);

        $data->update([
            'tahun'         => $request->tahun,
            'skor_iks'      => $request->skor_iks,
            'skor_ike'      => $request->skor_ike,
            'skor_ikl'      => $request->skor_ikl,
            'skor_komposit' => $skor_komposit,
            'status'        => $status,
        ]);

        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Data IDM berhasil diperbarui.'
        ]);
    }

    /**
     * Hapus data IDM dari Laporan (hanya kabupaten/super_admin)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kabupaten', 'super_admin'])) {
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melakukan tindakan ini.'
            ]);
        }

        $data = DataIDM::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Data IDM berhasil dihapus.'
        ]);
    }

    /**
     * Memasukkan data IDM historis 2021-2025 secara otomatis untuk seluruh desa (khusus kabupaten/super_admin)
     */
    public function seedHistoricalData()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['kabupaten', 'super_admin'])) {
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melakukan tindakan ini.'
            ]);
        }

        try {
            // Karena seeder mungkin menggunakan command output ($this->command), kita mock/tangani outputnya
            $seeder = new \Database\Seeders\DataIDMSeeder();
            // Buat command output dummy agar tidak error di controller environment
            $output = new class {
                public function info($msg) { \Log::info($msg); }
                public function warn($msg) { \Log::warning($msg); }
            };
            $seeder->command = $output;
            $seeder->run();

            return redirect()->route('laporan')->with('toast', [
                'type' => 'success',
                'message' => 'Sukses! Semua data IDM desa tahun 2021-2025 berhasil dimasukkan secara otomatis ke database.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Seeding error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal memasukkan data otomatis: ' . $e->getMessage()
            ]);
        }
    }
}
