<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        // DB::table('users')->truncate(); // Disabled to prevent accidental user data deletion
        Schema::enableForeignKeyConstraints();

        // 1. KABUPATEN (DINAS)
        $kabupatenId = DB::table('users')->insertGetId([
            'name' => 'Admin Kabupaten Indramayu',
            'username' => 'kabupaten_indramayu',
            'email' => 'kabupaten@indramayu.go.id',
            'password' => Hash::make('password'),
            'role' => 'kabupaten',
            'kabupaten_id' => 1,
            'is_active' => true,
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. KECAMATAN (31 Kecamatan)
        $kecamatanList = [
            ['nama' => 'Admin Kecamatan Gabuswetan', 'username' => 'camat_gabuswetan', 'kecamatan_id' => 1],
            ['nama' => 'Admin Kecamatan Cikedung', 'username' => 'camat_cikedung', 'kecamatan_id' => 2],
            ['nama' => 'Admin Kecamatan Lelea', 'username' => 'camat_lelea', 'kecamatan_id' => 3],
            ['nama' => 'Admin Kecamatan Bangodua', 'username' => 'camat_bangodua', 'kecamatan_id' => 4],
            ['nama' => 'Admin Kecamatan Widasari', 'username' => 'camat_widasari', 'kecamatan_id' => 5],
            ['nama' => 'Admin Kecamatan Kertasemaya', 'username' => 'camat_kertasemaya', 'kecamatan_id' => 6],
            ['nama' => 'Admin Kecamatan Krangkeng', 'username' => 'camat_krangkeng', 'kecamatan_id' => 7],
            ['nama' => 'Admin Kecamatan Karangampel', 'username' => 'camat_karangampel', 'kecamatan_id' => 8],
            ['nama' => 'Admin Kecamatan Juntinyuat', 'username' => 'camat_juntinyuat', 'kecamatan_id' => 9],
            ['nama' => 'Admin Kecamatan Sliyeg', 'username' => 'camat_sliyeg', 'kecamatan_id' => 10],
            ['nama' => 'Admin Kecamatan Jatibarang', 'username' => 'camat_jatibarang', 'kecamatan_id' => 11],
            ['nama' => 'Admin Kecamatan Balongan', 'username' => 'camat_balongan', 'kecamatan_id' => 12],
            ['nama' => 'Admin Kecamatan Indramayu', 'username' => 'camat_indramayu', 'kecamatan_id' => 13],
            ['nama' => 'Admin Kecamatan Sindang', 'username' => 'camat_sindang', 'kecamatan_id' => 14],
            ['nama' => 'Admin Kecamatan Cantigi', 'username' => 'camat_cantigi', 'kecamatan_id' => 15],
            ['nama' => 'Admin Kecamatan Lohbener', 'username' => 'camat_lohbener', 'kecamatan_id' => 16],
            ['nama' => 'Admin Kecamatan Arahan', 'username' => 'camat_arahan', 'kecamatan_id' => 17],
            ['nama' => 'Admin Kecamatan Losarang', 'username' => 'camat_losarang', 'kecamatan_id' => 18],
            ['nama' => 'Admin Kecamatan Kandanghaur', 'username' => 'camat_kandanghaur', 'kecamatan_id' => 19],
            ['nama' => 'Admin Kecamatan Bongas', 'username' => 'camat_bongas', 'kecamatan_id' => 20],
            ['nama' => 'Admin Kecamatan Anjatan', 'username' => 'camat_anjatan', 'kecamatan_id' => 21],
            ['nama' => 'Admin Kecamatan Sukra', 'username' => 'camat_sukra', 'kecamatan_id' => 22],
            ['nama' => 'Admin Kecamatan Gantar', 'username' => 'camat_gantar', 'kecamatan_id' => 23],
            ['nama' => 'Admin Kecamatan Terisi', 'username' => 'camat_terisi', 'kecamatan_id' => 24],
            ['nama' => 'Admin Kecamatan Sukagumiwang', 'username' => 'camat_sukagumiwang', 'kecamatan_id' => 25],
            ['nama' => 'Admin Kecamatan Kedokan Bunder', 'username' => 'camat_kedokanbunder', 'kecamatan_id' => 26],
            ['nama' => 'Admin Kecamatan Pasekan', 'username' => 'camat_pasekan', 'kecamatan_id' => 27],
            ['nama' => 'Admin Kecamatan Tukdana', 'username' => 'camat_tukdana', 'kecamatan_id' => 28],
            ['nama' => 'Admin Kecamatan Patrol', 'username' => 'camat_patrol', 'kecamatan_id' => 29],
            ['nama' => 'Admin Kecamatan Haurgeulis', 'username' => 'camat_haurgeulis', 'kecamatan_id' => 30],
            ['nama' => 'Admin Kecamatan Kroya', 'username' => 'camat_kroya', 'kecamatan_id' => 31],
        ];

        foreach ($kecamatanList as $kec) {
            DB::table('users')->insert([
                'name' => $kec['nama'],
                'username' => $kec['username'],
                'email' => $kec['username'] . '@kecamatan.go.id',
                'password' => Hash::make('password'),
                'role' => 'kecamatan',
                'kecamatan_id' => $kec['kecamatan_id'],
                'kabupaten_id' => 1,
                'is_active' => true,
                'created_by' => $kabupatenId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. DESA - Dibuat oleh Kecamatan masing-masing
        $desaList = [
            // Kecamatan Gabuswetan (ID: 1)
            ['nama' => 'Admin Desa Kroyasuka', 'username' => 'desa_kroyasuka', 'desa_id' => 1, 'kecamatan_id' => 1],
            ['nama' => 'Admin Desa Sumberjaya', 'username' => 'desa_sumberjaya', 'desa_id' => 2, 'kecamatan_id' => 1],
            ['nama' => 'Admin Desa Kedungdawa', 'username' => 'desa_kedungdawa', 'desa_id' => 3, 'kecamatan_id' => 1],
            ['nama' => 'Admin Desa Babakanjaya', 'username' => 'desa_babakanjaya', 'desa_id' => 4, 'kecamatan_id' => 1],
            ['nama' => 'Admin Desa Gabus Kulon', 'username' => 'desa_gabus_kulon', 'desa_id' => 5, 'kecamatan_id' => 1],
            
            // ... tambahkan semua desa dari semua kecamatan
        ];

        foreach ($desaList as $desa) {
            // Cari user kecamatan yang membuat
            $kecamatanUser = DB::table('users')->where('kecamatan_id', $desa['kecamatan_id'])->where('role', 'kecamatan')->first();
            $createdBy = $kecamatanUser ? $kecamatanUser->id : null;

            DB::table('users')->insert([
                'name' => $desa['nama'],
                'username' => $desa['username'],
                'email' => $desa['username'] . '@desa.go.id',
                'password' => Hash::make('password'),
                'role' => 'desa',
                'desa_id' => $desa['desa_id'],
                'kecamatan_id' => $desa['kecamatan_id'],
                'kabupaten_id' => 1,
                'is_active' => true,
                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
