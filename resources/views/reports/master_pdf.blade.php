<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 20px; }
        .header-table { width: 100%; border-bottom: 2px solid #1a5c3a; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header-table td { border: none; vertical-align: middle; }
        .logo { height: 70px; width: auto; }
        .title { font-size: 16px; font-weight: bold; margin: 0; color: #1a5c3a; text-align: center; }
        .subtitle { font-size: 11px; color: #333; text-align: center; margin: 4px 0 0 0; }
        .report-meta { font-size: 10px; color: #555; text-align: center; margin: 3px 0 0 0; }
        .section-title { font-size: 12px; font-weight: bold; margin-top: 25px; margin-bottom: 10px; background-color: #d1fae5; padding: 5px; border-left: 3px solid #065f46; color: #065f46;}
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #f8f9fa; }
        .page-break { page-break-before: always; }
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
                <h1 class="title">SDIT 2 Dar El-Iman Padang</h1>
                <p class="subtitle">{{ $title }}</p>
                <p class="report-meta">Periode: {{ $labelPeriode }} &nbsp;|&nbsp; Sumber Dana: {{ $sumberDana }}</p>
            </td>
            <td width="160px" style="text-align: right; vertical-align: middle;">
                @php $logoYayasanPath = public_path('images/logo-yayasan.png'); @endphp
                @if(file_exists($logoYayasanPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoYayasanPath)) }}" style="height: 50px; width: auto; object-fit: contain;" alt="Logo Yayasan">
                @endif
            </td>
        </tr>
    </table>

    @php 
        $sectionCount = 1; 
        $inclusions = $inclusions ?? ['aset_aktif', 'aset_lepas', 'pelepasan_aset', 'pemeliharaan'];
    @endphp

    @if(in_array('aset_aktif', $inclusions))
    <!-- 1. Aset Aktif -->
    <div class="section-title">{{ $sectionCount++ }}. Daftar Aset Aktif</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama Barang/Aset</th>
                <th>Ruangan</th>
                <th>Kondisi</th>
                <th>Sumber Dana</th>
                <th>Tgl Masuk</th>
                <th>Harga Perolehan</th>
                <th>Penyusutan/Thn</th>
                <th>Akumulasi Penyusutan</th>
                <th>Sisa Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalHargaAktif = 0; $totalPenyusutanAktif = 0; $totalNilaiBukuAktif = 0; 
                $i = 1; 
            @endphp
            @forelse($dataAktif as $asset)
                @php 
                    $totalHargaAktif += $asset->harga_perolehan;
                    $totalPenyusutanAktif += $asset->akumulasi_penyusutan;
                    $totalNilaiBukuAktif += $asset->nilai_buku;
                @endphp
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $asset->kode_aset ?? '-' }}</td>
                    <td>{{ $asset->nama_aset }}</td>
                    <td>{{ $asset->location->nama_ruangan ?? '-' }}</td>
                    <td>{{ $asset->kondisi }}</td>
                    <td>{{ $asset->sumber_dana }}</td>
                    <td>{{ $asset->perolehanAset && $asset->perolehanAset->tanggal_masuk ? \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->penyusutan_per_tahun, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->akumulasi_penyusutan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->nilai_buku, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center">Tidak ada aset aktif pada periode ini.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="7" class="text-right">TOTAL ASET AKTIF:</td>
                <td class="text-right">Rp {{ number_format($totalHargaAktif, 0, ',', '.') }}</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalPenyusutanAktif, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalNilaiBukuAktif, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if(in_array('total_aset', $inclusions))
    @if($sectionCount > 1)<div class="page-break"></div>@endif
    <!-- 1A. Total Aset -->
    <div class="section-title">{{ $sectionCount++ }}. Total Aset (Per Akhir Periode Terpilih)</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama Barang/Aset</th>
                <th>Ruangan</th>
                <th>Kondisi</th>
                <th>Sumber Dana</th>
                <th>Harga Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @php $iTotal = 1; @endphp
            @forelse($dataTotal as $asset)
                <tr>
                    <td class="text-center">{{ $iTotal++ }}</td>
                    <td>{{ $asset->kode_aset ?? '-' }}</td>
                    <td>{{ $asset->nama_aset }}</td>
                    <td>{{ $asset->location->nama_ruangan ?? '-' }}</td>
                    <td>{{ $asset->kondisi }}</td>
                    <td>{{ $asset->sumber_dana }}</td>
                    <td class="text-right">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada aset pada periode ini.</td></tr>
            @endforelse
            @if(isset($totalsTotal))
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL ASET:</td>
                <td class="text-right">Rp {{ number_format($totalsTotal['total_harga_perolehan'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    @if(in_array('penyusutan_aset', $inclusions))
    @if($sectionCount > 1)<div class="page-break"></div>@endif
    <!-- 1B. Penyusutan Aset -->
    <div class="section-title">{{ $sectionCount++ }}. Laporan Penyusutan Aset per Periode</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama Barang/Aset</th>
                <th>Harga Perolehan</th>
                <th>Penyusutan Periode Ini</th>
                <th>Akumulasi (s.d Akhir Periode)</th>
                <th>Nilai Buku Sisa</th>
            </tr>
        </thead>
        <tbody>
            @php $iPenyusutan = 1; @endphp
            @forelse($dataPenyusutan as $asset)
                <tr>
                    <td class="text-center">{{ $iPenyusutan++ }}</td>
                    <td>{{ $asset->kode_aset ?? '-' }}</td>
                    <td>{{ $asset->nama_aset }}</td>
                    <td class="text-right">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                    <td class="text-right" style="color:#d32f2f;">Rp {{ number_format($asset->calculated_penyusutan_periode ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->calculated_akumulasi ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->calculated_nilai_buku ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data penyusutan pada periode ini.</td></tr>
            @endforelse
            @if(isset($totalsPenyusutan))
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">Rp {{ number_format($totalsPenyusutan['total_harga_perolehan'], 0, ',', '.') }}</td>
                <td class="text-right" style="color:#d32f2f;">Rp {{ number_format($totalsPenyusutan['total_penyusutan_periode'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalsPenyusutan['total_akumulasi_penyusutan'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalsPenyusutan['total_nilai_buku'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    @if(in_array('aset_lepas', $inclusions) || in_array('pelepasan_aset', $inclusions))
    @if($sectionCount > 1)<div class="page-break"></div>@endif
    <!-- 2. Aset Pelepasan -->
    <div class="section-title">{{ $sectionCount++ }}. Daftar Aset Pelepasan / Keluar</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Aset</th>
                <th>Merek</th>
                <th>Kategori</th>
                <th>Ruangan</th>
                <th>Kondisi</th>
                <th>Diajukan Oleh</th>
                <th>Metode</th>
                <th>Harga Jual</th>
                <th>Harga Perolehan</th>
                <th>Penyusutan/Thn</th>
                <th>Akm. Penyusutan</th>
                <th>Sisa Nilai Buku (Saat Keluar)</th>
                <th>Tgl Masuk</th>
                <th>Tgl Keluar</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalHargaJual = 0; 
                $totalBukuLepas = 0; 
                $totalHargaPerolehanLepas = 0;
                $totalAkumulasiLepas = 0;
                $j = 1; 
            @endphp
            @forelse($dataLepas as $asset)
                @php 
                    $jual = $asset->disposal ? $asset->disposal->harga_jual : 0;
                    $totalHargaJual += $jual;
                    $totalBukuLepas += $asset->nilai_buku;
                    $totalHargaPerolehanLepas += $asset->harga_perolehan;
                    $totalAkumulasiLepas += $asset->akumulasi_penyusutan;
                @endphp
                <tr>
                    <td class="text-center">{{ $j++ }}</td>
                    <td>{{ $asset->kode_aset ?? '-' }}</td>
                    <td>{{ $asset->nama_aset }}</td>
                    <td>{{ $asset->merek_aset ?? '-' }}</td>
                    <td>{{ $asset->category->nama_kategori ?? '-' }}</td>
                    <td>{{ $asset->location->nama_ruangan ?? '-' }}</td>
                    <td>{{ $asset->kondisi }}</td>
                    <td class="text-center">
                        {{ $asset->disposal->requester?->name ?? ($asset->disposal->requester_name ?? '-') }}<br>
                        <span style="font-size: 8px; color: #555;">({{ $asset->disposal->requester?->role ?? ($asset->disposal->requester_role ?? '-') }})</span>
                    </td>
                    <td>{{ $asset->disposal ? $asset->disposal->jenis_transaksi_keluar : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($jual, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->penyusutan_per_tahun, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->akumulasi_penyusutan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($asset->nilai_buku, 0, ',', '.') }}</td>
                    <td>{{ $asset->perolehanAset && $asset->perolehanAset->tanggal_masuk ? \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $asset->disposal && $asset->disposal->tanggal_keluar ? \Carbon\Carbon::parse($asset->disposal->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center">Tidak ada aset yang dilepas pada periode ini.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="9" class="text-right">TOTAL ASET LEPAS:</td>
                <td class="text-right">Rp {{ number_format($totalHargaJual, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalHargaPerolehanLepas, 0, ',', '.') }}</td>
                <td class="text-right">-</td>
                <td class="text-right">Rp {{ number_format($totalAkumulasiLepas, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalBukuLepas, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    @endif

    @if(in_array('pemeliharaan', $inclusions))
    @if($sectionCount > 1)<div class="page-break"></div>@endif
    <!-- 3. Riwayat Servis -->
    <div class="section-title">{{ $sectionCount++ }}. Riwayat Pemeliharaan & Servis Alat</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama Alat</th>
                <th>Tanggal Servis</th>
                <th>Tindakan Pemeliharaan / Keluhan</th>
                <th>Biaya Servis</th>
                <th>Jadwal Berikutnya</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBiayaServis = 0; $k = 1; @endphp
            @forelse($dataServis as $log)
                @php $totalBiayaServis += $log->biaya_perbaikan; @endphp
                <tr>
                    <td class="text-center">{{ $k++ }}</td>
                    <td>{{ $log->asset->kode_aset ?? '-' }}</td>
                    <td>{{ $log->asset->nama_aset ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->tanggal_servis)->format('d/m/Y') }}</td>
                    <td>{{ $log->deskripsi_kerusakan }}</td>
                    <td class="text-right">Rp {{ number_format($log->biaya_perbaikan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $log->next_service_date ? \Carbon\Carbon::parse($log->next_service_date)->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada catatan pemeliharaan pada periode ini.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL BIAYA PEMELIHARAAN:</td>
                <td class="text-right text-danger">Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    @endif

</body>
</html>
