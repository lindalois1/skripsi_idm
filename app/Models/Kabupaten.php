<?php
// app/Models/Kabupaten.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    
    protected $fillable = [
        'nama_kabupaten',
        'kode_kabupaten',
        'provinsi_id',
        'alamat',
        'telepon',
        'email',
        'website',
        'bupati',
        'nip_bupati',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
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