<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tambah Data IDM</title>

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

<h3 class="mb-4 text-success">Tambah Data IDM Desa</h3>

<form action="/desa/store" method="POST">
@csrf

<div class="mb-3">
<label>Nama Desa</label>
<input type="text" name="nama_desa" class="form-control" required>
</div>

<div class="mb-3">
<label>Kecamatan</label>
<input type="text" name="kecamatan" class="form-control" required>
</div>

<div class="mb-3">
<label>Tahun</label>
<input type="number" name="tahun" class="form-control" required>
</div>

<div class="mb-3">
<label>Skor Sosial</label>
<input type="number" step="0.01" name="skor_sosial" class="form-control" required>
</div>

<div class="mb-3">
<label>Skor Ekonomi</label>
<input type="number" step="0.01" name="skor_ekonomi" class="form-control" required>
</div>

<div class="mb-3">
<label>Skor Lingkungan</label>
<input type="number" step="0.01" name="skor_lingkungan" class="form-control" required>
</div>

<div class="mb-3">
<label>Nilai IDM</label>
<input type="number" step="0.01" name="nilai_idm" class="form-control" required>
</div>

<button class="btn btn-success">Simpan</button>
<a href="/desa" class="btn btn-secondary">Kembali</a>

</form>

</div>

</div>

</body>
</html>