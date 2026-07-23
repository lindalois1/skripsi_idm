<?php
// database/migrations/2026_06_29_xxxxxx_add_kecamatan_id_to_desas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('desas', function (Blueprint $table) {
            // Tambahkan kolom kecamatan_id jika belum ada
            if (!Schema::hasColumn('desas', 'kecamatan_id')) {
                $table->unsignedBigInteger('kecamatan_id')->nullable()->after('kode_desa');
            }
            
            // Tambahkan kolom kecamatan jika belum ada
            if (!Schema::hasColumn('desas', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('nama_desa');
            }
        });
    }

    public function down()
    {
        Schema::table('desas', function (Blueprint $table) {
            if (Schema::hasColumn('desas', 'kecamatan_id')) {
                $table->dropColumn('kecamatan_id');
            }
            if (Schema::hasColumn('desas', 'kecamatan')) {
                $table->dropColumn('kecamatan');
            }
        });
    }
};