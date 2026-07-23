<?php
// database/migrations/2026_06_29_xxxxxx_create_kecamatan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kecamatan')) {
            Schema::create('kecamatan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kecamatan');
                $table->string('kode_kecamatan')->unique();
                $table->foreignId('kabupaten_id')->nullable();
                $table->string('alamat')->nullable();
                $table->string('telepon')->nullable();
                $table->string('email')->nullable();
                $table->string('camat')->nullable();
                $table->string('nip_camat')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index('kabupaten_id');
                $table->index('is_active');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('kecamatan');
    }
};