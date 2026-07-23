@extends('layouts.app')

@section('title', 'Riwayat Upload')
@section('active-beranda', 'active')

@section('content')
<div class="top-bar">
    <div class="greeting">
        <h3>Riwayat Upload</h3>
        <p>Daftar seluruh file yang pernah diunggah dan status verifikasinya.</p>
    </div>
    <a href="{{ route('beranda') }}" class="btn-outline" style="padding: 8px 16px;">← Kembali</a>
</div>

<div class="card">
    <div class="card-title">Daftar Riwayat Upload</div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>NAMA FILE</th>
                    <th>STATUS</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
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
                    <td colspan="4" style="text-align: center; padding: 24px; color: #6c7a91;">
                        Belum ada riwayat upload.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($riwayat) && method_exists($riwayat, 'links'))
    <div style="margin-top: 16px;">
        {{ $riwayat->links() }}
    </div>
    @endif
</div>
@endsection
