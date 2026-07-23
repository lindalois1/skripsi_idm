@extends('layouts.app')

@section('title', 'Analisis IDM Kabupaten')
@section('active-analisis', 'active')


@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>{{ $pageTitle ?? 'Analisis IDM Kabupaten' }}</h3>
        <p>Analisis tren, dimensi, prediksi, dan pengelompokan data Indeks Desa Membangun.</p>
    </div>
</div>

<form method="GET" action="{{ route($analysisRoute ?? 'analisis') }}" class="year-filter">
    <label for="tahun">Tahun Analisis</label>
    <select name="tahun" id="tahun" onchange="this.form.submit()">
        <option value="semua" {{ ($tahunAnalisis ?? '') === 'semua' ? 'selected' : '' }}>Semua Tahun</option>
        @foreach(($tahunList ?? range(2021, 2025)) as $tahun)
            <option value="{{ $tahun }}" {{ ($tahunAnalisis ?? '') !== 'semua' && (int) ($tahunAnalisis ?? date('Y')) === (int) $tahun ? 'selected' : '' }}>
                {{ $tahun }}
            </option>
        @endforeach
    </select>
</form>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card stat-total">
        <div class="stat-icon"><i class="fas fa-city"></i></div>
        <div class="stat-label">TOTAL DESA</div>
        <div class="stat-number">{{ $totalDesa }}</div>
        <div class="stat-note">Master desa Indramayu</div>
    </div>
    <div class="stat-card stat-sudah">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">SUDAH INPUT</div>
        <div class="stat-number">{{ $sudahInput ?? 0 }}</div>
        <div class="stat-note">{{ $persentaseInput ?? 0 }}% desa punya data IDM</div>
    </div>
    <div class="stat-card stat-belum">
        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-label">BELUM INPUT</div>
        <div class="stat-number">{{ $belumInput ?? 0 }}</div>
        <div class="stat-note">Menunggu upload/verifikasi</div>
    </div>
    <div class="stat-card stat-terklaster">
        <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
        <div class="stat-label">DATA TERKLASTER</div>
        <div class="stat-number">{{ ($mandiri ?? 0) + ($maju ?? 0) + ($berkembang ?? 0) + ($tertinggal ?? 0) }}</div>
        <div class="stat-note">Mandiri, maju, berkembang, tertinggal</div>
    </div>
</div>

<!-- ========== TREN DAN PERBANDINGAN DIMENSI ========== -->
<div class="analysis-grid">
    <div class="card">
        <div class="card-title">
            <i class="fas fa-arrow-trend-up"></i> Tren Skor IDM 2021-2025
        </div>
        <canvas id="trendIdmChart" height="120"></canvas>
        <div style="overflow-x: auto; margin-top: 16px;">
            <table class="table compact-table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Skor IDM</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($trenTahunan ?? []) as $item)
                        <tr>
                            <td>{{ $item['tahun'] }}</td>
                            <td>{{ $item['jumlah_data'] > 0 ? number_format($item['skor_idm'], 4) : '-' }}</td>
                            <td>{{ $item['jumlah_data'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-title">
            <i class="fas fa-chart-column"></i> Perbandingan Dimensi
        </div>
        <canvas id="dimensiCompareChart" height="120"></canvas>
        <div style="overflow-x: auto; margin-top: 16px;">
            <table class="table compact-table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>IKS</th>
                        <th>IKE</th>
                        <th>IKL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($trenTahunan ?? []) as $item)
                        <tr>
                            <td>{{ $item['tahun'] }}</td>
                            <td>{{ $item['jumlah_data'] > 0 ? number_format($item['iks'], 4) : '-' }}</td>
                            <td>{{ $item['jumlah_data'] > 0 ? number_format($item['ike'], 4) : '-' }}</td>
                            <td>{{ $item['jumlah_data'] > 0 ? number_format($item['ikl'], 4) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========== PREDIKSI IDM ========== -->
<div class="card prediction-card">
    <div class="card-title">
        <i class="fas fa-chart-line"></i> Prediksi IDM Tahun Berikutnya
    </div>
    <div class="prediction-grid">
        <div class="prediction-summary">
            <div class="prediction-label">Prediksi Tahun {{ $prediksi['tahun_berikutnya'] ?? '-' }}</div>
            <div class="prediction-score">{{ number_format($prediksi['skor_prediksi'] ?? 0, 4) }}</div>
            <div class="prediction-status">{{ $prediksi['status_prediksi'] ?? 'Belum ada data' }}</div>
            <div class="prediction-note">
                Tren: {{ $prediksi['tren'] ?? 'Belum ada data' }}
                @if(!($prediksi['cukup_data'] ?? false))
                    <br>Minimal butuh 2 tahun data untuk membaca tren.
                @endif
            </div>
        </div>
        <div class="prediction-history">
            <div class="mini-title">Riwayat Rata-rata IDM</div>
            <div style="overflow-x: auto;">
                <table class="table compact-table">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Rata-rata IDM</th>
                            <th>Jumlah Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($prediksi['riwayat'] ?? []) as $item)
                            <tr>
                                <td>{{ $item['tahun'] }}</td>
                                <td>{{ number_format($item['rata_rata'], 4) }}</td>
                                <td>{{ $item['jumlah_data'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6c7a91;">Belum ada data historis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="prediction-footnote">
                Prediksi memakai regresi linear sederhana dari maksimal 5 tahun data IDM terakhir.
            </p>
        </div>
    </div>
</div>

<!-- ========== ANALISIS CLUSTERING ========== -->
<div class="card">
    <div class="card-title">
        <i class="fas fa-chart-pie"></i> Analisis Clustering Desa
    </div>
    <p style="font-size: 0.75rem; color: #5b6e8c; margin-bottom: 20px;">
        Pengelompokan desa berdasarkan skor IDM dan status kemandirian untuk menentukan prioritas intervensi pembangunan.
    </p>

    @if(session('success'))
        <div style="padding: 12px 16px; background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; border-radius: 12px; margin-bottom: 20px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle" style="color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div style="padding: 12px 16px; background-color: #fffbeb; border: 1px solid #f59e0b; color: #92400e; border-radius: 12px; margin-bottom: 20px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Form Clustering K-Means -->
    <form action="{{ route('clustering.process') }}" method="POST" style="margin-bottom: 24px;">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahunAnalisis ?? 'semua' }}">
        <button type="submit"
            class="bg-blue-700 text-white px-4 py-2 rounded-lg"
            style="background-color: #1d4ed8; color: white; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: background-color 0.2s;">
            <i class="fas fa-sync-alt"></i> Proses Clustering K-Means
        </button>
    </form>
    
    <!-- 4 Kartu Klaster -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="cluster-card cluster-unggul">
            <div class="cluster-icon"><i class="fas fa-crown"></i></div>
            <div class="cluster-name">Klaster Unggul</div>
            <div class="cluster-count">{{ $clusterUnggul }} Desa</div>
            <div class="cluster-desc">Skor IDM ≥ 0.8<br>Status MANDIRI</div>
            <div class="cluster-rekom">🏆 Pertahankan prestasi</div>
        </div>
        <div class="cluster-card cluster-potensial">
            <div class="cluster-icon"><i class="fas fa-chart-line"></i></div>
            <div class="cluster-name">Klaster Potensial</div>
            <div class="cluster-count">{{ $clusterPotensial }} Desa</div>
            <div class="cluster-desc">Skor IDM 0.7 - 0.8<br>Status MAJU</div>
            <div class="cluster-rekom">📈 Dorong ke mandiri</div>
        </div>
        <div class="cluster-card cluster-berkembang">
            <div class="cluster-icon"><i class="fas fa-seedling"></i></div>
            <div class="cluster-name">Klaster Berkembang</div>
            <div class="cluster-count">{{ $clusterBerkembang }} Desa</div>
            <div class="cluster-desc">Skor IDM 0.6 - 0.7<br>Status BERKEMBANG</div>
            <div class="cluster-rekom">🌱 Perkuat program</div>
        </div>
        <div class="cluster-card cluster-prioritas">
            <div class="cluster-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="cluster-name">Klaster Prioritas</div>
            <div class="cluster-count">{{ $clusterPrioritas }} Desa</div>
            <div class="cluster-desc">Skor IDM < 0.6<br>Status TERTINGGAL</div>
            <div class="cluster-rekom">⚠️ Intervensi khusus</div>
        </div>
    </div>
    
    <!-- Tabel Detail Klaster -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Klaster</th>
                    <th>Jumlah Desa</th>
                    <th>Skor Rata-rata</th>
                    <th>Skor Terendah</th>
                    <th>Skor Tertinggi</th>
                    <th>Rekomendasi Strategi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-unggul">🏆 Unggul</span></td>
                    <td><strong>{{ $clusterUnggul }}</strong></td>
                    <td>{{ number_format($clusterUnggulRata, 3) }}</td>
                    <td>{{ number_format($clusterUnggulMin, 3) }}</td>
                    <td>{{ number_format($clusterUnggulMax, 3) }}</td>
                    <td>Pertahankan dan kembangkan program unggulan, jadikan percontohan</td>
                </tr>
                <tr>
                    <td><span class="badge badge-potensial">📈 Potensial</span></td>
                    <td><strong>{{ $clusterPotensial }}</strong></td>
                    <td>{{ number_format($clusterPotensialRata, 3) }}</td>
                    <td>{{ number_format($clusterPotensialMin, 3) }}</td>
                    <td>{{ number_format($clusterPotensialMax, 3) }}</td>
                    <td>Dorong peningkatan infrastruktur dan ekonomi menuju mandiri</td>
                </tr>
                <tr>
                    <td><span class="badge badge-berkembang">🌱 Berkembang</span></td>
                    <td><strong>{{ $clusterBerkembang }}</strong></td>
                    <td>{{ number_format($clusterBerkembangRata, 3) }}</td>
                    <td>{{ number_format($clusterBerkembangMin, 3) }}</td>
                    <td>{{ number_format($clusterBerkembangMax, 3) }}</td>
                    <td>Perkuat program pemberdayaan ekonomi dan sosial masyarakat</td>
                </tr>
                <tr>
                    <td><span class="badge badge-prioritas">⚠️ Prioritas</span></td>
                    <td><strong>{{ $clusterPrioritas }}</strong></td>
                    <td>{{ number_format($clusterPrioritasRata, 3) }}</td>
                    <td>{{ number_format($clusterPrioritasMin, 3) }}</td>
                    <td>{{ number_format($clusterPrioritasMax, 3) }}</td>
                    <td>Intervensi khusus percepatan pembangunan, alokasi anggaran prioritas</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========== TABEL CENTROID ========== -->
<div class="card">
    <div class="card-title">
        <i class="fas fa-dot-circle"></i> Titik Pusat Klaster (Centroid)
    </div>
    <p style="font-size: 0.75rem; color: #5b6e8c; margin-bottom: 16px;">
        Nilai centroid merupakan nilai rata-rata dari setiap fitur dalam suatu klaster yang merepresentasikan karakteristik pusat klaster.
    </p>
    
    <div style="overflow-x: auto;">
        <table class="table centroid-table">
            <thead>
                <tr>
                    <th>Klaster</th>
                    <th>IKS (Centroid)</th>
                    <th>IKE (Centroid)</th>
                    <th>IKL (Centroid)</th>
                    <th>IDM (Centroid)</th>
                    <th>Jarak ke Pusat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-unggul">🏆 Unggul</span></td>
                    <td><strong>{{ number_format($centroidUnggulIks, 3) }}</strong></td>
                    <td><strong>{{ number_format($centroidUnggulIke, 3) }}</strong></td>
                    <td><strong>{{ number_format($centroidUnggulIkl, 3) }}</strong></td>
                    <td><strong>{{ number_format($centroidUnggulIdm, 3) }}</strong></td>
                    <td>{{ number_format($centroidUnggulJarak, 3) }}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-potensial">📈 Potensial</span></td>
                    <td>{{ number_format($centroidPotensialIks, 3) }}</td>
                    <td>{{ number_format($centroidPotensialIke, 3) }}</td>
                    <td>{{ number_format($centroidPotensialIkl, 3) }}</td>
                    <td>{{ number_format($centroidPotensialIdm, 3) }}</td>
                    <td>{{ number_format($centroidPotensialJarak, 3) }}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-berkembang">🌱 Berkembang</span></td>
                    <td>{{ number_format($centroidBerkembangIks, 3) }}</td>
                    <td>{{ number_format($centroidBerkembangIke, 3) }}</td>
                    <td>{{ number_format($centroidBerkembangIkl, 3) }}</td>
                    <td>{{ number_format($centroidBerkembangIdm, 3) }}</td>
                    <td>{{ number_format($centroidBerkembangJarak, 3) }}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-prioritas">⚠️ Prioritas</span></td>
                    <td>{{ number_format($centroidPrioritasIks, 3) }}</td>
                    <td>{{ number_format($centroidPrioritasIke, 3) }}</td>
                    <td>{{ number_format($centroidPrioritasIkl, 3) }}</td>
                    <td>{{ number_format($centroidPrioritasIdm, 3) }}</td>
                    <td>{{ number_format($centroidPrioritasJarak, 3) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
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
    /* Sudah Input — emerald */
    .stat-sudah { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; }
    .stat-sudah::before { background: #10b981; }
    .stat-sudah .stat-icon { color: #059669; }
    .stat-sudah .stat-number { color: #065f46; }
    /* Belum Input — amber */
    .stat-belum { background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; }
    .stat-belum::before { background: #f59e0b; }
    .stat-belum .stat-icon { color: #d97706; }
    .stat-belum .stat-number { color: #92400e; }
    /* Terklaster — violet/purple */
    .stat-terklaster { background: linear-gradient(135deg, #faf5ff, #f3e8ff); border-color: #e9d5ff; }
    .stat-terklaster::before { background: #a855f7; }
    .stat-terklaster .stat-icon { color: #9333ea; }
    .stat-terklaster .stat-number { color: #6b21a8; }
    
    .stat-icon { font-size: 1.4rem; margin-bottom: 10px; }
    .stat-label { font-size: 0.6rem; text-transform: uppercase; font-weight: 700; color: #5b6e8c; letter-spacing: 0.5px; }
    .stat-number { font-size: 2rem; font-weight: 800; margin: 6px 0 4px; }
    .stat-note { font-size: 0.62rem; color: #6c7a91; }
    .year-filter {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 18px;
    }
    .year-filter label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e3a5f;
    }
    .year-filter select {
        min-width: 130px;
        padding: 9px 12px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: white;
        color: #1e3a5f;
        font-weight: 700;
    }
    .analysis-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
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
    .prediction-grid {
        display: grid;
        grid-template-columns: minmax(220px, 0.8fr) 1.2fr;
        gap: 20px;
        align-items: stretch;
    }
    .prediction-summary {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        background: #f8fafc;
    }
    .prediction-label {
        font-size: 0.72rem;
        color: #5b6e8c;
        font-weight: 700;
        text-transform: uppercase;
    }
    .prediction-score {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-top: 8px;
    }
    .prediction-status {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 12px;
        border-radius: 30px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .prediction-note,
    .prediction-footnote {
        margin-top: 12px;
        font-size: 0.72rem;
        color: #6c7a91;
        line-height: 1.5;
    }
    .mini-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 10px;
    }
    .compact-table th,
    .compact-table td {
        padding: 9px 8px;
    }
    .cluster-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        border: 1px solid #e2e8f0;
        transition: 0.2s;
    }
    .cluster-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .cluster-unggul { border-top: 4px solid #059669; }
    .cluster-potensial { border-top: 4px solid #2563eb; }
    .cluster-berkembang { border-top: 4px solid #f97316; }
    .cluster-prioritas { border-top: 4px solid #dc2626; }
    .cluster-icon { font-size: 2rem; margin-bottom: 8px; }
    .cluster-name { font-weight: 700; font-size: 1rem; margin-bottom: 4px; }
    .cluster-count { font-size: 1.8rem; font-weight: 700; margin: 8px 0; }
    .cluster-desc { font-size: 0.7rem; color: #6c7a91; }
    .cluster-rekom { font-size: 0.7rem; margin-top: 8px; padding: 5px 10px; background: #f8fafc; border-radius: 20px; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 30px; font-size: 0.65rem; font-weight: 600; color: white; }
    .badge-unggul { background: #059669; }
    .badge-potensial { background: #2563eb; }
    .badge-berkembang { background: #f97316; }
    .badge-prioritas { background: #dc2626; }
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
    .centroid-table td {
        font-size: 0.8rem;
    }
    .centroid-table td strong {
        font-size: 0.85rem;
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .analysis-grid { grid-template-columns: 1fr; }
        .year-filter { justify-content: flex-start; }
        .prediction-grid { grid-template-columns: 1fr; }
        .cluster-card { margin-bottom: 12px; }
        .table th, .table td { font-size: 0.65rem; padding: 8px 4px; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const trenTahunan = @json($trenTahunan ?? []);
    const labelsTahun = trenTahunan.map((item) => item.tahun);
    const skorIdm = trenTahunan.map((item) => item.jumlah_data > 0 ? item.skor_idm : null);
    const iksData = trenTahunan.map((item) => item.jumlah_data > 0 ? item.iks : null);
    const ikeData = trenTahunan.map((item) => item.jumlah_data > 0 ? item.ike : null);
    const iklData = trenTahunan.map((item) => item.jumlah_data > 0 ? item.ikl : null);

    new Chart(document.getElementById('trendIdmChart'), {
        type: 'line',
        data: {
            labels: labelsTahun,
            datasets: [{
                label: 'Rata-rata Skor IDM',
                data: skorIdm,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                fill: true,
                tension: 0.25,
                spanGaps: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 1 } }
        }
    });

    new Chart(document.getElementById('dimensiCompareChart'), {
        type: 'bar',
        data: {
            labels: labelsTahun,
            datasets: [
                { label: 'IKS', data: iksData, backgroundColor: '#2563eb' },
                { label: 'IKE', data: ikeData, backgroundColor: '#059669' },
                { label: 'IKL', data: iklData, backgroundColor: '#f97316' }
            ]
        },
        options: {
            responsive: true,
            scales: { y: { min: 0, max: 1 } }
        }
    });
</script>
@endsection
