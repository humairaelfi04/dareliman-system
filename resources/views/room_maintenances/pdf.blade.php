<!DOCTYPE html>
<html>

<head>
    <title>KARTU PEMELIHARAAN SARANA RUANGAN</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a5c3a;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            height: 75px;
            width: auto;
        }

        .school-name {
            font-size: 17px;
            font-weight: bold;
            margin: 0;
            color: #1a5c3a;
            text-align: center;
        }

        .school-address {
            font-size: 11px;
            margin: 4px 0 0 0;
            text-align: center;
            color: #333;
        }

        .contact {
            font-size: 10px;
            margin: 3px 0 0 0;
            text-align: center;
            color: #555;
        }

        .report-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            margin-top: 15px;
        }

        .report-period {
            text-align: center;
            font-size: 11px;
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #333;
        }

        th {
            padding: 8px 5px;
            background-color: #d1fae5;
            text-align: center;
            font-size: 10px;
            color: #065f46;
        }

        td {
            padding: 6px;
            font-size: 10px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .signature {
            width: 250px;
            float: right;
            text-align: center;
            margin-top: 30px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 70px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td width="80px">
                @php $logoPath = public_path('images/logo-sdit.jpg'); @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo"
                        alt="Logo SDIT">
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
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoYayasanPath)) }}"
                        style="height: 50px; width: auto; object-fit: contain;" alt="Logo Yayasan">
                @endif
            </td>
        </tr>
    </table>

    <div class="report-title">
        KARTU PEMELIHARAAN SARANA RUANGAN
    </div>
    <div class="report-period" style="margin-bottom: 10px;">
        <strong>RUANG: {{ mb_strtoupper($roomMaintenance->ruangan_name) }}</strong><br>
        TAHUN PELAJARAN {{ $roomMaintenance->tahun }}/{{ $roomMaintenance->tahun + 1 }} - Smt
        {{ $roomMaintenance->semester == 1 ? 'Ganjil' : 'Genap' }}<br>
        Penanggung Jawab: {{ $roomMaintenance->penanggung_jawab }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Sarana/Perabot</th>
                <th width="10%">Bulan</th>
                <th width="15%">Kondisi</th>
                <th width="15%">Status Keberadaan</th>
                <th width="42%">Catatan Temuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roomMaintenance->details as $index => $detail)
                @php
                    $months = $roomMaintenance->semester == 1 ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
                    $isDeleted = !$detail->asset;
                    $assetName = $isDeleted ? 'Aset Dihapus' : $detail->asset->nama_aset;
                @endphp
                <tr>
                    <td rowspan="6" class="text-center">{{ $index + 1 }}</td>
                    <td rowspan="6" class="text-left font-weight-bold">
                        <strong>{{ $assetName }}</strong>
                        @if(!$isDeleted)<br><span
                        style="color:#555; font-size:9px;">{{ $detail->asset->merek_aset }}</span>@endif
                    </td>
                    <td class="text-center">{{ $months[0] }}</td>
                    <td class="text-center">{{ $detail->kondisi_b1 ?? '-' }}</td>
                    <td class="text-center">{{ $detail->status_temuan_b1 ?? '-' }}</td>
                    <td class="text-left">{{ $detail->catatan_temuan_b1 ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $months[1] }}</td>
                    <td class="text-center">{{ $detail->kondisi_b2 ?? '-' }}</td>
                    <td class="text-center">{{ $detail->status_temuan_b2 ?? '-' }}</td>
                    <td class="text-left">{{ $detail->catatan_temuan_b2 ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $months[2] }}</td>
                    <td class="text-center">{{ $detail->kondisi_b3 ?? '-' }}</td>
                    <td class="text-center">{{ $detail->status_temuan_b3 ?? '-' }}</td>
                    <td class="text-left">{{ $detail->catatan_temuan_b3 ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $months[3] }}</td>
                    <td class="text-center">{{ $detail->kondisi_b4 ?? '-' }}</td>
                    <td class="text-center">{{ $detail->status_temuan_b4 ?? '-' }}</td>
                    <td class="text-left">{{ $detail->catatan_temuan_b4 ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $months[4] }}</td>
                    <td class="text-center">{{ $detail->kondisi_b5 ?? '-' }}</td>
                    <td class="text-center">{{ $detail->status_temuan_b5 ?? '-' }}</td>
                    <td class="text-left">{{ $detail->catatan_temuan_b5 ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">{{ $months[5] }}</td>
                    <td class="text-center">{{ $detail->kondisi_b6 ?? '-' }}</td>
                    <td class="text-center">{{ $detail->status_temuan_b6 ?? '-' }}</td>
                    <td class="text-left">{{ $detail->catatan_temuan_b6 ?? '-' }}</td>
                </tr>
            @endforeach
            @if(count($roomMaintenance->details) == 0)
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data aset pada KIR ini.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature">
        <p>Padang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p>Penanggung Jawab,</p>
        <div class="signature-name">{{ $roomMaintenance->penanggung_jawab }}</div>
    </div>
</body>

</html>