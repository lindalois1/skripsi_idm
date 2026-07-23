<?php
// database/migrations/2026_06_29_xxxxxx_create_data_idm_desa_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('data_idm_desa')) {
            Schema::create('data_idm_desa', function (Blueprint $table) {
                $table->id();
                $table->foreignId('desa_id')->nullable()->constrained('desas')->onDelete('set null');
                $table->string('nama_desa')->nullable();
                $table->string('kecamatan')->nullable();
                $table->year('tahun')->nullable();
                $table->decimal('skor_iks', 8, 4)->default(0);
                $table->decimal('skor_ike', 8, 4)->default(0);
                $table->decimal('skor_ikl', 8, 4)->default(0);
                $table->decimal('skor_komposit', 8, 4)->default(0);
                $table->string('status')->default('tertinggal');
                $table->string('verifikasi_status', 20)->default('menunggu');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->json('iks_detail')->nullable();
                $table->json('ike_detail')->nullable();
                $table->json('ikl_detail')->nullable();
                $table->text('catatan_verifikasi')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->index(['desa_id', 'tahun']);
                $table->index('status');
                $table->index('verifikasi_status');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('data_idm_desa');
    }
};