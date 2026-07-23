@extends('layouts.app')

@section('title', 'Verifikasi Data IDM')
@section('active-verifikasi', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Verifikasi Data IDM</h3>
        <p>Verifikasi data yang telah diupload oleh desa.</p>
    </div>
</div>

<!-- NOTIFIKASI TOAST -->
@if(session('toast'))
<div class="toast-notification toast-{{ session('toast.type') }}">
    <i class="fas {{ session('toast.type') == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
    {{ session('toast.message') }}
</div>
@endif

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card stat-menunggu">
        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-label">MENUNGGU VERIFIKASI</div>
        <div class="stat-number">{{ $statistik['total_menunggu'] ?? 0 }}</div>
        <div class="stat-note">Data yang perlu diverifikasi</div>
    </div>
    <div class="stat-card stat-terverifikasi">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">TERVERIFIKASI</div>
        <div class="stat-number">{{ $statistik['total_terverifikasi'] ?? 0 }}</div>
        <div class="stat-note">Data sudah diverifikasi</div>
    </div>
    <div class="stat-card stat-ditolak">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-label">DITOLAK</div>
        <div class="stat-number">{{ $statistik['total_ditolak'] ?? 0 }}</div>
        <div class="stat-note">Data perlu revisi</div>
    </div>
</div>

<!-- DATA MENUNGGU VERIFIKASI -->
<div class="card">
    <div class="card-title">
        <span>⏳ Data Menunggu Verifikasi</span>
        <span class="badge badge-warning">{{ $dataMenunggu->count() }} Data</span>
    </div>
    
    @if($dataMenunggu->count() > 0)
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>DESA</th>
                    <th>KECAMATAN</th>
                    <th>TAHUN</th>
                    <th>IKS</th>
                    <th>IKE</th>
                    <th>IKL</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataMenunggu as $item)
                <tr>
                    <td><strong>{{ $item->nama_desa ?? $item->desa->nama_desa ?? '-' }}</strong></td>
                    <td>{{ $item->kecamatan ?? $item->desa->kecamatan ?? '-' }}</td>
                    <td>{{ $item->tahun }}</td>
                    <td>{{ number_format($item->skor_iks ?? 0, 4) }}</td>
                    <td>{{ number_format($item->skor_ike ?? 0, 4) }}</td>
                    <td>{{ number_format($item->skor_ikl ?? 0, 4) }}</td>
                    <td>
                        <span class="badge badge-warning">Menunggu</span>
                    </td>
                    <td>
                        <button class="btn-verifikasi" onclick="openModal({{ $item->id }})">
                            <i class="fas fa-check-circle"></i> Verifikasi
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: #6c7a91;">
        <i class="fas fa-check-circle" style="font-size: 2rem; color: #059669; display: block; margin-bottom: 12px;"></i>
        Semua data sudah diverifikasi. Tidak ada data yang menunggu.
    </div>
    @endif
</div>

<!-- DATA TERVERIFIKASI -->
<div class="card">
    <div class="card-title">
        <span>✅ Data Terverifikasi</span>
        <span class="badge badge-success">{{ $dataTerverifikasi->count() }} Data</span>
    </div>
    
    @if($dataTerverifikasi->count() > 0)
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>DESA</th>
                    <th>KECAMATAN</th>
                    <th>TAHUN</th>
                    <th>IKS</th>
                    <th>IKE</th>
                    <th>IKL</th>
                    <th>STATUS</th>
                    <th>VERIFIKASI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataTerverifikasi as $item)
                <tr>
                    <td><strong>{{ $item->nama_desa ?? $item->desa->nama_desa ?? '-' }}</strong></td>
                    <td>{{ $item->kecamatan ?? $item->desa->kecamatan ?? '-' }}</td>
                    <td>{{ $item->tahun }}</td>
                    <td>{{ number_format($item->skor_iks ?? 0, 4) }}</td>
                    <td>{{ number_format($item->skor_ike ?? 0, 4) }}</td>
                    <td>{{ number_format($item->skor_ikl ?? 0, 4) }}</td>
                    <td>
                        @php
                            $status = $item->status ?? 'tertinggal';
                            $badgeClass = match($status) {
                                'mandiri' => 'badge-success',
                                'maju' => 'badge-info',
                                'berkembang' => 'badge-warning',
                                default => 'badge-danger'
                            };
                            $statusLabel = match($status) {
                                'mandiri' => 'Mandiri',
                                'maju' => 'Maju',
                                'berkembang' => 'Berkembang',
                                default => 'Tertinggal'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <span class="badge badge-success">✅ Terverifikasi</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 20px; color: #6c7a91;">
        Belum ada data yang terverifikasi.
    </div>
    @endif
</div>

<!-- MODAL VERIFIKASI -->
<div id="verifikasiModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Verifikasi Data IDM</h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form id="verifikasiForm" method="POST" action="">
            @csrf
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memverifikasi data ini?</p>
                <div class="form-group">
                    <label for="status">Status Verifikasi</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="disetujui">✅ Setujui</option>
                        <option value="ditolak">❌ Tolak (Revisi)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="catatan">Catatan (Opsional)</label>
                    <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary">Simpan Verifikasi</button>
            </div>
        </form>
    </div>
</div>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card {
        background: white;
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
    /* Menunggu — amber */
    .stat-menunggu { background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; }
    .stat-menunggu::before { background: #f59e0b; }
    .stat-menunggu .stat-icon { color: #d97706; }
    .stat-menunggu .stat-number { color: #92400e; }
    /* Terverifikasi — emerald */
    .stat-terverifikasi { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; }
    .stat-terverifikasi::before { background: #10b981; }
    .stat-terverifikasi .stat-icon { color: #059669; }
    .stat-terverifikasi .stat-number { color: #065f46; }
    /* Ditolak — red */
    .stat-ditolak { background: linear-gradient(135deg, #fef2f2, #fee2e2); border-color: #fca5a5; }
    .stat-ditolak::before { background: #ef4444; }
    .stat-ditolak .stat-icon { color: #dc2626; }
    .stat-ditolak .stat-number { color: #991b1b; }
    
    .stat-icon { font-size: 1.4rem; margin-bottom: 10px; }
    .stat-label { font-size: 0.6rem; text-transform: uppercase; font-weight: 700; color: #5b6e8c; letter-spacing: 0.5px; }
    .stat-number { font-size: 2rem; font-weight: 800; margin: 6px 0 4px; }
    .stat-note { font-size: 0.62rem; color: #6c7a91; }
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
        color: #0f3b4c;
        border-left: 3px solid #116b47;
        padding-left: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    .badge-info { background: #dbeafe; color: #2563eb; }
    .table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .table th { text-align: left; padding: 12px 8px; background: #f8fafc; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
    .table td { padding: 10px 8px; border-bottom: 1px solid #edf2f7; }
    .btn-verifikasi {
        background: #2563eb;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.7rem;
        transition: all 0.3s;
    }
    .btn-verifikasi:hover { background: #1d4ed8; }
    .btn-primary {
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-secondary {
        background: #e2e8f0;
        color: #475569;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary:hover { background: #cbd5e1; }
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { margin: 0; }
    .modal-close { font-size: 1.5rem; cursor: pointer; color: #6c7a91; }
    .modal-close:hover { color: #dc2626; }
    .modal-body { padding: 20px; }
    .modal-footer { padding: 16px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 4px; color: #475569; }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.85rem;
    }
    .form-control:focus { outline: none; border-color: #2563eb; }
    .toast-notification {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }
    .toast-success { background: #d1fae5; color: #059669; border-color: #059669; }
    .toast-error { background: #fee2e2; color: #dc2626; border-color: #dc2626; }
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .greeting h3 { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
    .greeting p { font-size: 0.85rem; color: #64748b; margin: 0; }
    .user-actions { display: flex; gap: 20px; font-size: 0.8rem; color: #475569; }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .top-bar { flex-direction: column; align-items: flex-start; }
    }
</style>

<script>
    function openModal(id) {
        const modal = document.getElementById('verifikasiModal');
        const form = document.getElementById('verifikasiForm');
        form.action = '/verifikasi/' + id + '/update';
        modal.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('verifikasiModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('verifikasiModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection