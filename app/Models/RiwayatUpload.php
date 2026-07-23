<?php
// app/Models/RiwayatUpload.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatUpload extends Model
{
    protected $fillable = [
        'user_id',
        'desa_id',
        'nama_file',
        'file_path',
        'ukuran',
        'tahun',
        'status',
        'keterangan',
        'catatan',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Accessor untuk mendapatkan URL file
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    // Accessor untuk mendapatkan ukuran file dalam format yang terbaca
    public function getFileSizeFormattedAttribute()
    {
        if (!$this->ukuran) return '-';
        
        $bytes = $this->ukuran;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Cek apakah file ada
    public function getFileExistsAttribute()
    {
        if ($this->file_path) {
            return file_exists(storage_path('app/public/' . $this->file_path));
        }
        return false;
    }
}