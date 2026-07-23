@extends('layouts.app')

@section('title', 'Laporan IDM')
@section('active-laporan', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Laporan Indeks Desa Membangun</h3>
        <p>Rekapitulasi dan analisis data IDM seluruh desa.</p>
    </div>
</div>

<!-- STATS CARD (DARI DATA REAL) -->
<div class="stats-grid">
    <div class="stat-card stat-total">
        <div class="stat-icon"><i class="fas fa-city"></i></div>
        <div class="stat-label">TOTAL DESA</div>
        <div class="stat-number">{{ $totalDesa }}</div>
        <div class="stat-note">Seluruh desa di wilayah</div>
    </div>
    <div class="stat-card stat-rata">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-label">SKOR RATA-RATA</div>
        <div class="stat-number">{{ number_format($rataSkor, 3) }}</div>
        <div class="stat-note">IDM Kabupaten</div>
    </div>
    <div class="stat-card stat-mandiri">
        <div class="stat-icon"><i class="fas fa-award"></i></div>
        <div class="stat-label">DESA MANDIRI</div>
        <div class="stat-number">{{ $desaMandiri }}</div>
        <div class="stat-note">Skor ≥ 0.8000</div>
    </div>
    <div class="stat-card stat-terverifikasi">
        <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-label">TERVERIFIKASI</div>
        <div class="stat-number">{{ $terverifikasi }}</div>
        <div class="stat-note">Data sudah diverifikasi</div>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
@if(session('toast'))
<div class="toast toast-{{ session('toast')['type'] }}">
    <i class="fas {{ session('toast')['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
    {{ session('toast')['message'] }}
</div>
@endif

<!-- SEARCH & EXPORT -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <form method="GET" action="{{ route('laporan') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Cari desa atau kecamatan..." value="{{ request('search') }}" class="form-control" style="width: 250px;">
        <select name="kecamatan" class="form-control" style="width: 180px;">
            <option value="">Semua Kecamatan</option>
            @foreach($daftarKecamatan as $kec)
            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
            @endforeach
        </select>
        <select name="tahun" class="form-control" style="width: 130px;">
            @foreach(($tahunList ?? collect([$tahun ?? date('Y')])) as $itemTahun)
                <option value="{{ $itemTahun }}" {{ (int) ($tahun ?? request('tahun')) === (int) $itemTahun ? 'selected' : '' }}>
                    {{ $itemTahun }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary" style="padding: 8px 16px;">Filter</button>
        <a href="{{ route('laporan') }}" class="btn-outline" style="padding: 8px 16px;">Reset</a>
    </form>
    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        @if(in_array($role, ['kabupaten', 'super_admin']))
            <form action="{{ route('laporan.seed-data') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memasukkan semua data IDM historis 2021-2025 secara otomatis untuk seluruh desa? Tindakan ini akan membuat data baru yang belum ada di database.')" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-primary" style="padding: 8px 16px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; font-weight: bold; border-radius: 40px; cursor: pointer; font-size: 0.75rem;">
                    ⚡ Auto-Generate Data 2021-2025
                </button>
            </form>
        @endif
        <a href="{{ route('laporan.pdf') }}?tahun={{ $tahun }}&search={{ request('search') }}&kecamatan={{ request('kecamatan') }}" 
           class="btn-primary" 
           style="padding: 8px 16px; text-decoration: none; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"
           target="_blank">
            📄 Cetak PDF Rekap
        </a>
        <a href="{{ route('laporan.export') }}?tahun={{ $tahun }}&search={{ request('search') }}&kecamatan={{ request('kecamatan') }}" 
           class="btn-outline" 
           style="padding: 8px 16px; text-decoration: none;">
            📊 Ekspor Excel
        </a>
    </div>
</div>

<!-- TABEL DAFTAR DESA -->
<div class="card">
    <div class="card-title">Daftar Desa</div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>TAHUN</th>
                    <th>NAMA DESA</th>
                    <th>KECAMATAN</th>
                    <th>SKOR IDM</th>
                    <th>STATUS</th>
                    <th>VERIFIKASI</th>
                    <th>FILE</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($desaList as $index => $desa)
                @php
                    $dataIdm = $desa->dataIdm->first();
                    $riwayat = $desa->riwayatUpload->first();
                    $skor = $dataIdm ? $dataIdm->skor_komposit : 0;
                    $status = $dataIdm ? $dataIdm->status : 'Belum Ada Data';
                    $verifikasi = $dataIdm ? $dataIdm->verifikasi_status : 'Belum';

                    $badgeClass = match($status) {
                        'mandiri' => 'badge-success',
                        'maju' => 'badge-info',
                        'berkembang' => 'badge-warning',
                        'tertinggal' => 'badge-danger',
                        default => 'badge-danger'
                    };

                    $badgeVerif = match($verifikasi) {
                        'verified' => 'badge-success',
                        'disetujui' => 'badge-success',
                        'ditolak' => 'badge-danger',
                        'revisi' => 'badge-danger',
                        'proses' => 'badge-warning',
                        default => 'badge-warning'
                    };

                    $statusTeks = match($status) {
                        'mandiri' => 'Mandiri',
                        'maju' => 'Maju',
                        'berkembang' => 'Berkembang',
                        'tertinggal' => 'Tertinggal',
                        default => 'Belum Ada Data'
                    };

                    $verifTeks = match($verifikasi) {
                        'verified' => 'Terverifikasi',
                        'disetujui' => 'Terverifikasi',
                        'ditolak' => 'Ditolak',
                        'revisi' => 'Revisi',
                        'proses' => 'Diproses',
                        default => 'Menunggu'
                    };
                @endphp
                <tr>
                    <td>{{ $desaList instanceof \Illuminate\Pagination\LengthAwarePaginator ? $desaList->firstItem() + $index : $index + 1 }}</td>
                    <td>{{ $tahun ?? request('tahun', date('Y')) }}</td>
                    <td><strong>{{ $desa->nama_desa }}</strong></td>
                    <td>{{ $desa->kecamatan }}</td>
                    <td>{{ $skor > 0 ? number_format($skor, 4) : '-' }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $statusTeks }}</span></td>
                    <td><span class="badge {{ $badgeVerif }}">{{ $verifTeks }}</span></td>
                    <td>
                        @if($riwayat && $riwayat->file_path)
                            <a href="{{ route('download.file', $riwayat->id) }}"
                               class="btn-download"
                               title="Download file: {{ $riwayat->nama_file }}"
                               target="_blank">
                                <i class="fas fa-file-excel"></i>
                                <span style="font-size: 0.65rem;">{{ Str::limit($riwayat->nama_file, 15) }}</span>
                                <small style="font-size: 0.55rem; color: #6c7a91; display: block;">
                                    {{ $riwayat->file_size_formatted ?? '' }}
                                </small>
                            </a>
                        @else
                            <span style="color: #6c7a91; font-size: 0.7rem;">-</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 4px; align-items: center;">
                            <a href="{{ route('laporan.detail', $desa->id) }}" class="btn-detail" style="padding: 5px 8px;">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            @if($riwayat && $riwayat->file_path)
                                <a href="{{ route('download.file', $riwayat->id) }}"
                                   class="btn-download-icon"
                                   title="Download file"
                                   style="padding: 5px 8px;">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                            @if(in_array($role, ['kabupaten', 'super_admin']) && $dataIdm)
                                <button type="button" class="btn-aksi btn-edit-data"
                                    onclick="openEditModal({{ $dataIdm->id }}, {{ $dataIdm->tahun }}, {{ $dataIdm->skor_iks }}, {{ $dataIdm->skor_ike }}, {{ $dataIdm->skor_ikl }}, '{{ $desa->nama_desa }}')"
                                    style="padding: 5px 8px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn-aksi btn-hapus-data"
                                    onclick="openDeleteModal({{ $dataIdm->id }}, '{{ $desa->nama_desa }}', {{ $dataIdm->tahun }})"
                                    style="padding: 5px 8px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #6c7a91;">
                        <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                        Belum ada data desa
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===== PAGINATION CUSTOM ===== --}}
    @if(isset($desaList) && method_exists($desaList, 'hasPages') && $desaList->hasPages())
    <div class="laporan-pagination-wrapper">
        <div class="laporan-pagination-info">
            Menampilkan
            <strong>{{ $desaList->firstItem() }}–{{ $desaList->lastItem() }}</strong>
            dari <strong>{{ $desaList->total() }}</strong> desa
        </div>
        <div class="laporan-pagination-controls">
            {{-- Tombol Prev --}}
            @if($desaList->onFirstPage())
                <span class="lap-page-btn lap-page-btn-disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $desaList->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="lap-page-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @foreach($desaList->getUrlRange(1, $desaList->lastPage()) as $page => $url)
                @if($page == $desaList->currentPage())
                    <span class="lap-page-btn lap-page-btn-active">{{ $page }}</span>
                @elseif(abs($page - $desaList->currentPage()) <= 2 || $page == 1 || $page == $desaList->lastPage())
                    <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="lap-page-btn">{{ $page }}</a>
                @elseif(abs($page - $desaList->currentPage()) == 3)
                    <span class="lap-page-btn lap-page-btn-dots">…</span>
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if($desaList->hasMorePages())
                <a href="{{ $desaList->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="lap-page-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="lap-page-btn lap-page-btn-disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
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
    /* Total — slate */
    .stat-total { background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-color: #cbd5e1; }
    .stat-total::before { background: #64748b; }
    .stat-total .stat-icon { color: #64748b; }
    .stat-total .stat-number { color: #334155; }
    /* Rerata — blue */
    .stat-rata { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; }
    .stat-rata::before { background: #3b82f6; }
    .stat-rata .stat-icon { color: #2563eb; }
    .stat-rata .stat-number { color: #1e3a8a; }
    /* Mandiri — emerald */
    .stat-mandiri { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; }
    .stat-mandiri::before { background: #10b981; }
    .stat-mandiri .stat-icon { color: #059669; }
    .stat-mandiri .stat-number { color: #065f46; }
    /* Terverifikasi — violet/purple */
    .stat-terverifikasi { background: linear-gradient(135deg, #faf5ff, #f3e8ff); border-color: #e9d5ff; }
    .stat-terverifikasi::before { background: #a855f7; }
    .stat-terverifikasi .stat-icon { color: #9333ea; }
    .stat-terverifikasi .stat-number { color: #6b21a8; }
    
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
        color: #1e3a5f;
        border-left: 3px solid #2563eb;
        padding-left: 12px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        text-align: left;
        padding: 12px 8px;
        background: #f8fafc;
        font-size: 0.7rem;
        font-weight: 600;
        color: #1e3a5f;
        border-bottom: 2px solid #e2e8f0;
    }
    .table td {
        padding: 12px 8px;
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
    .badge-danger  { background: #fee2e2; color: #dc2626; }
    .badge-info    { background: #dbeafe; color: #2563eb; }

    .btn-download {
        display: inline-block;
        background: #059669;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
        transition: 0.2s;
        margin-right: 4px;
    }
    .btn-download:hover { background: #047857; color: white; transform: translateY(-1px); }

    .btn-download-icon {
        display: inline-block;
        background: #059669;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-download-icon:hover { background: #047857; color: white; }

    .btn-detail {
        display: inline-block;
        background: #3b82f6;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
        transition: 0.2s;
        margin-right: 4px;
    }
    .btn-detail:hover { background: #2563eb; color: white; }

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
    }
    .btn-outline {
        background: transparent;
        border: 1px solid #cbd5e1;
        padding: 8px 20px;
        border-radius: 40px;
        font-size: 0.75rem;
        cursor: pointer;
        text-decoration: none;
        color: #1e3a5f;
    }
    .btn-outline:hover { background: #f1f5f9; }

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
    .toast-error   { background: #dc2626; color: white; }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }

    /* ====== Pagination Laporan ====== */
    .laporan-pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }
    .laporan-pagination-info {
        font-size: 0.75rem;
        color: #64748b;
    }
    .laporan-pagination-info strong {
        color: #1e293b;
        font-weight: 600;
    }
    .laporan-pagination-controls {
        display: flex;
        align-items: center;
        gap: 3px;
        flex-wrap: wrap;
    }
    .lap-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        border-radius: 7px;
        font-size: 0.72rem;
        font-weight: 500;
        text-decoration: none;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .lap-page-btn:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 6px rgba(37,99,235,0.2);
        transform: translateY(-1px);
    }
    .lap-page-btn-active {
        background: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
        box-shadow: 0 2px 6px rgba(37,99,235,0.2);
        cursor: default;
    }
    .lap-page-btn-active:hover { transform: none; }
    .lap-page-btn-disabled {
        background: #f1f5f9;
        color: #cbd5e1 !important;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .lap-page-btn-disabled:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
        box-shadow: none;
        transform: none;
    }
    .lap-page-btn-dots {
        background: transparent;
        border-color: transparent;
        color: #94a3b8 !important;
        cursor: default;
    }
    .lap-page-btn-dots:hover {
        background: transparent;
        border-color: transparent;
        box-shadow: none;
        transform: none;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .table th, .table td { font-size: 0.6rem; padding: 6px 4px; }
        .btn-download, .btn-detail, .btn-download-icon { font-size: 0.6rem; padding: 3px 6px; }
        .laporan-pagination-wrapper { flex-direction: column; align-items: flex-start; }
    }
    .btn-aksi { border: none; border-radius: 8px; cursor: pointer; font-size: 0.75rem; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
    .btn-edit-data  { background: #dbeafe; color: #2563eb; }
    .btn-edit-data:hover  { background: #2563eb; color: white; }
    .btn-hapus-data { background: #fee2e2; color: #dc2626; }
    .btn-hapus-data:hover { background: #dc2626; color: white; }
</style>

<!-- ========== MODAL EDIT ========== -->
<div id="modalEdit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; padding:28px; width:100%; max-width:480px; margin:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="font-size:1rem; color:#1e3a5f; font-weight:700;"><i class="fas fa-edit" style="color:#2563eb;"></i> Edit Data IDM (<span id="editDesaName"></span>)</h3>
            <button onclick="closeEditModal()" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#6c7a91;">&times;</button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div style="display:grid; gap:14px;">
                <div>
                    <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">Tahun</label>
                    <select name="tahun" id="editTahun" class="form-control" style="margin-top:6px; width: 100%;">
                        @foreach([2021,2022,2023,2024,2025] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
                    <div>
                        <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">IKS</label>
                        <input type="number" name="skor_iks" id="editIks" step="0.0001" min="0" max="1" class="form-control" style="margin-top:6px; width: 100%;" required>
                    </div>
                    <div>
                        <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">IKE</label>
                        <input type="number" name="skor_ike" id="editIke" step="0.0001" min="0" max="1" class="form-control" style="margin-top:6px; width: 100%;" required>
                    </div>
                    <div>
                        <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">IKL</label>
                        <input type="number" name="skor_ikl" id="editIkl" step="0.0001" min="0" max="1" class="form-control" style="margin-top:6px; width: 100%;" required>
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px; justify-content:flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn-outline" style="padding: 8px 16px;">Batal</button>
                <button type="submit" class="btn-primary" style="padding: 8px 16px;"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========== MODAL HAPUS ========== -->
<div id="modalHapus" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; padding:28px; width:100%; max-width:420px; margin:16px; text-align:center;">
        <div style="font-size:3rem; margin-bottom:12px;">⚠️</div>
        <h3 style="font-size:1rem; color:#1e3a5f; font-weight:700; margin-bottom:8px;">Konfirmasi Hapus</h3>
        <p style="font-size:0.8rem; color:#6c7a91;" id="hapusKonfirmasiText">Anda akan menghapus data ini.</p>
        <div style="display:flex; gap:10px; margin-top:20px; justify-content:center;">
            <button type="button" onclick="closeDeleteModal()" class="btn-outline" style="padding: 8px 16px;">Batal</button>
            <form id="formHapus" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 20px; border-radius:10px; font-weight:700; cursor:pointer;">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto hide toast setelah 3 detik
    setTimeout(function() {
        let toast = document.querySelector('.toast');
        if (toast) {
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, 3000);
        }
    }, 100);

    function openEditModal(id, tahun, iks, ike, ikl, namaDesa) {
        document.getElementById('formEdit').action = '/laporan/data/' + id;
        document.getElementById('editDesaName').innerText = namaDesa;
        document.getElementById('editTahun').value = tahun;
        document.getElementById('editIks').value = parseFloat(iks).toFixed(4);
        document.getElementById('editIke').value = parseFloat(ike).toFixed(4);
        document.getElementById('editIkl').value = parseFloat(ikl).toFixed(4);
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('modalEdit').style.display = 'none';
    }

    function openDeleteModal(id, namaDesa, tahun) {
        document.getElementById('formHapus').action = '/laporan/data/' + id;
        document.getElementById('hapusKonfirmasiText').innerText = 
            'Apakah Anda yakin ingin menghapus data IDM Desa ' + namaDesa + ' Tahun ' + tahun + '?';
        document.getElementById('modalHapus').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('modalHapus').style.display = 'none';
    }
</script>
@endsection
