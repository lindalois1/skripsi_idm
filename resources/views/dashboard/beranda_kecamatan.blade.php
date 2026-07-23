@extends('layouts.app')

@section('title', 'Beranda IDM - Kecamatan')
@section('active-beranda', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Beranda IDM Kecamatan</h3>
        <p>Status dan verifikasi data Indeks Desa Membangun di wilayah kecamatan.</p>
    </div>
</div>

<!-- STATS 4 KARTU -->
<div class="stats-grid">
    <div class="stat-card stat-total">
        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
        <div class="stat-label">TOTAL UPLOAD</div>
        <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
        <div class="stat-note">Seluruh upload desa</div>
    </div>
    <div class="stat-card stat-menunggu">
        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-label">MENUNGGU VERIFIKASI</div>
        <div class="stat-number">{{ $stats['menunggu'] ?? 0 }}</div>
        <div class="stat-note">Perlu ditinjau</div>
    </div>
    <div class="stat-card stat-proses">
        <div class="stat-icon"><i class="fas fa-spinner"></i></div>
        <div class="stat-label">DIPROSES</div>
        <div class="stat-number">{{ $stats['proses'] ?? 0 }}</div>
        <div class="stat-note">Sedang diverifikasi</div>
    </div>
    <div class="stat-card stat-selesai">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">SELESAI</div>
        <div class="stat-number">{{ ($stats['terverifikasi'] ?? 0) + ($stats['ditolak'] ?? 0) }}</div>
        <div class="stat-note">Disetujui + Ditolak</div>
    </div>
</div>

{{-- Info: grafik tren & dimensi ada di halaman Analisis --}}
<div class="info-banner">
    <i class="fas fa-info-circle"></i>
    Grafik tren IDM, sebaran status desa, dan perbandingan dimensi tersedia di menu
    <a href="{{ route('analisis.kecamatan') }}" style="color: #2563eb; font-weight: 600;">Analisis</a>.
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
    /* Menunggu — amber */
    .stat-menunggu { background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; }
    .stat-menunggu::before { background: #f59e0b; }
    .stat-menunggu .stat-icon { color: #d97706; }
    .stat-menunggu .stat-number { color: #92400e; }
    /* Diproses — blue */
    .stat-proses { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; }
    .stat-proses::before { background: #3b82f6; }
    .stat-proses .stat-icon { color: #2563eb; }
    .stat-proses .stat-number { color: #1e3a8a; }
    /* Selesai — emerald */
    .stat-selesai { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; }
    .stat-selesai::before { background: #10b981; }
    .stat-selesai .stat-icon { color: #059669; }
    .stat-selesai .stat-number { color: #065f46; }
    .stat-icon { font-size: 1.4rem; margin-bottom: 10px; }
    .stat-label { font-size: 0.6rem; text-transform: uppercase; font-weight: 700; color: #5b6e8c; letter-spacing: 0.5px; }
    .stat-number { font-size: 2rem; font-weight: 800; margin: 6px 0 4px; }
    .stat-note { font-size: 0.62rem; color: #6c7a91; }
    .info-banner {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 0.8rem;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }
    .info-banner a:hover { text-decoration: underline; }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection