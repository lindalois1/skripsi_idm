<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Data IDM</title>

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
</style>

</head>
<body>

<div class="container mt-5">

<div class="card card-box p-4">

<h3 class="mb-4 text-primary">Edit Data IDM Desa</h3>

<form action="/desa/update/{{ $data->id }}" method="POST">
@csrf

<div class="mb-3">
<label>Nama Desa</label>
<input type="text" name="nama_desa" class="form-control" value="{{ $data->nama_desa }}" required>
</div>

<div class="mb-3">
<label>Kecamatan</label>
<input type="text" name="kecamatan" class="form-control" value="{{ $data->kecamatan }}" required>
</div>

<div class="mb-3">
<label>Tahun</label>
<input type="number" name="tahun" class="form-control" value="{{ $data->tahun }}" required>
</div>

<div class="mb-3">
<label>Skor Sosial</label>
<input type="number" step="0.01" name="skor_sosial" class="form-control" value="{{ $data->skor_sosial }}" required>
</div>

<div class="mb-3">
<label>Skor Ekonomi</label>
<input type="number" step="0.01" name="skor_ekonomi" class="form-control" value="{{ $data->skor_ekonomi }}" required>
</div>

<div class="mb-3">
<label>Skor Lingkungan</label>
<input type="number" step="0.01" name="skor_lingkungan" class="form-control" value="{{ $data->skor_lingkungan }}" required>
</div>

<div class="mb-3">
<label>Nilai IDM</label>
<input type="number" step="0.01" name="nilai_idm" class="form-control" value="{{ $data->nilai_idm }}" required>
</div>

<button class="btn btn-primary">Update</button>
<a href="/desa" class="btn btn-secondary">Kembali</a>

</form>

</div>

</div>

</body>
</html>