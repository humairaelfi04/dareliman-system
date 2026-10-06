@extends('layouts.app')

@section('title', 'Detail Aset')

@push('styles')
    <style>
        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .text-slate-800 {
            color: #1e293b;
        }

        .text-emerald-700 {
            color: #047857;
        }

        .text-emerald-800 {
            color: #065f46;
        }

        .bg-emerald-700 {
            background-color: #047857;
        }

        .bg-soft-emerald {
            background-color: #ecfdf5;
        }

        .breadcrumb-item a {
            transition: color 0.2s;
        }

        .breadcrumb-item a:hover {
            color: #047857 !important;
        }

        .table-borderless td,
        .table-borderless th {
            padding-left: 0;
            padding-right: 0;
        }

        /* Custom Tabs Styling */
        .nav-tabs .nav-link {
            color: #64748b;
            /* slate-500 */
            font-weight: 500;
            border: 0;
            border-bottom: 3px solid transparent;
            padding-bottom: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #1e293b;
            /* slate-800 */
            border-bottom-color: #cbd5e1;
            /* slate-300 */
        }

        .nav-tabs .nav-link.active {
            color: #047857 !important;
            /* emerald-700 */
            font-weight: 700 !important;
            border-bottom-color: #10b981 !important;
            /* emerald-500 */
            background-color: transparent !important;
        }

        .health-chart-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto;
            flex-shrink: 0;
        }

        #healthRadialChart {
            display: block;
            width: 140px !important;
            height: 140px !important;
        }

        .health-center-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            line-height: 1;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-2 pb-5">

        <!-- Header & Breadcrumbs -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small fw-medium">
                        <li class="breadcrumb-item"><a href="{{ route('assets.index') }}"
                                class="text-decoration-none text-muted">Data Inventaris</a></li>
                        <li class="breadcrumb-item"><span
                                class="text-muted">{{ $asset->category->nama_kategori ?? '-' }}</span></li>
                        <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                            {{ Str::limit($asset->nama_aset, 25) }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button data-bs-toggle="modal" data-bs-target="#qrModal"
                    class="btn btn-outline-dark fw-bold px-3 py-2 rounded-3 shadow-sm border border-slate-200"><i
                        class="bi bi-qr-code-scan me-2"></i> Label QR</button>
                <button data-bs-toggle="modal" data-bs-target="#addMaintenanceModal"
                    class="btn btn-light fw-bold px-3 py-2 rounded-3 shadow-sm border border-slate-200 text-slate-600"><i
                        class="bi bi-tools me-2"></i> Catat Servis</button>
                <a href="{{ route('assets.edit', $asset->id) }}"
                    class="btn fw-bold px-4 py-2 rounded-3 shadow-sm text-white" style="background-color: #047857;"><i
                        class="bi bi-pencil-fill me-2"></i> Ubah Aset</a>
            </div>
        </div>

        <!-- Top Hero Panel -->
        <div class="row g-4 mb-4">
            <!-- Left Hero Card -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-0 overflow-hidden bg-white">
                    <div class="p-4 p-xl-5 h-100 d-flex flex-column justify-content-center border-start border-4 border-emerald"
                        style="border-left-color: #047857 !important;">
                        @if(!$asset->disposal || is_null($asset->disposal->tanggal_keluar))
                            <div class="mb-3"><span class="badge bg-soft-emerald text-emerald rounded-pill px-3 py-2 fw-bolder"
                                    style="font-size: 0.70rem;"><i class="bi bi-circle-fill text-emerald me-2"
                                        style="font-size: 0.45rem; vertical-align: middle;"></i>DIGUNAKAN</span></div>
                        @else
                            <div class="mb-3"><span class="badge bg-slate-100 text-slate-500 rounded-pill px-3 py-2 fw-bolder"
                                    style="font-size: 0.70rem;">DIARSIP/DIJUAL</span></div>
                        @endif

                        <h3 class="fw-bolder text-slate-800 mb-1 mt-2" style="color: #0f172a;">{{ $asset->nama_aset }}</h3>
                        <p class="text-slate-500 mb-5 fw-medium">ID Aset: <span
                                class="text-slate-700 fw-bold">{{ $asset->kode_aset ?? 'EDU-' . str_pad($asset->id, 4, "0", STR_PAD_LEFT) }}</span>
                        </p>

                        <div class="d-flex flex-column flex-lg-row gap-3 mt-auto pt-4">
                            <div class="bg-slate-50 rounded-3 p-3 flex-grow-1 border border-slate-100 shadow-sm">
                                <p class="text-muted small fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    DITEMPATKAN DI</p>
                                <h6 class="fw-bold text-slate-800 mb-0 d-flex align-items-center"><i
                                        class="bi bi-person-badge text-primary opacity-50 me-2"></i>{{ $asset->unit ?? 'Tidak Diatur' }}
                                </h6>
                            </div>
                            <div class="bg-slate-50 rounded-3 p-3 flex-grow-1 border border-slate-100 shadow-sm">
                                <p class="text-muted small fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    LOKASI</p>
                                <h6 class="fw-bold text-slate-800 mb-0 d-flex align-items-center"><i
                                        class="bi bi-geo-alt-fill text-danger opacity-50 me-2"></i>{{ $asset->location->nama_ruangan ?? 'Gudang' }}
                                </h6>
                            </div>
                            @php
                                $loc = $asset->location;
                            @endphp
                            <div class="bg-slate-50 rounded-3 p-3 flex-grow-1 border border-slate-100 shadow-sm">
                                <p class="text-muted small fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    PENANGGUNG JAWAB</p>
                                <h6 class="fw-bold text-slate-800 mb-0 d-flex align-items-center"><i
                                        class="bi bi-person-check-fill text-emerald-500 opacity-75 me-2"></i>{{ $loc && $loc->penanggung_jawab ? $loc->penanggung_jawab : 'Tidak Diatur' }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Health Index Card -->
            @php
                $healthPercentage = 90;
                $healthText = "EXCELLENT";
                $healthColor = "#047857"; // emerald
                if ($asset->kondisi == "Rusak Ringan") {
                    $healthPercentage = 60;
                    $healthText = "FAIR";
                    $healthColor = "#eab308"; // yellow
                } elseif ($asset->kondisi == "Rusak Berat") {
                    $healthPercentage = 20;
                    $healthText = "CRITICAL";
                    $healthColor = "#ef4444"; // red
                }

                $isDisposed = $asset->disposal && !is_null($asset->disposal->tanggal_keluar);
                $nextServiceLog = $asset->maintenanceLogs->first();
                $nextService = 'Belum Dijadwalkan';
                if ($isDisposed) {
                    $nextService = 'Tidak Berlaku';
                } elseif ($asset->is_rutin_servis && $asset->tanggal_servis_berikutnya) {
                    $nextService = \Carbon\Carbon::parse($asset->tanggal_servis_berikutnya)->format('d M Y');
                } elseif ($nextServiceLog && $nextServiceLog->next_service_date) {
                    $nextService = \Carbon\Carbon::parse($nextServiceLog->next_service_date)->format('d M Y');
                }
            @endphp
            <div class="col-xl-4 mt-4 mt-xl-0">
                <div class="card bg-white border-0 shadow-sm rounded-4 h-100 p-4 d-flex flex-column"
                    style="border-left: 4px solid #065f46 !important;">
                    <p class="text-slate-500 fw-bold mb-4 text-center letter-spacing-1 text-uppercase flex-shrink-0"
                        style="font-size: 0.75rem;">INDEKS KESEHATAN ASET</p>

                    <div class="d-flex justify-content-center align-items-center flex-grow-1 mb-4">
                        <div class="health-chart-wrapper">
                            <canvas id="healthRadialChart"></canvas>
                            <div class="health-center-label">
                                <div class="fw-bolder mb-0"
                                    style="color: {{ $healthColor }}; font-size: 1.4rem; line-height: 1.2;">
                                    {{ $healthPercentage }}%
                                </div>
                                <div class="fw-bold text-muted" style="font-size: 0.60rem; letter-spacing: 0.5px;">
                                    {{ $healthText }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto border-top border-slate-100 pt-3">
                        @if(!$isDisposed)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-slate-500 small fw-medium">Servis Berikutnya</span>
                                <span class="fw-bold text-slate-800 small">{{ $nextService }}</span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-slate-500 small fw-medium">Status</span>
                                <span
                                    class="badge bg-secondary bg-opacity-10 text-secondary small fw-bold px-2 py-1">Dilepas</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Columns Details -->
        <div class="row g-4 mb-5">
            <!-- Technical Specs -->
            <div class="col-lg-4">
                <h6 class="fw-bolder text-emerald-800 mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing:0.5px;">
                    <i class="bi bi-sliders me-2 fs-5 align-middle"></i> Spesifikasi Teknis
                </h6>
                <div class="card border-0 shadow-sm rounded-4 px-4 py-3 h-100 bg-white">
                    <table class="table table-borderless table-sm mb-0">
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Merek / Model</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                {{ $asset->merek_aset ?: 'Tidak ditentukan' }}
                            </td>
                        </tr>
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Kategori</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                {{ $asset->category->nama_kategori ?? '-' }}
                            </td>
                        </tr>
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Kode Aset</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                {{ $asset->kode_aset ?: 'Tidak ada' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-medium py-3">Keterangan</td>
                            <td class="text-end fw-bold text-slate-800 py-3" style="font-size: 0.75rem;">
                                {{ Str::limit($asset->keterangan ?: 'Konfigurasi aset standar.', 50) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Procurement Detail -->
            <div class="col-lg-4">
                <h6 class="fw-bolder text-emerald-800 mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing:0.5px;">
                    <i class="bi bi-cart3 me-2 fs-5 align-middle"></i> Data Pengadaan
                </h6>
                <div class="card border-0 shadow-sm rounded-4 px-4 py-3 h-100 bg-white">
                    <table class="table table-borderless table-sm mb-0">
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Vendor</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                {{ $asset->vendor_aset ?: 'Pembelian Langsung' }}
                            </td>
                        </tr>
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Tanggal Masuk</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                {{ $asset->perolehanAset && $asset->perolehanAset->tanggal_masuk ? \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->format('d M Y') : 'Tidak diketahui' }}
                            </td>
                        </tr>
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Sumber Dana</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle font-monospace">
                                {{ $asset->sumber_dana }}
                            </td>
                        </tr>
                        @if($asset->perolehanAset)
                            <tr class="border-bottom border-slate-100">
                                <td class="text-muted small fw-medium py-3 align-middle">Jenis Transaksi</td>
                                <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                    {{ $asset->perolehanAset->jenis_transaksi_masuk ?? '-' }}
                                </td>
                            </tr>
                            <tr class="border-bottom border-slate-100">
                                <td class="text-muted small fw-medium py-3 align-middle">Pihak Kedua Karyawan?</td>
                                <td class="text-end fw-bold py-3 small align-middle">
                                    @if($asset->perolehanAset->is_pihak_kedua_karyawan_masuk)
                                        <span class="badge bg-success bg-opacity-10 text-success">Ya (Internal)</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Tidak (Eksternal)</span>
                                    @endif
                                </td>
                            </tr>
                            @if($asset->perolehanAset->is_pihak_kedua_karyawan_masuk)
                                <tr class="border-bottom border-slate-100">
                                    <td class="text-muted small fw-medium py-3 align-middle">NIK Pihak Kedua</td>
                                    <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                        {{ $asset->perolehanAset->nik_pihak_kedua_masuk ?? '-' }}
                                    </td>
                                </tr>
                            @else
                                <tr class="border-bottom border-slate-100">
                                    <td class="text-muted small fw-medium py-3 align-middle">Nama Pihak Kedua</td>
                                    <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                        {{ $asset->perolehanAset->nama_pihak_kedua_masuk ?? '-' }}
                                    </td>
                                </tr>
                                <tr class="border-bottom border-slate-100">
                                    <td class="text-muted small fw-medium py-3 align-middle">Alamat Pihak Kedua</td>
                                    <td class="text-end fw-bold text-slate-800 py-3 small align-middle">
                                        {{ $asset->perolehanAset->alamat_pihak_kedua_masuk ?? '-' }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                        <tr
                            class="@if($asset->perolehanAset && $asset->perolehanAset->bukti_transaksi_masuk) border-bottom border-slate-100 @endif">
                            <td class="text-muted small fw-medium py-3 align-middle">Kondisi</td>
                            <td class="text-end py-3">
                                <span
                                    class="badge {{ $asset->kondisi == 'Baik' ? 'bg-soft-emerald text-emerald' : ($asset->kondisi == 'Rusak Ringan' ? 'bg-warning bg-opacity-10 text-warning-emphasis' : 'bg-danger bg-opacity-10 text-danger') }} px-2 py-1"
                                    style="font-size: 0.7rem;">{{ $asset->kondisi }}</span>
                            </td>
                        </tr>
                        @if($asset->perolehanAset && $asset->perolehanAset->bukti_transaksi_masuk)
                            <tr>
                                <td class="text-muted small fw-medium py-3 align-middle">Bukti / Kwitansi</td>
                                <td class="text-end py-3">
                                    <a href="{{ asset('storage/' . $asset->perolehanAset->bukti_transaksi_masuk) }}"
                                        target="_blank"
                                        class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-2 py-1 border border-primary-subtle shadow-sm"><i
                                            class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat Kwitansi</a>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Financial Data -->
            <div class="col-lg-4">
                <h6 class="fw-bolder text-primary mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing:0.5px;"><i
                        class="bi bi-piggy-bank align-middle me-2 fs-5"></i> Data Keuangan</h6>
                <div class="card border-0 shadow-sm rounded-4 px-4 py-3 h-100 bg-white">
                    <table class="table table-borderless table-sm mb-0">
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Harga Perolehan</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">Rp
                                {{ number_format($asset->harga_perolehan, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted fw-medium align-middle" style="font-size:0.75rem;">Nilai Buku <br><span
                                    style="font-size: 0.60rem;">{{ ($asset->disposal && $asset->disposal->tanggal_keluar) ? '(Saat Pelepasan)' : '(Saat Ini)' }}</span>
                            </td>
                            <td class="text-end fw-bold text-primary py-3 small align-middle">Rp
                                {{ number_format($asset->nilai_buku, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="border-bottom border-slate-100">
                            <td class="text-muted small fw-medium py-3 align-middle">Penyusutan</td>
                            <td class="text-end fw-bold text-slate-800 py-3 small align-middle">Rp
                                {{ number_format($asset->akumulasi_penyusutan, 0, ',', '.') }} <br><span
                                    class="text-muted fw-normal" style="font-size: 0.65rem;">(Garis Lurus)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-medium py-3 align-middle">Umur Ekonomis</td>
                            @php
                                $lifeStatusText = $asset->umur_ekonomis_tahun . ' Years';
                                $lifeStatusColor = 'text-slate-800';
                                $isExpired = false;

                                $tglBatas = \Carbon\Carbon::now()->addDays(90);

                                if ($asset->perolehanAset && $asset->perolehanAset->tanggal_masuk) {
                                    $tglBatas = \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->addYears($asset->umur_ekonomis_tahun);
                                    if (now()->greaterThanOrEqualTo($tglBatas)) {
                                        $lifeStatusText = "Kedaluwarsa ({$asset->umur_ekonomis_tahun} Thn)";
                                        $lifeStatusColor = 'text-danger';
                                        $isExpired = true;
                                    }
                                }
                            @endphp
                            <td class="text-end fw-bold {{ $lifeStatusColor }} py-3 small align-middle">
                                {{ $lifeStatusText }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Disposal Info (Conditional) -->
        @if($asset->disposal && !is_null($asset->disposal->tanggal_keluar))
            <div class="card border-0 rounded-4 mb-5 overflow-hidden shadow-sm"
                style="background-color: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="row g-0">
                    <!-- Left Side: Status & Data -->
                    <div class="col-lg-8 p-4 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="bi bi-archive-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bolder text-slate-800 mb-0" style="letter-spacing: -0.5px;">Aset Telah Dilepas /
                                    Dihapus</h5>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-danger shadow-sm px-2 py-1" style="font-size: 0.65rem;">
                                        {{ strtoupper($asset->disposal->jenis_transaksi_keluar ?? 'N/A') }}
                                    </span>
                                    <span class="text-slate-500 fw-medium small">Status Kepemilikan Telah Berakhir</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <div class="bg-slate-50 px-3 py-2 rounded-3 border border-slate-200">
                                <span class="d-block text-slate-400 small fw-bold text-uppercase mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">TANGGAL DILEPAS</span>
                                <span class="fw-bold text-slate-700 fs-6"><i
                                        class="bi bi-calendar-event me-2 text-slate-400"></i>{{ \Carbon\Carbon::parse($asset->disposal->tanggal_keluar)->format('d M Y') }}</span>
                            </div>
                            <div class="bg-slate-50 px-3 py-2 rounded-3 border border-slate-200">
                                <span class="d-block text-slate-400 small fw-bold text-uppercase mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">NILAI PELEPASAN</span>
                                <span class="fw-bold text-slate-700 fs-6"><i
                                        class="bi bi-cash me-2 text-slate-400"></i>@if($asset->disposal->harga_jual) Rp
                                        {{ number_format($asset->disposal->harga_jual, 0, ',', '.') }} @else Rp 0 @endif</span>
                            </div>
                            <div class="bg-slate-50 px-3 py-2 rounded-3 border border-slate-200 flex-grow-1">
                                <span class="d-block text-slate-400 small fw-bold text-uppercase mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">PIHAK PENERIMA</span>
                                <span class="fw-bold text-slate-700 fs-6"><i
                                        class="bi bi-person-badge me-2 text-slate-400"></i>{{ $asset->disposal->is_pihak_kedua_karyawan_keluar ? ($asset->disposal->nik_pihak_kedua_keluar ?? '-') : ($asset->disposal->nama_pihak_kedua_keluar ?? '-') }}</span>
                            </div>
                        </div>

                        <hr class="border-slate-200 my-3">

                        <div class="row g-3">
                            <!-- Alamat Penerima -->
                            @if(!$asset->disposal->is_pihak_kedua_karyawan_keluar && $asset->disposal->alamat_pihak_kedua_keluar)
                                <div class="col-md-12">
                                    <span class="d-block text-slate-400 small fw-bold text-uppercase mb-1"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">ALAMAT PIHAK KEDUA / PENERIMA</span>
                                    <p
                                        class="text-slate-700 small mb-0 fw-medium bg-slate-50 p-2 rounded-2 border border-slate-200">
                                        <i class="bi bi-geo-alt me-1 text-slate-400"></i>
                                        {{ $asset->disposal->alamat_pihak_kedua_keluar }}
                                    </p>
                                </div>
                            @endif

                            <div class="col-md-7">
                                <div class="h-100 p-3 rounded-3 border border-slate-200" style="background-color: #f8fafc;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-chat-quote-fill text-danger opacity-75"></i>
                                        <span class="text-slate-500 small fw-bold text-uppercase"
                                            style="font-size: 0.65rem; letter-spacing: 0.5px;">ALASAN PELEPASAN ASET</span>
                                    </div>
                                    <p class="text-slate-700 small mb-0 fw-medium fst-italic">
                                        "{{ $asset->disposal->alasan_pengajuan ?? 'Sistem tidak mencatat rincian alasan pelepasan untuk aset ini.' }}"
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="h-100 p-3 rounded-3 border border-slate-200 bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-shield-check text-success opacity-75"></i>
                                        <span class="text-slate-500 small fw-bold text-uppercase"
                                            style="font-size: 0.65rem; letter-spacing: 0.5px;">PROSES PERSETUJUAN</span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-slate-100 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 28px; height: 28px;">
                                                <i class="bi bi-person-up text-slate-500" style="font-size: 0.80rem;"></i>
                                            </div>
                                            <div class="small fw-medium text-slate-500"
                                                style="font-size: 0.75rem; line-height: 1.2;">
                                                Diajukan <br><span
                                                    class="fw-bold text-slate-800">{{ $asset->disposal->requester?->name ?? ($asset->disposal->requester_name ?? 'Sistem / Migrasi Lama') }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 28px; height: 28px;">
                                                <i class="bi bi-check2-all text-success" style="font-size: 0.80rem;"></i>
                                            </div>
                                            <div class="small fw-medium text-slate-500"
                                                style="font-size: 0.75rem; line-height: 1.2;">
                                                Disetujui <br><span
                                                    class="fw-bold text-slate-800">{{ $asset->disposal->approver?->name ?? 'Otomatis' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Action -->
                    <div class="col-lg-4 p-0 d-flex flex-column" style="background-color: #f8fafc; border-left: 1px dashed #cbd5e1;">
                        <div class="p-4 d-flex flex-column h-100 justify-content-center">
                            <h6 class="fw-bolder text-slate-700 mb-3 text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Dokumen Pendukung</h6>
                            
                            <div class="d-flex flex-column gap-3 w-100">
                                @if($asset->disposal->foto_kondisi)
                                <a href="{{ asset('storage/' . $asset->disposal->foto_kondisi) }}" target="_blank" 
                                   class="d-flex align-items-center bg-white p-2 rounded-4 border border-slate-200 text-decoration-none shadow-sm hover-scale w-100" style="transition: all 0.3s ease;">
                                    <div class="rounded-3 overflow-hidden bg-slate-100 d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 55px; height: 55px;">
                                        <img src="{{ asset('storage/' . $asset->disposal->foto_kondisi) }}" class="w-100 h-100 object-fit-cover" alt="Foto">
                                    </div>
                                    <div class="overflow-hidden pe-2">
                                        <div class="fw-bold text-slate-800 text-truncate" style="font-size: 0.85rem;">Foto Kondisi</div>
                                        <div class="text-slate-500 small text-truncate" style="font-size: 0.75rem;">Klik untuk melihat</div>
                                    </div>
                                    <div class="ms-auto pe-2 text-slate-400">
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </a>
                                @endif

                                @if($asset->disposal->bukti_transaksi_keluar)
                                <a href="{{ asset('storage/' . $asset->disposal->bukti_transaksi_keluar) }}" target="_blank" 
                                   class="d-flex align-items-center bg-white p-2 rounded-4 border border-slate-200 text-decoration-none shadow-sm hover-scale w-100" style="transition: all 0.3s ease;">
                                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 55px; height: 55px;">
                                        <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                    </div>
                                    <div class="overflow-hidden pe-2">
                                        <div class="fw-bold text-slate-800 text-truncate" style="font-size: 0.85rem;">Dokumen BAST</div>
                                        <div class="text-slate-500 small text-truncate" style="font-size: 0.75rem;">Buka lampiran</div>
                                    </div>
                                    <div class="ms-auto pe-2 text-slate-400">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </div>
                                </a>
                                @endif

                                @if(!$asset->disposal->foto_kondisi && !$asset->disposal->bukti_transaksi_keluar)
                                <div class="text-center py-4 bg-white rounded-4 border border-slate-200" style="border-style: dashed !important;">
                                    <i class="bi bi-folder-x text-slate-300 mb-2 d-block" style="font-size: 2rem;"></i>
                                    <h6 class="fw-bold text-slate-500 mb-1" style="font-size: 0.85rem;">Tidak Ada Lampiran</h6>
                                    <p class="text-slate-400 small mb-0 px-3" style="font-size: 0.75rem;">Belum ada foto kondisi atau dokumen BAST.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Warning Box (Removed per request) -->

        <!-- Maintenance Table Container -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mt-4">
            <!-- Tabs Heading -->
            <ul class="nav nav-tabs border-bottom border-slate-100 overflow-auto mb-4 border-0 d-flex flex-nowrap"
                id="assetTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="servis-tab" data-bs-toggle="tab" data-bs-target="#servis-pane"
                        type="button" role="tab" aria-controls="servis-pane" aria-selected="true"
                        style="white-space: nowrap;">
                        <i class="bi bi-clipboard2-pulse me-2"></i> Riwayat Servis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="perubahan-tab" data-bs-toggle="tab" data-bs-target="#perubahan-pane"
                        type="button" role="tab" aria-controls="perubahan-pane" aria-selected="false"
                        style="white-space: nowrap;">
                        <i class="bi bi-intersect me-2"></i> Riwayat Perubahan
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="assetTabsContent">
                <!-- Tab Pane: Riwayat Servis -->
                <div class="tab-pane fade show active" id="servis-pane" role="tabpanel" aria-labelledby="servis-tab"
                    tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle mb-0">
                            <thead style="background-color: #f1f5f9;">
                                <tr>
                                    <th
                                        class="py-3 px-4 text-slate-500 small fw-bold text-uppercase letter-spacing-1 rounded-start-2">
                                        TANGGAL</th>
                                    <th class="py-3 text-slate-500 small fw-bold text-uppercase letter-spacing-1">JENIS</th>
                                    <th class="py-3 text-slate-500 small fw-bold text-uppercase letter-spacing-1">DESKRIPSI
                                    </th>
                                    <th class="py-3 text-slate-500 small fw-bold text-uppercase letter-spacing-1">BIAYA</th>
                                    <th
                                        class="py-3 text-slate-500 small fw-bold text-uppercase letter-spacing-1 rounded-end-2">
                                        STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($asset->maintenanceLogs as $log)
                                    <tr class="border-bottom border-slate-50 cursor-pointer">
                                        <td class="py-3 px-4">
                                            <div class="fw-bold text-slate-800">
                                                {{ \Carbon\Carbon::parse($log->tanggal_servis)->format('d M') }}
                                            </div>
                                            <div class="text-muted small fw-medium">
                                                {{ \Carbon\Carbon::parse($log->tanggal_servis)->format('Y') }}
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if(str_contains(strtolower($log->deskripsi_kerusakan), 'rutin'))
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold">Rutin</span>
                                            @else
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-dark px-3 py-1 rounded-pill fw-bold"
                                                    style="border: 1px solid #fef08a;">Perbaikan</span>
                                            @endif
                                        </td>
                                        <td class="py-3 fw-medium text-slate-700" style="max-width: 250px;">
                                            {{ Str::limit($log->deskripsi_kerusakan, 50) }}
                                            @if($log->next_service_date)
                                            <div class="small text-muted mt-1 opacity-75">Berikutnya:
                                                {{ \Carbon\Carbon::parse($log->next_service_date)->format('d M y') }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="py-3 fw-medium text-slate-700 border-start-0">
                                            @if($log->biaya_perbaikan > 0)
                                                Rp {{ number_format($log->biaya_perbaikan, 0, ',', '.') }}
                                            @else
                                                Rp 0 (Internal)
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-bold text-emerald small bg-soft-emerald px-2 py-1 rounded-pill"><i
                                                    class="bi bi-check me-1"></i> Selesai</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-slate-400 mb-2"><i class="bi bi-clipboard-x display-6"></i></div>
                                            <p class="text-muted fw-medium mb-0">Belum ada catatan pemeliharaan untuk aset ini.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Pane: Riwayat Perubahan -->
                <div class="tab-pane fade" id="perubahan-pane" role="tabpanel" aria-labelledby="perubahan-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-3 px-4 text-slate-500 fw-bold small">TANGGAL</th>
                                    <th class="py-3 text-slate-500 fw-bold small">USER</th>
                                    <th class="py-3 text-slate-500 fw-bold small">JENIS</th>
                                    <th class="py-3 text-slate-500 fw-bold small">NILAI LAMA</th>
                                    <th class="py-3 text-slate-500 fw-bold small">NILAI BARU</th>
                                    <th class="py-3 pe-4 text-slate-500 fw-bold small">CATATAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asset->histories as $history)
                                    <tr class="border-bottom border-slate-100">
                                        <td class="py-3 px-4 text-slate-600 fw-medium small">
                                            {{ \Carbon\Carbon::parse($history->tanggal)->format('d M Y') }}
                                        </td>
                                        <td class="py-3 fw-bold text-slate-700 small">
                                            {{ $history->user ? $history->user->name : 'Sistem' }}
                                        </td>
                                        <td class="py-3 fw-bold text-slate-700 small">{{ $history->jenis_perubahan }}</td>
                                        <td class="py-3 text-slate-500 small"><del>{{ $history->nilai_lama ?? '-' }}</del></td>
                                        <td class="py-3 fw-bold text-emerald-700 small">{{ $history->nilai_baru }}</td>
                                        <td class="py-3 pe-4 text-muted small">{{ $history->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-muted fw-medium py-5">Belum ada riwayat
                                            perubahan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Data Modals hidden -> Included for functionality -->
    <div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white px-4 pt-4 pb-3 border-bottom">
                    <h5 class="modal-title text-dark fw-bold"><i class="bi bi-tools fs-5 me-2 text-primary"></i>Tambah
                        Catatan Servis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('maintenance-logs.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-slate-700 small">Tanggal Dilaksanakan <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_servis" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-slate-700 small">Deskripsi / Tindakan <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi_kerusakan" rows="2"
                                placeholder="mis. Format drive, ganti filter" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-slate-700 small">Biaya (Rp) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="biaya_perbaikan_mask" value="0" required>
                            <input type="hidden" name="biaya_perbaikan" id="biaya_perbaikan_real" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-slate-700 small">Kondisi Setelah Servis <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="kondisi_setelah_servis" required>
                                <option value="Baik" selected>Kembali Baik</option>
                                <option value="Rusak Ringan">Masih Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat (Gagal Servis)</option>
                            </select>
                            <div class="text-muted mt-1" style="font-size:0.75rem;">Sistem akan otomatis mengupdate kondisi
                                aset ini.</div>
                        </div>
                        <!-- Toggle servis rutin -->
                        @if(!$asset->is_rutin_servis)
                            <div class="mb-3 p-3 bg-white rounded-3 border border-slate-100">
                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="toggleRutin"
                                        onchange="toggleJadwalRutin(this.checked)">
                                    <label class="form-check-label fw-bold text-slate-700 small mb-0" for="toggleRutin">
                                        Perlu servis rutin berkala?
                                    </label>
                                </div>
                                <p class="text-muted mb-0 mt-1" style="font-size:0.75rem;">Aktifkan jika aset ini memerlukan
                                    jadwal pemeliharaan rutin.</p>
                            </div>
                            <div class="mb-2" id="wrapNextService" style="display:none;">
                                <label class="form-label fw-bold text-slate-700 small">Jadwal Servis Berikutnya</label>
                                <input type="date" class="form-control" name="next_service_date" id="nextServiceDate">
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-white border-subtle px-4 py-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border border-slate-200"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white rounded-pill px-4 fw-bold">Simpan
                            Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white px-4 border-bottom pt-4 pb-3 text-center d-block">
                    <h5 class="modal-title text-slate-800 fw-bold"><i class="bi bi-qr-code fs-5 me-2 text-primary"></i>
                        Label QR Code</h5>
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center bg-slate-50">
                    <div id="printArea"
                        class="d-inline-block border border-slate-200 p-4 rounded-4 shadow-sm bg-white w-100">
                        <h6 class="fw-bold mb-3 text-dark fs-5">
                            {{ $asset->kode_aset ?? 'AST-' . str_pad($asset->id, 4, "0", STR_PAD_LEFT) }}
                        </h6>
                        <div id="qrcode" class="mx-auto d-flex justify-content-center mb-3"></div>
                        <small class="text-muted mt-2 d-block fw-bold">{{ Str::limit($asset->nama_aset, 25) }}</small>
                        <small class="text-slate-400 d-block mt-1" style="font-size: 0.70rem;">SDIT 2 Dar El-Iman</small>
                    </div>
                </div>
                <div
                    class="modal-footer bg-white px-4 py-3 border-top border-slate-100 d-flex justify-content-center w-100">
                    <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm w-100"
                        onclick="printQR()"><i class="bi bi-printer-fill me-2"></i>Cetak Label ini</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var url = "{{ route('assets.show', $asset->id) }}";
            new QRCode(document.getElementById("qrcode"), {
                text: url,
                width: 150,
                height: 150,
                colorDark: "#1e293b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });

        function printQR() {
            var printContent = document.getElementById('printArea').innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <div style="width: 50mm; padding: 2mm; font-family: sans-serif; text-align: center; margin: 0 auto; color: #000;">
                    ${printContent}
                </div>
            `;

            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload(); // Reload needed because overwriting body breaks event listeners
        }
    </script>
    <script>
        function toggleJadwalRutin(checked) {
            const wrap = document.getElementById('wrapNextService');
            const input = document.getElementById('nextServiceDate');
            wrap.style.display = checked ? 'block' : 'none';
            input.required = checked;
            if (!checked) input.value = '';
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const biayaMask = document.getElementById('biaya_perbaikan_mask');
            const biayaReal = document.getElementById('biaya_perbaikan_real');

            if (biayaMask && biayaReal) {
                // Formatting on typing
                biayaMask.addEventListener('input', function (e) {
                    // Hanya izinkan angka
                    let value = this.value.replace(/\D/g, '');

                    if (value === '') {
                        value = '0';
                    }

                    // Format dengan pemisah ribuan titik
                    this.value = parseInt(value, 10).toLocaleString('id-ID');

                    // Simpan nilai murni ke hidden field untuk form submit
                    biayaReal.value = parseInt(value, 10);
                });

                // Select all when focused
                biayaMask.addEventListener('focus', function () {
                    this.select();
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const hPercent = {{ $healthPercentage }};
            const hColor = '{{ $healthColor }}';

            Chart.defaults.font.family = "'Inter', sans-serif";
            const ctx = document.getElementById('healthRadialChart').getContext('2d');

            const doughnutShadow = {
                id: 'doughnutShadow',
                beforeDraw: (chart) => {
                    const ctx = chart.ctx;
                    ctx.save();
                    const chartArea = chart.chartArea;
                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;
                    const radius = Math.min(chartArea.right - chartArea.left, chartArea.bottom - chartArea.top) / 2;

                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius * 0.85, 0, 2 * Math.PI);
                    ctx.fillStyle = '#f8fafc';
                    ctx.fill();
                    ctx.restore();
                }
            };

            new Chart(ctx, {
                type: 'doughnut',
                plugins: [doughnutShadow],
                data: {
                    datasets: [{
                        data: [hPercent, 100 - hPercent],
                        backgroundColor: [hColor, '#f1f5f9'],
                        borderWidth: 0,
                        borderRadius: (ctx) => {
                            return ctx.dataIndex === 0 ? 10 : 0; // rounded caps only for the filled part
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    rotation: -90,
                    circumference: 360,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: false
                    }
                }
            });
        });
    </script>
@endpush