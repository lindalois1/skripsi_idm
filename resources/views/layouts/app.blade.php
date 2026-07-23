<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IDM Digital - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            font-size: 14px;
            color: #1e293b;
        }

        /* ========== SIDEBAR - BIRU CERAH MODERN ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 4px 0 16px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        /* Custom scrollbar untuk sidebar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #3b82f6;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #1e40af;
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .sidebar-header p {
            color: #bfdbfe;
            font-size: 0.65rem;
            margin-top: 6px;
            line-height: 1.3;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-container i {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .nav-menu {
            padding: 20px 16px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            color: #e0e7ff;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .nav-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(4px);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(4px);
        }

        /* User Info di Sidebar */
        .user-info-sidebar {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin-top: auto;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar i {
            font-size: 1.8rem;
            color: white;
        }

        .user-name {
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 2px;
        }

        .user-role {
            color: #bfdbfe;
            font-size: 0.65rem;
        }

        .user-role i {
            margin-right: 4px;
            font-size: 0.6rem;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: 280px;
            padding: 28px 32px;
            min-height: 100vh;
        }

        /* ========== TOP BAR ========== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .greeting h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        .greeting p {
            font-size: 0.75rem;
            color: #5b6e8c;
            margin-top: 4px;
        }

        .user-actions {
            display: flex;
            gap: 12px;
        }

        .user-actions span {
            background: white;
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: 0.2s;
            color: #1e3a5f;
        }

        .user-actions span:hover {
            background: #e0f2fe;
            transform: translateY(-2px);
        }

        /* ========== CARD ========== */
        .card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e3a5f;
            border-left: 4px solid #3b82f6;
            padding-left: 14px;
        }

        /* ========== STATS GRID ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-color: #3b82f6;
        }

        .stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #5b6e8c;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 10px 0 6px;
        }

        .stat-note {
            font-size: 0.65rem;
            color: #6c7a91;
        }

        /* ========== FORM ========== */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1e3a5f;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.8rem;
            transition: 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            resize: vertical;
        }

        /* ========== TABLE ========== */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 14px 10px;
            background: #f8fafc;
            font-size: 0.7rem;
            font-weight: 600;
            color: #1e3a5f;
            border-bottom: 2px solid #e2e8f0;
        }

        .table td {
            padding: 12px 10px;
            font-size: 0.75rem;
            border-bottom: 1px solid #edf2f7;
        }

        .table tr:hover {
            background: #f8fafc;
        }

        /* ========== BADGE ========== */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fed7aa; color: #c2410c; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-primary { background: #2563eb; color: white; }
        .badge-super { background: #8b5cf6; color: white; }

        /* ========== BUTTON ========== */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: 0.2s;
            color: #1e3a5f;
        }
        .btn-outline:hover {
            background: #f1f5f9;
            border-color: #3b82f6;
        }

        .btn-approve {
            background: #059669;
            color: white;
            border: none;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            cursor: pointer;
            margin-right: 6px;
        }
        .btn-approve:hover {
            background: #047857;
        }
        .btn-reject {
            background: #dc2626;
            color: white;
            border: none;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            cursor: pointer;
        }
        .btn-reject:hover {
            background: #b91c1c;
        }

        /* ========== PROGRESS BAR ========== */
        .progress-bar {
            background: #e2e8f0;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, #3b82f6, #059669);
            height: 8px;
        }

        /* ========== UPLOAD AREA ========== */
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 35px;
            text-align: center;
            background: #fafdfe;
            cursor: pointer;
            transition: 0.3s;
        }
        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .upload-area i {
            font-size: 2.2rem;
            color: #3b82f6;
            margin-bottom: 10px;
        }

        /* ========== GRID ========== */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 14px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            z-index: 9999;
            animation: slideIn 0.4s ease;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            max-width: 400px;
        }
        .toast-success { background: #059669; }
        .toast-error { background: #dc2626; }
        .toast-warning { background: #f59e0b; }
        .toast-info { background: #2563eb; }

        .notification-widget {
            position: fixed;
            top: 18px;
            right: 24px;
            z-index: 1000;
        }

        .notification-button {
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: white;
            color: #1e3a5f;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.16);
            cursor: pointer;
            position: relative;
        }

        .notification-button i {
            font-size: 1rem;
        }

        .notification-count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 20px;
            background: #dc2626;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 18px;
        }

        .notification-panel {
            display: none;
            position: absolute;
            top: 52px;
            right: 0;
            width: min(360px, calc(100vw - 32px));
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .notification-widget:hover .notification-panel,
        .notification-widget:focus-within .notification-panel {
            display: block;
        }

        .notification-header {
            padding: 14px 16px;
            font-weight: 700;
            color: #1e3a5f;
            border-bottom: 1px solid #e2e8f0;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-message {
            font-size: 0.78rem;
            color: #1e293b;
            line-height: 1.35;
        }

        .notification-meta {
            margin-top: 5px;
            font-size: 0.68rem;
            color: #64748b;
        }

        .notification-empty {
            padding: 18px 16px;
            font-size: 0.78rem;
            color: #64748b;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 900px) {
            .sidebar { width: 80px; }
            .sidebar-header h2 span, .sidebar-header p, .nav-item span, .user-info-sidebar .user-name, .user-info-sidebar .user-role { display: none; }
            .main-content { margin-left: 80px; padding: 20px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: 1fr; }
            .two-columns { grid-template-columns: 1fr; }
            .logo-container { margin: 0 auto; }
            .sidebar-header { padding: 16px; }
            .sidebar-header h2 { font-size: 1rem; }
            .user-info-sidebar .user-avatar { margin: 0 auto; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .top-bar { flex-direction: column; align-items: flex-start; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR BIRU CERAH MODERN -->
<div class="sidebar">
    <div class="sidebar-header">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="logo-container">
                <i class="fas fa-landmark" style="font-size: 2rem; color: white;"></i>
            </div>
            <div>
                <h2>IDM <span>Digital</span></h2>
                <p>Satu Data Desa Untuk Pembangunan Indonesia Yang Berkelanjutan</p>
            </div>
        </div>
    </div>
    <div class="nav-menu">
        <!-- MENU UNTUK SUPER ADMIN -->
        @auth
            @if(Auth::user()->role == 'super_admin')
            <a href="{{ route('super.beranda') }}" class="nav-item @yield('active-super-beranda')">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('super.analisis') }}" class="nav-item @yield('active-super-analisis')">
                <i class="fas fa-chart-line"></i> <span>Analisis</span>
            </a>
            <a href="{{ route('super.laporan') }}" class="nav-item @yield('active-super-laporan')">
                <i class="fas fa-file-alt"></i> <span>Laporan</span>
            </a>
            <a href="{{ route('kelola-akun') }}" class="nav-item @yield('active-kelola-akun')">
                <i class="fas fa-users-cog"></i> <span>Kelola Akun</span>
            </a>
            @endif
        @endauth

        <!-- MENU UNTUK DESA -->
        @auth
            @if(Auth::user()->role == 'desa')
            <a href="{{ route('beranda') }}" class="nav-item @yield('active-beranda')">
                <i class="fas fa-tachometer-alt"></i> <span>Beranda</span>
            </a>
            <a href="{{ route('input.data') }}" class="nav-item @yield('active-input')">
                <i class="fas fa-edit"></i> <span>Input Data</span>
            </a>
            <a href="{{ route('analisis.desa') }}" class="nav-item @yield('active-analisis')">
                <i class="fas fa-chart-line"></i> <span>Analisis Desa</span>
            </a>
            @endif
        @endauth

        <!-- MENU UNTUK KECAMATAN -->
        @auth
            @if(Auth::user()->role == 'kecamatan')
            <a href="{{ route('beranda') }}" class="nav-item @yield('active-beranda')">
                <i class="fas fa-tachometer-alt"></i> <span>Beranda</span>
            </a>
            <a href="{{ route('verifikasi') }}" class="nav-item @yield('active-verifikasi')">
                <i class="fas fa-check-double"></i> <span>Verifikasi</span>
            </a>
            <a href="{{ route('analisis.kecamatan') }}" class="nav-item @yield('active-analisis')">
                <i class="fas fa-chart-line"></i> <span>Analisis Kecamatan</span>
            </a>
            <a href="{{ route('laporan') }}" class="nav-item @yield('active-laporan')">
                <i class="fas fa-file-alt"></i> <span>Laporan</span>
            </a>
            <a href="{{ route('kelola-akun') }}" class="nav-item @yield('active-kelola-akun')">
                <i class="fas fa-users-cog"></i> <span>Kelola Akun</span>
            </a>
            @endif
        @endauth

        <!-- MENU UNTUK KABUPATEN -->
        @auth
            @if(Auth::user()->role == 'kabupaten')
            <a href="{{ route('beranda') }}" class="nav-item @yield('active-beranda')">
                <i class="fas fa-tachometer-alt"></i> <span>Beranda</span>
            </a>
            <a href="{{ route('analisis') }}" class="nav-item @yield('active-analisis')">
                <i class="fas fa-chart-line"></i> <span>Analisis IDM</span>
            </a>
            <a href="{{ route('laporan') }}" class="nav-item @yield('active-laporan')">
                <i class="fas fa-file-alt"></i> <span>Laporan</span>
            </a>
            <a href="{{ route('kelola-akun') }}" class="nav-item @yield('active-kelola-akun')">
                <i class="fas fa-users-cog"></i> <span>Kelola Akun</span>
            </a>
            @endif
        @endauth
    </div>

    <!-- INFO USER LOGIN DI SIDEBAR -->
    @auth
    <div class="user-info-sidebar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div style="flex: 1;">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">
                    <i class="fas fa-tag"></i> 
                    @if(Auth::user()->role == 'super_admin')
                        Super Admin
                    @elseif(Auth::user()->role == 'desa')
                        Desa
                    @elseif(Auth::user()->role == 'kecamatan')
                        Kecamatan
                    @else
                        Kabupaten
                    @endif
                </div>
                @if(Auth::user()->desa)
                <div class="user-role" style="font-size: 0.6rem; color: #93c5fd;">
                    <i class="fas fa-map-pin"></i> {{ Auth::user()->desa->nama_desa ?? '' }}
                </div>
                @endif
            </div>
        </div>
        <div style="margin-top: 12px;">
            <a href="{{ route('logout') }}" class="nav-item" style="margin: 0; background: rgba(255,255,255,0.1);">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </div>
    @endauth
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    @yield('content')
</div>

@auth
<div class="notification-widget">
    <button class="notification-button" type="button" aria-label="Notifikasi upload">
        <i class="far fa-bell"></i>
        @if(($unreadNotifications ?? 0) > 0)
            <span class="notification-count">{{ $unreadNotifications }}</span>
        @endif
    </button>
    <div class="notification-panel">
        <div class="notification-header">Notifikasi Upload</div>
        @forelse(($uploadNotifications ?? collect()) as $notification)
            <div class="notification-item">
                <div class="notification-message">{{ $notification['message'] }}</div>
                <div class="notification-meta">{{ $notification['status_label'] ?? ucfirst($notification['status']) }} - {{ $notification['time'] }}</div>
                @if(!empty($notification['detail']))
                    <div class="notification-meta">{{ $notification['detail'] }}</div>
                @endif
            </div>
        @empty
            <div class="notification-empty">Belum ada notifikasi upload.</div>
        @endforelse
    </div>
</div>
@endauth

<!-- Auto hide toast -->
<script>
    setTimeout(function() {
        let toast = document.querySelector('.toast');
        if (toast) {
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, 3000);
        }
    }, 100);
</script>

@stack('scripts')
</body>
</html>
