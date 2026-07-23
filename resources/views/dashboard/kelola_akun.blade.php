@extends('layouts.app')

@section('title', 'Kelola Akun')
@section('active-kelola-akun', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>👥 Kelola Akun</h3>
        <p>
            @if($role == 'kabupaten')
                Kelola akun untuk kecamatan dan desa di Kabupaten Anda
            @elseif($role == 'kecamatan')
                Kelola akun untuk desa di Kecamatan Anda
            @elseif($role == 'super_admin')
                Kelola semua akun pengguna
            @else
                Daftar akun pengguna
            @endif
        </p>
    </div>
    <button class="btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Tambah Akun
    </button>
</div>

<!-- TOAST -->
@if(session('toast'))
<div class="toast toast-{{ session('toast')['type'] }}">
    <i class="fas {{ session('toast')['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
    {{ session('toast')['message'] }}
</div>
@endif

@if(isset($error))
<div class="toast toast-error">
    <i class="fas fa-exclamation-circle"></i> {{ $error }}
</div>
@endif

<!-- SEARCH & FILTER -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <form method="GET" action="{{ route('kelola-akun') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Cari nama atau username..." value="{{ request('search') }}" class="form-control" style="width: 220px;">
        <select name="role" class="form-control" style="width: 140px;">
            <option value="semua" {{ request('role') == 'semua' ? 'selected' : '' }}>Semua Role</option>
            <option value="desa" {{ request('role') == 'desa' ? 'selected' : '' }}>Desa</option>
            @if($role == 'kabupaten' || $role == 'super_admin')
            <option value="kecamatan" {{ request('role') == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
            @endif
            @if($role == 'super_admin')
            <option value="kabupaten" {{ request('role') == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
            @endif
        </select>
        <button type="submit" class="btn-primary" style="padding: 8px 16px;">Filter</button>
        <a href="{{ route('kelola-akun') }}" class="btn-outline" style="padding: 8px 16px;">Reset</a>
    </form>
</div>

<!-- TABEL -->
<div class="card">
    <div class="card-title">Daftar Akun</div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>USERNAME</th>
                    <th>EMAIL</th>
                    <th>ROLE</th>
                    <th>LOKASI</th>
                    <th>STATUS</th>
                    <th>DIBUAT OLEH</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $key => $item)
                <tr>
                    <td>{{ $users->firstItem() + $key }}</td>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ $item->username ?? '-' }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        @php
                            $roleBadge = match($item->role) {
                                'kabupaten' => 'badge-info',
                                'kecamatan' => 'badge-warning',
                                'desa' => 'badge-success',
                                'super_admin' => 'badge-super',
                                default => 'badge-secondary'
                            };
                            $roleLabel = match($item->role) {
                                'super_admin' => '⭐ Super Admin',
                                'kabupaten' => '🏛️ Kabupaten',
                                'kecamatan' => '🏢 Kecamatan',
                                'desa' => '🏘️ Desa',
                                default => ucfirst($item->role)
                            };
                        @endphp
                        <span class="badge {{ $roleBadge }}">{{ $roleLabel }}</span>
                    </td>
                    <td>
                        @if($item->role == 'desa' && $item->desa)
                            {{ $item->desa->nama_desa }}
                        @elseif($item->role == 'kecamatan' && $item->kecamatan)
                            {{ $item->kecamatan->nama_kecamatan }}
                        @elseif($item->role == 'kabupaten' && ($hasKabupatenTable ?? false) && $item->kabupaten)
                            {{ $item->kabupaten->nama_kabupaten }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $item->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                        </span>
                    </td>
                    <td>{{ $item->creator->name ?? '-' }}</td>
                    <td class="action-cell">
                        <button class="btn-edit" onclick="openEditModal({{ $item->id }})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-toggle" onclick="toggleActive({{ $item->id }})" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fas {{ $item->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                        </button>
                        <button class="btn-delete" onclick="confirmDelete({{ $item->id }}, '{{ $item->name }}')" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #6c7a91;">
                        <i class="fas fa-users" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                        Belum ada akun yang dibuat
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan
            <strong>{{ $users->firstItem() }}–{{ $users->lastItem() }}</strong>
            dari <strong>{{ $users->total() }}</strong> akun
        </div>
        <div class="pagination-controls">
            @if($users->onFirstPage())
                <span class="page-btn page-btn-disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="page-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @elseif(abs($page - $users->currentPage()) <= 2 || $page == 1 || $page == $users->lastPage())
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @elseif(abs($page - $users->currentPage()) == 3)
                    <span class="page-btn page-btn-dots">…</span>
                @endif
            @endforeach

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="page-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="page-btn page-btn-disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL CREATE AKUN -->
<!-- ========================================== -->
<div id="createModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.1rem;">➕ Tambah Akun Baru</h3>
            <button onclick="closeCreateModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6c7a91;">&times;</button>
        </div>
        <form id="createForm" method="POST" action="{{ route('kelola-akun.store') }}">
            @csrf
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required placeholder="Nama lengkap pengguna">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Username untuk login">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required placeholder="email@domain.com">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Role</label>
                <select name="role" id="createRole" class="form-control" required onchange="toggleCreateLocation(this.value)">
                    <option value="desa">Desa</option>
                    @if($role == 'kabupaten' || $role == 'super_admin')
                    <option value="kecamatan">Kecamatan</option>
                    @endif
                    @if($role == 'super_admin')
                    <option value="kabupaten">Kabupaten</option>
                    @endif
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 12px;" id="createDesaContainer">
                <label>Pilih Desa</label>
                <select name="desa_id" class="form-control">
                    <option value="">Pilih Desa</option>
                    @foreach($desaList as $desa)
                    <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 12px; display: none;" id="createKecamatanContainer">
                <label>Pilih Kecamatan</label>
                <select name="kecamatan_id" class="form-control">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatanList as $kec)
                    <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 16px;">
                <button type="button" onclick="closeCreateModal()" class="btn-outline">Batal</button>
                <button type="submit" class="btn-primary">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT AKUN -->
<!-- ========================================== -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.1rem;">✏️ Edit Akun</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6c7a91;">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Username</label>
                <input type="text" name="username" id="editUsername" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Email</label>
                <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Password (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Role</label>
                <select name="role" id="editRole" class="form-control" required>
                    <option value="desa">Desa</option>
                    @if($role == 'kabupaten' || $role == 'super_admin')
                    <option value="kecamatan">Kecamatan</option>
                    @endif
                    @if($role == 'super_admin')
                    <option value="kabupaten">Kabupaten</option>
                    @endif
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label>Pilih Desa</label>
                <select name="desa_id" id="editDesa" class="form-control">
                    <option value="">Pilih Desa</option>
                    @foreach($desaList as $desa)
                    <option value="{{ $desa->id }}">{{ $desa->nama_desa }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 16px;">
                <button type="button" onclick="closeEditModal()" class="btn-outline">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DELETE -->
<!-- ========================================== -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 24px; max-width: 400px; width: 90%;">
        <h3 style="margin-bottom: 12px; color: #dc2626;">⚠️ Hapus Akun?</h3>
        <p style="font-size: 0.85rem; color: #5b6e8c; margin-bottom: 20px;">
            Apakah Anda yakin ingin menghapus akun <strong id="deleteName"></strong>? 
            Tindakan ini tidak dapat dibatalkan.
        </p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeDeleteModal()" class="btn-outline">Batal</button>
                <button type="submit" class="btn-reject" style="padding: 10px 24px;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentId = null;
    
    function openCreateModal() {
        document.getElementById('createModal').style.display = 'flex';
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }
    
    function toggleCreateLocation(role) {
        const desaContainer = document.getElementById('createDesaContainer');
        const kecContainer = document.getElementById('createKecamatanContainer');
        
        if (role == 'desa') {
            desaContainer.style.display = 'block';
            kecContainer.style.display = 'none';
        } else if (role == 'kecamatan') {
            desaContainer.style.display = 'none';
            kecContainer.style.display = 'block';
        } else {
            desaContainer.style.display = 'none';
            kecContainer.style.display = 'none';
        }
    }
    
    function openEditModal(id) {
        currentId = id;
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('editForm').action = '/kelola-akun/' + id;
        
        fetch('/kelola-akun/' + id + '/edit')
            .then(response => response.json())
            .then(data => {
                document.getElementById('editName').value = data.name;
                document.getElementById('editUsername').value = data.username || '';
                document.getElementById('editEmail').value = data.email;
                document.getElementById('editRole').value = data.role;
                
                if (data.role == 'desa' && data.desa_id) {
                    document.getElementById('editDesa').value = data.desa_id;
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    
    function toggleActive(id) {
        if (confirm('Ubah status akun ini?')) {
            window.location.href = '/kelola-akun/' + id + '/toggle';
        }
    }
    
    function confirmDelete(id, name) {
        currentId = id;
        document.getElementById('deleteModal').style.display = 'flex';
        document.getElementById('deleteForm').action = '/kelola-akun/' + id;
        document.getElementById('deleteName').textContent = name;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    window.onclick = function(event) {
        const createModal = document.getElementById('createModal');
        const editModal = document.getElementById('editModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === createModal) closeCreateModal();
        if (event.target === editModal) closeEditModal();
        if (event.target === deleteModal) closeDeleteModal();
    }
</script>

<style>
    .btn-edit {
        background: #f59e0b;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 4px;
    }
    .btn-edit:hover { background: #d97706; }
    
    .btn-toggle {
        background: #6366f1;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 4px;
    }
    .btn-toggle:hover { background: #4f46e5; }
    
    .btn-delete {
        background: #dc2626;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 8px;
    }
    .btn-delete:hover { background: #b91c1c; }
    
    .btn-reject {
        background: #dc2626;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-reject:hover { background: #b91c1c; }
    
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 12px;
        z-index: 2000;
        animation: slideIn 0.3s ease;
        transition: opacity 0.5s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .toast-success { background: #059669; color: white; }
    .toast-error { background: #dc2626; color: white; }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .action-cell {
        white-space: nowrap;
    }
    /* ====== Pagination ====== */
    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .pagination-info {
        font-size: 0.78rem;
        color: #64748b;
    }
    .pagination-info strong {
        color: #1e293b;
        font-weight: 600;
    }
    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 500;
        text-decoration: none;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .page-btn:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37,99,235,0.25);
        transform: translateY(-1px);
    }
    .page-btn-active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37,99,235,0.25);
        cursor: default;
    }
    .page-btn-active:hover {
        transform: none;
    }
    .page-btn-disabled {
        background: #f1f5f9;
        color: #cbd5e1;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .page-btn-disabled:hover {
        background: #f1f5f9;
        color: #cbd5e1;
        border-color: #e2e8f0;
        box-shadow: none;
        transform: none;
    }
    .page-btn-dots {
        background: transparent;
        border-color: transparent;
        color: #94a3b8;
        cursor: default;
    }
    .page-btn-dots:hover {
        background: transparent;
        border-color: transparent;
        color: #94a3b8;
        box-shadow: none;
        transform: none;
    }
</style>
@endsection
