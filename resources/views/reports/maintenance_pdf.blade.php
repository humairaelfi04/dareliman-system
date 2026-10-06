<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Laporan Pemeliharaan Aset' }} SDIT 2 Dar El-Iman</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header-table { width: 100%; border: none !important; border-bottom: 2px solid #1a5c3a !important; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header-table tr { border: none !important; }
        .header-table td { border: none !important; vertical-align: middle; padding: 5px; }
        .header-table img.logo { height: 75px; width: 75px; display: block; }
        .school-name { font-size: 17px; font-weight: bold; margin: 0; color: #1a5c3a; text-align: center; }
        .school-address { font-size: 11px; margin: 4px 0 0 0; text-align: center; color: #333; }
        .contact { font-size: 10px; margin: 3px 0 0 0; text-align: center; color: #555; }
        .report-title { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; margin-top: 15px; }
        .report-period { text-align: center; font-size: 11px; margin-bottom: 20px; color: #555; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table, table.data-table th, table.data-table td { border: 1px solid #333; }
        table.data-table th { padding: 8px 5px; background-color: #d1fae5; text-align: center; font-size: 10px; color: #065f46; }
        table.data-table td { padding: 6px; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .signature { width: 250px; float: right; text-align: center; margin-top: 30px; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 70px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="80px">
                @php $logoPath = public_path('images/logo-sdit.jpg'); @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo SDIT">
                @endif
            </td>
            <td>
                <h1 class="school-name">SDIT 2 Dar El-Iman Padang</h1>
                <p class="school-address">Jln. Gunung Juaro, Kelurahan Surau Gadang, Kecamatan Nanggalo, Padang</p>
                <p class="contact">Yayasan Dar El-Iman &nbsp;|&nbsp; Sistem Informasi Manajemen Aset</p>
            </td>
            <td width="160px" style="text-align: right; vertical-align: middle;">
                @php $logoYayasanPath = public_path('images/logo-yayasan.png'); @endphp
                @if(file_exists($logoYayasanPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoYayasanPath)) }}" style="height: 50px; width: auto; object-fit: contain;" alt="Logo Yayasan">
                @endif
            </td>
        </tr>
    </table>

    <div class="report-title">
        {{ $title ?? 'Laporan Pemeliharaan & Riwayat Servis Aset' }}
    </div>
    <div class="report-period">
        Periode: {{ str_replace('_', ' ', $labelPeriode ?? '') }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode Aset</th>
                <th width="20%">Nama Aset</th>
                <th width="12%">Tanggal Servis</th>
                <th width="28%">Deskripsi Kerusakan / Servis</th>
                <th width="12%">Biaya Perbaikan (Rp)</th>
                <th width="12%">Jadwal Berikutnya</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBiaya = 0; @endphp
            @forelse($data as $index => $log)
            @php $totalBiaya += $log->biaya_perbaikan; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $log->asset->kode_aset ?? '-' }}</td>
                <td>{{ $log->asset->nama_aset ?? '-' }}</td>
                <td class="text-center">{{ $log->tanggal_servis ? \Carbon\Carbon::parse($log->tanggal_servis)->format('d/m/Y') : '-' }}</td>
                <td>{{ $log->deskripsi_kerusakan }}</td>
                <td class="text-right">Rp{{ number_format($log->biaya_perbaikan, 0, ',', '.') }}</td>
                <td class="text-center">{{ $log->next_service_date ? \Carbon\Carbon::parse($log->next_service_date)->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 15px;">Tidak ada data pemeliharaan pada rentang periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right" style="font-weight: bold; background-color: #f2f2f2;">TOTAL BIAYA SERVIS</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totalBiaya, 0, ',', '.') }}</td>
                <td style="background-color: #f2f2f2;"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="signature">
        <p>Padang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p>Mengetahui,</p>
        <div class="signature-name">{{ auth()->check() ? auth()->user()->name : 'Administrator' }}</div>
    </div>
</body>
</html>
