<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Aset SDIT 2 Dar El-Iman</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header-table { width: 100%; border-bottom: 2px solid #1a5c3a; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header-table td { border: none; vertical-align: middle; }
        .logo { height: 75px; width: auto; }
        .school-name { font-size: 17px; font-weight: bold; margin: 0; color: #1a5c3a; text-align: center; }
        .school-address { font-size: 11px; margin: 4px 0 0 0; text-align: center; color: #333; }
        .contact { font-size: 10px; margin: 3px 0 0 0; text-align: center; color: #555; }
        .report-title { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; margin-top: 15px; }
        .report-period { text-align: center; font-size: 11px; margin-bottom: 20px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #333; }
        th { padding: 8px 5px; background-color: #d1fae5; text-align: center; font-size: 10px; color: #065f46; }
        td { padding: 6px; font-size: 10px; }
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
        Daftar Inventaris Aset {{ $sumberDana !== 'Semua' ? '(Sumber Dana: ' . $sumberDana . ')' : '(Keluruhan)' }}
    </div>
    <div class="report-period">
        Periode: {{ str_replace('_', ' ', $labelPeriode) }}
    </div>

    <table>
        <thead>
                <th width="3%">No</th>
                <th width="10%">Nama Aset</th>
                <th width="9%">Merek</th>
                <th width="7%">Kategori</th>
                <th width="7%">Ruangan</th>
                <th width="6%">Kondisi</th>
                @if(isset($jenisLaporan) && ($jenisLaporan == 'aset_lepas' || $jenisLaporan == 'pelepasan_aset'))
                <th width="10%">Diajukan Oleh</th>
                <th width="8%">Metode</th>
                <th width="9%">Harga Jual</th>
                @else
                <th width="6%">Status</th>
                @endif
                <th width="9%">Harga Perolehan</th>
                @if(isset($jenisLaporan) && $jenisLaporan == 'penyusutan_aset')
                <th width="9%">Penyusutan Periode Ini</th>
                @endif
                <th width="9%">Akm. Penyusutan</th>
                <th width="8%">Nilai Buku</th>
                @if(!isset($jenisLaporan) || $jenisLaporan != 'penyusutan_aset')
                <th width="8%">Tgl Masuk</th>
                <th width="8%">Tgl Keluar</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
            @php
                // Tentukan status aktif/lepas berdasarkan tanggal keluar
                $isDisposed = $asset->disposal && $asset->disposal->tanggal_keluar;
                $statusText = $isDisposed ? 'Dilepas' : 'Aktif';
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $asset->nama_aset }}</td>
                <td>{{ $asset->merek_aset }}</td>
                <td class="text-center">{{ $asset->category->nama_kategori ?? '-' }}</td>
                <td class="text-center">{{ $asset->location->nama_ruangan ?? '-' }}</td>
                <td class="text-center">{{ $asset->kondisi }}</td>
                @if(isset($jenisLaporan) && ($jenisLaporan == 'aset_lepas' || $jenisLaporan == 'pelepasan_aset'))
                <td class="text-center">
                    {{ $asset->disposal->requester?->name ?? ($asset->disposal->requester_name ?? '-') }}<br>
                    <span style="font-size: 8px; color: #555;">({{ $asset->disposal->requester?->role ?? ($asset->disposal->requester_role ?? '-') }})</span>
                </td>
                <td class="text-center">{{ $asset->disposal ? $asset->disposal->jenis_transaksi_keluar : '-' }}</td>
                <td class="text-right">Rp{{ number_format($asset->disposal ? $asset->disposal->harga_jual : 0, 0, ',', '.') }}</td>
                @else
                <td class="text-center">{{ $statusText }}</td>
                @endif
                <td class="text-right">Rp{{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                @if(isset($jenisLaporan) && $jenisLaporan == 'penyusutan_aset')
                <td class="text-right" style="color: #d32f2f; font-weight: bold;">Rp{{ number_format($asset->calculated_penyusutan_periode ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($asset->calculated_akumulasi ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($asset->calculated_nilai_buku ?? 0, 0, ',', '.') }}</td>
                @else
                <td class="text-right">Rp{{ number_format($asset->akumulasi_penyusutan, 0, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($asset->nilai_buku, 0, ',', '.') }}</td>
                <td class="text-center">{{ $asset->perolehanAset && $asset->perolehanAset->tanggal_masuk ? \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->format('d/m/y') : '-' }}</td>
                <td class="text-center">{{ $isDisposed ? \Carbon\Carbon::parse($asset->disposal->tanggal_keluar)->format('d/m/y') : '-' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center" style="padding: 15px;">Tidak ada data aset pada rentang periode dan filter sumber dana ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if(isset($totals) && $totals)
        <tfoot>
            <tr>
                @if(isset($jenisLaporan) && ($jenisLaporan == 'aset_lepas' || $jenisLaporan == 'pelepasan_aset'))
                <td colspan="8" class="text-right" style="font-weight: bold; background-color: #f2f2f2;">TOTAL KESELURUHAN</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_harga_jual'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_harga_perolehan'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_akumulasi_penyusutan'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_nilai_buku'], 0, ',', '.') }}</td>
                <td colspan="2" style="background-color: #f2f2f2;"></td>
                @elseif(isset($jenisLaporan) && $jenisLaporan == 'penyusutan_aset')
                <td colspan="7" class="text-right" style="font-weight: bold; background-color: #f2f2f2;">TOTAL KESELURUHAN</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_harga_perolehan'], 0, ',', '.') }}</td>
                <td class="text-right" style="color: #d32f2f; font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_penyusutan_periode'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_akumulasi_penyusutan'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_nilai_buku'], 0, ',', '.') }}</td>
                @else
                <td colspan="7" class="text-right" style="font-weight: bold; background-color: #f2f2f2;">TOTAL KESELURUHAN</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_harga_perolehan'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_akumulasi_penyusutan'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; background-color: #f2f2f2;">Rp{{ number_format($totals['total_nilai_buku'], 0, ',', '.') }}</td>
                <td colspan="2" style="background-color: #f2f2f2;"></td>
                @endif
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
