@extends('layouts.app')

@section('title', 'Beranda IDM')
@section('active-beranda', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Beranda IDM</h3>
        <p>Status dan pemanfaatan data Indeks Desa Membangun terkini.</p>
    </div>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">SKOR IDM {{ isset($dataIdm) ? $dataIdm->tahun : '2025' }}</div>
        <div class="stat-number">{{ isset($dataIdm) ? number_format($dataIdm->skor_komposit ?? 0, 4) : '0.0000' }}</div>
        <div class="stat-note">
            Status: 
            @php
                $status = isset($dataIdm) ? $dataIdm->status : 'tertinggal';
                $statusLabel = [
                    'mandiri' => 'Mandiri',
                    'maju' => 'Maju',
                    'berkembang' => 'Berkembang',
                    'tertinggal' => 'Tertinggal'
                ][$status] ?? 'Tertinggal';
            @endphp
            {{ $statusLabel }} | Target 0.9000
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">TOTAL UPLOAD</div>
        <div class="stat-number">
            {{ isset($riwayat) ? ($riwayat instanceof \Illuminate\Pagination\LengthAwarePaginator ? $riwayat->total() : $riwayat->count()) : 0 }}
        </div>
        <div class="stat-note">Riwayat pengiriman data</div>
    </div>
</div>


<!-- RIWAYAT UPLOAD -->
<div class="card">
    <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Riwayat Upload</span>
        @if(!isset($lihatSemua) || !$lihatSemua)
        <a href="{{ route('riwayat.lengkap') }}" style="color: #116b47; text-decoration: none; font-size: 0.7rem;">Lihat Semua →</a>
        @endif
    </div>
    
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr><th>TANGGAL</th><th>NAMA FILE</th><th>STATUS</th><th>CATATAN</th></tr>
            </thead>
            <tbody>
                @forelse($riwayat ?? [] as $item)
                <tr>
                    <td>{{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}</td>
                    <td>{{ $item->nama_file ?? '-' }}</td>
                    <td>
                        @php
                            $badgeClass = match($item->status ?? 'proses') {
                                'verified', 'disetujui' => 'badge-success',
                                'ditolak', 'revisi' => 'badge-danger',
                                'menunggu', 'proses' => 'badge-warning',
                                default => 'badge-warning'
                            };
                            $statusText = match($item->status ?? 'proses') {
                                'verified', 'disetujui' => 'Disetujui',
                                'ditolak', 'revisi' => 'Ditolak',
                                'menunggu', 'proses' => 'Menunggu',
                                default => 'Menunggu'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                    <td>{{ $item->keterangan ?? $item->catatan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #6c7a91;">
                        <i class="fas fa-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
                        Belum ada riwayat upload
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($lihatSemua) && $lihatSemua && isset($riwayat) && method_exists($riwayat, 'links'))
    <div style="margin-top: 16px;">{{ $riwayat->links() }}</div>
    <div style="margin-top: 12px; text-align: right;">
        <a href="{{ route('beranda') }}" class="btn-outline" style="padding: 6px 12px;">← Kembali</a>
    </div>
    @endif
</div>

<!-- DAFTAR DATA DESA TERBARU (Untuk Kabupaten/Kecamatan) -->
@if(isset($dataDesa) && $dataDesa->count() > 0)
<div class="card">
    <div class="card-title">📋 Data IDM Desa Terbaru</div>
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
                @foreach($dataDesa as $item)
                <tr>
                    <td><strong>{{ $item->nama_desa ?? $item->desa->nama_desa ?? '-' }}</strong></td>
                    <td>{{ $item->kecamatan ?? $item->desa->kecamatan ?? '-' }}</td>
                    <td>{{ $item->tahun ?? '-' }}</td>
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
                        @php
                            $verif = $item->verifikasi_status ?? 'menunggu';
                            $verifBadge = match($verif) {
                                'verified' => 'badge-success',
                                'ditolak' => 'badge-danger',
                                default => 'badge-warning'
                            };
                            $verifLabel = match($verif) {
                                'verified' => '✅ Terverifikasi',
                                'ditolak' => '❌ Ditolak',
                                default => '⏳ Menunggu'
                            };
                        @endphp
                        <span class="badge {{ $verifBadge }}">{{ $verifLabel }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tidak ada grafik di beranda desa — grafik ada di halaman Analisis
});
</script>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .stat-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 600;
        color: #5b6e8c;
        letter-spacing: 0.5px;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #0f3b4c;
        margin: 8px 0 4px;
    }
    .stat-note {
        font-size: 0.65rem;
        color: #6c7a91;
    }
    .card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #0f3b4c;
        border-left: 3px solid #116b47;
        padding-left: 12px;
    }
    .two-columns {
        display: none; /* Grafik dipindah ke halaman Analisis */
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .badge-success {
        background: #d1fae5;
        color: #059669;
    }
    .badge-warning {
        background: #fed7aa;
        color: #c2410c;
    }
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
    }
    .badge-info {
        background: #dbeafe;
        color: #2563eb;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .table th {
        text-align: left;
        padding: 12px 8px;
        background: #f8fafc;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.5px;
    }
    .table td {
        padding: 10px 8px;
        border-bottom: 1px solid #edf2f7;
        color: #1e293b;
    }
    .table tbody tr:hover {
        background: #f8fafc;
    }
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .greeting h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px 0;
    }
    .greeting p {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
    }
    .user-actions {
        display: flex;
        gap: 20px;
        font-size: 0.8rem;
        color: #475569;
    }
    .user-actions span {
        cursor: pointer;
        transition: color 0.3s;
    }
    .user-actions span:hover {
        color: #116b47;
    }
    .btn-outline {
        background: transparent;
        color: #116b47;
        border: 1px solid #116b47;
        padding: 6px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.7rem;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
    }
    .btn-outline:hover {
        background: #116b47;
        color: white;
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .two-columns {
            grid-template-columns: 1fr;
        }
        .top-bar {
            flex-direction: column;
            align-items: flex-start;
        }
        .user-actions {
            width: 100%;
            justify-content: flex-start;
            gap: 16px;
        }
    }
</style>
@endsection