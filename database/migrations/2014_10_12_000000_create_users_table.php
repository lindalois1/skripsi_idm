<?php
// database/migrations/2026_06_29_xxxxxx_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username')->unique();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role')->default('desa');
                
                // 🔥 TANPA FOREIGN KEY CONSTRAINT DULU
                $table->unsignedBigInteger('desa_id')->nullable();
                $table->unsignedBigInteger('kecamatan_id')->nullable();
                $table->unsignedBigInteger('kabupaten_id')->nullable();
                
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->rememberToken();
                $table->timestamps();

                // Index untuk performa
                $table->index('role');
                $table->index('is_active');
                $table->index('desa_id');
                $table->index('kecamatan_id');
                $table->index('kabupaten_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};