<?php
// database/migrations/2026_06_23_221332_create_desas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('desas')) {
            Schema::create('desas', function (Blueprint $table) {
                $table->id();
                $table->string('nama_desa');
                $table->string('kode_desa')->unique();
                $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->onDelete('set null');
                $table->string('alamat')->nullable();
                $table->string('kode_pos')->nullable();
                $table->string('telepon')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                $table->decimal('luas_wilayah', 10, 2)->nullable();
                $table->integer('jumlah_penduduk')->nullable();
                $table->string('kepala_desa')->nullable();
                $table->string('nip_kepala_desa')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('kecamatan_id');
                $table->index('is_active');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('desas');
    }
};