@extends('layouts.app')

@section('title', 'Verifikasi Data Desa')
@section('active-verifikasi', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Verifikasi Data Desa</h3>
        <p>Lakukan peninjauan dan verifikasi terhadap usulan data Indeks Desa Membangun (IDM) dari desa-desa di wilayah Indramayu tahun anggaran 2026.</p>
    </div>
</div>

<!-- STATS KARTU -->
<div class="stats-grid">
    <div class="stat-card" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-color: #cbd5e1; position: relative; overflow: hidden;">
        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:#64748b;"></div>
        <div class="stat-icon" style="color:#64748b; font-size:1.4rem; margin-bottom:10px;"><i class="fas fa-layer-group"></i></div>
        <div class="stat-label">TOTAL UPLOAD</div>
        <div class="stat-number" style="color:#334155;">{{ $stats['total'] ?? 0 }}</div>
        <div class="stat-note">Seluruh upload desa</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; position: relative; overflow: hidden;">
        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:#f59e0b;"></div>
        <div class="stat-icon" style="color:#d97706; font-size:1.4rem; margin-bottom:10px;"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-label">MENUNGGU</div>
        <div class="stat-number" style="color:#92400e;">{{ $stats['menunggu'] ?? 0 }}</div>
        <div class="stat-note">Perlu ditinjau</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; position: relative; overflow: hidden;">
        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:#3b82f6;"></div>
        <div class="stat-icon" style="color:#2563eb; font-size:1.4rem; margin-bottom:10px;"><i class="fas fa-spinner"></i></div>
        <div class="stat-label">DIPROSES</div>
        <div class="stat-number" style="color:#1e3a8a;">{{ $stats['proses'] ?? 0 }}</div>
        <div class="stat-note">Sedang diverifikasi</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; position: relative; overflow: hidden;">
        <div style="position:absolute; top:0; left:0; right:0; height:4px; background:#10b981;"></div>
        <div class="stat-icon" style="color:#059669; font-size:1.4rem; margin-bottom:10px;"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">SELESAI</div>
        <div class="stat-number" style="color:#065f46;">{{ ($stats['terverifikasi'] ?? 0) + ($stats['ditolak'] ?? 0) }}</div>
        <div class="stat-note">Disetujui + Ditolak</div>
    </div>
</div>

<!-- SEARCH & FILTER -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <form method="GET" action="{{ route('verifikasi') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Cari desa atau file..." value="{{ request('search') }}" class="form-control" style="width: 220px;">
            <select name="status" class="form-control" style="width: 140px;">
                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Diproses</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>Ditolak/Revisi</option>
            </select>
            <select name="tahun" class="form-control" style="width: 130px;">
                <option value="semua" {{ request('tahun') == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach(($tahunList ?? collect()) as $tahun)
                    <option value="{{ $tahun }}" {{ (string) request('tahun') === (string) $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary" style="padding: 8px 16px;">Filter</button>
            <a href="{{ route('verifikasi') }}" class="btn-outline" style="padding: 8px 16px;">Reset</a>
        </form>
    </div>
    <button class="btn-outline" onclick="exportData()" style="font-size: 0.7rem;">📊 Ekspor Laporan</button>
</div>

<!-- TOAST NOTIFICATION -->
@if(session('toast'))
<div id="toastMessage" class="toast toast-{{ session('toast')['type'] }}">
    <i class="fas {{ session('toast')['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
    {{ session('toast')['message'] }}
</div>
@endif

<!-- TABEL UPLOAD DESA -->
<div class="card">
    <div class="card-title">Daftar Upload Data Desa</div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>DESA</th>
                    <th>TAHUN</th>
                    <th>NAMA FILE</th>
                    <th>TANGGAL UPLOAD</th>
                    <th>STATUS</th>
                    <th>VERIFIKATOR</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                <tr data-id="{{ $item->id }}" data-status="{{ $item->status }}" data-nama-file="{{ $item->nama_file }}" data-catatan="{{ $item->catatan }}">
                    <td><strong>{{ $item->desa->nama_desa ?? 'Desa' }}</strong></td>
                    <td>{{ $item->tahun ?? '-' }}</td>
                    <td>{{ $item->nama_file }}</td>
                    <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') : '-' }}</td>
                    <td>
                        @php
                            $badgeClass = match($item->status) {
                                'disetujui' => 'badge-success',
                                'revisi' => 'badge-danger',
                                'proses' => 'badge-warning',
                                default => 'badge-warning'
                            };
                            $statusText = match($item->status) {
                                'disetujui' => '✅ Disetujui',
                                'revisi' => '❌ Ditolak',
                                'proses' => '🔄 Diproses',
                                default => '⏳ Menunggu'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        @if($item->catatan)
                        <br><small class="catatan-text" style="font-size: 0.6rem; color: #6c7a91;">Catatan: {{ $item->catatan }}</small>
                        @endif
                    </td>
                    <td>
                        @if($item->verified_by)
                            {{ $item->verified_by }}<br>
                            <small style="color: #6c7a91;">
                                @if($item->verified_at)
                                    {{ \Carbon\Carbon::parse($item->verified_at)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </small>
                        @else
                            <span style="color: #6c7a91;">-</span>
                        @endif
                    </td>
                    <td class="action-cell">
                        <div class="action-buttons-flex">
                            <!-- TOMBOL EDIT -->
                            <button class="btn-edit" onclick="openEditModal({{ $item->id }})" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <!-- TOMBOL HAPUS -->
                            <button class="btn-delete" onclick="confirmDelete({{ $item->id }})" title="Hapus Data">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            
                            <!-- TOMBOL VERIFIKASI -->
                            @if($item->status == 'menunggu' || $item->status == 'proses')
                                <button class="btn-approve" onclick="openModal({{ $item->id }}, 'disetujui')" title="Setujui">
                                    <i class="fas fa-check-circle"></i> Setujui
                                </button>
                                <button class="btn-reject" onclick="openModal({{ $item->id }}, 'revisi')" title="Tolak">
                                    <i class="fas fa-times-circle"></i> Tolak
                                </button>
                                <button class="btn-proses" onclick="openModal({{ $item->id }}, 'proses')" title="Proses">
                                    <i class="fas fa-spinner"></i> Proses
                                </button>
                            @else
                                <span style="color: #6c7a91; font-style: italic;">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">Belum ada upload dari desa</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 16px;">
        {{ $riwayat->links() }}
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL VERIFIKASI (SETUJUI / TOLAK / PROSES) -->
<!-- ========================================== -->
<div id="verifModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 24px; max-width: 400px; width: 90%;">
        <h3 style="margin-bottom: 12px;" id="verifModalTitle">Berikan Catatan</h3>
        <p style="font-size: 0.7rem; color: #6c7a91; margin-bottom: 12px;">Berikan alasan atau catatan untuk verifikasi ini</p>
        <textarea id="catatanText" rows="3" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; font-family: 'Inter', sans-serif; margin-bottom: 16px;" placeholder="Contoh: Data sudah lengkap dan valid..."></textarea>
        <form id="verifForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="verifStatus">
            <input type="hidden" name="catatan" id="verifCatatan">
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeModal()" class="btn-outline">Batal</button>
                <button type="submit" class="btn-primary" id="submitBtn">Konfirmasi</button>
            </div>
        </form>
        <div id="loadingSpinner" style="display: none; text-align: center; padding: 10px;">
            <i class="fas fa-spinner fa-spin"></i> Memproses...
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT (UPDATE DATA) -->
<!-- ========================================== -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.1rem;">✏️ Edit Data Upload</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6c7a91;">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.8rem;">Nama File</label>
                <input type="text" name="nama_file" id="editNamaFile" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.8rem;">Status</label>
                <select name="status" id="editStatus" class="form-control" required>
                    <option value="menunggu">Menunggu</option>
                    <option value="proses">Diproses</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="revisi">Ditolak/Revisi</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.8rem;">Catatan</label>
                <textarea name="catatan" id="editCatatan" rows="3" class="form-control" placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 16px;">
                <button type="button" onclick="closeEditModal()" class="btn-outline">Batal</button>
                <button type="submit" class="btn-primary" id="editSubmitBtn">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI HAPUS (DELETE) -->
<!-- ========================================== -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 24px; max-width: 400px; width: 90%;">
        <h3 style="margin-bottom: 12px; color: #dc2626;">⚠️ Hapus Data?</h3>
        <p style="font-size: 0.85rem; color: #5b6e8c; margin-bottom: 20px;">Apakah Anda yakin ingin menghapus data upload ini? Tindakan ini tidak dapat dibatalkan.</p>
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

<!-- PANDUAN VERIFIKASI -->
<div class="card" style="background: #eef2ff; border-left: 4px solid #1f6e8c;">
    <div style="display: flex; gap: 12px; align-items: center;">
        <i class="fas fa-info-circle" style="color: #1f6e8c; font-size: 1.2rem;"></i>
        <div>
            <strong style="font-size: 0.8rem;">Panduan Verifikasi</strong><br>
            <span style="font-size: 0.7rem; color: #334155;">Pastikan Anda telah mengunduh dan memeriksa kelengkapan file Excel/PDF sebelum menekan tombol "Setujui". Jika ada data yang tidak sinkron, pilih "Tolak" dan berikan catatan spesifik agar desa dapat segera melakukan revisi.</span>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT -->
<!-- ========================================== -->
<script>
    let currentId = null;
    
    // =============================================
    // 1. MODAL VERIFIKASI (SETUJUI / TOLAK / PROSES)
        // =============================================
        function openModal(id, status) {
            currentId = id;
            document.getElementById('verifStatus').value = status;
            document.getElementById('verifModal').style.display = 'flex';
            
            // 🔥 PERBAIKI: Gunakan route yang benar
            document.getElementById('verifForm').action = '/verifikasi/update/' + id;
            
            document.getElementById('catatanText').value = '';
            
            let title = '';
            let btnText = 'Konfirmasi';
            if (status == 'disetujui') {
                title = '✅ Setujui Upload Ini?';
                btnText = '✅ Setujui';
            } else if (status == 'revisi') {
                title = '❌ Tolak Upload Ini?';
                btnText = '❌ Tolak';
            } else {
                title = '🔄 Proses Verifikasi';
                btnText = '🔄 Proses';
            }
            document.querySelector('#verifModal h3').innerHTML = title;
            document.getElementById('submitBtn').innerHTML = btnText;
        }
        
        function closeModal() {
            document.getElementById('verifModal').style.display = 'none';
            document.getElementById('catatanText').value = '';
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('submitBtn').style.display = 'inline-block';
        }
        
        document.getElementById('verifForm').addEventListener('submit', function(e) {
            document.getElementById('verifCatatan').value = document.getElementById('catatanText').value;
            document.getElementById('submitBtn').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'block';
        });
        
        // =============================================
        // 2. MODAL EDIT (UPDATE)
        // =============================================
        function openEditModal(id) {
            currentId = id;
            document.getElementById('editModal').style.display = 'flex';
            
            // 🔥 PERBAIKI: Gunakan route yang benar
            document.getElementById('editForm').action = '/verifikasi/edit/' + id;
            
            // Ambil data dari row tabel
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const namaFile = row.dataset.namaFile || '';
                const status = row.dataset.status || 'menunggu';
                const catatan = row.dataset.catatan || '';
                
                document.getElementById('editNamaFile').value = namaFile;
                document.getElementById('editStatus').value = status;
                document.getElementById('editCatatan').value = catatan;
            }
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // =============================================
        // 3. MODAL HAPUS (DELETE)
        // =============================================
        function confirmDelete(id) {
            currentId = id;
            document.getElementById('deleteModal').style.display = 'flex';
            
            // 🔥 PERBAIKI: Gunakan route yang benar
            document.getElementById('deleteForm').action = '/verifikasi/delete/' + id;
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        // =============================================
        // 4. EKSPORT
        // =============================================
        function exportData() {
            window.location.href = '{{ route("laporan.export") }}?tahun=' + new Date().getFullYear();
        }
        
        // =============================================
        // 5. AUTO HIDE TOAST
        // =============================================
        setTimeout(function() {
            let toast = document.getElementById('toastMessage');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 3000);
            }
        }, 4000);
        
        // =============================================
        // 6. TUTUP MODAL KETIKA KLIK DI LUAR MODAL
        // =============================================
        window.onclick = function(event) {
            const verifModal = document.getElementById('verifModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === verifModal) closeModal();
            if (event.target === editModal) closeEditModal();
            if (event.target === deleteModal) closeDeleteModal();
        }
</script>
<!-- ========================================== -->
<!-- STYLE TAMBAHAN -->
<!-- ========================================== -->
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
        transition: 0.2s;
    }
    .btn-edit:hover {
        background: #d97706;
    }
    .btn-delete {
        background: #dc2626;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 8px;
        transition: 0.2s;
    }
    .btn-delete:hover {
        background: #b91c1c;
    }
    .btn-approve {
        background: #059669;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 5px;
    }
    .btn-approve:hover {
        background: #047857;
    }
    .btn-reject {
        background: #dc2626;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 5px;
    }
    .btn-reject:hover {
        background: #b91c1c;
    }
    .btn-proses {
        background: #f97316;
        color: white;
        border: none;
<!-- PANDUAN VERIFIKASI -->
<div class="card" style="background: #eef2ff; border-left: 4px solid #1f6e8c;">
    <div style="display: flex; gap: 12px; align-items: center;">
        <i class="fas fa-info-circle" style="color: #1f6e8c; font-size: 1.2rem;"></i>
        <div>
            <strong style="font-size: 0.8rem;">Panduan Verifikasi</strong><br>
            <span style="font-size: 0.7rem; color: #334155;">Pastikan Anda telah mengunduh dan memeriksa kelengkapan file Excel/PDF sebelum menekan tombol "Setujui". Jika ada data yang tidak sinkron, pilih "Tolak" dan berikan catatan spesifik agar desa dapat segera melakukan revisi.</span>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT -->
<!-- ========================================== -->
<script>
    let currentId = null;
    
    // =============================================
    // 1. MODAL VERIFIKASI (SETUJUI / TOLAK / PROSES)
        // =============================================
        function openModal(id, status) {
            currentId = id;
            document.getElementById('verifStatus').value = status;
            document.getElementById('verifModal').style.display = 'flex';
            
            // 🔥 PERBAIKI: Gunakan route yang benar
            document.getElementById('verifForm').action = '/verifikasi/update/' + id;
            
            document.getElementById('catatanText').value = '';
            
            let title = '';
            let btnText = 'Konfirmasi';
            if (status == 'disetujui') {
                title = '✅ Setujui Upload Ini?';
                btnText = '✅ Setujui';
            } else if (status == 'revisi') {
                title = '❌ Tolak Upload Ini?';
                btnText = '❌ Tolak';
            } else {
                title = '🔄 Proses Verifikasi';
                btnText = '🔄 Proses';
            }
            document.querySelector('#verifModal h3').innerHTML = title;
            document.getElementById('submitBtn').innerHTML = btnText;
        }
        
        function closeModal() {
            document.getElementById('verifModal').style.display = 'none';
            document.getElementById('catatanText').value = '';
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('submitBtn').style.display = 'inline-block';
        }
        
        document.getElementById('verifForm').addEventListener('submit', function(e) {
            document.getElementById('verifCatatan').value = document.getElementById('catatanText').value;
            document.getElementById('submitBtn').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'block';
        });
        
        // =============================================
        // 2. MODAL EDIT (UPDATE)
        // =============================================
        function openEditModal(id) {
            currentId = id;
            document.getElementById('editModal').style.display = 'flex';
            
            // 🔥 PERBAIKI: Gunakan route yang benar
            document.getElementById('editForm').action = '/verifikasi/edit/' + id;
            
            // Ambil data dari row tabel
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const namaFile = row.dataset.namaFile || '';
                const status = row.dataset.status || 'menunggu';
                const catatan = row.dataset.catatan || '';
                
                document.getElementById('editNamaFile').value = namaFile;
                document.getElementById('editStatus').value = status;
                document.getElementById('editCatatan').value = catatan;
            }
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // =============================================
        // 3. MODAL HAPUS (DELETE)
        // =============================================
        function confirmDelete(id) {
            currentId = id;
            document.getElementById('deleteModal').style.display = 'flex';
            
            // 🔥 PERBAIKI: Gunakan route yang benar
            document.getElementById('deleteForm').action = '/verifikasi/delete/' + id;
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        // =============================================
        // 4. EKSPORT
        // =============================================
        function exportData() {
            window.location.href = '{{ route("laporan.export") }}?tahun=' + new Date().getFullYear();
        }
        
        // =============================================
        // 5. AUTO HIDE TOAST
        // =============================================
        setTimeout(function() {
            let toast = document.getElementById('toastMessage');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 3000);
            }
        }, 4000);
        
        // =============================================
        // 6. TUTUP MODAL KETIKA KLIK DI LUAR MODAL
        // =============================================
        window.onclick = function(event) {
            const verifModal = document.getElementById('verifModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === verifModal) closeModal();
            if (event.target === editModal) closeEditModal();
            if (event.target === deleteModal) closeDeleteModal();
        }
</script>
<!-- ========================================== -->
<!-- STYLE TAMBAHAN -->
<!-- ========================================== -->
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
        transition: 0.2s;
    }
    .btn-edit:hover {
        background: #d97706;
    }
    .btn-delete {
        background: #dc2626;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 8px;
        transition: 0.2s;
    }
    .btn-delete:hover {
        background: #b91c1c;
    }
    .btn-approve {
        background: #059669;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 5px;
    }
    .btn-approve:hover {
        background: #047857;
    }
    .btn-reject {
        background: #dc2626;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
        margin-right: 5px;
    }
    .btn-reject:hover {
        background: #b91c1c;
    }
    .btn-proses {
        background: #f97316;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
    }
    .btn-proses:hover {
        background: #ea580c;
    }
    .toast {
        position: fixed;
        bottom: 20px;
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
    .toast-warning { background: #f59e0b; color: white; }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* === CARD, TABLE, STATS-GRID STYLES === */
    .card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }
    .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #1e3a5f;
        border-left: 3px solid #2563eb;
        padding-left: 12px;
    }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: white !important;
        border-radius: 16px;
        padding: 20px 16px 16px;
        border: 1px solid #e2e8f0;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
    }
    /* Total — slate */
    .stat-total { background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important; border-color: #cbd5e1 !important; }
    .stat-total::before { background: #64748b !important; }
    .stat-total .stat-icon { color: #64748b !important; }
    .stat-total .stat-number { color: #334155 !important; }
    /* Menunggu — amber */
    .stat-menunggu { background: linear-gradient(135deg, #fffbeb, #fef3c7) !important; border-color: #fde68a !important; }
    .stat-menunggu::before { background: #f59e0b !important; }
    .stat-menunggu .stat-icon { color: #d97706 !important; }
    .stat-menunggu .stat-number { color: #92400e !important; }
    /* Diproses — blue */
    .stat-proses { background: linear-gradient(135deg, #eff6ff, #dbeafe) !important; border-color: #bfdbfe !important; }
    .stat-proses::before { background: #3b82f6 !important; }
    .stat-proses .stat-icon { color: #2563eb !important; }
    .stat-proses .stat-number { color: #1e3a8a !important; }
    /* Selesai — emerald */
    .stat-selesai { background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important; border-color: #a7f3d0 !important; }
    .stat-selesai::before { background: #10b981 !important; }
    .stat-selesai .stat-icon { color: #059669 !important; }
    .stat-selesai .stat-number { color: #065f46 !important; }
    .stat-icon { font-size: 1.4rem; margin-bottom: 10px; }
    .stat-label { font-size: 0.6rem; text-transform: uppercase; font-weight: 700; color: #5b6e8c; letter-spacing: 0.5px; }
    .stat-number { font-size: 2rem; font-weight: 800; margin: 6px 0 4px; }
    .stat-note { font-size: 0.62rem; color: #6c7a91; }

    .action-buttons-flex {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }
    .action-buttons-flex .btn-edit,
    .action-buttons-flex .btn-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        padding: 0;
        border-radius: 8px;
        font-size: 0.75rem;
        margin-right: 0;
    }
    .action-buttons-flex .btn-approve,
    .action-buttons-flex .btn-reject,
    .action-buttons-flex .btn-proses {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
        padding: 5px 10px;
        white-space: nowrap;
        margin-right: 0;
        font-size: 0.68rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        text-align: left;
        padding: 12px 10px;
        background: #f8fafc;
        font-size: 0.7rem;
        font-weight: 600;
        color: #1e3a5f;
        border-bottom: 2px solid #e2e8f0;
    }
    .table td {
        padding: 12px 10px;
        font-size: 0.75rem;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.65rem;
        font-weight: 600;
    }
    .badge-success { background: #d1fae5; color: #059669; }
    .badge-warning { background: #fed7aa; color: #c2410c; }
    .badge-danger { background: #fee2e2; color: #dc2626; }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-primary:hover {
        transform: scale(1.02);
        opacity: 0.9;
    }
    .btn-outline {
        background: transparent;
        border: 1px solid #cbd5e1;
        padding: 8px 20px;
        border-radius: 40px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-outline:hover {
        background: #f8fafc;
    }
    .action-cell {
        white-space: normal;
        min-width: 180px;
    }
    .form-group label {
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
        font-size: 0.8rem;
        color: #1e3a5f;
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .table th, .table td { font-size: 0.65rem; padding: 8px 4px; }
        .btn-approve, .btn-reject, .btn-proses { padding: 3px 8px; font-size: 0.6rem; }
        .btn-edit, .btn-delete { padding: 3px 6px; font-size: 0.6rem; }
    }
</style>
@endsection
