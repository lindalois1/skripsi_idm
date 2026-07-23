<?php
// app/Models/DataIDM.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataIDM extends Model
{
    protected $table = 'data_idm_desa';
    
    protected $fillable = [
        'desa_id',
        'nama_desa',
        'kecamatan',
        'tahun',
        'skor_iks',
        'skor_ike',
        'skor_ikl',
        'skor_komposit',
        'status',
        'cluster',
        'verifikasi_status',
        'user_id',
        'created_by',
        'iks_detail',
        'ike_detail',
        'ikl_detail',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'skor_iks' => 'decimal:4',
        'skor_ike' => 'decimal:4',
        'skor_ikl' => 'decimal:4',
        'skor_komposit' => 'decimal:4',
        'iks_detail' => 'array',
        'ike_detail' => 'array',
        'ikl_detail' => 'array',
        'verified_at' => 'datetime',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}