<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->string('kecamatan')->nullable();
            $table->string('kode_desa')->nullable()->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            // INDEX saja, tanpa foreign key
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('desas');
    }
};