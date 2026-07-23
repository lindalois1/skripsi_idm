<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Laporan IDM Tahun {{ $tahun }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 40px;
            background: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #cbd5e1;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e3a5f;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #475569;
            margin: 0 0 4px 0;
        }
        .header p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background: #f1f5f9;
            color: #1e3a5f;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            text-align: left;
        }
        .table td {
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            font-size: 0.8rem;
        }
        .table tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
            font-size: 0.85rem;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-box p {
            margin: 0;
        }
        .signature-space {
            height: 70px;
        }

        /* Tombol print melayang (hanya muncul di layar, tidak ikut tercetak) */
        .print-btn-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
        }
        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-print:hover {
            background: #1d4ed8;
            transform: scale(1.05);
        }

        @media print {
            .print-btn-container {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button onclick="window.print()" class="btn-print">
            🖨️ Cetak Rekapitulasi (Simpan PDF)
        </button>
    </div>

    <div class="header">
        <h1>Pemerintah Kabupaten Indramayu</h1>
        <h2>Rekapitulasi Indeks Desa Membangun (IDM)</h2>
        <p>Tahun Anggaran {{ $tahun }} | Klasifikasi & Status Verifikasi Desa</p>
    </div>

    <div class="meta-info">
        <div>Dicetak oleh: {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
        <div>Tanggal cetak: {{ date('d M Y, H:i') }}</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">No</th>
                <th>Nama Desa</th>
                <th>Kecamatan</th>
                <th style="width: 80px; text-align: right;">IKS</th>
                <th style="width: 80px; text-align: right;">IKE</th>
                <th style="width: 80px; text-align: right;">IKL</th>
                <th style="width: 100px; text-align: right;">Skor IDM</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 120px;">Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($desaList as $index => $desa)
                @php
                    $dataIdm = $desa->dataIdm->first();
                    $skor = $dataIdm ? $dataIdm->skor_komposit : 0;
                    $status = $dataIdm ? $dataIdm->status : 'belum ada data';
                    $verifikasi = $dataIdm ? $dataIdm->verifikasi_status : 'belum';

                    $badgeClass = match($status) {
                        'mandiri' => 'badge-success',
                        'maju' => 'badge-info',
                        'berkembang' => 'badge-warning',
                        'tertinggal' => 'badge-danger',
                        default => 'badge-danger'
                    };

                    $badgeVerif = match($verifikasi) {
                        'verified', 'disetujui' => 'badge-success',
                        'ditolak', 'revisi' => 'badge-danger',
                        'proses' => 'badge-info',
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
                        'verified', 'disetujui' => 'Terverifikasi',
                        'ditolak', 'revisi' => 'Ditolak',
                        'proses' => 'Diproses',
                        default => 'Menunggu'
                    };
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $desa->nama_desa }}</strong></td>
                    <td>{{ $desa->kecamatan }}</td>
                    <td style="text-align: right;">{{ $dataIdm ? number_format($dataIdm->skor_iks, 4) : '-' }}</td>
                    <td style="text-align: right;">{{ $dataIdm ? number_format($dataIdm->skor_ike, 4) : '-' }}</td>
                    <td style="text-align: right;">{{ $dataIdm ? number_format($dataIdm->skor_ikl, 4) : '-' }}</td>
                    <td style="text-align: right; font-weight: 700; color: #1e3a5f;">
                        {{ $skor > 0 ? number_format($skor, 4) : '-' }}
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $statusTeks }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $badgeVerif }}">{{ $verifTeks }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 30px;">
                        Tidak ada data desa untuk tahun {{ $tahun }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Indramayu, {{ date('d M Y') }}</p>
            <p style="font-weight: 600; margin-top: 5px;">Kepala Dinas PMD Kab. Indramayu</p>
            <div class="signature-space"></div>
            <p style="font-weight: 700; text-decoration: underline;">PMD KABUPATEN INDRAMAYU</p>
            <p style="font-size: 0.75rem; color: #64748b;">NIP. 19780512 200501 1 002</p>
        </div>
    </div>

    <script>
        // Otomatis memicu dialog cetak pada saat halaman selesai dimuat
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
