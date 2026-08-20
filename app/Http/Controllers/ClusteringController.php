<?php

namespace App\Http\Controllers;

use App\Models\DataIDM;
use App\Services\KMeansClusteringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClusteringController extends Controller
{
    public function process(Request $request, KMeansClusteringService $clusteringService)
    {
        $user = Auth::user();
        $tahunInput = $request->input('tahun');

        // Fetch verified/approved data
        $query = DataIDM::query();
        if ($tahunInput && $tahunInput !== 'semua') {
            $query->where('tahun', $tahunInput);
        }
        
        $dataIdm = $query->whereIn('verifikasi_status', ['verified', 'disetujui'])->get();

        if ($dataIdm->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada data IDM terverifikasi untuk diproses clustering.');
        }

        $records = $dataIdm->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_desa' => $item->nama_desa,
                'skor_iks' => (float) $item->skor_iks,
                'skor_ike' => (float) $item->skor_ike,
                'skor_ikl' => (float) $item->skor_ikl,
                'skor_komposit' => (float) $item->skor_komposit,
            ];
        })->all();

        // Run clustering service with 3 clusters
        $clusters = $clusteringService->cluster($records, 3);

        $clusterLabels = ['Unggul', 'Potensial', 'Berkembang'];

        foreach ($clusters as $index => $cluster) {
            $label = $clusterLabels[$index] ?? 'Klaster ' . ($index + 1);
            $members = $cluster['members'] ?? [];

            foreach ($members as $member) {
                DataIDM::where('id', $member['id'])->update([
                    'cluster' => $label
                ]);
            }
        }

        return redirect()->back()->with('success', 'Proses Clustering K-Means berhasil diselesaikan!');
    }
}
