<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Aset - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-200: #a7f3d0;
            --emerald-300: #6ee7b7;
            --emerald-400: #34d399;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --emerald-700: #047857;
            --emerald-800: #065f46;
            --emerald-900: #064e3b;
            --bs-primary: var(--emerald-600);
            --bs-primary-rgb: 5, 150, 105;
            --bs-success: var(--emerald-500);
            --bs-success-rgb: 16, 185, 129;
            --bs-body-bg: #f8fafc;
            /* Using Plus Jakarta Sans for headings and Inter for body text for a premium feel */
            --bs-font-sans-serif: 'Inter', system-ui, -apple-system, sans-serif;
            --font-heading: 'Plus Jakarta Sans', system-ui, sans-serif;
            --bs-border-radius: 0.75rem;
            --bs-border-radius-lg: 1rem;
            --bs-border-radius-xl: 1.5rem;
        }

        body {
            font-family: var(--bs-font-sans-serif);
            background-color: var(--bs-body-bg);
            color: #334155;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .card-title,
        .modal-title,
        .navbar-brand {
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
        }

        /* --- Global Responsiveness (Tata letak, Teks, & Gambar) --- */
        img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        html {
            font-size: 16px;
        }

        table {
            width: 100%;
        }

        @media (max-width: 768px) {
            html {
                font-size: 14px;
                /* Teks otomatis mengecil pada layar medium */
            }

            .display-4 {
                font-size: 2.5rem;
            }

            .navbar {
                padding: 12px 15px !important;
                border-radius: 1rem !important;
            }
        }

        @media (max-width: 576px) {
            html {
                font-size: 13px;
                /* Teks lebih kecil lagi pada HP */
            }

            .card-body,
            .card-header,
            .modal-body,
            .modal-header {
                padding: 1.25rem !important;
                /* Tata letak ruang diperkecil otomatis */
            }
        }

        /* ---------------------------------------------------- */

        /* Modern Sidebar */
        .sidebar {
            min-height: 100vh;
            background: #ffffff;
            color: #334155;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.02);
            z-index: 1030;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand-icon {
            background: #ffffff;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .sidebar-brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar a {
            color: #64748b;
            text-decoration: none;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 0.90rem;
            transition: all 0.25s ease;
            margin: 4px 16px;
            border-radius: 12px;
            border: none;
        }

        .sidebar a i {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            margin-right: 12px;
            transition: transform 0.25s ease;
        }

        .sidebar a:hover {
            color: var(--emerald-600);
            background-color: var(--emerald-50);
            transform: translateX(4px);
        }

        .sidebar a.active {
            color: #ffffff;
            background: linear-gradient(135deg, var(--emerald-500) 0%, var(--emerald-600) 100%);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
            font-weight: 700;
        }

        .sidebar a.active i {
            color: #ffffff;
        }

        /* Glassmorphism Navbar */
        .main-content {
            padding: 35px 40px;
            transition: all 0.3s ease;
            min-width: 0;
            /* Mencegah Flexbox membesar gara-gara tabel */
            width: 100%;
            max-width: 100vw;
        }

        .navbar {
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05) !important;
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 16px 28px;
            margin-bottom: 40px !important;
            border: 1px solid rgba(255, 255, 255, 1);
            position: sticky;
            top: 20px;
            z-index: 1020;
        }

        /* Cards */
        .card {
            box-shadow: 0 10px 35px -5px rgba(15, 23, 42, 0.04), 0 4px 12px -2px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--bs-border-radius-xl);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s ease, border-color 0.35s ease;
            background: white;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.08);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem 1.75rem;
        }

        .card-body {
            padding: 1.75rem;
        }

        /* Forms */
        .form-control,
        .form-select {
            border-radius: 0.85rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.25s ease;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: white;
            border-color: var(--emerald-400);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        /* Buttons */
        .btn {
            border-radius: 0.85rem;
            padding: 0.65rem 1.5rem;
            font-weight: 600;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 0.6rem;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
        }

        .btn:active {
            transform: translateY(0) scale(1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-500) 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--emerald-700) 0%, var(--emerald-600) 100%);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.35);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25);
        }

        .btn-outline-primary {
            color: var(--emerald-600);
            border: 2px solid var(--emerald-500);
            font-weight: 700;
        }

        .btn-outline-primary:hover {
            background-color: var(--emerald-500);
            color: white;
            border-color: var(--emerald-500);
        }

        /* Tables */
        .table-responsive {
            border-radius: 1.25rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            background: white;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            /* Smooth scroll on mobile */
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-family: var(--font-heading);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }

        .table tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: var(--emerald-50);
            transform: scale(1.002);
            box-shadow: inset 0 0 0 9999px rgba(16, 185, 129, 0.02);
        }

        /* Badges */
        .badge {
            padding: 0.55em 0.9em;
            border-radius: 2rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .bg-success {
            background-color: var(--emerald-100) !important;
            color: var(--emerald-800) !important;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .bg-warning {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border: 1px solid rgba(253, 230, 138, 0.5);
        }

        .bg-danger {
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
            border: 1px solid rgba(254, 202, 202, 0.5);
        }

        .bg-primary {
            background-color: #e0e7ff !important;
            color: #4338ca !important;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .bg-info {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }

        .bg-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Utility */
        .bg-soft-emerald {
            background-color: var(--emerald-50) !important;
        }

        .text-emerald {
            color: var(--emerald-700) !important;
        }

        .border-emerald {
            border-color: var(--emerald-200) !important;
        }

        .bg-slate-50 {
            background-color: #f8fafc;
        }

        /* Modal Customization */
        .modal-content {
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            background: white;
        }

        .modal-header {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem 2rem;
        }

        .modal-header .modal-title {
            font-weight: 800;
            color: #1e293b;
        }

        .modal-header.bg-success {
            background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-500) 100%);
            border-bottom: none;
            color: white !important;
        }

        .modal-header.bg-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-bottom: none;
            color: white !important;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            padding: 1.25rem 2rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%);
                z-index: 1050;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                padding: 20px 15px;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.4);
                z-index: 1040;
                display: none;
                backdrop-filter: blur(2px);
                transition: opacity 0.3s;
                opacity: 0;
            }

            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="d-flex w-100 overflow-hidden" style="max-width: 100vw;">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column" style="width: 270px; min-width: 270px;">
            <div class="p-4 mb-2 d-flex align-items-center border-bottom border-slate-100"
                style="margin-bottom: 0.75rem !important;">
                <div class="sidebar-brand-icon me-3">
                    <img src="{{ asset('images/logo-sdit.jpg') }}" alt="Logo SDIT">
                </div>
                <div>
                    <h5 class="fw-bolder mb-0 text-slate-800" style="letter-spacing: -0.5px; font-size: 1.10rem;">Dar El
                        Iman 2</h5>
                    <small class="text-slate-400 fw-bold text-uppercase"
                        style="font-size: 0.65rem; letter-spacing: 0.5px;">Curator & Asset</small>
                </div>
            </div>

            <ul class="nav flex-column gap-1 w-100 flex-grow-1 mt-3">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Dashboard
                    </a>
                </li>
                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara', 'sarpras']))
                    <li class="nav-item">
                        <a href="{{ route('assets.index') }}"
                            class="{{ request()->routeIs('assets.index', 'assets.create', 'assets.edit', 'assets.show') ? 'active' : '' }}">
                            <i class="bi bi-inboxes"></i> Daftar Aset
                        </a>
                    </li>
                @endif

                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara', 'sarpras']))
                    <li class="nav-item">
                        <a href="{{ route('room-maintenances.index') }}"
                            class="{{ request()->routeIs('room-maintenances.*') ? 'active' : '' }}">
                            <i class="bi bi-card-checklist"></i> Kartu Ruangan (KIR)
                        </a>
                    </li>
                @endif

                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara', 'sarpras']))
                    <li class="nav-item">
                        <a href="{{ route('maintenance-logs.index') }}"
                            class="{{ request()->routeIs('maintenance-logs.*') ? 'active' : '' }}">
                            <i class="bi bi-wrench"></i> Perawatan
                        </a>
                    </li>
                @endif

                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara', 'kepala_sekolah', 'sarpras']))
                    <li class="nav-item">
                        <a href="{{ route('disposal-requests.index') }}"
                            class="{{ request()->routeIs('assets.disposed', 'disposal-requests.*') ? 'active' : '' }}">
                            <i class="bi bi-recycle"></i> Pelepasan Aset
                        </a>
                    </li>
                @endif

                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara', 'sarpras']))
                    <li class="nav-item">
                        <a href="{{ route('master-data.index') }}"
                            class="{{ request()->routeIs('master-data.*') ? 'active' : '' }}">
                            <i class="bi bi-database"></i> Data Utama
                        </a>
                    </li>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> Manajemen Pengguna
                        </a>
                    </li>
                @endif

                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara', 'kepala_sekolah', 'sarpras']))
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}"
                            class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line"></i> Laporan
                        </a>
                    </li>
                @endif
            </ul>

            <div class="mt-auto mb-4 w-100">
                <hr class="border-slate-200 mx-4 my-3 opacity-50">
                <ul class="nav flex-column gap-1 w-100">

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="w-100 m-0">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="">
                                <i class="bi bi-box-arrow-left"></i> Keluar Sistem
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex-grow-1 main-content bg-body-bg overflow-auto" style="height: 100vh;">
            <!-- Glassmorphism Navbar Header -->
            <nav class="navbar navbar-expand border-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button
                        class="btn btn-light bg-white border border-slate-200 shadow-sm rounded-circle d-flex align-items-center justify-content-center me-4"
                        id="sidebarToggle" style="width: 44px; height: 44px;">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark d-none d-md-block" style="letter-spacing: -0.5px;">
                            @yield('title')</h4>
                        <span class="text-muted small fw-medium mt-1 d-none d-md-block">Sistem Informasi Manajemen
                            Aset</span>
                        <h5 class="mb-0 fw-bold text-dark d-md-none text-truncate"
                            style="max-width: 140px; letter-spacing: -0.5px;">@yield('title')</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    @auth
                        <div class="dropdown">
                            <button
                                class="btn btn-light bg-white border dropdown-toggle shadow-sm d-flex align-items-center rounded-pill px-3 py-2 transition-all hover-scale"
                                type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                style="border-color: #e2e8f0 !important;">
                                <div class="bg-soft-emerald text-emerald rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm"
                                    style="width:36px; height:36px;">
                                    <span class="fw-bold fs-6">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <div class="text-start me-2 lh-1 d-none d-sm-block">
                                    <div class="fw-bold text-dark" style="font-size:0.9rem;">{{ Auth::user()->name }}</div>
                                    <small class="text-slate-500"
                                        style="font-size:0.75rem;">{{ Auth::user()->role === 'kepala_sekolah' ? 'Kepala Sekolah' : (Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'sarpras' ? 'Sarpras' : 'Bendahara')) }}</small>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 rounded-4 p-2 scale-up"
                                aria-labelledby="userDropdown"
                                style="min-width:260px; animation: dropdownScale 0.2s ease forwards;">
                                <li class="px-3 py-2">
                                    <span class="d-block fw-bold text-dark fs-6">{{ Auth::user()->name }}</span>
                                    <span class="d-block text-muted small">{{ Auth::user()->email }}</span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider opacity-25 my-2">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item text-danger fw-semibold rounded-3 py-2 d-flex align-items-center transition-all hover-bg-danger">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                                style="width:32px; height:32px;">
                                                <i class="bi bi-box-arrow-right"></i>
                                            </div>
                                            Keluar Sistem
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <span
                            class="badge bg-soft-emerald text-emerald px-4 py-2 rounded-pill fs-6 border border-emerald"><i
                                class="bi bi-person-circle me-2"></i>Tamu / Publik</span>
                    @endauth
                </div>
            </nav>

            <!-- Page Content (Container) -->
            <div class="container-fluid px-0 pb-5">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('modals')

    <!-- Bootstrap Bundle with Popper & Tooltips -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @keyframes dropdownScale {
            0% {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .hover-bg-danger:hover {
            background-color: #fef2f2 !important;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebarToggle && sidebar) {
                // Ensure desktop starts expanded
                let isExpanded = true;
                sidebarToggle.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        sidebar.classList.toggle('show');
                        if (overlay) overlay.classList.toggle('show');
                    } else {
                        isExpanded = !isExpanded;
                        sidebar.style.marginLeft = isExpanded ? '0px' : '-280px';
                    }
                });

                if (overlay) {
                    overlay.addEventListener('click', function () {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    });
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Ups..',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            @endif

                @if($errors->any())
                    let errorMessages = '';
                    @foreach ($errors->all() as $error)
                        errorMessages += '{{ $error }}\n';
                    @endforeach
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Validasi Gagal!',
                        text: errorMessages,
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                @endif
        });
    </script>
    @stack('scripts')
</body>

</html>