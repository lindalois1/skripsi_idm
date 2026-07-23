<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'desa_id',
        'kecamatan_id',
        'kabupaten_id',
        'is_active',
        'created_by',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
    ];

    // 🔥 HAPUS ATAU KOMENTAR INI - Biarkan default menggunakan email
    // public function getAuthIdentifierName()
    // {
    //     return 'username';
    // }

    /**
     * Get the desa associated with the user.
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    /**
     * Get the kecamatan associated with the user.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the kabupaten associated with the user.
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    /**
     * Get the user who created this account.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users created by this user.
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Scope for active users only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users only.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope by role.
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope by desa.
     */
    public function scopeByDesa($query, $desaId)
    {
        return $query->where('desa_id', $desaId);
    }

    /**
     * Scope by kecamatan.
     */
    public function scopeByKecamatan($query, $kecamatanId)
    {
        return $query->where('kecamatan_id', $kecamatanId);
    }

    /**
     * Scope by kabupaten.
     */
    public function scopeByKabupaten($query, $kabupatenId)
    {
        return $query->where('kabupaten_id', $kabupatenId);
    }

    /**
     * Check if user has role desa.
     */
    public function isDesa()
    {
        return $this->role === 'desa';
    }

    /**
     * Check if user has role kecamatan.
     */
    public function isKecamatan()
    {
        return $this->role === 'kecamatan';
    }

    /**
     * Check if user has role kabupaten.
     */
    public function isKabupaten()
    {
        return $this->role === 'kabupaten';
    }

    /**
     * Check if user has role super_admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user can create accounts.
     */
    public function canCreateAccounts()
    {
        return in_array($this->role, ['kecamatan', 'kabupaten', 'super_admin']);
    }

    /**
     * Check if user can manage specific role.
     */
    public function canManageRole($targetRole)
    {
        $allowed = [
            'super_admin' => ['super_admin', 'kabupaten', 'kecamatan', 'desa'],
            'kabupaten' => ['kecamatan', 'desa'],
            'kecamatan' => ['desa'],
        ];

        return in_array($targetRole, $allowed[$this->role] ?? []);
    }

    /**
     * Get role label in Indonesian.
     */
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'super_admin' => '⭐ Super Admin',
            'kabupaten' => '🏛️ Kabupaten',
            'kecamatan' => '🏢 Kecamatan',
            'desa' => '🏘️ Desa',
            default => '❓ Unknown',
        };
    }

    /**
     * Get role badge class.
     */
    public function getRoleBadgeAttribute()
    {
        return match($this->role) {
            'super_admin' => 'badge-super',
            'kabupaten' => 'badge-info',
            'kecamatan' => 'badge-warning',
            'desa' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    /**
     * Get location name based on role.
     */
    public function getLocationAttribute()
    {
        return match($this->role) {
            'desa' => $this->desa?->nama_desa ?? '-',
            'kecamatan' => $this->kecamatan?->nama_kecamatan ?? '-',
            'kabupaten' => $this->kabupaten?->nama_kabupaten ?? '-',
            'super_admin' => 'Semua Wilayah',
            default => '-',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? '✅ Aktif' : '❌ Nonaktif';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? 'badge-success' : 'badge-danger';
    }

    /**
     * Get all roles available.
     */
    public static function getRoles()
    {
        return [
            'super_admin' => '⭐ Super Admin',
            'kabupaten' => '🏛️ Kabupaten',
            'kecamatan' => '🏢 Kecamatan',
            'desa' => '🏘️ Desa',
        ];
    }

    /**
     * Get roles that can be created by specific role.
     */
    public static function getCreatableRoles($byRole)
    {
        return match($byRole) {
            'super_admin' => ['kabupaten', 'kecamatan', 'desa'],
            'kabupaten' => ['kecamatan', 'desa'],
            'kecamatan' => ['desa'],
            default => [],
        };
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
        });

        static::deleting(function ($user) {
            if ($user->createdUsers()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus akun karena masih memiliki akun bawaan.');
            }
        });
    }
}