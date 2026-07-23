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
            $table->foreignId('desa_id')->nullable()->constrained('desa')->onDelete('set null')->after('role');
            $table->string('kecamatan_id')->nullable()->after('desa_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropColumn(['role', 'desa_id', 'kecamatan_id']);
        });
    }
};