<?php

namespace App\Http\Controllers;

use App\Models\DataIDM;
use App\Models\Desa;
use App\Services\KMeansClusteringService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AnalisisController extends Controller
{
    public function index(Request $request, KMeansClusteringService $clusteringService)
    {
        $user = Auth::user();
        $analysisRoute = match ($user->role ?? null) {
            'desa' => 'analisis.desa',
            'kecamatan' => 'analisis.kecamatan',
            default => 'analisis',
        };
        $pageTitle = match ($user->role ?? null) {
            'desa' => 'Analisis IDM Desa',
            'kecamatan' => 'Analisis IDM Kecamatan',
            default => 'Analisis IDM Kabupaten',
        };

        $tahunList = range(2021, 2025);
        $tahunInput = $request->input('tahun');

        if ($tahunInput === 'semua') {
            $tahun = 'semua';
        } else {
            // Default to max year if no input or not valid
            $defaultTahun = (int) ($this->applyScope(DataIDM::query(), $user)->max('tahun') ?? date('Y'));
            if (!in_array($defaultTahun, $tahunList)) {
                $defaultTahun = 2025; // fallback
            }
            $tahun = $tahunInput ? (int)$tahunInput : $defaultTahun;

            if (!in_array($tahun, $tahunList, true)) {
                $tahunList[] = $tahun;
                sort($tahunList);
            }
        }

        $tahunAnalisis = $tahun;
        $totalDesaMaster = $this->totalDesaMaster($user);
        $prediksi = $this->hitungPrediksiIdm($user);
        $trenTahunan = $this->trenTahunan($tahunList, $user);

        $query = DataIDM::with('desa');
        if ($tahun !== 'semua') {
            $query->where('tahun', $tahun);
        }
        $semuaDataIdm = $this->applyScope($query, $user)->get();

        // Analisis resmi hanya memakai data yang sudah disetujui kecamatan.
        $dataIdm = $semuaDataIdm->filter(function ($item) {
            return in_array($item->verifikasi_status, ['verified', 'disetujui'], true);
        })->values();

        $sudahInput = $semuaDataIdm->pluck('desa_id')->filter()->unique()->count();
        $belumInput = max($totalDesaMaster - $sudahInput, 0);
        $persentaseInput = $totalDesaMaster > 0 ? round(($sudahInput / $totalDesaMaster) * 100, 1) : 0;
        
        if ($dataIdm->isEmpty()) {
            return view('dashboard.analisis', [
                'totalDesa' => $totalDesaMaster,
                'sudahInput' => 0,
                'belumInput' => $totalDesaMaster,
                'persentaseInput' => 0,
                'mandiri' => 0,
                'maju' => 0,
                'berkembang' => 0,
                'tertinggal' => 0,
                'clusterUnggul' => 0,
                'clusterPotensial' => 0,
                'clusterBerkembang' => 0,
                'clusterPrioritas' => 0,
                'clusterUnggulRata' => 0,
                'clusterPotensialRata' => 0,
                'clusterBerkembangRata' => 0,
                'clusterPrioritasRata' => 0,
                'clusterUnggulMin' => 0,
                'clusterPotensialMin' => 0,
                'clusterBerkembangMin' => 0,
                'clusterPrioritasMin' => 0,
                'clusterUnggulMax' => 0,
                'clusterPotensialMax' => 0,
                'clusterBerkembangMax' => 0,
                'clusterPrioritasMax' => 0,
                // Centroid data
                'centroidUnggulIks' => 0,
                'centroidUnggulIke' => 0,
                'centroidUnggulIkl' => 0,
                'centroidUnggulIdm' => 0,
                'centroidUnggulJarak' => 0,
                'centroidPotensialIks' => 0,
                'centroidPotensialIke' => 0,
                'centroidPotensialIkl' => 0,
                'centroidPotensialIdm' => 0,
                'centroidPotensialJarak' => 0,
                'centroidBerkembangIks' => 0,
                'centroidBerkembangIke' => 0,
                'centroidBerkembangIkl' => 0,
                'centroidBerkembangIdm' => 0,
                'centroidBerkembangJarak' => 0,
                'centroidPrioritasIks' => 0,
                'centroidPrioritasIke' => 0,
                'centroidPrioritasIkl' => 0,
                'centroidPrioritasIdm' => 0,
                'centroidPrioritasJarak' => 0,
                'tahunAnalisis' => $tahun,
                'tahunList' => $tahunList,
                'trenTahunan' => $trenTahunan,
                'prediksi' => $prediksi,
                'analysisRoute' => $analysisRoute,
                'pageTitle' => $pageTitle,
            ]);
        }
        
        // Data statistik berdasarkan pengelompokan tingkat capaian desa
        $totalDesa = $totalDesaMaster;
        $mandiri = $dataIdm->where('status', 'mandiri')->count();
        $maju = $dataIdm->where('status', 'maju')->count();
        $berkembang = $dataIdm->where('status', 'berkembang')->count();
        $tertinggal = $dataIdm->where('status', 'tertinggal')->count();

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

        $clusters = $clusteringService->cluster($records, 4);

        $clusterLabels = ['Unggul', 'Potensial', 'Berkembang', 'Prioritas'];
        $clusterData = [];
        foreach ($clusters as $index => $cluster) {
            $clusterData[$clusterLabels[$index] ?? 'Klaster '.($index + 1)] = [
                'count' => count($cluster['members']),
                'centroid' => $cluster['centroid'],
                'members' => $cluster['members'],
            ];
        }

        $clusterUnggul = $clusterData['Unggul']['count'] ?? 0;
        $clusterPotensial = $clusterData['Potensial']['count'] ?? 0;
        $clusterBerkembang = $clusterData['Berkembang']['count'] ?? 0;
        $clusterPrioritas = $clusterData['Prioritas']['count'] ?? 0;

        $clusterUnggulRata = $clusterData['Unggul']['centroid']['idm'] ?? 0;
        $clusterPotensialRata = $clusterData['Potensial']['centroid']['idm'] ?? 0;
        $clusterBerkembangRata = $clusterData['Berkembang']['centroid']['idm'] ?? 0;
        $clusterPrioritasRata = $clusterData['Prioritas']['centroid']['idm'] ?? 0;

        $clusterUnggulMin = collect($clusterData['Unggul']['members'] ?? [])->min('skor_komposit') ?? 0;
        $clusterPotensialMin = collect($clusterData['Potensial']['members'] ?? [])->min('skor_komposit') ?? 0;
        $clusterBerkembangMin = collect($clusterData['Berkembang']['members'] ?? [])->min('skor_komposit') ?? 0;
        $clusterPrioritasMin = collect($clusterData['Prioritas']['members'] ?? [])->min('skor_komposit') ?? 0;

        $clusterUnggulMax = collect($clusterData['Unggul']['members'] ?? [])->max('skor_komposit') ?? 0;
        $clusterPotensialMax = collect($clusterData['Potensial']['members'] ?? [])->max('skor_komposit') ?? 0;
        $clusterBerkembangMax = collect($clusterData['Berkembang']['members'] ?? [])->max('skor_komposit') ?? 0;
        $clusterPrioritasMax = collect($clusterData['Prioritas']['members'] ?? [])->max('skor_komposit') ?? 0;

        $centroidUnggulIks = $clusterData['Unggul']['centroid']['iks'] ?? 0;
        $centroidUnggulIke = $clusterData['Unggul']['centroid']['ike'] ?? 0;
        $centroidUnggulIkl = $clusterData['Unggul']['centroid']['ikl'] ?? 0;
        $centroidUnggulIdm = $clusterData['Unggul']['centroid']['idm'] ?? 0;
        $centroidUnggulJarak = $this->hitungJarak($centroidUnggulIks, $centroidUnggulIke, $centroidUnggulIkl, $centroidUnggulIdm);

        $centroidPotensialIks = $clusterData['Potensial']['centroid']['iks'] ?? 0;
        $centroidPotensialIke = $clusterData['Potensial']['centroid']['ike'] ?? 0;
        $centroidPotensialIkl = $clusterData['Potensial']['centroid']['ikl'] ?? 0;
        $centroidPotensialIdm = $clusterData['Potensial']['centroid']['idm'] ?? 0;
        $centroidPotensialJarak = $this->hitungJarak($centroidPotensialIks, $centroidPotensialIke, $centroidPotensialIkl, $centroidPotensialIdm);

        $centroidBerkembangIks = $clusterData['Berkembang']['centroid']['iks'] ?? 0;
        $centroidBerkembangIke = $clusterData['Berkembang']['centroid']['ike'] ?? 0;
        $centroidBerkembangIkl = $clusterData['Berkembang']['centroid']['ikl'] ?? 0;
        $centroidBerkembangIdm = $clusterData['Berkembang']['centroid']['idm'] ?? 0;
        $centroidBerkembangJarak = $this->hitungJarak($centroidBerkembangIks, $centroidBerkembangIke, $centroidBerkembangIkl, $centroidBerkembangIdm);

        $centroidPrioritasIks = $clusterData['Prioritas']['centroid']['iks'] ?? 0;
        $centroidPrioritasIke = $clusterData['Prioritas']['centroid']['ike'] ?? 0;
        $centroidPrioritasIkl = $clusterData['Prioritas']['centroid']['ikl'] ?? 0;
        $centroidPrioritasIdm = $clusterData['Prioritas']['centroid']['idm'] ?? 0;
        $centroidPrioritasJarak = $this->hitungJarak($centroidPrioritasIks, $centroidPrioritasIke, $centroidPrioritasIkl, $centroidPrioritasIdm);
        
        return view('dashboard.analisis', compact(
            'totalDesa', 'sudahInput', 'belumInput', 'persentaseInput', 'mandiri', 'maju', 'berkembang', 'tertinggal',
            'tahunAnalisis', 'tahunList', 'trenTahunan', 'prediksi', 'analysisRoute', 'pageTitle',
            'clusterUnggul', 'clusterPotensial', 'clusterBerkembang', 'clusterPrioritas',
            'clusterUnggulRata', 'clusterPotensialRata', 'clusterBerkembangRata', 'clusterPrioritasRata',
            'clusterUnggulMin', 'clusterPotensialMin', 'clusterBerkembangMin', 'clusterPrioritasMin',
            'clusterUnggulMax', 'clusterPotensialMax', 'clusterBerkembangMax', 'clusterPrioritasMax',
            'centroidUnggulIks', 'centroidUnggulIke', 'centroidUnggulIkl', 'centroidUnggulIdm', 'centroidUnggulJarak',
            'centroidPotensialIks', 'centroidPotensialIke', 'centroidPotensialIkl', 'centroidPotensialIdm', 'centroidPotensialJarak',
            'centroidBerkembangIks', 'centroidBerkembangIke', 'centroidBerkembangIkl', 'centroidBerkembangIdm', 'centroidBerkembangJarak',
            'centroidPrioritasIks', 'centroidPrioritasIke', 'centroidPrioritasIkl', 'centroidPrioritasIdm', 'centroidPrioritasJarak'
        ));
    }

    private function trenTahunan(array $tahunList, $user): array
    {
        $rows = [];

        foreach ($tahunList as $tahun) {
            $data = $this->applyScope(DataIDM::query(), $user)
                ->where('tahun', $tahun)
                ->whereIn('verifikasi_status', ['verified', 'disetujui'])
                ->get();

            $rows[] = [
                'tahun' => (int) $tahun,
                'jumlah_data' => $data->count(),
                'skor_idm' => round((float) $data->avg('skor_komposit'), 4),
                'iks' => round((float) $data->avg('skor_iks'), 4),
                'ike' => round((float) $data->avg('skor_ike'), 4),
                'ikl' => round((float) $data->avg('skor_ikl'), 4),
                'mandiri' => $data->where('status', 'mandiri')->count(),
                'maju' => $data->where('status', 'maju')->count(),
                'berkembang' => $data->where('status', 'berkembang')->count(),
                'tertinggal' => $data->where('status', 'tertinggal')->count(),
            ];
        }

        return $rows;
    }

    private function hitungPrediksiIdm($user): array
    {
        $perTahun = $this->applyScope(DataIDM::query(), $user)
            ->whereNotNull('tahun')
            ->whereNotNull('skor_komposit')
            ->whereIn('verifikasi_status', ['verified', 'disetujui'])
            ->orderBy('tahun')
            ->get()
            ->groupBy('tahun')
            ->map(function ($items, $tahun) {
                return [
                    'tahun' => (int) $tahun,
                    'rata_rata' => round((float) $items->avg('skor_komposit'), 4),
                    'jumlah_data' => $items->count(),
                ];
            })
            ->values();

        if ($perTahun->isEmpty()) {
            return [
                'tahun_berikutnya' => (int) date('Y') + 1,
                'skor_prediksi' => 0,
                'status_prediksi' => 'Belum ada data',
                'tren' => 'Belum ada data',
                'riwayat' => [],
                'cukup_data' => false,
            ];
        }

        $riwayat = $perTahun->take(-5)->values();
        $tahunBerikutnya = (int) $riwayat->max('tahun') + 1;

        if ($riwayat->count() < 2) {
            $skor = (float) $riwayat->last()['rata_rata'];

            return [
                'tahun_berikutnya' => $tahunBerikutnya,
                'skor_prediksi' => round($skor, 4),
                'status_prediksi' => $this->statusPrediksi($skor),
                'tren' => 'Data belum cukup untuk tren',
                'riwayat' => $riwayat->all(),
                'cukup_data' => false,
            ];
        }

        $n = $riwayat->count();
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumXX = 0;

        foreach ($riwayat as $index => $item) {
            $x = $index + 1;
            $y = (float) $item['rata_rata'];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumXX += $x * $x;
        }

        $denominator = ($n * $sumXX) - ($sumX * $sumX);
        $slope = $denominator != 0 ? (($n * $sumXY) - ($sumX * $sumY)) / $denominator : 0;
        $intercept = ($sumY - ($slope * $sumX)) / $n;
        $predicted = max(0, min(1, $intercept + ($slope * ($n + 1))));

        return [
            'tahun_berikutnya' => $tahunBerikutnya,
            'skor_prediksi' => round($predicted, 4),
            'status_prediksi' => $this->statusPrediksi($predicted),
            'tren' => $slope > 0.0005 ? 'Naik' : ($slope < -0.0005 ? 'Turun' : 'Stabil'),
            'riwayat' => $riwayat->all(),
            'cukup_data' => true,
        ];
    }

    private function statusPrediksi(float $skor): string
    {
        if ($skor >= 0.8155) {
            return 'Mandiri';
        }

        if ($skor >= 0.7072) {
            return 'Maju';
        }

        if ($skor >= 0.5989) {
            return 'Berkembang';
        }

        if ($skor >= 0.4907) {
            return 'Tertinggal';
        }

        return 'Sangat Tertinggal';
    }

    private function applyScope(Builder $query, $user): Builder
    {
        if (($user->role ?? null) === 'desa' && $user->desa_id) {
            return $query->where('desa_id', $user->desa_id);
        }

        if (($user->role ?? null) === 'kecamatan' && $user->kecamatan_id) {
            return $query->whereHas('desa', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        }

        return $query;
    }

    private function totalDesaMaster($user): int
    {
        if (!Schema::hasTable('desas')) {
            return 0;
        }

        if (($user->role ?? null) === 'desa') {
            return $user->desa_id ? 1 : 0;
        }

        if (($user->role ?? null) === 'kecamatan' && $user->kecamatan_id) {
            return Desa::where('kecamatan_id', $user->kecamatan_id)->count();
        }

        return Desa::count();
    }
    
    private function hitungJarak($iks, $ike, $ikl, $idm)
    {
        // Euclidean distance dari titik pusat (0,0,0,0)
        return round(sqrt(pow($iks, 2) + pow($ike, 2) + pow($ikl, 2) + pow($idm, 2)), 3);
    }
}
