@extends('layouts.app')

@section('title', 'Beranda Kabupaten')
@section('active-beranda', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Beranda Kabupaten</h3>
        <p>Status dan pemanfaatan data Indeks Desa Membangun di Kabupaten Indramayu.</p>
    </div>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">TOTAL DESA</div>
        <div class="stat-number">{{ $totalDesa }}</div>
        <div class="stat-note">Seluruh desa di kabupaten</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">SKOR RATA-RATA</div>
        <div class="stat-number">{{ number_format($rataSkor, 3) }}</div>
        <div class="stat-note">IDM Kabupaten</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">DESA MANDIRI</div>
        <div class="stat-number">{{ $desaMandiri }}</div>
        <div class="stat-note">Skor IDM ≥ 0.8</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">DESA MAJU</div>
        <div class="stat-number">{{ $desaMaju }}</div>
        <div class="stat-note">Skor IDM 0.7 - 0.8</div>
    </div>
</div>

<!-- GRAFIK TREN IDM -->
<div class="card">
    <div class="card-title">📈 Tren IDM Kabupaten (5 Tahun Terakhir)</div>
    <canvas id="trendChart" style="max-height: 300px; width: 100%;"></canvas>
    <div style="display: flex; justify-content: space-around; margin-top: 20px;">
        @foreach($tahunList as $index => $tahun)
        <div style="text-align: center;">
            <div style="font-size: 0.7rem; color: #5b6e8c;">{{ $tahun }}</div>
            <div style="font-size: 1rem; font-weight: 700;">{{ number_format($trenData[$index], 3) }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- GRAFIK SKOR PER KECAMATAN -->
<div class="card">
    <div class="card-title">🏆 Skor IDM per Kecamatan</div>
    <canvas id="kecamatanChart" style="max-height: 350px; width: 100%;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Grafik Tren
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($tahunList) !!},
            datasets: [{
                label: 'Skor IDM Rata-rata Kabupaten',
                data: {!! json_encode($trenData) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 5
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, max: 1 } } }
    });

    // Grafik Bar per Kecamatan
    new Chart(document.getElementById('kecamatanChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($kecamatanLabels) !!},
            datasets: [{
                label: 'Skor IDM Rata-rata',
                data: {!! json_encode($kecamatanValues) !!},
                backgroundColor: '#2563eb',
                borderRadius: 8
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, max: 1 } } }
    });
</script>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    .stat-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 600;
        color: #5b6e8c;
    }
    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e3a5f;
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
    }
    .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #1e3a5f;
        border-left: 3px solid #2563eb;
        padding-left: 12px;
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection