@extends('layouts.app')

@section('title', 'Data Inventaris')

@section('content')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border border-success border-opacity-25 rounded-4 d-flex align-items-center p-4 mb-4"
            role="alert" style="background-color: var(--emerald-50);">
            <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
            </div>
            <div>
                <strong class="d-block text-success mb-1">Berhasil!</strong>
                <span class="text-slate-700">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 25%;"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border border-danger border-opacity-25 rounded-4 d-flex align-items-center p-4 mb-4"
            role="alert" style="background-color: #fef2f2;">
            <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                <i class="bi bi-exclamation-octagon-fill text-danger fs-4"></i>
            </div>
            <div>
                <strong class="d-block text-danger mb-1">Terjadi Kesalahan!</strong>
                <span class="text-slate-700">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 25%;"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden">
        <div
            class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center py-4 px-4 border-bottom border-slate-100">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-soft-emerald p-3 rounded-4 me-3 d-flex justify-content-center align-items-center shadow-sm">
                    <i class="bi bi-inboxes-fill text-emerald fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark letter-spacing-tight">Daftar Aset</h5>
                    <p class="text-slate-500 small mb-0 fw-medium">Pengelolaan seluruh aset SDIT 2 Dar El-Iman</p>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-center">

                <a href="{{ route('assets.create') }}" class="btn btn-primary shadow-sm fw-bold rounded-pill px-4">
                    <i class="bi bi-plus-lg me-2 fw-bolder"></i> Tambah Aset
                </a>
            </div>
        </div>

        <div class="card-body px-4 py-4" style="background-color: #f8fafc;">
            <form action="{{ route('assets.index') }}" method="GET" class="row g-3 align-items-end" id="searchForm">
                <div class="col-12 mb-2">
                    <label class="form-label small fw-bold text-slate-600 mb-2">Pencarian Barang</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white">
                        <span class="input-group-text bg-transparent border-end-0 border-slate-200 ps-3">
                            <i class="bi bi-search text-slate-400" id="searchIcon"></i>
                            <div class="spinner-border spinner-border-sm text-primary d-none" id="searchSpinner" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                        <input type="text" class="form-control border-start-0 border-slate-200 ps-1" name="search" id="searchInput" placeholder="Cari berdasarkan Nama Aset atau Kode Aset..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <label class="form-label small fw-bold text-slate-600 mb-2">Kategori Aset</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white">
                        <span class="input-group-text bg-transparent border-end-0 border-slate-200 ps-3"><i
                                class="bi bi-tags text-slate-400"></i></span>
                        <select class="form-select border-start-0 border-slate-200 ps-1" name="category_id">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <label class="form-label small fw-bold text-slate-600 mb-2">Lokasi Ruangan</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white">
                        <span class="input-group-text bg-transparent border-end-0 border-slate-200 ps-3"><i
                                class="bi bi-geo-alt text-slate-400"></i></span>
                        <select class="form-select border-start-0 border-slate-200 ps-1" name="location_id">
                            <option value="">Semua Ruangan</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4">
                    <label class="form-label small fw-bold text-slate-600 mb-2">Status Umur Pemakaian</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white">
                        <span class="input-group-text bg-transparent border-end-0 border-slate-200 ps-3"><i
                                class="bi bi-hourglass-split text-slate-400"></i></span>
                        <select class="form-select border-start-0 border-slate-200 ps-1" name="status_umur">
                            <option value="">Semua Status</option>
                            <option value="aman" {{ request('status_umur') == 'aman' ? 'selected' : '' }}>Aman (Optimal)
                            </option>
                            <option value="peringatan" {{ request('status_umur') == 'peringatan' ? 'selected' : '' }}>
                                Peringatan Dini</option>
                            <option value="habis" {{ request('status_umur') == 'habis' ? 'selected' : '' }}>Habis Masa Pakai
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <label class="form-label small fw-bold text-slate-600 mb-2">Kondisi Fisik</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white">
                        <span class="input-group-text bg-transparent border-end-0 border-slate-200 ps-3"><i
                                class="bi bi-clipboard-pulse text-slate-400"></i></span>
                        <select class="form-select border-start-0 border-slate-200 ps-1" name="kondisi">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak
                                Ringan</option>
                            <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak
                                Berat</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-8 d-flex gap-2 align-items-end mt-4 mt-xl-0">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm fw-bold"><i
                            class="bi bi-funnel-fill me-2"></i> Terapkan Format</button>
                    <a href="{{ route('assets.index') }}"
                        class="btn btn-light border border-slate-200 shadow-sm text-slate-600 px-3" data-bs-toggle="tooltip"
                        title="Reset Filter"><i class="bi bi-arrow-counterclockwise fs-5"></i></a>
                </div>
            </form>
        </div>

        <div id="assetListContainer">
        <div class="card-body p-0">
            <div class="table-responsive border-0 border-radius-0 mb-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-white text-slate-500">
                        <tr>
                            <th class="text-center ps-4" style="width: 60px;">#</th>
                            <th style="min-width: 250px;">Identitas Aset</th>
                            <th style="min-width: 150px;">Lokasi & Kategori</th>
                            <th>Jalur Dana</th>
                            <th class="text-end">Harga Beli</th>
                            <th class="text-end">Akumulasi Susut</th>
                            <th class="text-end">Nilai Buku</th>
                            <th class="text-center" style="min-width: 120px;">Kondisi</th>
                            <th class="text-center pe-4" style="min-width: 140px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                            <tr>
                                <td class="text-center ps-4 text-slate-400 fw-bold">{{ $assets->firstItem() + $index }}</td>
                                <td class="text-start py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm"
                                            style="width:48px; height:48px;">
                                            <i
                                                class="bi {{ ($asset->category->nama_kategori ?? '') == 'Elektronik' ? 'bi-pc-display' : (($asset->category->nama_kategori ?? '') == 'Kendaraan' ? 'bi-car-front' : 'bi-box-seam') }} fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-800 d-flex align-items-center gap-2">
                                                {{ $asset->nama_aset }}
                                            </div>
                                            <div class="small fw-medium text-slate-500 mt-1 d-flex align-items-center gap-2">
                                                <span
                                                    class="font-monospace bg-slate-50 px-2 py-1 rounded border">{{ $asset->kode_aset ?? 'TANPA-KODE' }}</span>

                                                @if($asset->disposal && $asset->disposal->tanggal_keluar)
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill"><i
                                                            class="bi bi-box-arrow-right me-1"></i> Dilepas</span>
                                                @else
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill"><i
                                                            class="bi bi-check-circle me-1"></i> Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-slate-700">{{ $asset->category->nama_kategori ?? '-' }}</div>
                                    @if($asset->location)
                                        <div class="small text-emerald fw-medium mt-1 d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill me-1"></i>{{ $asset->location->nama_ruangan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @php
                                        $badgeObj = [
                                            'Yayasan' => ['bg' => 'bg-primary bg-opacity-10', 'text' => 'text-primary', 'border' => 'border-primary-subtle'],
                                            'BOS' => ['bg' => 'bg-info bg-opacity-10', 'text' => 'text-info-emphasis', 'border' => 'border-info-subtle'],
                                            'Wakaf' => ['bg' => 'bg-success bg-opacity-10', 'text' => 'text-success', 'border' => 'border-success-subtle'],
                                            'Hibah' => ['bg' => 'bg-warning bg-opacity-10', 'text' => 'text-warning-emphasis', 'border' => 'border-warning-subtle'],
                                        ];
                                        $style = $badgeObj[$asset->sumber_dana] ?? ['bg' => 'bg-secondary bg-opacity-10', 'text' => 'text-secondary', 'border' => 'border-secondary-subtle'];
                                    @endphp
                                    <span
                                        class="badge {{ $style['bg'] }} {{ $style['text'] }} border {{ $style['border'] }} px-3 py-2 shadow-sm rounded-pill fw-bold">
                                        {{ $asset->sumber_dana }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-slate-700 py-3">Rp
                                    {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                                <td class="text-end text-danger fw-bold py-3">
                                    <span
                                        class="bg-danger bg-opacity-10 px-2 py-1 rounded text-danger border border-danger-subtle">
                                        - {{ number_format($asset->akumulasi_penyusutan, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end fw-bolder text-emerald fs-6 py-3">
                                    Rp {{ number_format($asset->nilai_buku, 0, ',', '.') }}
                                </td>
                                <td class="text-center py-3">
                                    <!-- Reusing logic from the model status_umur which returns HTML -->
                                    {!! str_replace('badge', 'badge px-3 py-2 shadow-sm rounded-pill border', $asset->status_umur) !!}
                                    <div class="mt-2 small font-monospace text-muted">{{ $asset->kondisi }}</div>
                                </td>
                                <td class="text-center pe-4 py-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('assets.show', $asset->id) }}"
                                            class="btn btn-sm btn-light border shadow-sm text-primary rounded-circle"
                                            style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;"
                                            data-bs-toggle="tooltip" title="Lihat Detail Utuh">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="{{ route('assets.edit', $asset->id) }}"
                                            class="btn btn-sm btn-light border shadow-sm text-warning rounded-circle"
                                            style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;"
                                            data-bs-toggle="tooltip" title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        @if(!$asset->disposal || !$asset->disposal->tanggal_keluar)
                                            @if($asset->hasPendingDisposalRequest())
                                                <button type="button" class="btn btn-sm btn-secondary border shadow-sm rounded-circle"
                                                    style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;"
                                                    title="Pelepasan Tertunda" disabled>
                                                    <i class="bi bi-hourglass-split"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-light border shadow-sm text-danger rounded-circle"
                                                    style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;"
                                                    title="Pelepasan Aset" data-bs-toggle="modal"
                                                    data-bs-target="#disposeModal{{ $asset->id }}">
                                                    <i class="bi bi-box-arrow-right"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>

                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="d-inline mt-2"
                                        onsubmit="event.preventDefault(); Swal.fire({title: 'Hapus Permanen?', text: 'Tindakan ini bersifat PERMANEN. Anda yakin ingin menghapus data aset ini dari sistem?', icon: 'error', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Hapus Permanen!'}).then((result) => { if (result.isConfirmed) { document.getElementById('form-del-{{ $asset->id }}').submit(); } })" id="form-del-{{ $asset->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-link text-danger p-0 mt-2 text-decoration-none small fw-bold">
                                            <i class="bi bi-trash3-fill me-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center my-4">
                                        <div class="bg-slate-100 p-4 rounded-circle mb-3">
                                            <i class="bi bi-search display-5 text-slate-300"></i>
                                        </div>
                                        <h5 class="fw-bold text-slate-600">Tidak ada data aset ditemukan</h5>
                                        <p class="text-slate-500 max-w-md">Silakan sesuaikan filter pencarian Anda atau klik
                                            tombol "Tambah Aset" untuk memasukkan data inventaris baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <div class="px-4 py-3 bg-white border-top border-slate-100">
                <div class="d-flex align-items-center text-slate-500 small fw-medium">
                    <i class="bi bi-info-circle-fill text-primary ms-1 me-2 fs-5"></i>
                    <span>Modul penyusutan menggunakan <strong>Straight Line Method</strong> sesuai dengan regulasi
                        pembukuan tanpa perhitungan nilai residu di akhir masa manfaat.</span>
                </div>
            </div>
        </div>
    </div>

    {{ $assets->links() }}
    </div>

@endsection

@section('modals')
    <!-- Modals (Placed safely inside content area or yields) -->
    @foreach($assets as $asset)
        @if(!$asset->disposal || !$asset->disposal->tanggal_keluar)
            <!-- Modal Pelepasan -->
            <div class="modal fade" id="disposeModal{{ $asset->id }}" tabindex="-1"
                aria-labelledby="disposeModalLabel{{ $asset->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-start border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-danger pt-4 pb-3 px-4">
                            <h5 class="modal-title fw-bold text-white d-flex align-items-center"
                                id="disposeModalLabel{{ $asset->id }}">
                                <i class="bi bi-box-arrow-right fs-4 me-3"></i> Ajukan Pelepasan Aset
                            </h5>
                            <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('assets.dispose', $asset->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4 bg-slate-50">
                                <div
                                    class="alert bg-white border border-danger-subtle text-slate-700 d-flex align-items-start mb-4 rounded-4 p-4 shadow-sm">
                                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3 flex-shrink-0">
                                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-danger mb-1">Pengajuan Pelepasan</h6>
                                        <p class="mb-0 small fw-medium lh-base">Anda akan mengajukan pelepasan untuk aset
                                            <strong>{{ $asset->nama_aset }} ({{ $asset->kode_aset ?? '-' }})</strong>. Pengajuan ini
                                            membutuhkan persetujuan Kepala Sekolah.</p>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-4 shadow-sm border border-slate-200">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label text-slate-800 fw-bold">Alasan Pelepasan <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control bg-slate-50" name="alasan_pengajuan" rows="3"
                                                placeholder="Jelaskan secara detail mengapa aset ini perlu dilepas (misal: rusak berat, sudah usang, dll)"
                                                required></textarea>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label text-slate-800 fw-bold">Foto Bukti Kondisi Aset
                                                <span class="text-danger">*</span></label>
                                            <div
                                                class="p-3 border border-dashed border-2 border-slate-300 rounded-4 bg-slate-50 d-flex flex-column align-items-center justify-content-center">
                                                <i class="bi bi-image text-primary fs-3 mb-2"></i>
                                                <input type="file" class="form-control border-0 bg-transparent w-75"
                                                    name="foto_kondisi" accept=".jpg,.jpeg,.png" required>
                                            </div>
                                            <small class="text-muted mt-2 d-block fw-medium"><i
                                                    class="bi bi-info-circle me-1 text-primary"></i>Lampirkan foto kondisi aset
                                                terakhir jika diperlukan.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-white border-top border-slate-100 px-4 py-4 d-flex">
                                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm flex-grow-1 ms-3">
                                    <i class="bi bi-send-fill me-2"></i> Ajukan ke Kepala Sekolah
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary pt-4 pb-3 px-4 border-0">
                    <h5 class="modal-title fw-bold text-primary d-flex align-items-center" id="exportModalLabel">
                        <i class="bi bi-cloud-download-fill fs-4 me-3"></i> Modul Cetak Laporan
                    </h5>
                    <button type="button" class="btn-close opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-slate-50">
                    <form id="exportForm" method="GET" action="{{ route('assets.export-excel') }}">
                        <div class="bg-white p-4 rounded-4 shadow-sm border border-slate-200">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-800">Filter Sumber Pendanaan</label>
                                <select class="form-select form-select-lg border-slate-200 shadow-sm" name="sumber_dana"
                                    id="exportSumberDana">
                                    <option value="Semua">Tampilkan Seluruh Sumber Dana</option>
                                    <option value="Yayasan">Dana Yayasan Dar El-Iman</option>
                                    <option value="BOS">Dana Bantuan Operasional Sekolah (BOS)</option>
                                </select>
                            </div>

                            <hr class="text-slate-200 my-4">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-800 d-flex align-items-center">
                                    <i class="bi bi-calendar-range text-primary me-2"></i> Rentang Ekstraksi Data
                                </label>
                                <select name="periode" id="periodeSelectorAsset"
                                    class="form-select form-select-lg border-slate-200 shadow-sm">
                                    <option value="bulanan">Berdasarkan Bulan Spesifik</option>
                                    <option value="tahunan" selected>Seluruh Tahun (Periode Tahunan)</option>
                                    <option value="kustom">Rentang Kustom (Custom Range)</option>
                                </select>
                            </div>

                            <!-- Input Bulan & Tahun dalam baris -->
                            <div class="row g-3" id="wrapperBulanTahunAsset">
                                <div class="col-md-6" id="wrapperBulanAsset" style="display: none;">
                                    <label class="form-label small fw-bold text-slate-600">Bulan Transaksi</label>
                                    <select name="bulan" class="form-select border-slate-200">
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6" id="wrapperTahunAsset">
                                    <label class="form-label small fw-bold text-slate-600">Tahun Buku</label>
                                    <select name="tahun" class="form-select border-slate-200">
                                        @php $tahunSekarang = date('Y'); @endphp
                                        @for($t = $tahunSekarang; $t >= 2020; $t--)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mt-1" id="wrapperKustomAsset" style="display: none;">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-slate-600">Terhitung Dari</label>
                                    <input type="date" class="form-control border-slate-200" name="start_date"
                                        id="exportStartDate">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-slate-600">Hingga Tanggal</label>
                                    <input type="date" class="form-control border-slate-200" name="end_date"
                                        id="exportEndDate">
                                </div>
                                <div class="col-12 mt-2">
                                    <div
                                        class="alert bg-primary bg-opacity-10 border-0 text-primary small py-2 px-3 fw-medium d-flex align-items-center mb-0">
                                        <i class="bi bi-info-circle-fill me-2"></i> Lebihi rentang tanggal untuk unduh total
                                        data.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                            <button type="button" class="btn btn-success flex-grow-1 fw-bold shadow-sm py-2 rounded-pill"
                                onclick="document.getElementById('exportForm').action='{{ route('assets.export-excel') }}'; document.getElementById('exportForm').submit();">
                                <i class="bi bi-file-earmark-excel-fill fs-5 me-2"></i> Format Excel (.xlsx)
                            </button>
                            <!-- Note: PDF link is kept identical to original functionality, though previously marked not operational, it is part of the original logic to keep -->
                            <button type="button" class="btn btn-danger flex-grow-1 fw-bold shadow-sm py-2 rounded-pill"
                                onclick="document.getElementById('exportForm').action='{{ route('assets.export-pdf') }}'; document.getElementById('exportForm').submit();">
                                <i class="bi bi-file-earmark-pdf-fill fs-5 me-2"></i> Format Lembar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('.handler-radio-keluar');

            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const targetId = this.getAttribute('data-target');
                    const formKaryawan = document.getElementById('formKaryawan' + targetId);
                    const formEksternal = document.getElementById('formEksternal' + targetId);

                    if (this.value === '1') {
                        // Slide down transition approach for visual feedback
                        formKaryawan.style.display = 'block';
                        formEksternal.style.display = 'none';
                        // clear eksternal inputs
                        formEksternal.querySelectorAll('input, textarea').forEach(el => el.value = '');
                    } else {
                        formKaryawan.style.display = 'none';
                        formEksternal.style.display = 'block';
                        // clear internal inputs
                        formKaryawan.querySelectorAll('input').forEach(el => el.value = '');
                    }
                });
            });

            // Toggle mandatory Harga Jual pada Pelepasan
            const selectsJenis = document.querySelectorAll('.selector-jenis-keluar');
            selectsJenis.forEach(sel => {
                sel.addEventListener('change', function () {
                    const targetId = this.getAttribute('data-target');
                    const inputHarga = document.getElementById('hargaJual' + targetId);
                    const labelWajib = document.getElementById('labelWajibHarga' + targetId);

                    if (this.value === 'Penjualan') {
                        inputHarga.setAttribute('required', 'required');
                        inputHarga.setAttribute('min', '1');
                        labelWajib.style.display = 'inline-block';
                    } else {
                        inputHarga.removeAttribute('required');
                        inputHarga.setAttribute('min', '0');
                        labelWajib.style.display = 'none';
                    }
                });
            });

            // Toggle Export Period Asset
            const periodeSelectorAsset = document.getElementById('periodeSelectorAsset');
            if (periodeSelectorAsset) {
                function toggleExportPeriod() {
                    const val = periodeSelectorAsset.value;
                    document.getElementById('wrapperBulanAsset').style.display = val === 'bulanan' ? 'block' : 'none';
                    // Adjust layout based on selection
                    if (val === 'bulanan') {
                        document.getElementById('wrapperTahunAsset').className = 'col-md-6';
                        document.getElementById('wrapperTahunAsset').style.display = 'block';
                    } else if (val === 'tahunan') {
                        document.getElementById('wrapperTahunAsset').className = 'col-md-12';
                        document.getElementById('wrapperTahunAsset').style.display = 'block';
                    } else {
                        document.getElementById('wrapperTahunAsset').style.display = 'none';
                    }
                    document.getElementById('wrapperKustomAsset').style.display = val === 'kustom' ? 'flex' : 'none';
                }
                periodeSelectorAsset.addEventListener('change', toggleExportPeriod);
                toggleExportPeriod(); // init
            }

            // Real-time AJAX Search
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const assetListContainer = document.getElementById('assetListContainer');
            const searchIcon = document.getElementById('searchIcon');
            const searchSpinner = document.getElementById('searchSpinner');
            let debounceTimer;

            if (searchInput && searchForm && assetListContainer) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    
                    searchIcon.classList.add('d-none');
                    searchSpinner.classList.remove('d-none');

                    debounceTimer = setTimeout(() => {
                        const formData = new FormData(searchForm);
                        const queryString = new URLSearchParams(formData).toString();
                        const url = searchForm.action + '?' + queryString;

                        fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContent = doc.getElementById('assetListContainer');
                            
                            if (newContent) {
                                assetListContainer.innerHTML = newContent.innerHTML;
                            }
                            
                            window.history.pushState({}, '', url);
                            
                            searchSpinner.classList.add('d-none');
                            searchIcon.classList.remove('d-none');
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                            searchSpinner.classList.add('d-none');
                            searchIcon.classList.remove('d-none');
                        });
                    }, 400); // 400ms debounce
                });
            }
        });
    </script>
@endpush