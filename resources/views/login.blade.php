<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IDM Digital</title>
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
            background: linear-gradient(135deg, #ffffff 0%, #dbeafe 100%);
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

        .logo-wrapper .kabupaten-logo {
            width: 42px;
            height: 46px;
            object-fit: contain;
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.2));
        }

        .login-header h2 {
            color: #0f2c6b;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .login-header p {
            color: #3b5f94;
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

        .level-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            background: #f8fafc;
            padding: 6px 8px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
        }

        .radio-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-weight: 500;
            font-size: 0.65rem;
            cursor: pointer;
            color: #1e3a5f;
            padding: 4px 6px;
            border-radius: 10px;
            transition: 0.2s;
            text-align: center;
        }

        .radio-label:hover {
            background: #e0f2fe;
        }

        .radio-label input {
            width: 12px;
            height: 12px;
            cursor: pointer;
            accent-color: #2563eb;
        }

        .radio-label:has(input:checked) {
            background: #dbeafe;
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

        .forgot-link {
            display: inline-block;
            margin-top: 6px;
            font-size: 0.6rem;
            color: #2563eb;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 14px 0;
        }

        .checkbox-group input {
            width: 14px;
            height: 14px;
            accent-color: #2563eb;
        }

        .checkbox-group label {
            font-size: 0.7rem;
            color: #4b5563;
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
        }

        .register-info:hover {
            background: #dbeafe;
        }

        .register-info strong {
            color: #2563eb;
        }

        .footer-links {
            text-align: center;
            font-size: 0.6rem;
            color: #6c7a91;
            margin-top: 12px;
        }

        .footer-links a {
            color: #2563eb;
            text-decoration: none;
            margin: 0 4px;
            cursor: pointer;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .login-footer {
            background: #f8fafc;
            text-align: center;
            font-size: 0.55rem;
            padding: 12px;
            color: #5b6e8c;
            border-top: 1px solid #e2e8f0;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-header h3 {
            color: #1e3a5f;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c7a91;
        }

        .close-modal:hover {
            color: #dc2626;
        }

        .contact-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 14px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid #e2e8f0;
        }

        .contact-card:hover {
            background: #e0f2fe;
            border-color: #2563eb;
        }

        .contact-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .contact-info {
            flex: 1;
        }

        .contact-info strong {
            display: block;
            color: #1e3a5f;
            font-size: 0.85rem;
        }

        .contact-info .contact-role {
            font-size: 0.6rem;
            color: #2563eb;
        }

        .contact-info .contact-number {
            font-size: 0.7rem;
            color: #5b6e8c;
        }

        .whatsapp-btn {
            background: #25D366;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 0.65rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 360px;
            }
            .login-body {
                padding: 20px 16px;
            }
            .level-options {
                grid-template-columns: repeat(2, 1fr);
            }
            .radio-label {
                font-size: 0.6rem;
            }
        }
    </style>
</head>
<body>

<!-- TULISAN PORTAL IDM -->
<div class="portal-title">
    <h1>PORTAL IDM</h1>
    <p>Indeks Desa Membangun</p>
</div>

<div class="login-container">
    <div class="login-header">
        <div class="logo-wrapper">
            <img class="kabupaten-logo" src="{{ asset('images/lambang-kabupaten-indramayu.png') }}" alt="Lambang Kabupaten Indramayu">
            <h2>IDM Digital</h2>
        </div>
        <p>Satu Data Desa Untuk Pembangunan Berkelanjutan</p>
    </div>
    <div class="login-body">
        <h3>Selamat Datang</h3>
        <p class="login-subtitle">Silakan masuk menggunakan akun resmi</p>

        @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)
                <i class="fas fa-exclamation-circle"></i> {{ $error }}<br>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <div class="form-label">LEVEL PENGGUNA</div>
                <div class="level-options">
                    <label class="radio-label">
                        <input type="radio" name="role" value="super_admin" {{ old('role') == 'super_admin' ? 'checked' : '' }}> 
                        <i class="fas fa-crown" style="color: #8b5cf6;"></i> Super Admin
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="role" value="kabupaten" {{ old('role') == 'kabupaten' ? 'checked' : '' }}> 
                        <i class="fas fa-city" style="color: #3b82f6;"></i> Kabupaten
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="role" value="kecamatan" {{ old('role') == 'kecamatan' ? 'checked' : '' }}> 
                        <i class="fas fa-building" style="color: #f59e0b;"></i> Kecamatan
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="role" value="desa" {{ old('role') == 'desa' ? 'checked' : '' }}> 
                        <i class="fas fa-home" style="color: #059669;"></i> Desa
                    </label>
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
                <a href="#" class="forgot-link">Lupa sandi?</a>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="register-info" onclick="openContactModal()">
            Belum memiliki akun?<br>
            <strong>Hubungi Admin DPMD Kabupaten</strong>
        </div>

        <div class="footer-links">
            <a href="#" onclick="openContactModal()">Panduan</a> |
            <a href="#" onclick="openContactModal()">Privasi</a> |
            <a href="#" onclick="openContactModal()">Kontak</a>
        </div>
    </div>
    <div class="login-footer">
        © Kementerian Desa RI
    </div>
</div>

<!-- MODAL KONTAK -->
<div id="contactModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-address-card"></i> Hubungi Admin</h3>
            <button class="close-modal" onclick="closeContactModal()">&times;</button>
        </div>
        
        <div class="contact-card" onclick="openWhatsApp('6281234567891', 'Halo Admin Kecamatan, saya ingin mendaftarkan desa saya.')">
            <div class="contact-icon"><i class="fas fa-building"></i></div>
            <div class="contact-info">
                <strong>Daftar Desa</strong>
                <div class="contact-role">Admin Kecamatan</div>
                <div class="contact-number"><i class="fab fa-whatsapp"></i> 0812-3456-7891</div>
            </div>
            <button class="whatsapp-btn" onclick="event.stopPropagation(); openWhatsApp('6281234567891', 'Halo Admin Kecamatan, saya ingin mendaftarkan desa saya.')">
                <i class="fab fa-whatsapp"></i> Chat
            </button>
        </div>
        
        <div class="contact-card" onclick="openWhatsApp('6281234567892', 'Halo Admin Kabupaten, saya ingin mendaftarkan kecamatan saya.')">
            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="contact-info">
                <strong>Daftar Kecamatan</strong>
                <div class="contact-role">Admin Kabupaten</div>
                <div class="contact-number"><i class="fab fa-whatsapp"></i> 0812-3456-7892</div>
            </div>
            <button class="whatsapp-btn" onclick="event.stopPropagation(); openWhatsApp('6281234567892', 'Halo Admin Kabupaten, saya ingin mendaftarkan kecamatan saya.')">
                <i class="fab fa-whatsapp"></i> Chat
            </button>
        </div>
        
        <div class="contact-card" onclick="openWhatsApp('6281234567893', 'Halo Admin Dinas, saya ingin mendaftarkan kabupaten saya.')">
            <div class="contact-icon"><i class="fas fa-landmark"></i></div>
            <div class="contact-info">
                <strong>Daftar Kabupaten</strong>
                <div class="contact-role">Admin Kedinasan (DPMD)</div>
                <div class="contact-number"><i class="fab fa-whatsapp"></i> 0812-3456-7893</div>
            </div>
            <button class="whatsapp-btn" onclick="event.stopPropagation(); openWhatsApp('6281234567893', 'Halo Admin Dinas, saya ingin mendaftarkan kabupaten saya.')">
                <i class="fab fa-whatsapp"></i> Chat
            </button>
        </div>
        
        <div class="contact-card" onclick="openWhatsApp('6281234567894', 'Halo Super Admin, saya ingin mendaftarkan akun baru.')">
            <div class="contact-icon"><i class="fas fa-user-shield"></i></div>
            <div class="contact-info">
                <strong>Daftar Super Admin</strong>
                <div class="contact-role">Super Admin DPMD</div>
                <div class="contact-number"><i class="fab fa-whatsapp"></i> 0812-3456-7894</div>
            </div>
            <button class="whatsapp-btn" onclick="event.stopPropagation(); openWhatsApp('6281234567894', 'Halo Super Admin, saya ingin mendaftarkan akun baru.')">
                <i class="fab fa-whatsapp"></i> Chat
            </button>
        </div>
        
        <button onclick="closeContactModal()" class="login-btn" style="margin-top: 12px;">Tutup</button>
    </div>
</div>

<script>
    function openContactModal() {
        document.getElementById('contactModal').style.display = 'flex';
    }

    function closeContactModal() {
        document.getElementById('contactModal').style.display = 'none';
    }

    function openWhatsApp(phoneNumber, message) {
        let cleanNumber = phoneNumber.replace(/\D/g, '');
        let encodedMessage = encodeURIComponent(message);
        let whatsappUrl = `https://wa.me/${cleanNumber}?text=${encodedMessage}`;
        window.open(whatsappUrl, '_blank');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('contactModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

</body>
</html>
