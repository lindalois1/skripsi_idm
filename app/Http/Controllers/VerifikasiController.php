<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataIDM;
use App\Models\RiwayatUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan halaman verifikasi dengan daftar upload
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $riwayat = RiwayatUpload::with('desa')
                                ->orderBy('created_at', 'desc');

        if ($user->role === 'kecamatan' && $user->kecamatan_id) {
            $riwayat->whereHas('desa', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $riwayat->where(function($q) use ($search) {
                $q->whereHas('desa', function($q2) use ($search) {
                    $q2->where('nama_desa', 'like', "%{$search}%");
                })->orWhere('nama_file', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status') && $request->status != 'semua') {
            $riwayat->where('status', $request->status);
        }

        if ($request->filled('tahun') && $request->tahun != 'semua') {
            $riwayat->where('tahun', (int) $request->tahun);
        }
        
        $riwayat = $riwayat->paginate(10);
        
        // Statistics
        $statsQuery = RiwayatUpload::query();
        if ($user->role === 'kecamatan' && $user->kecamatan_id) {
            $statsQuery->whereHas('desa', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        }

        if ($request->filled('tahun') && $request->tahun != 'semua') {
            $statsQuery->where('tahun', (int) $request->tahun);
        }

        $statsBase = $statsQuery->get();
        $stats = [
            'total' => $statsBase->count(),
            'menunggu' => $statsBase->where('status', 'menunggu')->count(),
            'proses' => $statsBase->where('status', 'proses')->count(),
            'terverifikasi' => $statsBase->where('status', 'disetujui')->count(),
            'ditolak' => $statsBase->where('status', 'revisi')->count(),
        ];

        $tahunList = collect([2021, 2022, 2023, 2024, 2025]);
        
        return view('dashboard.verifikasi', compact('riwayat', 'stats', 'tahunList'));
    }

    /**
     * UPDATE STATUS VERIFIKASI (Setujui / Tolak / Proses)
     * Route: PUT /verifikasi/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:disetujui,revisi,proses,menunggu',
                'catatan' => 'nullable|string|max:500'
            ]);

            $riwayat = RiwayatUpload::findOrFail($id);
            
            // Update data
            $riwayat->status = $request->status;
            $riwayat->catatan = $request->catatan;
            $riwayat->verified_by = Auth::user()->name;
            $riwayat->verified_at = now();
            $riwayat->save();

            $this->syncToDataIDM($riwayat);

            $statusLabel = [
                'disetujui' => '✅ DISETUJUI',
                'revisi' => '❌ DITOLAK (REVISI)',
                'proses' => '🔄 DIPROSES',
                'menunggu' => '⏳ MENUNGGU'
            ][$request->status] ?? strtoupper($request->status);

            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'success',
                'message' => "Status berhasil diubah menjadi {$statusLabel}"
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'error',
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors())
            ]);
        } catch (\Exception $e) {
            Log::error('Verifikasi update error: ' . $e->getMessage());
            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'error',
                'message' => 'Gagal verifikasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * EDIT DATA UPLOAD (nama_file, status, catatan)
     * Route: PUT /verifikasi/{id}
     * 
     * CATATAN: Karena route sama dengan update(), 
     * kita bedakan dengan mengecek apakah ada field 'nama_file'
     */
    public function edit(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_file' => 'nullable|string|max:255',
                'status' => 'required|in:disetujui,revisi,proses,menunggu',
                'catatan' => 'nullable|string|max:500'
            ]);

            $riwayat = RiwayatUpload::findOrFail($id);
            
            // Update field yang diizinkan
            if ($request->filled('nama_file')) {
                $riwayat->nama_file = $request->nama_file;
            }
            
            $riwayat->status = $request->status;
            $riwayat->catatan = $request->catatan;
            $riwayat->verified_by = Auth::user()->name;
            $riwayat->verified_at = now();
            $riwayat->save();

            $this->syncToDataIDM($riwayat);

            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'success',
                'message' => 'Data upload berhasil diperbarui!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'error',
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors())
            ]);
        } catch (\Exception $e) {
            Log::error('Edit data error: ' . $e->getMessage());
            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'error',
                'message' => 'Gagal edit data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * HAPUS DATA UPLOAD
     * Route: DELETE /verifikasi/{id}
     */
    public function delete($id)
    {
        try {
            $riwayat = RiwayatUpload::findOrFail($id);
            
            // Hapus file fisik jika ada
            if ($riwayat->file_path && Storage::disk('public')->exists($riwayat->file_path)) {
                Storage::disk('public')->delete($riwayat->file_path);
            }
            
            // Hapus record dari database
            $riwayat->delete();
            
            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'success',
                'message' => 'Data dan file berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete data error: ' . $e->getMessage());
            return redirect()->route('verifikasi')->with('toast', [
                'type' => 'error',
                'message' => 'Gagal hapus data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sinkronkan status upload ke DataIDM agar laporan dan analisis ikut berubah.
     */
    private function syncToDataIDM($riwayat)
    {
        try {
            if (!$riwayat->desa_id) {
                return;
            }

            $dataIdm = DataIDM::where('desa_id', $riwayat->desa_id)
                ->where('tahun', $riwayat->tahun ?? date('Y'))
                ->latest()
                ->first();

            if (!$dataIdm) {
                $dataIdm = DataIDM::where('desa_id', $riwayat->desa_id)
                    ->orderBy('tahun', 'desc')
                    ->latest()
                    ->first();
            }

            if (!$dataIdm) {
                return;
            }

            $verifikasiStatus = match ($riwayat->status) {
                'disetujui' => 'verified',
                'revisi' => 'revisi',
                'proses' => 'proses',
                default => 'menunggu',
            };

            $dataIdm->update([
                'verifikasi_status' => $verifikasiStatus,
                'catatan_verifikasi' => $riwayat->catatan,
                'verified_by' => Auth::id(),
                'verified_at' => in_array($riwayat->status, ['disetujui', 'revisi']) ? now() : null,
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Sync to DataIDM failed: ' . $e->getMessage());
        }
    }
}
