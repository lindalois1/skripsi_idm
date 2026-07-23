<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataIDM;
use App\Models\Desa;
use App\Models\RiwayatUpload;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Halaman Beranda Super Admin - Grafik Kabupaten
     */
    public function beranda()
    {
        // Statistik kabupaten
        $totalDesa = DataIDM::where('tahun', 2025)->count();
        $totalKecamatan = Desa::distinct('kecamatan')->count('kecamatan');
        $rataSkor = DataIDM::where('tahun', 2025)->avg('skor_komposit') ?? 0;

        $desaMandiri = DataIDM::where('tahun', 2025)->where('status', 'mandiri')->count();
        $desaMaju = DataIDM::where('tahun', 2025)->where('status', 'maju')->count();
        $desaBerkembang = DataIDM::where('tahun', 2025)->where('status', 'berkembang')->count();
        $desaTertinggal = DataIDM::where('tahun', 2025)->where('status', 'tertinggal')->count();

        // Grafik tren 5 tahun (2021-2025)
        $tahunList = [];
        $trenData = [];
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

        // Grafik sebaran status
        $statusData = [$desaMandiri, $desaMaju, $desaBerkembang, $desaTertinggal];

        return view('super_admin.beranda', compact(
            'totalDesa', 'totalKecamatan', 'rataSkor',
            'desaMandiri', 'desaMaju', 'desaBerkembang', 'desaTertinggal',
            'tahunList', 'trenData', 'kecamatanLabels', 'kecamatanValues',
            'statusData'
        ));
    }

    /**
     * Halaman Analisis - Clustering dan Grafik per Kecamatan
    public function analisis()
    {
        // Data IDM tahun ini (2025)
        $dataIdm = DataIDM::with('desa')
                          ->where('tahun', 2025)
                          ->get();

        $totalDesa = $dataIdm->count();
        $mandiri = $dataIdm->where('status', 'mandiri')->count();
        $maju = $dataIdm->where('status', 'maju')->count();
        $berkembang = $dataIdm->where('status', 'berkembang')->count();
        $tertinggal = $dataIdm->where('status', 'tertinggal')->count();

        // Data clustering per kecamatan
        $kecamatanClustering = DataIDM::with('desa')
                                      ->where('tahun', 2025)
                                      ->get()
                                      ->groupBy(function($item) {
                                          return $item->desa->kecamatan ?? 'Lainnya';
                                      })
                                      ->map(function($group) {
                                          return [
                                              'total' => $group->count(),
                                              'rata' => round($group->avg('skor_komposit'), 4),
                                              'mandiri' => $group->where('status', 'mandiri')->count(),
                                              'maju' => $group->where('status', 'maju')->count(),
                                              'berkembang' => $group->where('status', 'berkembang')->count(),
                                              'tertinggal' => $group->where('status', 'tertinggal')->count(),
                                          ];
                                      });

        // Scatter plot data (IKS vs IKE)
        $scatterMandiri = [];
        $scatterMaju = [];
        $scatterBerkembang = [];
        $scatterTertinggal = [];

        foreach ($dataIdm as $item) {
            $point = ['x' => $item->skor_iks, 'y' => $item->skor_ike];
            if ($item->status == 'mandiri') {
                $scatterMandiri[] = $point;
            } elseif ($item->status == 'maju') {
                $scatterMaju[] = $point;
            } elseif ($item->status == 'berkembang') {
                $scatterBerkembang[] = $point;
            } else {
                $scatterTertinggal[] = $point;
            }
        }

        return view('super_admin.analisis', compact(
            'totalDesa', 'mandiri', 'maju', 'berkembang', 'tertinggal',
            'kecamatanClustering', 'scatterMandiri', 'scatterMaju',
            'scatterBerkembang', 'scatterTertinggal'
        ));
    }

    /**
     * Halaman Laporan - Daftar Kecamatan
     */
    public function laporan(Request $request)
    {
        // Ambil semua kecamatan
        $kecamatanList = Desa::select('kecamatan')
                             ->distinct()
                             ->orderBy('kecamatan')
                             ->get();

        // Filter search
        $search = $request->search;
        if ($search) {
            $kecamatanList = $kecamatanList->filter(function($item) use ($search) {
                return stripos($item->kecamatan, $search) !== false;
            });
        }

        // Data per kecamatan
        $dataKecamatan = [];
        foreach ($kecamatanList as $kec) {
            $desaIds = Desa::where('kecamatan', $kec->kecamatan)->pluck('id');
            $dataIdmKec = DataIDM::whereIn('desa_id', $desaIds)
                                 ->where('tahun', 2025)
                                 ->get();

            $dataKecamatan[] = [
                'nama' => $kec->kecamatan,
                'total_desa' => $desaIds->count(),
                'jumlah_data' => $dataIdmKec->count(),
                'rata_skor' => round($dataIdmKec->avg('skor_komposit') ?? 0, 4),
                'mandiri' => $dataIdmKec->where('status', 'mandiri')->count(),
                'maju' => $dataIdmKec->where('status', 'maju')->count(),
                'berkembang' => $dataIdmKec->where('status', 'berkembang')->count(),
                'tertinggal' => $dataIdmKec->where('status', 'tertinggal')->count(),
            ];
        }

        return view('super_admin.laporan', compact('dataKecamatan', 'search'));
    }

    /**
     * Detail laporan per kecamatan
     */
    public function detailKecamatan($kecamatan)
    {
        $desaIds = Desa::where('kecamatan', $kecamatan)->pluck('id');
        $desaList = Desa::where('kecamatan', $kecamatan)->get();

        $dataIdm = DataIDM::with('desa')
                          ->whereIn('desa_id', $desaIds)
                          ->where('tahun', 2025)
                          ->get();

        $rataSkor = round($dataIdm->avg('skor_komposit') ?? 0, 4);
        $totalDesa = $desaIds->count();
        $jumlahData = $dataIdm->count();

        return view('super_admin.detail_kecamatan', compact(
            'kecamatan', 'desaList', 'dataIdm', 'rataSkor', 'totalDesa', 'jumlahData'
        ));
    }
}