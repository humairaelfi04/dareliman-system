@extends('layouts.app')

@section('title', 'Cetak Laporan')

@push('styles')
    <style>
        body {
            background-color: #fafafa !important;
        }

        .report-layout {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        @media (min-width: 992px) {
            .report-layout {
                flex-direction: row;
            }
        }

        .report-sidebar {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 1.75rem;
            width: 100%;
            max-width: 320px;
            flex-shrink: 0;
        }

        .report-preview-area {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 2rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .section-title {
            font-size: 0.70rem;
            font-weight: 700;
            color: #065f46;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            text-transform: uppercase;
        }

        .option-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.85rem 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.2s;
        }

        .option-card:hover {
            border-color: #cbd5e1;
        }

        .option-card.active {
            border-color: #065f46;
            background-color: #ffffff;
        }

        .radio-circle {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #94a3b8;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .option-card.active .radio-circle {
            border-color: #065f46;
        }

        .option-card.active .radio-circle::after {
            content: '';
            width: 8px;
            height: 8px;
            background-color: #065f46;
            border-radius: 50%;
        }

        .check-square {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            border: 2px solid #94a3b8;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .option-card.checked .check-square {
            border-color: #065f46;
            background-color: #065f46;
        }

        .option-card.checked .check-square::after {
            content: '';
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-bottom: 2px;
        }

        .period-select {
            display: flex;
            gap: 0.4rem;
            margin-top: 0.6rem;
            flex-wrap: wrap;
        }

        .period-select select {
            flex: 1;
            min-width: 80px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
        }

        .date-inputs {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .date-inputs input {
            width: 50%;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 0.4rem 0.5rem;
            font-size: 0.75rem;
            color: #475569;
        }

        .format-btn {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
            cursor: pointer;
        }

        .format-btn.active {
            background: #ecfdf5;
            border-color: #065f46;
            color: #065f46;
        }

        .btn-generate {
            background-color: #065f46;
            border-color: #065f46;
            color: #ffffff;
            font-weight: 700;
            padding: 1rem;
            border-radius: 6px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(6, 95, 70, 0.2);
        }

        .btn-generate:hover {
            background-color: #047857;
            color: white;
        }

        .alert-no-selection {
            display: none;
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 6px;
            padding: 0.6rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #92400e;
            margin-top: 0.5rem;
        }

        /* Report Paper Preview Styling */
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .preview-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 1px;
        }

        .preview-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .paper-canvas {
            background: white;
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border-radius: 2px;
            min-height: 800px;
            display: flex;
            flex-direction: column;
        }

        .paper-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #a7f3d0;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .paper-logo h2 {
            color: #065f46;
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 0.2rem;
        }

        .paper-logo span {
            font-size: 0.70rem;
            font-weight: 700;
            color: #475569;
            letter-spacing: 1px;
        }

        .paper-logo p {
            font-size: 0.60rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }

        .paper-meta p.meta-tag {
            font-size: 0.65rem;
            font-weight: 800;
            color: #475569;
            text-align: right;
            margin-bottom: 0;
        }

        .paper-meta h3 {
            color: #3b82f6;
            font-weight: 600;
            font-size: 1.4rem;
            margin: 0.2rem 0;
            text-align: right;
        }

        .paper-meta p.meta-date {
            font-size: 0.55rem;
            font-weight: 700;
            color: #94a3b8;
            text-align: right;
            font-family: inherit;
        }

        .section-ribbon {
            border-left: 3px solid #065f46;
            padding-left: 0.5rem;
            font-size: 0.60rem;
            font-weight: 800;
            color: #065f46;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .summary-box {
            background-color: #f8fafc;
            padding: 1.25rem 1rem;
        }

        .summary-box label {
            font-size: 0.55rem;
            font-weight: 700;
            color: #64748b;
            display: block;
            margin-bottom: 0.5rem;
        }

        .summary-box .val-dark {
            font-size: 1.4rem;
            font-weight: 800;
            color: #065f46;
        }

        .summary-box .val-blue {
            font-size: 1.4rem;
            font-weight: 800;
            color: #2563eb;
        }

        .summary-box .val-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .paper-table {
            width: 100%;
            margin-bottom: 2.5rem;
            border-collapse: collapse;
        }

        .paper-table th {
            background-color: #e2e8f0;
            padding: 0.5rem 0.75rem;
            font-size: 0.60rem;
            font-weight: 800;
            color: #0f172a;
            text-align: left;
        }

        .paper-table td {
            padding: 0.75rem;
            font-size: 0.70rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 500;
        }

        .paper-table tr:last-child td {
            border-bottom: none;
        }

        .disclaimer-box {
            background-color: #f8fafc;
            border-left: 3px solid #2563eb;
            padding: 1rem 1.5rem;
            font-size: 0.65rem;
            color: #475569;
            font-style: italic;
            line-height: 1.6;
            margin-top: auto;
            margin-bottom: 2rem;
        }

        .paper-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.55rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 991px) {
            .report-sidebar {
                max-width: 100%;
                margin-bottom: 2rem;
            }

            .paper-canvas {
                padding: 1.5rem;
                min-height: 500px;
            }

            .paper-table {
                min-width: 600px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 pb-5 pt-3" style="max-width: 1200px;">

        <!-- Header Title -->
        <div class="mb-4">
            <h2 class="fw-bolder mb-1" style="color: #0f172a; font-size: 1.8rem; letter-spacing: -0.5px;">Buat Laporan</h2>
            <p class="text-slate-500 mb-0 fw-medium" style="font-size: 0.95rem;">
                Konfigurasikan dan ekspor data aset institusi untuk tinjauan resmi.
            </p>
        </div>

        <div class="report-layout">
            <!-- Sidebar Controls -->
            <div class="report-sidebar">
                <form action="{{ route('reports.export') }}" method="GET" id="reportForm"
                    onsubmit="return validateAndSubmit()">
                    <!-- Hidden inputs -->
                    <input type="hidden" name="sumber_dana" value="Semua">
                    <input type="hidden" name="jenis_laporan" id="inputJenisLaporan" value="aset_aktif">
                    <input type="hidden" name="periode" id="inputPeriode" value="tahunan">
                    <input type="hidden" name="format" id="inputFormat" value="pdf">

                    <!-- TIME PERIOD Section -->
                    <div class="section-title">
                        <i class="bi bi-calendar4 me-2 fs-6"></i> PERIODE LAPORAN
                    </div>

                    <!-- Yearly -->
                    <div class="option-card active" id="card-tahunan" onclick="setPeriode('tahunan', this)">
                        <div class="radio-circle"></div>
                        <div class="w-100">
                            <div>Laporan Tahunan</div>
                            <div class="period-select" id="wrap-tahun">
                                <select name="tahun" onclick="event.stopPropagation()">
                                    @php $tahunSekarang = date('Y'); @endphp
                                    @for($t = $tahunSekarang; $t >= 2020; $t--)
                                        <option value="{{ $t }}" {{ $t == $tahunSekarang ? 'selected' : '' }}>{{ $t }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly -->
                    <div class="option-card" id="card-bulanan" onclick="setPeriode('bulanan', this)">
                        <div class="radio-circle"></div>
                        <div class="w-100">
                            <div>Laporan Bulanan</div>
                            <div class="period-select" id="wrap-bulanan" style="display:none;">
                                <select name="bulan" onclick="event.stopPropagation()">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="tahun_bulanan" id="tahunBulanan" onclick="event.stopPropagation()">
                                    @for($t = $tahunSekarang; $t >= 2020; $t--)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Custom -->
                    <div class="option-card" id="card-kustom" onclick="setPeriode('kustom', this)">
                        <div class="radio-circle"></div>
                        <div class="w-100">
                            <div>Pilih Rentang Tanggal</div>
                            <div class="date-inputs" id="wrap-kustom" style="display:none;">
                                <input type="date" name="start_date" id="startDate" onclick="event.stopPropagation()">
                                <input type="date" name="end_date" id="endDate" onclick="event.stopPropagation()">
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-200 my-4">

                    <!-- DATA INCLUSION Section -->
                    <div class="section-title">
                        <i class="bi bi-sliders me-2 fs-6"></i> DATA YANG DISERTAKAN
                    </div>

                    <div class="option-card checked" id="chk-aset_aktif" onclick="toggleDataInclusion('aset_aktif', this)">
                        <div class="check-square"></div>
                        <span style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                            Aset Aktif
                            <i class="bi bi-inbox-fill text-success fs-5"></i>
                        </span>
                    </div>

                    <div class="option-card" id="chk-pemeliharaan" onclick="toggleDataInclusion('pemeliharaan', this)">
                        <div class="check-square"></div>
                        <span style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                            Catatan Pemeliharaan
                            <i class="bi bi-wrench text-primary fs-5"></i>
                        </span>
                    </div>

                    <div class="option-card" id="chk-aset_lepas" onclick="toggleDataInclusion('aset_lepas', this)">
                        <div class="check-square"></div>
                        <span style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                            Aset Dilepas
                            <i class="bi bi-recycle text-danger fs-5"></i>
                        </span>
                    </div>


                    <!-- Depreciation option hidden per user request -->

                    <div class="alert-no-selection" id="alertNoSelection">
                        <i class="bi bi-exclamation-triangle me-1"></i> Pilih minimal satu jenis data laporan.
                    </div>

                    <hr class="border-slate-200 my-4">

                    <!-- EXPORT FORMAT Section -->
                    <div class="section-title">
                        <i class="bi bi-file-earmark-check me-2 fs-6"></i> FORMAT EKSPOR
                    </div>

                    <div class="d-flex gap-2">
                        <div class="format-btn active" id="btnPdf" onclick="setFormat('pdf')">
                            <i class="bi bi-file-earmark-pdf-fill fs-5"></i> PDF
                        </div>
                        <div class="format-btn" id="btnExcel" onclick="setFormat('excel')">
                            <i class="bi bi-file-earmark-excel-fill fs-5"></i> Excel
                        </div>
                    </div>

                    <button type="submit" class="btn btn-generate">
                        <i class="bi bi-download"></i> Buat Laporan
                    </button>
                </form>
            </div>

            <!-- Preview Area -->
            <div class="report-preview-area">
                <div class="preview-header">
                    <div class="preview-title">PRATINJAU DOKUMEN</div>
                    <div class="preview-actions">
                        <div class="action-icon-btn" title="Perbarui Pratinjau" onclick="fetchPreview()"
                            style="cursor:pointer;">
                            <i class="bi bi-arrow-clockwise" id="refreshIcon"></i>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="previewLoading"
                    style="display:none; text-align:center; padding:3rem 0; flex-direction:column; align-items:center;">
                    <div class="spinner-border text-success" role="status" style="width:2rem;height:2rem;"></div>
                    <p class="text-slate-500 small fw-medium mt-3 mb-0">Memuat data laporan...</p>
                </div>

                <!-- Pages Container â€” diisi JS, satu paper-canvas per laporan -->
                <div id="pagesContainer" style="display:flex; flex-direction:column; gap:2rem;">
                    <!-- placeholder saat pertama kali load -->
                    <div class="paper-canvas"
                        style="min-height:500px; display:flex; align-items:center; justify-content:center;">
                        <div class="text-center text-slate-400">
                            <i class="bi bi-file-earmark-text" style="font-size:3rem;"></i>
                            <p class="mt-3 fw-medium small mb-0">Memuat pratinjau...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // â”€â”€â”€ State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        let currentPeriode = 'tahunan';
        const selectedInclusions = new Set(['aset_aktif']);
        let previewDebounce = null;
        const VERIF_CODE = '{{ substr(md5(date("Y-m-d")), 0, 24) }}';
        const TODAY_STR = '{{ date("d M Y") }}';
        const YEAR_STR = '{{ date("Y") }}';

        // â”€â”€â”€ Paper header HTML (shared) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function paperHeader(periodLabel, sumberDana, pageNum, totalPages, repTitle) {
            return `
            <div class="paper-top">
                <div class="paper-logo">
                    <h2>SDIT Dar El Iman 2</h2>
                    <span>MANAJEMEN &amp; LOGISTIK ASET</span>
                    <p>Padang, Sumatera Barat, Indonesia</p>
                </div>
                <div class="paper-meta">
                    <p class="meta-tag">LAPORAN RESMI</p>
                    <h3>#REP-${YEAR_STR}-${String(pageNum).padStart(3, '0')}</h3>
                    <p class="meta-date">Tanggal: ${TODAY_STR}</p>
                </div>
            </div>
            <div style="background:#ecfdf5;border-radius:6px;padding:0.6rem 1rem;margin-bottom:1.5rem;display:flex;gap:1.5rem;font-size:0.68rem;font-weight:700;color:#065f46;">
                <span><i class="bi bi-calendar3 me-1"></i>${periodLabel || 'Semua Periode'}</span>
                <span><i class="bi bi-funnel me-1"></i>${(sumberDana && sumberDana !== 'Semua') ? sumberDana : 'Semua Sumber Dana'}</span>
                <span style="margin-left:auto;opacity:0.7;">${repTitle}</span>
            </div>`;
        }

        function paperFooter(pageNum, totalPages) {
            return `
            <div class="disclaimer-box">
                Laporan ini adalah dokumen resmi yang dibuat oleh platform Sistem Manajemen Aset SDIT Dar El Iman 2.
                Semua penilaian berdasarkan data aktual dalam sistem. Setiap ketidaksesuaian harap dilaporkan kepada Departemen Logistik dalam 5 hari kerja.
            </div>
            <div class="paper-footer">
                <span>Kode Verifikasi: ${VERIF_CODE}</span>
                <span>Halaman ${pageNum} dari ${totalPages}</span>
            </div>`;
        }

        // â”€â”€â”€ Number formatting â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function fmt(num) {
            if (!num && num !== 0) return 'â€”';
            const n = parseFloat(num);
            if (n >= 1e9) return 'Rp ' + (n / 1e9).toFixed(1) + 'M';
            if (n >= 1e6) return 'Rp ' + (n / 1e6).toFixed(1) + 'Jt';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
        }
        function fmtFull(num) {
            if (!num && num !== 0) return 'Rp 0';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(num));
        }

        // â”€â”€â”€ Build page HTML for Aset Aktif â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function buildPageAsetAktif(data, periodLabel, sumberDana, pageNum, totalPages) {
            const d = data.aset_aktif;

            // Summary boxes
            const summaryHTML = `
                <div class="summary-box"><label>TOTAL ASET AKTIF</label><div class="val-dark">${d.total}</div></div>
                <div class="summary-box"><label>TOTAL NILAI (IDR)</label><div class="val-blue">${fmt(d.total_nilai)}</div></div>
                <div class="summary-box"><label>NILAI BUKU</label><div class="val-text">${fmt(d.total_nilai_buku)}</div></div>`;

            // Category table rows
            const catRows = d.kategoris.length === 0
                ? '<tr><td colspan="4" style="text-align:center;color:#94a3b8;">Tidak ada data untuk periode ini</td></tr>'
                : d.kategoris.map(c => `<tr><td>${c.kategori}</td><td>${c.jumlah}</td><td>${c.kondisi}</td><td class="text-end">${fmtFull(c.nilai)}</td></tr>`).join('');

            // Sample rows
            let sampleSection = '';
            if (d.sample_rows.length > 0) {
                const sampleRows = d.sample_rows.map(r =>
                    `<tr><td>${r.nama}</td><td style="font-size:0.60rem;">${r.kode}</td><td>${r.kategori}</td><td>${r.kondisi}</td><td class="text-end">${fmtFull(r.nilai)}</td></tr>`
                ).join('');
                sampleSection = `
                    <div style="font-size:0.60rem;font-weight:800;color:#64748b;letter-spacing:0.5px;margin-bottom:0.4rem;">â†³ ${d.total > 5 ? '5 DATA PERTAMA (dari ' + d.total + ')' : 'DAFTAR ASET'}</div>
                    <div class="table-responsive-wrapper">
                        <table class="paper-table">
                            <thead><tr><th>NAMA ASET</th><th>KODE</th><th>KATEGORI</th><th>KONDISI</th><th class="text-end">NILAI</th></tr></thead>
                            <tbody>${sampleRows}</tbody>
                        </table>
                    </div>`;
            }

            return `<div class="paper-canvas">
                ${paperHeader(periodLabel, sumberDana, pageNum, totalPages, 'DAFTAR ASET AKTIF')}
                <div class="section-ribbon">RINGKASAN EKSEKUTIF</div>
                <div class="summary-grid">${summaryHTML}</div>
                <div class="section-ribbon">RINCIAN PER KATEGORI</div>
                <div class="table-responsive-wrapper">
                    <table class="paper-table">
                        <thead><tr><th>KATEGORI</th><th>JUMLAH</th><th>KONDISI</th><th class="text-end">NILAI PEROLEHAN</th></tr></thead>
                        <tbody>${catRows}</tbody>
                    </table>
                </div>
                ${sampleSection}
                ${paperFooter(pageNum, totalPages)}
            </div>`;
        }

        // â”€â”€â”€ Build page HTML for Pemeliharaan â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function buildPagePemeliharaan(data, periodLabel, sumberDana, pageNum, totalPages) {
            const d = data.pemeliharaan;

            const summaryHTML = `
                <div class="summary-box"><label>TOTAL CATATAN SERVIS</label><div class="val-dark">${d.total}</div></div>
                <div class="summary-box"><label>TOTAL BIAYA SERVIS</label><div class="val-blue">${fmt(d.total_biaya)}</div></div>`;

            const rows = d.sample_rows.length === 0
                ? '<tr><td colspan="4" style="text-align:center;color:#94a3b8;">Tidak ada data untuk periode ini</td></tr>'
                : d.sample_rows.map(r =>
                    `<tr><td>${r.aset}</td><td>${r.tanggal}</td><td>${r.deskripsi}</td><td class="text-end">${fmtFull(r.biaya)}</td></tr>`
                ).join('');

            return `<div class="paper-canvas">
                ${paperHeader(periodLabel, sumberDana, pageNum, totalPages, 'CATATAN PEMELIHARAAN')}
                <div class="section-ribbon">RINGKASAN EKSEKUTIF</div>
                <div class="summary-grid">${summaryHTML}</div>
                <div class="section-ribbon">LOG SERVIS ${d.total > 5 ? '(5 DATA TERBARU DARI ' + d.total + ')' : ''}</div>
                <div class="table-responsive-wrapper">
                    <table class="paper-table">
                        <thead><tr><th>ASET</th><th>TANGGAL</th><th>DESKRIPSI</th><th class="text-end">BIAYA</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                ${paperFooter(pageNum, totalPages)}
            </div>`;
        }

        // â”€â”€â”€ Build page HTML for Aset Dilepas â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function buildPageAsetLepas(data, periodLabel, sumberDana, pageNum, totalPages) {
            const d = data.aset_lepas;

            const summaryHTML = `
                <div class="summary-box"><label>TOTAL DILEPAS</label><div class="val-dark">${d.total}</div></div>
                <div class="summary-box"><label>TOTAL NILAI JUAL</label><div class="val-blue">${fmt(d.total_harga_jual)}</div></div>`;

            const rows = d.sample_rows.length === 0
                ? '<tr><td colspan="4" style="text-align:center;color:#94a3b8;">Tidak ada data untuk periode ini</td></tr>'
                : d.sample_rows.map(r =>
                    `<tr><td>${r.nama}</td><td>${r.tgl_keluar}</td><td>${r.jenis}</td><td class="text-end">${fmtFull(r.harga_jual)}</td></tr>`
                ).join('');

            return `<div class="paper-canvas">
                ${paperHeader(periodLabel, sumberDana, pageNum, totalPages, 'ASET DILEPAS')}
                <div class="section-ribbon">RINGKASAN EKSEKUTIF</div>
                <div class="summary-grid">${summaryHTML}</div>
                <div class="section-ribbon">DAFTAR ASET YANG DILEPAS ${d.total > 5 ? '(5 DATA DARI ' + d.total + ')' : ''}</div>
                <div class="table-responsive-wrapper">
                    <table class="paper-table">
                        <thead><tr><th>NAMA ASET</th><th>TGL DILEPAS</th><th>JENIS PELEPASAN</th><th class="text-end">NILAI JUAL</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                ${paperFooter(pageNum, totalPages)}
            </div>`;
        }

        // ─── Build page HTML for Total Aset ──────────────────────────
        function buildPageTotalAset(data, periodLabel, sumberDana, pageNum, totalPages) {
            const d = data.total_aset;

            const summaryHTML = `
                <div class="summary-box"><label>TOTAL ASET</label><div class="val-dark">${d.total}</div></div>
                <div class="summary-box"><label>TOTAL NILAI (IDR)</label><div class="val-blue">${fmt(d.total_nilai)}</div></div>
                <div class="summary-box"><label>NILAI BUKU</label><div class="val-text">${fmt(d.total_nilai_buku)}</div></div>`;

            const catRows = d.kategoris.length === 0
                ? '<tr><td colspan="3" style="text-align:center;color:#94a3b8;">Tidak ada data untuk periode ini</td></tr>'
                : d.kategoris.map(c => `<tr><td>${c.kategori}</td><td>${c.jumlah}</td><td class="text-end">${fmtFull(c.nilai)}</td></tr>`).join('');

            return `<div class="paper-canvas">
                ${paperHeader(periodLabel, sumberDana, pageNum, totalPages, 'LAPORAN TOTAL ASET (PERIODE TERPILIH)')}
                <div class="section-ribbon">RINGKASAN EKSEKUTIF</div>
                <div class="summary-grid">${summaryHTML}</div>
                <div class="section-ribbon">RINCIAN PER KATEGORI</div>
                <div class="table-responsive-wrapper">
                    <table class="paper-table">
                        <thead><tr><th>KATEGORI</th><th>JUMLAH</th><th class="text-end">NILAI PEROLEHAN</th></tr></thead>
                        <tbody>${catRows}</tbody>
                    </table>
                </div>
                ${paperFooter(pageNum, totalPages)}
            </div>`;
        }

        // ─── Build page HTML for Penyusutan ──────────────────────────
        function buildPagePenyusutanAset(data, periodLabel, sumberDana, pageNum, totalPages) {
            const d = data.penyusutan_aset;

            const summaryHTML = `
                <div class="summary-box"><label>PENYUSUTAN PERIODE INI</label><div class="val-dark">${fmt(d.total_penyusutan_periode)}</div></div>
                <div class="summary-box"><label>AKUMULASI S.D. AKHIR PERIODE</label><div class="val-blue">${fmt(d.total_akumulasi)}</div></div>
                <div class="summary-box"><label>TOTAL NILAI BUKU</label><div class="val-text">${fmt(d.total_nilai_buku)}</div></div>`;

            let sampleSection = '';
            if (d.sample_rows.length > 0) {
                const sampleRows = d.sample_rows.map(r =>
                    `<tr><td>${r.nama}</td><td class="text-end">${fmtFull(r.harga_perolehan)}</td><td class="text-end fw-bold text-danger">${fmtFull(r.penyusutan_periode)}</td><td class="text-end">${fmtFull(r.akumulasi)}</td><td class="text-end">${fmtFull(r.nilai_buku)}</td></tr>`
                ).join('');
                sampleSection = `
                    <div class="section-ribbon">SAMPEL PENYUSUTAN ASET ${d.total > 5 ? '(5 DATA DARI ' + d.total + ')' : ''}</div>
                    <div class="table-responsive-wrapper">
                        <table class="paper-table">
                            <thead><tr><th>NAMA ASET</th><th class="text-end">HARGA PEROLEHAN</th><th class="text-end">PENYUSUTAN PERIODE INI</th><th class="text-end">AKUMULASI S.D PERIODE</th><th class="text-end">NILAI BUKU</th></tr></thead>
                            <tbody>${sampleRows}</tbody>
                        </table>
                    </div>`;
            } else {
                sampleSection = `<div class="text-center text-slate-400 mt-4">Tidak ada data penyusutan untuk periode ini.</div>`;
            }

            return `<div class="paper-canvas">
                ${paperHeader(periodLabel, sumberDana, pageNum, totalPages, 'LAPORAN PENYUSUTAN ASET')}
                <div class="section-ribbon">RINGKASAN EKSEKUTIF</div>
                <div class="summary-grid">${summaryHTML}</div>
                ${sampleSection}
                ${paperFooter(pageNum, totalPages)}
            </div>`;
        }

        // â”€â”€â”€ Periode â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function setPeriode(val, elem) {
            document.querySelectorAll('#card-tahunan, #card-bulanan, #card-kustom').forEach(el => el.classList.remove('active'));
            document.getElementById('wrap-bulanan').style.display = 'none';
            document.getElementById('wrap-kustom').style.display = 'none';
            document.getElementById('wrap-tahun').style.display = 'flex';

            elem.classList.add('active');
            currentPeriode = val;
            document.getElementById('inputPeriode').value = val;

            if (val === 'bulanan') {
                document.getElementById('wrap-tahun').style.display = 'none';
                document.getElementById('wrap-bulanan').style.display = 'flex';
            } else if (val === 'kustom') {
                document.getElementById('wrap-tahun').style.display = 'none';
                document.getElementById('wrap-kustom').style.display = 'flex';
            }
            schedulePreview();
        }

        // â”€â”€â”€ Inclusions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function toggleDataInclusion(val, elem) {
            if (selectedInclusions.has(val)) {
                if (selectedInclusions.size > 1) { selectedInclusions.delete(val); elem.classList.remove('checked'); }
            } else {
                selectedInclusions.add(val); elem.classList.add('checked');
            }
            updateJenisLaporan();
            schedulePreview();
        }

        function updateJenisLaporan() {
            let jenis = 'aset_aktif';
            if (selectedInclusions.size >= 2) jenis = 'keseluruhan';
            else if (selectedInclusions.size === 1) jenis = [...selectedInclusions][0];
            document.getElementById('inputJenisLaporan').value = jenis;

            let incInput = document.getElementById('inputInclusions');
            if (!incInput) {
                incInput = document.createElement('input');
                incInput.type = 'hidden';
                incInput.name = 'inclusions';
                incInput.id = 'inputInclusions';
                document.getElementById('reportForm').appendChild(incInput);
            }
            incInput.value = [...selectedInclusions].join(',');
        }

        // â”€â”€â”€ Format â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function setFormat(val) {
            document.getElementById('btnPdf').classList.remove('active');
            document.getElementById('btnExcel').classList.remove('active');
            if (val === 'pdf') document.getElementById('btnPdf').classList.add('active');
            if (val === 'excel') document.getElementById('btnExcel').classList.add('active');
            document.getElementById('inputFormat').value = val;
        }

        // â”€â”€â”€ Submit validation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function validateAndSubmit() {
            if (selectedInclusions.size === 0) {
                document.getElementById('alertNoSelection').style.display = 'block';
                return false;
            }
            document.getElementById('alertNoSelection').style.display = 'none';
            if (currentPeriode === 'bulanan') {
                const tahunBulanan = document.getElementById('tahunBulanan').value;
                let inp = document.querySelector('input[name="tahun"]');
                if (!inp) { inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'tahun'; document.getElementById('reportForm').appendChild(inp); }
                inp.value = tahunBulanan;
            }
            if (currentPeriode === 'kustom') {
                const start = document.getElementById('startDate').value;
                const end = document.getElementById('endDate').value;
                if (!start || !end) { alert('Harap isi tanggal awal dan tanggal akhir untuk Rentang Tanggal Kustom.'); return false; }
            }
            return true;
        }

        // â”€â”€â”€ Preview schedule & fetch â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function schedulePreview() {
            clearTimeout(previewDebounce);
            previewDebounce = setTimeout(fetchPreview, 450);
        }

        function fetchPreview() {
            const params = new URLSearchParams();
            params.set('periode', currentPeriode);
            if (currentPeriode === 'tahunan') {
                const el = document.querySelector('select[name="tahun"]');
                if (el) params.set('tahun', el.value);
            } else if (currentPeriode === 'bulanan') {
                const bEl = document.querySelector('select[name="bulan"]');
                const tEl = document.getElementById('tahunBulanan');
                if (bEl) params.set('bulan', bEl.value);
                if (tEl) params.set('tahun', tEl.value);
            } else if (currentPeriode === 'kustom') {
                const sd = document.getElementById('startDate').value;
                const ed = document.getElementById('endDate').value;
                if (sd) params.set('start_date', sd);
                if (ed) params.set('end_date', ed);
            }
            params.set('inclusions', [...selectedInclusions].join(','));
            params.set('sumber_dana', document.querySelector('input[name="sumber_dana"]').value || 'Semua');

            // Show loading
            document.getElementById('previewLoading').style.display = 'flex';
            document.getElementById('pagesContainer').style.opacity = '0.3';
            document.getElementById('refreshIcon').classList.add('spin');

            fetch(`{{ route('reports.preview') }}?${params.toString()}`)
                .then(r => r.json())
                .then(data => renderPreview(data))
                .catch(err => console.error('Preview error:', err))
                .finally(() => {
                    document.getElementById('previewLoading').style.display = 'none';
                    document.getElementById('pagesContainer').style.opacity = '1';
                    document.getElementById('refreshIcon').classList.remove('spin');
                });
        }

        function renderPreview(data) {
            const periodLabel = data.label_periode;
            const sumberDana = data.sumber_dana;

            // Determine order of pages from inclusions
            const ORDER = ['aset_aktif', 'total_aset', 'penyusutan_aset', 'pemeliharaan', 'aset_lepas'];
            const activePages = ORDER.filter(k => data[k] !== undefined);
            const totalPages = activePages.length;

            let pagesHTML = '';
            activePages.forEach((key, idx) => {
                const pageNum = idx + 1;
                if (key === 'aset_aktif') pagesHTML += buildPageAsetAktif(data, periodLabel, sumberDana, pageNum, totalPages);
                else if (key === 'total_aset') pagesHTML += buildPageTotalAset(data, periodLabel, sumberDana, pageNum, totalPages);
                else if (key === 'penyusutan_aset') pagesHTML += buildPagePenyusutanAset(data, periodLabel, sumberDana, pageNum, totalPages);
                else if (key === 'pemeliharaan') pagesHTML += buildPagePemeliharaan(data, periodLabel, sumberDana, pageNum, totalPages);
                else if (key === 'aset_lepas') pagesHTML += buildPageAsetLepas(data, periodLabel, sumberDana, pageNum, totalPages);
            });

            document.getElementById('pagesContainer').innerHTML = pagesHTML || `
                <div class="paper-canvas" style="min-height:400px;display:flex;align-items:center;justify-content:center;">
                    <div class="text-center text-slate-400">
                        <i class="bi bi-file-earmark-x" style="font-size:3rem;"></i>
                        <p class="mt-3 fw-medium small mb-0">Tidak ada data laporan untuk ditampilkan.</p>
                    </div>
                </div>`;
        }

        // â”€â”€â”€ Init â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        document.addEventListener('DOMContentLoaded', function () {
            updateJenisLaporan();
            fetchPreview();

            document.querySelectorAll('select[name="tahun"], select[name="bulan"], #tahunBulanan').forEach(el => {
                el.addEventListener('change', schedulePreview);
            });
            document.getElementById('startDate').addEventListener('change', schedulePreview);
            document.getElementById('endDate').addEventListener('change', schedulePreview);
        });
    </script>
    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .spin {
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }

        #previewLoading {
            justify-content: center;
            align-items: center;
        }

        .paper-canvas+.paper-canvas {
            border-top: 3px dashed #e2e8f0;
        }
    </style>
@endpush