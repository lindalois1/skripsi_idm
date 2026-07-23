<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - IDM Digital</title>
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
            background: linear-gradient(135deg, #0f3b5f 0%, #1e5a7a 50%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><path fill="white" d="M10,10 L90,10 L90,90 L10,90 Z" stroke="white" stroke-width="2"/><circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="2"/></svg>') repeat;
            pointer-events: none;
        }

        .portal-title {
            text-align: center;
            margin-bottom: 16px;
        }

        .portal-title h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .portal-title p {
            color: rgba(255,255,255,0.8);
            font-size: 0.6rem;
            letter-spacing: 2px;
            margin-top: 3px;
        }

        .login-container {
            max-width: 420px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding: 22px 20px;
            text-align: center;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            right: 0;
            height: 12px;
            background: white;
            border-radius: 12px 12px 0 0;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .logo-wrapper i {
            font-size: 1.8rem;
            color: white;
        }

        .login-header h2 {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .login-header p {
            color: #bfdbfe;
            font-size: 0.6rem;
            margin-top: 4px;
        }

        .login-body {
            padding: 24px 22px;
        }

        .login-body h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 4px;
        }

        .login-subtitle {
            color: #5b6e8c;
            font-size: 0.7rem;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            margin-bottom: 16px;
            border-left: 3px solid #dc2626;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e3a5f;
            display: block;
            margin-bottom: 6px;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .input-wrapper i {
            padding: 0 12px;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .input-wrapper:focus-within {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .input-field {
            width: 100%;
            padding: 10px 12px 10px 0;
            border: none;
            background: transparent;
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            outline: none;
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            padding: 11px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
            margin: 12px 0 14px;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e3a5f);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .register-info {
            background: #f0f9ff;
            padding: 12px;
            border-radius: 14px;
            text-align: center;
            font-size: 0.7rem;
            margin: 14px 0;
            border: 1px solid #dbeafe;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            display: block;
            color: #1e293b;
        }

        .register-info:hover {
            background: #dbeafe;
        }

        .register-info strong {
            color: #2563eb;
        }

        .login-footer {
            background: #f8fafc;
            text-align: center;
            font-size: 0.55rem;
            padding: 12px;
            color: #5b6e8c;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 360px;
            }
            .login-body {
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>

<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
    <!-- TULISAN PORTAL IDM -->
    <div class="portal-title">
        <h1>PORTAL IDM</h1>
        <p>Indeks Desa Membangun</p>
    </div>

    <div class="login-container">
        <div class="login-header">
            <div class="logo-wrapper">
                <i class="fas fa-landmark"></i>
                <h2>IDM Digital</h2>
            </div>
            <p>Satu Data Desa Untuk Pembangunan Berkelanjutan</p>
        </div>
        <div class="login-body">
            <h3>Daftar Akun Baru</h3>
            <p class="login-subtitle">Silakan isi formulir untuk mendaftar sebagai operator Desa</p>

            @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    <i class="fas fa-exclamation-circle"></i> {{ $error }}<br>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <div class="form-label">NAMA LENGKAP</div>
                    <div class="input-wrapper">
                        <i class="far fa-user"></i>
                        <input type="text" name="name" class="input-field" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">USERNAME (PILIHAN)</div>
                    <div class="input-wrapper">
                        <i class="far fa-user-circle"></i>
                        <input type="text" name="username" class="input-field" placeholder="Masukkan username" value="{{ old('username') }}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">EMAIL</div>
                    <div class="input-wrapper">
                        <i class="far fa-envelope"></i>
                        <input type="email" name="email" class="input-field" placeholder="Masukkan email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">KATA SANDI</div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="input-field" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">KONFIRMASI KATA SANDI</div>
                    <div class="input-wrapper">
                        <i class="fas fa-shield-alt"></i>
                        <input type="password" name="password_confirmation" class="input-field" placeholder="Masukkan ulang password" required>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-user-plus" style="margin-right: 6px;"></i> Daftar Sekarang
                </button>
            </form>

            <a href="{{ route('login') }}" class="register-info">
                Sudah memiliki akun? <strong>Masuk di sini</strong>
            </a>

        </div>
        <div class="login-footer">
            © Kementerian Desa RI
        </div>
    </div>
</div>

</body>
</html>
