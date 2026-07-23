<?php
// app/Models/Kecamatan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    
    protected $fillable = [
        'nama_kecamatan',
        'kode_kecamatan',
        'kabupaten_id',
        'alamat',
        'telepon',
        'email',
        'camat',
        'nip_camat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function desa()
    {
        return $this->hasMany(Desa::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}