<?php
// app/Models/Desa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'desas';
    
    protected $fillable = [
        'nama_desa',
        'kode_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kecamatan_id',
        'alamat',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'luas_wilayah',
        'jumlah_penduduk',
        'kepala_desa',
        'nip_kepala_desa',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'luas_wilayah' => 'float',
        'jumlah_penduduk' => 'integer',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'desa_id');
    }

    public function dataIdm()
    {
        return $this->hasMany(DataIDM::class, 'desa_id');
    }

    public function riwayatUpload()
    {
        return $this->hasMany(RiwayatUpload::class, 'desa_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}