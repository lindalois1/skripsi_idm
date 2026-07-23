<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('riwayat_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('desa_id')->nullable();
            $table->string('nama_file');
            $table->bigInteger('ukuran')->nullable();
            $table->integer('tahun')->nullable();
            $table->string('status')->default('menunggu');
            $table->text('keterangan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            
            // INDEX
            $table->index('user_id');
            $table->index('desa_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_uploads');
    }
};