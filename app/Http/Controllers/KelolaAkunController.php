<?php
// app/Http/Controllers/KelolaAkunController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class KelolaAkunController extends Controller
{
    /**
     * Halaman daftar akun
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        
        // Cek apakah tabel users ada
        if (!Schema::hasTable('users')) {
            return view('dashboard.kelola_akun', [
                'users' => collect(),
                'desaList' => collect(),
                'kecamatanList' => collect(),
                'kabupatenList' => collect(),
                'role' => $role,
                'error' => 'Tabel users belum ada. Jalankan migration terlebih dahulu.'
            ]);
        }
        
        // Cek kolom yang ada di tabel users
        $hasKecamatanId = Schema::hasColumn('users', 'kecamatan_id');
        $hasKabupatenId = Schema::hasColumn('users', 'kabupaten_id');
        $hasDesaId = Schema::hasColumn('users', 'desa_id');
        $hasUsername = Schema::hasColumn('users', 'username');
        $hasIsActive = Schema::hasColumn('users', 'is_active');
        $hasCreatedBy = Schema::hasColumn('users', 'created_by');
        $hasKabupatenTable = Schema::hasTable('kabupaten');
        
        $relations = ['desa', 'kecamatan', 'creator'];
        if ($hasKabupatenTable) {
            $relations[] = 'kabupaten';
        }

        $query = User::with($relations)->where('id', '!=', $user->id);
        
        // Filter berdasarkan role (hanya jika kolom ada)
        if ($role == 'kabupaten' && $hasKabupatenId && $user->kabupaten_id) {
            $query->where(function($q) use ($user) {
                $q->where('kabupaten_id', $user->kabupaten_id)
                  ->orWhereNull('kabupaten_id');
            });
        } elseif ($role == 'kecamatan' && $hasKecamatanId && $user->kecamatan_id) {
            $query->where('role', 'desa')
                  ->where('kecamatan_id', $user->kecamatan_id);
        }
        
        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $hasUsername) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
                if ($hasUsername) {
                    $q->orWhere('username', 'like', "%{$search}%");
                }
            });
        }
        
        // Filter role
        if ($request->filled('role') && $request->role != 'semua') {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Data untuk dropdown
        $desaList = collect();
        $kecamatanList = collect();
        $kabupatenList = collect();
        
        if (Schema::hasTable('desas')) {
            $hasDesaKecamatanId = Schema::hasColumn('desas', 'kecamatan_id');
            
            if ($role == 'kabupaten' && $hasKabupatenId && $user->kabupaten_id) {
                if (Schema::hasTable('kecamatan')) {
                    $kecamatanList = Kecamatan::where('kabupaten_id', $user->kabupaten_id)->get();
                    if ($hasDesaKecamatanId) {
                        $desaList = Desa::whereIn('kecamatan_id', $kecamatanList->pluck('id'))->get();
                    } else {
                        $desaList = Desa::all();
                    }
                }
                if ($hasKabupatenTable) {
                    $kabupatenList = Kabupaten::where('id', $user->kabupaten_id)->get();
                }
            } elseif ($role == 'kecamatan' && $hasKecamatanId && $user->kecamatan_id) {
                if ($hasDesaKecamatanId) {
                    $desaList = Desa::where('kecamatan_id', $user->kecamatan_id)->get();
                } else {
                    $desaList = Desa::all();
                }
                if (Schema::hasTable('kecamatan')) {
                    $kecamatanList = Kecamatan::where('id', $user->kecamatan_id)->get();
                }
            } else {
                // Super admin atau lainnya
                if (Schema::hasTable('kecamatan')) {
                    $kecamatanList = Kecamatan::all();
                }
                $desaList = Desa::all();
                if ($hasKabupatenTable) {
                    $kabupatenList = Kabupaten::all();
                }
            }
        }
        
        return view('dashboard.kelola_akun', compact('users', 'desaList', 'kecamatanList', 'kabupatenList', 'role', 'hasKabupatenTable'));
    }

    /**
     * Simpan akun baru
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validasi
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'role' => 'required|in:desa,kecamatan,kabupaten,super_admin',
                'desa_id' => 'nullable|exists:desas,id',
                'kecamatan_id' => 'nullable|exists:kecamatan,id',
            ];

            $rules['kabupaten_id'] = Schema::hasTable('kabupaten')
                ? 'nullable|exists:kabupaten,id'
                : 'nullable|integer';
            
            // Validasi username hanya jika kolom ada
            if (Schema::hasColumn('users', 'username')) {
                $rules['username'] = 'required|string|max:255|unique:users,username';
            }
            
            $request->validate($rules);
            
            // Validasi role berdasarkan pembuat
            if ($user->role == 'kecamatan' && $request->role != 'desa') {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Kecamatan hanya bisa membuat akun untuk desa!'
                ]);
            }
            
            if ($user->role == 'kabupaten' && !in_array($request->role, ['kecamatan', 'desa'])) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Kabupaten hanya bisa membuat akun untuk kecamatan dan desa!'
                ]);
            }
            
            if ($user->role == 'super_admin' && !in_array($request->role, ['kabupaten', 'kecamatan', 'desa'])) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Super Admin hanya bisa membuat akun untuk kabupaten, kecamatan, dan desa!'
                ]);
            }

            // Siapkan data
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ];
            
            // Tambahkan username jika kolom ada
            if (Schema::hasColumn('users', 'username')) {
                $data['username'] = $request->username;
            }
            
            // Tambahkan is_active jika kolom ada
            if (Schema::hasColumn('users', 'is_active')) {
                $data['is_active'] = true;
            }
            
            // Tambahkan created_by jika kolom ada
            if (Schema::hasColumn('users', 'created_by')) {
                $data['created_by'] = $user->id;
            }
            
            // Set relasi berdasarkan role
            if ($request->role == 'desa' && $request->filled('desa_id')) {
                if (Schema::hasColumn('users', 'desa_id')) {
                    $data['desa_id'] = $request->desa_id;
                }
                $desa = Desa::find($request->desa_id);
                if ($desa && Schema::hasColumn('users', 'kecamatan_id')) {
                    $data['kecamatan_id'] = $desa->kecamatan_id;
                }
                if (Schema::hasColumn('users', 'kabupaten_id')) {
                    $data['kabupaten_id'] = $user->kabupaten_id ?? null;
                }
            } elseif ($request->role == 'kecamatan' && $request->filled('kecamatan_id')) {
                if (Schema::hasColumn('users', 'kecamatan_id')) {
                    $data['kecamatan_id'] = $request->kecamatan_id;
                }
                if (Schema::hasColumn('users', 'kabupaten_id')) {
                    $data['kabupaten_id'] = $user->kabupaten_id ?? null;
                }
            } elseif ($request->role == 'kabupaten' && $request->filled('kabupaten_id')) {
                if (Schema::hasColumn('users', 'kabupaten_id')) {
                    $data['kabupaten_id'] = $request->kabupaten_id;
                }
            }
            
            // Buat user
            $newUser = User::create($data);
            
            return redirect()->route('kelola-akun')->with('toast', [
                'type' => 'success',
                'message' => 'Akun berhasil dibuat! Username: ' . ($request->username ?? $request->email)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Create account error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal membuat akun: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Tampilkan data untuk edit
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update akun
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();
            
            // Validasi akses
            if ($currentUser->role == 'kecamatan' && $user->role != 'desa') {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Anda tidak memiliki akses!'
                ]);
            }
            
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:desa,kecamatan,kabupaten,super_admin',
            ];
            
            // Validasi username hanya jika kolom ada
            if (Schema::hasColumn('users', 'username')) {
                $rules['username'] = 'required|string|max:255|unique:users,username,' . $id;
            }
            
            $request->validate($rules);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];
            
            // Tambahkan username jika kolom ada
            if (Schema::hasColumn('users', 'username')) {
                $data['username'] = $request->username;
            }
            
            // Update password jika diisi
            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6|confirmed']);
                $data['password'] = Hash::make($request->password);
            }
            
            // Update relasi
            if ($request->role == 'desa' && $request->filled('desa_id')) {
                if (Schema::hasColumn('users', 'desa_id')) {
                    $data['desa_id'] = $request->desa_id;
                }
                $desa = Desa::find($request->desa_id);
                if ($desa && Schema::hasColumn('users', 'kecamatan_id')) {
                    $data['kecamatan_id'] = $desa->kecamatan_id;
                }
            }
            
            if ($request->role == 'kecamatan' && $request->filled('kecamatan_id')) {
                if (Schema::hasColumn('users', 'kecamatan_id')) {
                    $data['kecamatan_id'] = $request->kecamatan_id;
                }
            }
            
            $user->update($data);
            
            return redirect()->route('kelola-akun')->with('toast', [
                'type' => 'success',
                'message' => 'Akun berhasil diperbarui!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update account error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal memperbarui akun: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Aktifkan/Nonaktifkan akun
     */
    public function toggleActive($id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();
            
            // Validasi akses
            if ($currentUser->role == 'kecamatan' && $user->role != 'desa') {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Anda tidak memiliki akses!'
                ]);
            }
            
            // Cek apakah kolom is_active ada
            if (!Schema::hasColumn('users', 'is_active')) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Kolom is_active tidak ditemukan!'
                ]);
            }
            
            $user->is_active = !$user->is_active;
            $user->save();
            
            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('kelola-akun')->with('toast', [
                'type' => 'success',
                'message' => "Akun berhasil {$status}!"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Toggle account error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal mengubah status akun: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus akun
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();
            
            // Validasi akses
            if ($currentUser->role == 'kecamatan' && $user->role != 'desa') {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Anda tidak memiliki akses!'
                ]);
            }
            
            // Jangan hapus sendiri
            if ($user->id == $currentUser->id) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Tidak bisa menghapus akun sendiri!'
                ]);
            }
            
            // Cek apakah user memiliki relasi
            if ($user->createdUsers()->count() > 0) {
                return redirect()->back()->with('toast', [
                    'type' => 'error',
                    'message' => 'Tidak bisa menghapus akun karena masih memiliki akun bawaan!'
                ]);
            }
            
            $user->delete();
            
            return redirect()->route('kelola-akun')->with('toast', [
                'type' => 'success',
                'message' => 'Akun berhasil dihapus!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete account error: ' . $e->getMessage());
            return redirect()->back()->with('toast', [
                'type' => 'error',
                'message' => 'Gagal menghapus akun: ' . $e->getMessage()
            ]);
        }
    }
}
