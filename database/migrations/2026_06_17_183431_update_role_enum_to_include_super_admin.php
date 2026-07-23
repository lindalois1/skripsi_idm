<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah kolom role menjadi enum dengan nilai baru
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('desa', 'kecamatan', 'kabupaten', 'super_admin') NOT NULL DEFAULT 'desa'");
    }

    public function down()
    {
        // Kembalikan ke nilai awal
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('desa', 'kecamatan', 'kabupaten') NOT NULL DEFAULT 'desa'");
    }
};