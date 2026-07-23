@extends('layouts.app')

@section('title', 'Input Data IDM')
@section('active-input', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Input Data Indeks Desa Membangun</h3>
        <p>Upload file Excel untuk mengisi data IKS, IKE, IKL secara otomatis.</p>
    </div>
</div>

<!-- NOTIFIKASI -->
@if(session('success'))
<div style="background: #d1fae5; color: #059669; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #059669;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #dc2626;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- ========== UPLOAD EXCEL SECTION ========== -->
<div class="card" style="border: 2px dashed #2563eb; background: #f0f9ff;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="font-size: 1rem; color: #2563eb;">
                <i class="fas fa-file-excel"></i> Upload Data IDM dari Excel
            </h3>
            <p style="font-size: 0.7rem; color: #5b6e8c;">
                Upload file Excel dengan format DATA IDM (IKS, IKE, IKL, IDM, STATUS IDM)
            </p>
        </div>
        <div>
            <input type="file" id="excelFile" accept=".xlsx,.xls" style="display: none;">
            <button type="button" class="btn-primary" onclick="document.getElementById('excelFile').click()" style="background: #25D366;">
                <i class="fas fa-upload"></i> Upload Excel
            </button>
            <a href="{{ route('download.template') }}" class="btn-outline" style="margin-left: 10px;">
                <i class="fas fa-download"></i> Template
            </a>
        </div>
    </div>

    <div id="excelLoading" style="display: none; text-align: center; padding: 20px;">
        <i class="fas fa-spinner fa-spin"></i> Memproses file...
    </div>

    <div id="excelError" style="display: none; margin-top: 16px; padding: 12px; background: #fee2e2; border-radius: 12px; color: #dc2626; font-size: 0.75rem;">
        <i class="fas fa-exclamation-circle"></i> <span id="errorMessage"></span>
    </div>
</div>

<!-- ========== HASIL EKSTRAK EXCEL ========== -->
<div id="excelResult" style="display: none; margin-top: 24px;">

    <!-- SKOR HASIL -->
    <div class="card" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; text-align: center;">
            <div>
                <div style="font-size: 0.7rem; opacity: 0.8;">SKOR IKS</div>
                <div style="font-size: 1.8rem; font-weight: 700;" id="hasilIKS">0.0000</div>
            </div>
            <div>
                <div style="font-size: 0.7rem; opacity: 0.8;">SKOR IKE</div>
                <div style="font-size: 1.8rem; font-weight: 700;" id="hasilIKE">0.0000</div>
            </div>
            <div>
                <div style="font-size: 0.7rem; opacity: 0.8;">SKOR IKL</div>
                <div style="font-size: 1.8rem; font-weight: 700;" id="hasilIKL">0.0000</div>
            </div>
            <div>
                <div style="font-size: 0.7rem; opacity: 0.8;">STATUS</div>
                <div style="font-size: 1.2rem; font-weight: 700;" id="hasilStatus">-</div>
            </div>
        </div>
    </div>

    <!-- DETAIL DATA DESA -->
    <div class="card">
        <div class="card-title">📋 Detail Data Desa</div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
            <div>
                <div style="font-size: 0.65rem; color: #5b6e8c;">NAMA DESA</div>
                <div style="font-size: 1rem; font-weight: 600;" id="hasilNamaDesa">-</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #5b6e8c;">KECAMATAN</div>
                <div style="font-size: 1rem; font-weight: 600;" id="hasilKecamatan">-</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #5b6e8c;">TAHUN</div>
                <div style="font-size: 1rem; font-weight: 600;" id="hasilTahun">-</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #5b6e8c;">STATUS VERIFIKASI</div>
                <div style="font-size: 1rem; font-weight: 600;"><span class="badge badge-warning" id="hasilVerifikasi">Menunggu</span></div>
            </div>
        </div>
    </div>

    <!-- Tabel Dimensi -->
    <div class="card">
        <div class="card-title">📊 Rekapitulasi Dimensi</div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>DIMENSI</th>
                        <th>SKOR</th>
                        <th>STATUS</th>
                        <th>PROGRESS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>IKS</strong><br><small>Ketahanan Sosial</small></td>
                        <td id="tabelIKS">0.0000</td>
                        <td><span class="badge" id="tabelIKSStatus">-</span></td>
                        <td>
                            <div class="progress-bar"><div class="progress-fill" id="tabelIKSProgress" style="width: 0%;"></div></div>
                            <span id="tabelIKSProgressText">0%</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>IKE</strong><br><small>Ketahanan Ekonomi</small></td>
                        <td id="tabelIKE">0.0000</td>
                        <td><span class="badge" id="tabelIKEStatus">-</span></td>
                        <td>
                            <div class="progress-bar"><div class="progress-fill" id="tabelIKEProgress" style="width: 0%;"></div></div>
                            <span id="tabelIKEProgressText">0%</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>IKL</strong><br><small>Ketahanan Lingkungan</small></td>
                        <td id="tabelIKL">0.0000</td>
                        <td><span class="badge" id="tabelIKLStatus">-</span></td>
                        <td>
                            <div class="progress-bar"><div class="progress-fill" id="tabelIKLProgress" style="width: 0%;"></div></div>
                            <span id="tabelIKLProgressText">0%</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; gap: 12px; flex-wrap: wrap;">
        <div id="downloadExcelFile" style="font-size: 0.8rem; color: #2563eb;"></div>
        <button type="button" class="btn-primary" id="simpanDataBtn" style="padding: 12px 32px;">
            <i class="fas fa-save"></i> Simpan Data IDM
        </button>
    </div>
</div>

<div id="previewSection" style="display:none; margin-top: 24px;" class="card">
    <div class="card-title">👀 Preview Data Excel</div>
    <div style="overflow-x:auto;">
        <table class="table" style="font-size: 0.8rem;">
            <thead>
                <tr>
                    <th>Nama Desa</th>
                    <th>Kecamatan</th>
                    <th>Tahun</th>
                    <th>IKS</th>
                    <th>IKE</th>
                    <th>IKL</th>
                </tr>
            </thead>
            <tbody id="previewTableBody">
            </tbody>
        </table>
    </div>
</div>

<!-- ========== TOAST NOTIFICATION ========== -->
@if(session('toast'))
<div class="toast-notification toast-{{ session('toast')['type'] }}" id="toastMsg">
    <i class="fas {{ session('toast')['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
    {{ session('toast')['message'] }}
</div>
@endif

<!-- ========== DATA YANG DIAJUKAN ========== -->
<div class="card">
    <div class="card-title">Data IDM yang Diajukan</div>
    <form method="GET" action="{{ route('input.data') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
        <select name="tahun" class="form-control" style="width: 160px;">
            <option value="semua" {{ request('tahun') == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
            @foreach(($tahunList ?? collect()) as $tahun)
                <option value="{{ $tahun }}" {{ (string) request('tahun', $tahunFilter ?? 'semua') === (string) $tahun ? 'selected' : '' }}>
                    {{ $tahun }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('input.data') }}" class="btn-outline">Reset</a>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Tahun</th>
                    <th>Desa</th>
                    <th>IKS</th>
                    <th>IKE</th>
                    <th>IKL</th>
                    <th>Skor IDM</th>
                    <th>Status</th>
                    <th>Verifikasi</th>
                    <th>Diajukan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($dataPengajuan ?? collect()) as $item)
                    @php
                        $badgeVerif = match($item->verifikasi_status) {
                            'verified', 'disetujui' => 'badge-success',
                            'revisi' => 'badge-danger',
                            'proses' => 'badge-info',
                            default => 'badge-warning',
                        };
                        $verifText = match($item->verifikasi_status) {
                            'verified', 'disetujui' => 'Terverifikasi',
                            'revisi' => 'Revisi',
                            'proses' => 'Diproses',
                            default => 'Menunggu',
                        };
                        $bisaEdit = !in_array($item->verifikasi_status, ['verified', 'disetujui']);
                    @endphp
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ $item->desa->nama_desa ?? $item->nama_desa ?? '-' }}</td>
                        <td>{{ number_format($item->skor_iks ?? 0, 4) }}</td>
                        <td>{{ number_format($item->skor_ike ?? 0, 4) }}</td>
                        <td>{{ number_format($item->skor_ikl ?? 0, 4) }}</td>
                        <td><strong>{{ number_format($item->skor_komposit ?? 0, 4) }}</strong></td>
                        <td>{{ ucfirst($item->status ?? '-') }}</td>
                        <td><span class="badge {{ $badgeVerif }}">{{ $verifText }}</span></td>
                        <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                        <td style="white-space:nowrap;">
                            @if($bisaEdit)
                            <button type="button" class="btn-aksi btn-edit-data"
                                onclick="openEditModal({{ $item->id }}, {{ $item->tahun }}, {{ $item->skor_iks }}, {{ $item->skor_ike }}, {{ $item->skor_ikl }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn-aksi btn-hapus-data"
                                onclick="openDeleteModal({{ $item->id }}, '{{ $item->desa->nama_desa ?? $item->nama_desa ?? '' }}', {{ $item->tahun }})">
                                <i class="fas fa-trash"></i>
                            </button>
                            @else
                            <span style="font-size:0.65rem; color:#6c7a91;">Terverifikasi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; color:#64748b; padding:24px;">Belum ada data yang diajukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($dataPengajuan) && method_exists($dataPengajuan, 'links'))
        <div style="margin-top: 16px;">{{ $dataPengajuan->links() }}</div>
    @endif
</div>

<!-- ========== MODAL EDIT ========== -->
<div id="modalEdit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; padding:28px; width:100%; max-width:480px; margin:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="font-size:1rem; color:#1e3a5f; font-weight:700;"><i class="fas fa-edit" style="color:#2563eb;"></i> Edit Data IDM</h3>
            <button onclick="closeEditModal()" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#6c7a91;">&times;</button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="_method" value="PUT">
            <div style="display:grid; gap:14px;">
                <div>
                    <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">Tahun</label>
                    <select name="tahun" id="editTahun" class="form-control" style="margin-top:6px;">
                        @foreach([2021,2022,2023,2024,2025] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
                    <div>
                        <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">IKS</label>
                        <input type="number" name="skor_iks" id="editIks" step="0.0001" min="0" max="1" class="form-control" style="margin-top:6px;" required>
                    </div>
                    <div>
                        <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">IKE</label>
                        <input type="number" name="skor_ike" id="editIke" step="0.0001" min="0" max="1" class="form-control" style="margin-top:6px;" required>
                    </div>
                    <div>
                        <label style="font-size:0.7rem; font-weight:700; color:#5b6e8c; text-transform:uppercase;">IKL</label>
                        <input type="number" name="skor_ikl" id="editIkl" step="0.0001" min="0" max="1" class="form-control" style="margin-top:6px;" required>
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px; justify-content:flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn-outline">Batal</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
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
            <button type="button" onclick="closeDeleteModal()" class="btn-outline">Batal</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const excelFile    = document.getElementById('excelFile');
    const excelLoading = document.getElementById('excelLoading');
    const excelResult  = document.getElementById('excelResult');
    const excelError   = document.getElementById('excelError');
    const errorMessage = document.getElementById('errorMessage');

    excelFile?.addEventListener('change', function(e) {
        if (!e.target.files.length) return;

        const file = e.target.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        const requestOptions = {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };

        excelLoading.style.display = 'block';
        document.getElementById('previewSection').style.display = 'none';
        excelResult.style.display = 'none';
        excelError.style.display = 'none';

        fetch('{{ route("upload.excel.bulk") }}', requestOptions)
        .then(async response => {
            const text = await response.text();
            let data = null;
            try {
                data = text ? JSON.parse(text) : null;
            } catch (e) {
                data = { success: false, message: 'Server mengembalikan respons yang tidak valid. Pastikan sesi login masih aktif.' };
            }
            return { response, data };
        })
        .then(({ data }) => {
            excelLoading.style.display = 'none';

            if (data.success) {
                fetch('{{ route("excel.preview") }}', requestOptions)
                .then(async response => {
                    const text = await response.text();
                    let previewData = null;
                    try {
                        previewData = text ? JSON.parse(text) : null;
                    } catch (e) {
                        previewData = { success: false, message: 'Server mengembalikan respons preview yang tidak valid.' };
                    }
                    return previewData;
                })
                .then(previewData => {
                    const tbody = document.getElementById('previewTableBody');
                    tbody.innerHTML = '';
                    if (previewData && previewData.success && previewData.preview && previewData.preview.length) {
                        previewData.preview.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = '<td>' + (item.nama_desa || '-') + '</td>' +
                                '<td>' + (item.kecamatan || '-') + '</td>' +
                                '<td>' + (item.tahun || '-') + '</td>' +
                                '<td>' + (item.iks || '-') + '</td>' +
                                '<td>' + (item.ike || '-') + '</td>' +
                                '<td>' + (item.ikl || '-') + '</td>';
                            tbody.appendChild(tr);
                        });
                        document.getElementById('previewSection').style.display = 'block';
                    } else {
                        document.getElementById('previewSection').style.display = 'none';
                    }

                    const previewItem  = previewData && previewData.preview && previewData.preview.length ? previewData.preview[0] : null;
                    const detailItem   = data.detail || previewItem;
                    const formatValue  = v => (v === null || v === undefined || v === '' || v === '-') ? '-' : v;
                    const formatNumber = v => (v === null || v === undefined || v === '' || v === '-') ? '0.0000' : Number(v).toFixed(4);

                    document.getElementById('hasilIKS').innerText    = formatNumber(detailItem?.iks);
                    document.getElementById('hasilIKE').innerText    = formatNumber(detailItem?.ike);
                    document.getElementById('hasilIKL').innerText    = formatNumber(detailItem?.ikl);
                    document.getElementById('hasilStatus').innerText = 'SELESAI';

                    document.getElementById('tabelIKS').innerText = formatNumber(detailItem?.iks);
                    document.getElementById('tabelIKE').innerText = formatNumber(detailItem?.ike);
                    document.getElementById('tabelIKL').innerText = formatNumber(detailItem?.ikl);

                    ['IKS','IKE','IKL'].forEach(d => {
                        document.getElementById('tabel' + d + 'Progress').style.width = '100%';
                        document.getElementById('tabel' + d + 'ProgressText').innerText = '100%';
                    });

                    document.getElementById('hasilNamaDesa').innerText   = formatValue(detailItem?.nama_desa || detailItem?.nama || '-');
                    document.getElementById('hasilKecamatan').innerText  = formatValue(detailItem?.kecamatan || '-');
                    document.getElementById('hasilTahun').innerText      = formatValue(detailItem?.tahun || '-');
                    document.getElementById('hasilVerifikasi').innerText = 'Berhasil';
                    document.getElementById('hasilVerifikasi').className = 'badge badge-success';

                    const downloadWrap = document.getElementById('downloadExcelFile');
                    if (downloadWrap) {
                        downloadWrap.innerHTML = '<a href="' + (data.download_url || '#') + '" target="_blank" style="color:#2563eb;text-decoration:underline;">Download file Excel</a>';
                    }

                    excelResult.style.display = 'block';
                    showToast('success', 'Import Excel selesai');
                })
                .catch(error => {
                    console.error(error);
                    excelError.style.display = 'block';
                    errorMessage.innerText = 'Gagal membaca preview file. Import mungkin sudah berhasil.';
                    showToast('warning', 'Gagal membaca preview file.');
                });
            } else {
                excelError.style.display = 'block';
                errorMessage.innerText = data.message || 'Gagal memproses file';
                showToast('error', data.message || 'Gagal memproses file');
            }
        })
        .catch(error => {
            excelLoading.style.display = 'none';
            excelError.style.display = 'block';
            errorMessage.innerText = 'Gagal menghubungi server. Coba refresh halaman lalu upload ulang.';
            console.error(error);
            showToast('error', 'Gagal menghubungi server.');
        });
    });

    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : (type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle')) + '"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    const toastMsg = document.getElementById('toastMsg');
    if (toastMsg) {
        setTimeout(() => { toastMsg.style.opacity = '0'; }, 3000);
        setTimeout(() => { toastMsg.remove(); }, 3500);
    }
});

function openEditModal(id, tahun, iks, ike, ikl) {
    document.getElementById('formEdit').action = '/input-data/' + id;
    document.getElementById('editTahun').value = tahun;
    document.getElementById('editIks').value   = parseFloat(iks).toFixed(4);
    document.getElementById('editIke').value   = parseFloat(ike).toFixed(4);
    document.getElementById('editIkl').value   = parseFloat(ikl).toFixed(4);
    document.getElementById('modalEdit').style.display = 'flex';
}
function closeEditModal() { document.getElementById('modalEdit').style.display = 'none'; }

function openDeleteModal(id, nama, tahun) {
    document.getElementById('formHapus').action = '/input-data/' + id;
    document.getElementById('hapusKonfirmasiText').innerText =
        'Anda akan menghapus data IDM ' + nama + ' tahun ' + tahun + '. Tindakan ini tidak dapat dibatalkan.';
    document.getElementById('modalHapus').style.display = 'flex';
}
function closeDeleteModal() { document.getElementById('modalHapus').style.display = 'none'; }
</script>

<style>
    /* === TOAST NOTIFICATIONS — compact/kecil === */
    .toast {
        position: fixed;
        bottom: 16px;
        right: 16px;
        padding: 8px 14px;
        border-radius: 8px;
        z-index: 2000;
        animation: toastSlideIn 0.25s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        font-size: 0.75rem;
        font-weight: 500;
        max-width: 260px;
        line-height: 1.4;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .toast-success { background: #059669; color: white; }
    .toast-error   { background: #dc2626; color: white; }
    .toast-warning { background: #d97706; color: white; }
    @keyframes toastSlideIn {
        from { transform: translateX(110%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
    /* === PROGRESS BAR === */
    .progress-bar {
        background: #e2e8f0;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        width: 120px;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
    }
    .progress-fill {
        background: #2563eb;
        height: 8px;
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    /* === BADGE === */
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; display: inline-block; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger  { background: #fee2e2; color: #991b1b; }
    .badge-info    { background: #dbeafe; color: #1e40af; }
    /* === BUTTONS === */
    .btn-primary {
        background: #2563eb;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-primary:hover    { background: #1d4ed8; }
    .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-outline {
        background: transparent;
        color: #2563eb;
        border: 1px solid #2563eb;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-outline:hover { background: #2563eb; color: white; }
    /* === CARD === */
    .card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .card-title { font-size: 1rem; font-weight: 600; margin-bottom: 16px; color: #1e293b; }
    /* === TABLE === */
    .table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .table th { text-align: left; padding: 10px 12px; background: #f1f5f9; color: #475569; font-weight: 600; }
    .table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
    .table tbody tr:hover { background: #f8fafc; }
    /* === TOP BAR === */
    .top-bar { margin-bottom: 24px; }
    .greeting h3 { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .greeting p  { font-size: 0.85rem; color: #64748b; margin: 0; }
    /* === FORM CONTROL === */
    .form-control {
        padding: 7px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.8rem;
        color: #334155;
        background: white;
        height: 36px;
    }
    .form-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,0.15); }
    .toast-notification {
        position: fixed; bottom: 20px; right: 20px;
        padding: 10px 16px; border-radius: 10px; z-index: 9000;
        font-size: 0.78rem; font-weight: 600;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: opacity 0.5s ease;
    }
    .toast-notification.toast-success { background: #059669; color: white; }
    .toast-notification.toast-error   { background: #dc2626; color: white; }
    .btn-aksi { border: none; border-radius: 8px; padding: 5px 10px; cursor: pointer; font-size: 0.75rem; transition: all 0.2s; margin-right: 4px; }
    .btn-edit-data  { background: #dbeafe; color: #2563eb; }
    .btn-edit-data:hover  { background: #2563eb; color: white; }
    .btn-hapus-data { background: #fee2e2; color: #dc2626; }
    .btn-hapus-data:hover { background: #dc2626; color: white; }
</style>
@endsection
