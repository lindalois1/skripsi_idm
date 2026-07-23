<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard Desa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}

.card-box{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.navbar{
    background:linear-gradient(90deg,#198754,#0d6efd);
}

.table thead{
    background:#198754;
    color:white;
}

.btn-custom{
    border-radius:10px;
}
</style>

</head>
<body>

<nav class="navbar navbar-dark px-4">
    <span class="navbar-brand mb-0 h1">SISTEM IDM DESA</span>

    <form action="/logout" method="POST">
        @csrf
        <button class="btn btn-light btn-sm">Logout</button>
    </form>
</nav>

<div class="container mt-4">

<div class="card card-box p-4">

<div class="d-flex justify-content-between mb-3">
<h3>Data IDM Desa</h3>

<a href="/desa/create" class="btn btn-success btn-custom">
+ Tambah Data
</a>
</div>

<table class="table table-bordered table-hover">
<thead>
<tr>
<th>No</th>
<th>Nama Desa</th>
<th>Kecamatan</th>
<th>Tahun</th>
<th>Nilai IDM</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

@foreach($data as $item)

<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $item->nama_desa }}</td>
<td>{{ $item->kecamatan }}</td>
<td>{{ $item->tahun }}</td>
<td>{{ $item->nilai_idm }}</td>

<td>
@if($item->status=='Pending')
<span class="badge bg-warning">Pending</span>
@elseif($item->status=='Disetujui')
<span class="badge bg-success">Disetujui</span>
@else
<span class="badge bg-danger">Ditolak</span>
@endif
</td>

<td>

<a href="/desa/edit/{{ $item->id }}" class="btn btn-primary btn-sm">
Edit
</a>

<a href="/desa/delete/{{ $item->id }}"
onclick="return confirm('Hapus data?')"
class="btn btn-danger btn-sm">
Hapus
</a>

</td>

</tr>

@endforeach

</tbody>
</table>

</div>

</div>

</body>
</html>