@extends('layouts.app')

@section('title', 'Laporan Detail Desa')
@section('active-laporan', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Laporan Detail Desa</h3>
        <p>{{ $desa->nama_desa ?? 'Desa' }}, Kec. {{ $desa->kecamatan ?? '-' }}</p>
    </div>
    <a href="{{ route('laporan') }}" class="btn-outline">← Kembali</a>
</div>

@if($dataIdm)
<!-- Jika data IDM tersedia -->
<div class="stats-grid">
    <div class="stat-card stat-status">
        <div class="stat-icon"><i class="fas fa-flag"></i></div>
        <div class="stat-label">STATUS IDM</div>
        <div class="stat-number">{{ ucfirst($dataIdm->status ?? 'Belum Ada Data') }}</div>
        <div class="stat-note">Skor komposit desa</div>
    </div>
    <div class="stat-card stat-skor">
        <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
        <div class="stat-label">SKOR KOMPOSIT</div>
        <div class="stat-number">{{ $dataIdm->skor_komposit ?? 0 }}</div>
        <div class="stat-note">Target Nasional: 0.7500</div>
    </div>
    <div class="stat-card stat-berkas">
        <div class="stat-icon"><i class="fas fa-folder-open"></i></div>
        <div class="stat-label">KELENGKAPAN BERKAS</div>
        <div class="stat-number">92%</div>
        <div class="stat-note">Lengkap</div>
    </div>
    <div class="stat-card stat-verif">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-label">VERIFIKASI CAMAT</div>
        <div class="stat-number">{{ ucfirst($dataIdm->verifikasi_status ?? 'Belum') }}</div>
        <div class="stat-note">
            Ditinjau: 
            @if($dataIdm->updated_at)
                {{ \Carbon\Carbon::parse($dataIdm->updated_at)->format('d M Y') }}
            @else
                -
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-title">Detail Rekapitulasi Dimensi</div>
    <table class="table">
        <thead>
            <tr>
                <th>INDIKATOR DIMENSI</th>
                <th>BOBOT</th>
                <th>NILAI 2024</th>
                <th>NILAI 2025</th>
                <th>STATUS</th>
                <th>PROGRESS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Dimensi Sosial (IKS)</strong><br><small>Kesehatan, Pendidikan, Modal Sosial</small></td>
                <td>0.333</div>
                <td>{{ number_format($dimensiData['iks']['nilai_lalu'] ?? 0, 4) }}</div>
                <td>{{ number_format($dimensiData['iks']['nilai_sekarang'] ?? 0, 4) }}</div>
                <td>
                    @php
                        $iksStatus = $dimensiData['iks']['status'] ?? 'TERENDAH';
                        $iksBadge = match($iksStatus) {
                            'OPTIMAL' => 'badge-success',
                            'PENINGKATAN' => 'badge-warning',
                            default => 'badge-danger'
                        };
                    @endphp
                    <span class="badge {{ $iksBadge }}">{{ $iksStatus }}</span>
                </div>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $dimensiData['iks']['progress'] ?? 0 }}%"></div>
                    </div>
                    {{ $dimensiData['iks']['progress'] ?? 0 }}%
                </div>
            </div>
            <tr>
                <td><strong>Dimensi Ekonomi (IKE)</strong><br><small>Keragaman Produksi, Akses Pasar</small></div>
                <td>0.333</div>
                <td>{{ number_format($dimensiData['ike']['nilai_lalu'] ?? 0, 4) }}</div>
                <td>{{ number_format($dimensiData['ike']['nilai_sekarang'] ?? 0, 4) }}</div>
                <td>
                    @php
                        $ikeStatus = $dimensiData['ike']['status'] ?? 'TERENDAH';
                        $ikeBadge = match($ikeStatus) {
                            'OPTIMAL' => 'badge-success',
                            'PENINGKATAN' => 'badge-warning',
                            default => 'badge-danger'
                        };
                    @endphp
                    <span class="badge {{ $ikeBadge }}">{{ $ikeStatus }}</span>
                </div>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $dimensiData['ike']['progress'] ?? 0 }}%"></div>
                    </div>
                    {{ $dimensiData['ike']['progress'] ?? 0 }}%
                </div>
            </div>
            <tr>
                <td><strong>Dimensi Lingkungan (IKL)</strong><br><small>Kualitas Lingkungan, Bencana Alam</small></div>
                <td>0.333</div>
                <td>{{ number_format($dimensiData['ikl']['nilai_lalu'] ?? 0, 4) }}</div>
                <td>{{ number_format($dimensiData['ikl']['nilai_sekarang'] ?? 0, 4) }}</div>
                <td>
                    @php
                        $iklStatus = $dimensiData['ikl']['status'] ?? 'TERENDAH';
                        $iklBadge = match($iklStatus) {
                            'OPTIMAL' => 'badge-success',
                            'PENINGKATAN' => 'badge-warning',
                            default => 'badge-danger'
                        };
                    @endphp
                    <span class="badge {{ $iklBadge }}">{{ $iklStatus }}</span>
                </div>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $dimensiData['ikl']['progress'] ?? 0 }}%"></div>
                    </div>
                    {{ $dimensiData['ikl']['progress'] ?? 0 }}%
                </div>
            </div>
        </tbody>
    </div>
</div>

<!-- Total Nilai IDM Desa -->
<div class="card" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <strong style="font-size: 0.9rem;">Total Nilai IDM Desa</strong>
            <div style="font-size: 2rem; font-weight: 700;">{{ number_format($dataIdm->skor_komposit ?? 0, 4) }}</div>
            <small>Meningkat dari tahun sebelumnya</small>
        </div>
        <i class="fas fa-chart-line" style="font-size: 3rem; opacity: 0.5;"></i>
    </div>
</div>

@else
<!-- Jika data IDM belum tersedia -->
<div class="card" style="text-align: center; padding: 40px;">
    <i class="fas fa-database" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
    <h3 style="color: #5b6e8c;">Belum Ada Data IDM</h3>
    <p style="color: #6c7a91; margin-top: 8px;">Desa {{ $desa->nama_desa ?? 'ini' }} belum menginput data IDM untuk tahun ini.</p>
    <a href="{{ route('input.data') }}" class="btn-primary" style="margin-top: 16px; display: inline-block;">Input Data Sekarang</a>
</div>
@endif

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
    /* Status — emerald */
    .stat-status { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; }
    .stat-status::before { background: #10b981; }
    .stat-status .stat-icon { color: #059669; }
    .stat-status .stat-number { color: #065f46; }
    /* Skor — blue */
    .stat-skor { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; }
    .stat-skor::before { background: #3b82f6; }
    .stat-skor .stat-icon { color: #2563eb; }
    .stat-skor .stat-number { color: #1e3a8a; }
    /* Berkas — amber */
    .stat-berkas { background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; }
    .stat-berkas::before { background: #f59e0b; }
    .stat-berkas .stat-icon { color: #d97706; }
    .stat-berkas .stat-number { color: #92400e; }
    /* Verif — violet */
    .stat-verif { background: linear-gradient(135deg, #faf5ff, #f3e8ff); border-color: #e9d5ff; }
    .stat-verif::before { background: #a855f7; }
    .stat-verif .stat-icon { color: #9333ea; }
    .stat-verif .stat-number { color: #6b21a8; }
    
    .stat-icon { font-size: 1.4rem; margin-bottom: 10px; }
    .stat-label { font-size: 0.6rem; text-transform: uppercase; font-weight: 700; color: #5b6e8c; letter-spacing: 0.5px; }
    .stat-number { font-size: 1.6rem; font-weight: 800; margin: 6px 0 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
    .progress-bar {
        background: #e2e8f0;
        border-radius: 10px;
        height: 6px;
        overflow: hidden;
        width: 80px;
        display: inline-block;
        margin-right: 8px;
    }
    .progress-fill {
        background: #2563eb;
        height: 6px;
    }
    .btn-outline {
        background: transparent;
        border: 1px solid #cbd5e1;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-outline:hover {
        background: #f1f5f9;
        border-color: #2563eb;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .table th, .table td { font-size: 0.65rem; padding: 8px 4px; }
    }
</style>
@endsection