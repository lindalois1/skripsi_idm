<?php
// database/migrations/2026_06_29_xxxxxx_add_kecamatan_id_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom kecamatan_id jika belum ada
            if (!Schema::hasColumn('users', 'kecamatan_id')) {
                $table->foreignId('kecamatan_id')->nullable()->after('desa_id')->constrained('kecamatan')->onDelete('set null');
            }
            
            // Tambahkan kolom kabupaten_id jika belum ada
            if (!Schema::hasColumn('users', 'kabupaten_id')) {
                $table->foreignId('kabupaten_id')->nullable()->after('kecamatan_id')->constrained('kabupaten')->onDelete('set null');
            }
            
            // Tambahkan kolom is_active jika belum ada
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('kabupaten_id');
            }
            
            // Tambahkan kolom created_by jika belum ada
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->onDelete('set null');
            }
            
            // Tambahkan kolom username jika belum ada
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('name');
            }
            
            // Tambahkan kolom last_login jika belum ada
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('created_by');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['kecamatan_id', 'kabupaten_id', 'is_active', 'created_by', 'username', 'last_login'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};