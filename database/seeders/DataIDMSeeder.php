<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Desa;
use App\Models\DataIDM;

class DataIDMSeeder extends Seeder
{
    public function run()
    {
        $desas = Desa::all();

        if ($desas->isEmpty()) {
            $this->command->warn('Tidak ada data desa ditemukan. Silakan jalankan DesaSeeder terlebih dahulu.');
            return;
        }

        $years = [2021, 2022, 2023, 2024, 2025];
        $insertedCount = 0;

        foreach ($desas as $desa) {
            // Kita generate base score untuk desa ini agar perkembangannya konsisten
            // Base score komposit awal (2021) berkisar antara 0.5000 s/d 0.7800
            $baseIks = rand(5500, 7500) / 10000;
            $baseIke = rand(5000, 7200) / 10000;
            $baseIkl = rand(5200, 7400) / 10000;

            foreach ($years as $index => $tahun) {
                // Cek apakah data sudah ada
                $exists = DB::table('data_idm_desa')
                    ->where('desa_id', $desa->id)
                    ->where('tahun', $tahun)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Setiap tahun naik sedikit demi sedikit secara acak (antara 0.01 s/d 0.04 per tahun)
                $growth = $index * (rand(150, 400) / 10000);
                
                $iks = min(0.98, $baseIks + $growth + (rand(-100, 100) / 10000));
                $ike = min(0.98, $baseIke + $growth + (rand(-100, 100) / 10000));
                $ikl = min(0.98, $baseIkl + $growth + (rand(-100, 100) / 10000));

                $skorKomposit = round(($iks + $ike + $ikl) / 3, 4);

                // Tentukan status berdasarkan skor komposit
                if ($skorKomposit >= 0.8155) {
                    $status = 'mandiri';
                } elseif ($skorKomposit >= 0.7072) {
                    $status = 'maju';
                } elseif ($skorKomposit >= 0.5989) {
                    $status = 'berkembang';
                } else {
                    $status = 'tertinggal';
                }

                // Masukkan data ke database
                DB::table('data_idm_desa')->insert([
                    'desa_id' => $desa->id,
                    'nama_desa' => $desa->nama_desa,
                    'kecamatan' => $desa->kecamatan ?? $desa->kecamatanRelation->nama_kecamatan ?? 'Kecamatan',
                    'tahun' => $tahun,
                    'skor_iks' => $iks,
                    'skor_ike' => $ike,
                    'skor_ikl' => $ikl,
                    'skor_komposit' => $skorKomposit,
                    'status' => $status,
                    'verifikasi_status' => 'verified',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $insertedCount++;
            }
        }

        $this->command->info("Berhasil menggenerate {$insertedCount} data IDM Desa dari tahun 2021-2025.");
    }
}
