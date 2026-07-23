<?php
// database/migrations/2026_06_29_xxxxxx_add_file_path_to_riwayat_uploads_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('riwayat_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_uploads', 'file_path')) {
                $table->string('file_path')->nullable()->after('nama_file');
            }
        });
    }

    public function down()
    {
        Schema::table('riwayat_uploads', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_uploads', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};