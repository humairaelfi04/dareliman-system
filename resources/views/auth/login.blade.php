<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Inventaris - SDIT 2 Dar El-Iman</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --emerald-700: #047857;
            --emerald-800: #065f46;
            --emerald-900: #064e3b;
            --emerald-950: #022c22;
            --accent-yellow: #fcd34d;
            --accent-yellow-hover: #fbbf24;

            --font-sans: 'Inter', sans-serif;
            --font-heading: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            color: #334155;
            background-color: #ffffff;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand {
            font-family: var(--font-heading);
            color: #0f172a;
        }

        /* --- Navbar --- */
        .navbar {
            background-color: #ffffff;
            padding: 1.25rem 0;
            border-bottom: 1px solid transparent;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar.scrolled {
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            padding: 1rem 0;
            background-color: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid #f1f5f9;
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--emerald-800) !important;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-brand img {
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            margin-right: 0.75rem;
        }

        /* --- Hero Section --- */
        .hero-section {
            position: relative;
            padding: 7rem 0 6rem;
            background-image: url('{{ asset('images/login_bg.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 85vh;
            display: flex;
            align-items: center;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.95) 0%, rgba(4, 120, 87, 0.85) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            color: #d1fae5;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            line-height: 1.6;
            color: #e2e8f0;
            margin-bottom: 2.5rem;
            font-weight: 400;
            max-width: 90%;
        }

        .btn-mulai {
            background-color: var(--accent-yellow);
            color: var(--emerald-950);
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 8px;
            transition: all 0.3s;
            border: none;
        }

        .btn-mulai:hover {
            background-color: var(--accent-yellow-hover);
            transform: translateY(-2px);
        }

        .btn-outline-light-custom {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
            font-weight: 600;
            padding: 0.85rem 2rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-outline-light-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        /* Hero Floating Card */
        .hero-floating-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            color: #1e293b;
        }

        .hero-floating-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .hero-floating-card-icon {
            background-color: var(--emerald-800);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .hero-floating-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            font-family: var(--font-sans);
        }

        .hero-floating-card-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }

        .stat-row {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .stat-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .stat-label i {
            color: var(--emerald-700);
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .stat-value {
            font-weight: 700;
            color: var(--emerald-800);
            font-size: 0.9rem;
        }

        /* --- Features Section --- */
        .features-section {
            padding: 6rem 0;
            background-color: #f8fafc;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .section-title h2 {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .section-title p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            border: 1px solid #e2e8f0;
            height: 100%;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: var(--emerald-200);
        }

        .feature-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        /* --- Login Section --- */
        .login-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
        }

        /* Decorative Background Shape */
        .login-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            width: 100%;
            height: 200%;
            background-color: #f8fafc;
            transform: rotate(-15deg);
            z-index: 0;
            box-shadow: -20px 0 50px rgba(0, 0, 0, 0.02);
        }

        .login-content {
            position: relative;
            z-index: 1;
        }

        .login-info-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            letter-spacing: -0.5px;
        }

        .login-info-desc {
            font-size: 1.1rem;
            color: #475569;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .login-checklist {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .login-checklist li {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
            font-size: 0.95rem;
            color: #334155;
            font-weight: 500;
        }

        .login-checklist li i {
            color: white;
            background-color: var(--emerald-700);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin-right: 1rem;
        }

        .login-form-card {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .form-control:focus {
            border-color: var(--emerald-500);
            box-shadow: 0 0 0 4px var(--emerald-50);
        }

        .input-group-text {
            background-color: white;
            border: 1px solid #cbd5e1;
            border-right: none;
            color: #64748b;
        }

        .form-control.border-start-0 {
            border-left: none;
        }

        .btn-login-submit {
            background-color: var(--emerald-700);
            color: white;
            font-weight: 700;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            border: none;
            transition: all 0.3s;
        }

        .btn-login-submit:hover {
            background-color: var(--emerald-600);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(4, 120, 87, 0.2);
        }

        .forgot-password {
            color: #b45309;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        /* --- Footer --- */
        .footer {
            background-color: #ffffff;
            padding: 3rem 0 2rem;
            border-top: 1px solid #f1f5f9;
        }

        .footer-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--emerald-900);
            margin-bottom: 0.5rem;
            font-family: var(--font-heading);
        }

        .footer-desc {
            font-size: 0.85rem;
            color: #64748b;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--emerald-600);
        }

        .footer-bottom-text {
            text-align: center;
            font-size: 0.75rem;
            color: #cbd5e1;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #f8fafc;
        }

        /* Responsive Adjustments */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-section {
                text-align: center;
                padding: 6rem 0 4rem;
            }

            .hero-badge {
                margin: 0 auto 1.5rem;
            }

            .hero-subtitle {
                margin: 0 auto 2.5rem;
            }

            .hero-floating-card {
                margin-top: 3rem;
            }

            .login-info-title {
                font-size: 2rem;
            }

            .footer-links {
                justify-content: flex-start;
                margin-top: 2rem;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container d-flex justify-content-center justify-content-lg-start">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/logo-sdit.jpg') }}" alt="Logo SDIT" width="40" height="40"
                    style="object-fit: cover;">
                SDIT 2 Dar El-Iman Padang
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="hero-badge">AMANAH & IHSAN</span>
                    <h1 class="hero-title text-white">Sistem Informasi Manajemen Aset dan Inventaris Sarana dan
                        Prasarana SDIT 2 Dar El-Iman Kota Padang</h1>
                    <p class="hero-subtitle">Mewujudkan efisiensi operasional dan ketertiban administrasi aset sekolah
                        melalui sistem pengelolaan yang transparan dan akuntabel.</p>
                </div>
                <div class="col-lg-5 mt-5 mt-lg-0">
                    <div class="login-form-card" id="login-form">
                        @if($errors->any())
                            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger rounded-3 p-3 mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-4 text-center">
                                <h4 class="fw-bold text-dark mb-1" style="font-family: var(--font-heading);">Portal
                                    Administrasi</h4>
                                <p class="text-muted small mb-0">Masuk menggunakan kredensial Anda</p>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Username / Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email"
                                        value="{{ old('email') }}" required autofocus
                                        placeholder="Masukkan email atau username">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password"
                                        name="password" required placeholder="••••••••">
                                </div>
                            </div>


                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-login-submit">Masuk ke Sistem</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Pengelolaan Aset yang Terintegrasi</h2>
                <p>Kami menyediakan solusi komprehensif untuk melacak, mengelola, dan melaporkan setiap sarana yang
                    dimiliki sekolah dengan standar keunggulan.</p>
            </div>
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h4>Pelacakan Presisi</h4>
                        <p>Sistem labeling ruang yang memudahkan identifikasi lokasi dan kondisi fisik aset secara
                            real-time di setiap ruangan.</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-bar-chart-line-fill"></i>
                        </div>
                        <h4>Laporan Otomatis</h4>
                        <p>Generate laporan inventaris bulanan dan tahunan secara instan untuk kebutuhan audit dan
                            perencanaan anggaran.</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h4>Jadwal Pemeliharaan</h4>
                        <p>Notifikasi otomatis untuk pemeliharaan rutin fasilitas guna memperpanjang masa pakai dan
                            menjamin keamanan siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="footer">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h4 class="footer-brand">SDIT 2 Dar El-Iman</h4>
                    <p class="footer-desc mb-0">&copy; {{ date('Y') }} SDIT 2 Dar El-Iman, Amanah & Ihsan in
                        Stewardship.</p>
                </div>
                <div class="col-md-6">
                    <div class="footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Help Desk</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom-text">
                UNIT PENGELOLA SARANA & PRASARANA PENDIDIKAN
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar Scrolled Effect
        window.addEventListener('scroll', function () {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>

</html>