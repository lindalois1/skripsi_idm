<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['desa', 'kecamatan', 'kabupaten'])->default('desa')->after('email');
            $table->string('desa_id')->nullable()->after('role'); // untuk role desa
            $table->string('kecamatan_id')->nullable()->after('desa_id'); // untuk role kecamatan
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'desa_id', 'kecamatan_id']);
        });
    }
};