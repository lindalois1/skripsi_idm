<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\KelolaAkunController;
use App\Http\Controllers\ClusteringController;

// ============ GUEST (TIDAK PERLU LOGIN) ============
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');
Route::post('/forgot-password', function () { return redirect()->back()->with('status', 'Link reset password telah dikirim.'); })->name('password.email');
Route::get('/reset-password/{token}', function ($token) { return view('auth.reset-password', ['token' => $token]); })->name('password.reset');
Route::post('/reset-password', function () { return redirect()->back()->with('status', 'Password berhasil direset.'); })->name('password.store');
Route::get('/confirm-password', function () { return view('auth.confirm-password'); })->name('password.confirm');
Route::post('/confirm-password', function () { return redirect()->route('beranda'); })->name('password.confirm.post');
Route::get('/profile', function () { return view('auth.profile'); })->name('profile');
Route::post('/profile', function () { return redirect()->back(); });

// ============ PROTECTED (HARUS LOGIN) ============
Route::middleware(['auth'])->group(function () {
    
    // ============ DASHBOARD (SEMUA ROLE) ============
    Route::get('/beranda', [DashboardController::class, 'beranda'])->name('beranda');
    Route::post('/upload-file', [DashboardController::class, 'uploadFile'])->name('upload.file');
    Route::get('/riwayat/lengkap', [DashboardController::class, 'riwayatLengkap'])->name('riwayat.lengkap');
    
    // Upload & Extract Excel (untuk desa)
    Route::post('/upload-excel', [InputDataController::class, 'uploadExcel'])->name('upload.excel');
    Route::post('/upload-excel-bulk', [\App\Http\Controllers\ExcelBulkImportController::class, 'import'])->name('upload.excel.bulk');
    Route::post('/excel-preview', [\App\Http\Controllers\ExcelPreviewController::class, 'preview'])->name('excel.preview');
    Route::get('/download-template', [InputDataController::class, 'downloadTemplate'])->name('download.template');
    
    // ============ DOWNLOAD FILE ============
    Route::get('/download/file/{id}', [LaporanController::class, 'downloadFile'])->name('download.file');
    
    // ============ KELOLA AKUN ============
    Route::middleware(['role:kecamatan,kabupaten,super_admin'])->group(function () {
        Route::get('/kelola-akun', [KelolaAkunController::class, 'index'])->name('kelola-akun');
        Route::post('/kelola-akun', [KelolaAkunController::class, 'store'])->name('kelola-akun.store');
        Route::get('/kelola-akun/{id}/edit', [KelolaAkunController::class, 'edit'])->name('kelola-akun.edit');
        Route::put('/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('kelola-akun.update');
        Route::get('/kelola-akun/{id}/toggle', [KelolaAkunController::class, 'toggleActive'])->name('kelola-akun.toggle');
        Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])->name('kelola-akun.delete');
    });
    
    // ============ INPUT DATA (HANYA ROLE DESA) ============
    Route::middleware(['role:desa'])->group(function () {
        Route::get('/input-data', [InputDataController::class, 'index'])->name('input.data');
        Route::post('/input-data', [InputDataController::class, 'store'])->name('input.data.store');
        Route::put('/input-data/{id}', [InputDataController::class, 'update'])->name('input.data.update');
        Route::delete('/input-data/{id}', [InputDataController::class, 'destroy'])->name('input.data.destroy');
        Route::get('/analisis-desa', [AnalisisController::class, 'index'])->name('analisis.desa');
    });
    
    // ============ VERIFIKASI (HANYA ROLE KECAMATAN) ============
    Route::middleware(['role:kecamatan'])->group(function () {
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi');
        Route::get('/analisis-kecamatan', [AnalisisController::class, 'index'])->name('analisis.kecamatan');
        
        // UPDATE: route untuk verifikasi (Setujui/Tolak/Proses)
        Route::put('/verifikasi/update/{id}', [VerifikasiController::class, 'update'])->name('verifikasi.update');
        
        // EDIT: route untuk edit data
        Route::put('/verifikasi/edit/{id}', [VerifikasiController::class, 'edit'])->name('verifikasi.edit');
        
        // DELETE: route untuk hapus data
        Route::delete('/verifikasi/delete/{id}', [VerifikasiController::class, 'delete'])->name('verifikasi.delete');
    });

    // ============ ANALISIS (HANYA ROLE KABUPATEN & SUPER ADMIN) ============
    Route::middleware(['role:kabupaten,super_admin'])->group(function () {
        Route::get('/analisis', [AnalisisController::class, 'index'])->name('analisis');
        Route::get('/dashboard-kabupaten', [DashboardController::class, 'dashboardKabupaten'])->name('dashboard.kabupaten');
        Route::post('/clustering/process', [ClusteringController::class, 'process'])->name('clustering.process');
    });
    
    // ============ LAPORAN ============
    Route::middleware(['auth'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/pdf', [LaporanController::class, 'downloadPdf'])->name('laporan.pdf');
        Route::get('/laporan/desa/{id}', [LaporanController::class, 'detail'])->name('laporan.detail');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        Route::put('/laporan/data/{id}', [LaporanController::class, 'update'])->name('laporan.update');
        Route::delete('/laporan/data/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
        Route::post('/laporan/seed-data', [LaporanController::class, 'seedHistoricalData'])->name('laporan.seed-data');
    });

    // ============ SUPER ADMIN (KEDINASAN) ============
    Route::middleware(['role:super_admin'])->prefix('super')->name('super.')->group(function () {
        // Dashboard Super Admin
        Route::get('/beranda', [SuperAdminController::class, 'beranda'])->name('beranda');
        Route::get('/dashboard', [SuperAdminController::class, 'beranda'])->name('dashboard');
        
        // Analisis Super Admin
        Route::get('/analisis', [SuperAdminController::class, 'analisis'])->name('analisis');
        Route::get('/analisis/data', [SuperAdminController::class, 'analisisData'])->name('analisis.data');
        
        // Laporan Super Admin
        Route::get('/laporan', [SuperAdminController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/export', [SuperAdminController::class, 'laporanExport'])->name('laporan.export');
        
        // Detail Kecamatan
        Route::get('/detail-kecamatan/{kecamatan}', [SuperAdminController::class, 'detailKecamatan'])->name('detail.kecamatan');
        
        // Detail Desa
        Route::get('/detail-desa/{desa}', [SuperAdminController::class, 'detailDesa'])->name('detail.desa');
        
        // Kelola Akun (Super Admin juga bisa akses)
        Route::get('/kelola-akun', [KelolaAkunController::class, 'index'])->name('kelola-akun');
        Route::post('/kelola-akun', [KelolaAkunController::class, 'store'])->name('kelola-akun.store');
        Route::get('/kelola-akun/{id}/edit', [KelolaAkunController::class, 'edit'])->name('kelola-akun.edit');
        Route::put('/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('kelola-akun.update');
        Route::get('/kelola-akun/{id}/toggle', [KelolaAkunController::class, 'toggleActive'])->name('kelola-akun.toggle');
        Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])->name('kelola-akun.delete');
    });

});

// ============ FALLBACK ROUTE (REDIRECT KE BERANDA) ============
Route::get('/', function () {
    return redirect()->route('beranda');
});
