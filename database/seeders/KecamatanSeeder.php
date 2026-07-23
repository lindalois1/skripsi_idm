<?php
// database/seeders/KecamatanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KecamatanSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('kecamatan')->truncate();
        Schema::enableForeignKeyConstraints();

        $kecamatan = [
            ['nama_kecamatan' => 'Gabuswetan', 'kode_kecamatan' => '321217'],
            ['nama_kecamatan' => 'Cikedung', 'kode_kecamatan' => '321213'],
            ['nama_kecamatan' => 'Lelea', 'kode_kecamatan' => '321205'],
            ['nama_kecamatan' => 'Bangodua', 'kode_kecamatan' => '321206'],
            ['nama_kecamatan' => 'Widasari', 'kode_kecamatan' => '321207'],
            ['nama_kecamatan' => 'Kertasemaya', 'kode_kecamatan' => '321204'],
            ['nama_kecamatan' => 'Krangkeng', 'kode_kecamatan' => '321208'],
            ['nama_kecamatan' => 'Karangampel', 'kode_kecamatan' => '321218'],
            ['nama_kecamatan' => 'Juntinyuat', 'kode_kecamatan' => '321230'],
            ['nama_kecamatan' => 'Sliyeg', 'kode_kecamatan' => '321211'],
            ['nama_kecamatan' => 'Jatibarang', 'kode_kecamatan' => '321202'],
            ['nama_kecamatan' => 'Balongan', 'kode_kecamatan' => '321203'],
            ['nama_kecamatan' => 'Indramayu', 'kode_kecamatan' => '321201'],
            ['nama_kecamatan' => 'Sindang', 'kode_kecamatan' => '321229'],
            ['nama_kecamatan' => 'Cantigi', 'kode_kecamatan' => '321224'],
            ['nama_kecamatan' => 'Lohbener', 'kode_kecamatan' => '321210'],
            ['nama_kecamatan' => 'Arahan', 'kode_kecamatan' => '321219'],
            ['nama_kecamatan' => 'Losarang', 'kode_kecamatan' => '321226'],
            ['nama_kecamatan' => 'Kandanghaur', 'kode_kecamatan' => '321212'],
            ['nama_kecamatan' => 'Bongas', 'kode_kecamatan' => '321227'],
            ['nama_kecamatan' => 'Anjatan', 'kode_kecamatan' => '321222'],
            ['nama_kecamatan' => 'Sukra', 'kode_kecamatan' => '321223'],
            ['nama_kecamatan' => 'Gantar', 'kode_kecamatan' => '321221'],
            ['nama_kecamatan' => 'Terisi', 'kode_kecamatan' => '321214'],
            ['nama_kecamatan' => 'Sukagumiwang', 'kode_kecamatan' => '321215'],
            ['nama_kecamatan' => 'Kedokan Bunder', 'kode_kecamatan' => '321231'],
            ['nama_kecamatan' => 'Pasekan', 'kode_kecamatan' => '321225'],
            ['nama_kecamatan' => 'Tukdana', 'kode_kecamatan' => '321209'],
            ['nama_kecamatan' => 'Patrol', 'kode_kecamatan' => '321228'],
            ['nama_kecamatan' => 'Haurgeulis', 'kode_kecamatan' => '321216'],
            ['nama_kecamatan' => 'Kroya', 'kode_kecamatan' => '321220'],
        ];

        foreach ($kecamatan as $index => $kec) {
            DB::table('kecamatan')->insert([
                'id' => $index + 1,
                'nama_kecamatan' => $kec['nama_kecamatan'],
                'kode_kecamatan' => $kec['kode_kecamatan'],
                'kabupaten_id' => 1, // Indramayu
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
