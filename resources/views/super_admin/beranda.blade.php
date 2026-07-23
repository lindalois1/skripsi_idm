@extends('layouts.app')

@section('title', 'Beranda Super Admin')
@section('active-super-beranda', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Beranda Super Admin</h3>
        <p>Dashboard koordinasi satu data Indeks Desa Membangun seluruh kabupaten.</p>
    </div>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">TOTAL KECAMATAN</div>
        <div class="stat-number">{{ $totalKecamatan }}</div>
        <div class="stat-note">Kecamatan di Kabupaten</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">TOTAL DESA</div>
        <div class="stat-number">{{ $totalDesa }}</div>
        <div class="stat-note">Desa terdata IDM</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">SKOR RATA-RATA</div>
        <div class="stat-number">{{ number_format($rataSkor, 4) }}</div>
        <div class="stat-note">IDM Tingkat Kabupaten</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">DESA MANDIRI</div>
        <div class="stat-number">{{ $desaMandiri }}</div>
        <div class="stat-note">Skor IDM ≥ 0.8155</div>
    </div>
</div>

<div class="two-columns">
    <!-- GRAFIK TREN IDM -->
    <div class="card">
        <div class="card-title">📈 Tren IDM Kabupaten (5 Tahun Terakhir)</div>
        <canvas id="trendChart" style="max-height: 250px; width: 100%;"></canvas>
    </div>

    <!-- SEBARAN STATUS DESA -->
    <div class="card" style="display: flex; flex-direction: column;">
        <div class="card-title">📊 Distribusi Status IDM Desa</div>
        <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
            <canvas id="statusChart" style="max-height: 200px; max-width: 200px;"></canvas>
        </div>
        <div class="status-legend" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 15px;">
            <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 6px;">
                <span style="display:inline-block; width:12px; height:12px; background-color:#059669; border-radius:3px;"></span>
                <span>Mandiri: <strong>{{ $desaMandiri }}</strong></span>
            </div>
            <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 6px;">
                <span style="display:inline-block; width:12px; height:12px; background-color:#2563eb; border-radius:3px;"></span>
                <span>Maju: <strong>{{ $desaMaju }}</strong></span>
            </div>
            <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 6px;">
                <span style="display:inline-block; width:12px; height:12px; background-color:#f97316; border-radius:3px;"></span>
                <span>Berkembang: <strong>{{ $desaBerkembang }}</strong></span>
            </div>
            <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 6px;">
                <span style="display:inline-block; width:12px; height:12px; background-color:#dc2626; border-radius:3px;"></span>
                <span>Tertinggal: <strong>{{ $desaTertinggal }}</strong></span>
            </div>
        </div>
    </div>
</div>

<!-- GRAFIK SKOR PER KECAMATAN -->
<div class="card">
    <div class="card-title">🏆 Rata-rata Skor IDM per Kecamatan</div>
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
                label: 'Skor IDM',
                data: {!! json_encode($trenData) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 5
            }]
        },
        options: { 
            responsive: true, 
            scales: { 
                y: { min: 0, max: 1 } 
            } 
        }
    });

    // Grafik Doughnut Status
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Mandiri', 'Maju', 'Berkembang', 'Tertinggal'],
            datasets: [{
                data: {!! json_encode($statusData) !!},
                backgroundColor: ['#059669', '#2563eb', '#f97316', '#dc2626'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Grafik Bar per Kecamatan
    new Chart(document.getElementById('kecamatanChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($kecamatanLabels) !!},
            datasets: [{
                label: 'Rata-rata Skor IDM',
                data: {!! json_encode($kecamatanValues) !!},
                backgroundColor: '#2563eb',
                borderRadius: 8
            }]
        },
        options: { 
            responsive: true, 
            scales: { 
                y: { min: 0, max: 1 } 
            } 
        }
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
    .two-columns {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .two-columns { grid-template-columns: 1fr; }
    }
</style>
@endsection
