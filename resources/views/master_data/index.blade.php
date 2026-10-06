@extends('layouts.app')

@section('title', 'Pusat Data Utama')

@push('styles')
<style>

    .bg-slate-50 { background-color: #f8fafc; }
    .text-slate-800 { color: #0f172a; }
    .text-slate-500 { color: #64748b; }
    
    .card-category {
        background-color: #f8fafc !important;
        border: none !important;
        border-radius: 8px !important;
        border-left: 4px solid #065f46 !important;
        box-shadow: none !important;
        padding: 1.25rem 1.5rem !important;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        position: relative;
    }
    
    .card-category .icon-box-cat {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background-color: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .card-category .icon-box-cat i {
        color: #3b82f6; /* Blue icon like in mockup */
        font-size: 1.2rem;
    }

    .card-category h5 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }
    .card-category p {
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 0.8rem;
    }

    .card-category .badge-assets {
        background-color: #a7f3d0;
        color: #047857;
        font-size: 0.70rem;
        font-weight: 700;
        padding: 0.35rem 0.6rem;
        border-radius: 20px;
    }

    .card-category .view-related {
        color: #2563eb;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        margin-left: 1rem;
    }

    .card-location {
        background-color: #f8fafc !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        padding: 1.25rem 1.5rem !important;
        height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .card-location .icon-box-loc {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .card-location .icon-box-loc i {
        color: #3b82f6; /* Blue icon */
        font-size: 0.9rem;
    }

    .card-location h5 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }

    .card-location p {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    .card-location .assets-count {
        font-size: 0.75rem;
        font-weight: 700;
        color: #0f172a;
    }

    .card-location .explore-btn {
        font-size: 0.70rem;
        font-weight: 800;
        color: #047857;
        text-decoration: none;
        letter-spacing: 0.5px;
    }

    .header-icon-cat {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        background-color: #d1fae5;
        color: #047857;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }
    
    .header-icon-loc {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        background-color: #e0e7ff;
        color: #3730a3;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    .btn-action-text {
        color: #047857;
        font-weight: 700;
        font-size: 0.85rem;
        background: none;
        border: none;
        padding: 0;
    }

    .btn-export-schema {
        background-color: #e2e8f0;
        color: #1e293b;
        font-size: 0.85rem;
        font-weight: 700;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1.25rem;
    }

    .banner-interactive-map {
        background: #065f46;
        border-radius: 12px;
        height: 180px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: flex-end;
        padding: 1.5rem;
    }

    .banner-interactive-map::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" opacity="0.3"><path d="M10,10 Q50,90 90,10 T190,10" fill="none" stroke="%23ffffff" stroke-width="1"/><circle cx="50" cy="50" r="2" fill="%23ffffff"/></svg>');
        background-size: cover;
        opacity: 0.4;
    }
    
    .banner-interactive-map .line {
        position: absolute;
        top: 0; bottom: 0;
        width: 2px;
        background-color: rgba(255,255,255,0.2);
    }
    .banner-interactive-map .line-1 { left: 30%; }
    .banner-interactive-map .line-2 { left: 60%; }

    .banner-interactive-map .content {
        position: relative;
        z-index: 2;
    }

    .banner-interactive-map .map-icon-btn {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 36px;
        height: 36px;
        background-color: #ffffff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .banner-interactive-map .map-icon-btn i {
        color: #065f46;
        font-size: 1.1rem;
    }

    .stat-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        border: none;
        box-shadow: 0 4px 10px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
    }

    .stat-card-title {
        font-size: 0.70rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        display: inline-flex;
        align-items: baseline;
    }

    .stat-card-sub {
        font-size: 0.75rem;
        font-weight: 600;
        color: #94a3b8;
        margin-left: 0.5rem;
    }

    .delete-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .card-category:hover .delete-overlay, .card-location:hover .delete-overlay {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5 pt-4" style="max-width: 1200px;">
    
    <!-- Top Header & Export -->
    <div class="d-flex justify-content-between align-items-start mb-5">
        <div>
            <h2 class="fw-bolder mb-1" style="color: #0f172a; font-size: 1.9rem; letter-spacing: -0.5px;">Pusat Data Utama</h2>
            <p class="text-slate-500 mb-0 fw-medium" style="font-size: 0.95rem;">
                Kategorisasi dan alokasi sumber daya institusi secara presisi.
            </p>
        </div>
        <div>
        </div>
    </div>

    <!-- Error/Success Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible bg-white border-0 shadow-sm rounded-3 py-2 px-3 mb-4"><i class="bi bi-check-circle-fill text-success me-2"></i><span class="fw-bold small">{{ session('success') }}</span><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible bg-white border-0 shadow-sm rounded-3 py-2 px-3 mb-4"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i><span class="fw-bold small">Terdapat kendala saat menyimpan data.</span><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-5">
        <!-- Asset Categories Column -->
        <div class="col-xl-6 mb-4 mb-xl-0">
            <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="header-icon-cat">
                        <i class="bi bi-diagram-3-fill fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem;">Kategori Aset</h4>
                </div>
                <button class="btn btn-sm btn-outline-success fw-bold px-3 py-2 rounded-2" data-bs-toggle="modal" data-bs-target="#modalNewCategory">
                    ＋ Kategori Baru
                </button>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($categories as $index => $cat)
                    @php 
                        $assetCount = \App\Models\Asset::where('category_id', $cat->id)->count(); 
                        $sub = "Peralatan Institusi Umum";
                        $iconUrl = 'bi-box-seam';
                        if ($cat->nama_kategori == 'Elektronik') { $sub = "Laptop, Server, Jaringan"; $iconUrl = 'bi-pc-display'; }
                        if (str_contains(strtolower($cat->nama_kategori), 'furn') || $cat->nama_kategori == 'Mebel') { $sub = "Meja, Kursi, Lemari"; $iconUrl = 'bi-lamp'; }
                        if ($cat->nama_kategori == 'Kendaraan') { $sub = "Mobil, Minibus, Operasional"; $iconUrl = 'bi-car-front'; }
                        if (str_contains(strtolower($cat->nama_kategori), 'library') || str_contains(strtolower($cat->nama_kategori), 'buku')) { $sub = "Buku Langka, Buku Teks, Jurnal"; $iconUrl = 'bi-book'; }
                    @endphp
                    <div class="card-category">
                        <div class="icon-box-cat">
                            <i class="bi {{ $iconUrl }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5>{{ $cat->nama_kategori }}</h5>
                            <p>{{ $sub }}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge-assets">{{ number_format($assetCount, 0, ',', '.') }} Aset</span>
                                <a href="{{ route('assets.index', ['category_id' => $cat->id]) }}" class="view-related">Lihat Terkait <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                        
                        <div class="delete-overlay">
                            <form action="{{ route('master-data.categories.destroy', $cat->id) }}" method="POST" id="form-delete-cat-{{ $cat->id }}" onsubmit="event.preventDefault(); Swal.fire({title: 'Hapus Kategori?', text: 'Hapus kategori ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Hapus!'}).then((result) => { if (result.isConfirmed) { document.getElementById('form-delete-cat-{{ $cat->id }}').submit(); } })">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm text-secondary p-0"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-slate-400">
                        <p class="fw-bold small">Belum ada kategori aset.</p>
                    </div>
                @endforelse
                
                @if($categories->hasPages())
                <div class="d-flex justify-content-center mt-3 small">
                    {{ $categories->appends(['loc_page' => request('loc_page')])->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
            </div>
        </div>

        <!-- Locations Column -->
        <div class="col-xl-6">
            <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="header-icon-loc">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem;">Lokasi/Ruangan</h4>
                </div>
                <button class="btn btn-sm btn-outline-primary fw-bold px-3 py-2 rounded-2" data-bs-toggle="modal" data-bs-target="#modalNewLocation">
                    <i class="bi bi-geo-alt me-1" style="font-size: 0.8rem;"></i> Lokasi Baru
                </button>
            </div>

            <div class="row g-3">
                @forelse($locations as $index => $loc)
                    @php 
                        $assetCount = \App\Models\Asset::where('location_id', $loc->id)
                            ->whereDoesntHave('disposal')->count();
                        $locSub = "Gedung Utama, Lantai 2";
                        $locIco = "bi-door-open";
                        if(str_contains(strtolower($loc->nama_ruangan), 'lab') || str_contains(strtolower($loc->nama_ruangan), 'kimia')) { $locSub = "Blok Sains, Lantai Dasar"; $locIco = "bi-flask"; }
                        if(str_contains(strtolower($loc->nama_ruangan), 'olahraga') || str_contains(strtolower($loc->nama_ruangan), 'sport')) { $locSub = "Sayap Timur, Area Luar"; $locIco = "bi-bicycle"; }
                        if(str_contains(strtolower($loc->nama_ruangan), 'komputer') || str_contains(strtolower($loc->nama_ruangan), 'computer')) { $locSub = "Gedung TI, Lantai 1"; $locIco = "bi-laptop"; }
                    @endphp
                    <div class="col-sm-6">
                        <div class="card-location">
                            <div class="icon-box-loc">
                                <i class="bi {{ $locIco }}"></i>
                            </div>
                            <h5>{{ $loc->nama_ruangan }}</h5>
                            <p class="mb-1">{{ $locSub }}</p>
                            <p class="mb-3 text-emerald-700 fw-bold" style="font-size: 0.8rem;">
                                <i class="bi bi-person-check-fill me-1"></i> {{ $loc->penanggung_jawab ?? 'Tanpa Penanggung Jawab' }}
                            </p>
                            
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="assets-count">{{ number_format($assetCount, 0, ',', '.') }} Aset</span>
                                <a href="{{ route('assets.index', ['location_id' => $loc->id]) }}" class="explore-btn">JELAJAHI</a>
                            </div>

                            <div class="delete-overlay d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm text-primary p-0 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#modalEditLocation{{ $loc->id }}"><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('master-data.locations.destroy', $loc->id) }}" method="POST" id="form-delete-loc-{{ $loc->id }}" class="m-0 p-0" onsubmit="event.preventDefault(); Swal.fire({title: 'Hapus Lokasi?', text: 'Hapus lokasi ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Hapus!'}).then((result) => { if (result.isConfirmed) { document.getElementById('form-delete-loc-{{ $loc->id }}').submit(); } })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-secondary p-0 shadow-none border-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-slate-400">
                        <p class="fw-bold small">Belum ada lokasi kampus.</p>
                    </div>
                @endforelse

                @if($locations->hasPages())
                <div class="col-12 d-flex justify-content-center mt-3 small">
                    {{ $locations->appends(['cat_page' => request('cat_page')])->links('pagination::bootstrap-5') }}
                </div>
                @endif
                

            </div>
            </div>
        </div>
    </div>

    <!-- Bottom Stat Row -->
    <div class="row g-4 mt-5 mb-4">
        <!-- Stats 1 -->
        <div class="col-md-6">
            <div class="stat-card">
                <p class="stat-card-title">TOTAL KATEGORI</p>
                <div class="stat-card-value">
                    {{ $totalCategoriesCount ?? 0 }}
                </div>
            </div>
        </div>
        <!-- Stats 2 -->
        <div class="col-md-6">
            <div class="stat-card">
                <p class="stat-card-title">LOKASI AKTIF</p>
                <div class="stat-card-value">
                    {{ $totalLocationsCount ?? 0 }}
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Kategori -->
<div class="modal fade" id="modalNewCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="{{ route('master-data.categories.store') }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center" style="background-color: #047857;">
                <h6 class="fw-bolder mb-0 text-white">Tambah Kategori Aset</h6>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <input type="text" name="nama_kategori" class="form-control bg-slate-50 border-slate-200 shadow-sm" placeholder="mis. Perangkat Lunak Server..." required>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="submit" class="btn btn-success fw-bold px-4 w-100 rounded-3 shadow-sm" style="background-color: #065f46; border-color: #065f46;">Buat Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Lokasi -->
<div class="modal fade" id="modalNewLocation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="{{ route('master-data.locations.store') }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center" style="background-color: #047857;">
                <h6 class="fw-bolder mb-0 text-white">Tambah Lokasi/Ruangan</h6>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-600 mb-1">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" class="form-control bg-slate-50 border-slate-200 shadow-sm" placeholder="mis. Ruang Rapat 3..." required>
                </div>
                <div>
                    <label class="form-label small fw-bold text-slate-600 mb-1">Penanggung Jawab <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="text" name="penanggung_jawab" class="form-control bg-slate-50 border-slate-200 shadow-sm" placeholder="mis. Ahmad Fauzi">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="submit" class="btn btn-success fw-bold px-4 w-100 rounded-3 shadow-sm" style="background-color: #065f46; border-color: #065f46;">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

@foreach($locations as $loc)
<!-- Modal Edit Lokasi -->
<div class="modal fade" id="modalEditLocation{{ $loc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="{{ route('master-data.locations.update', $loc->id) }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center" style="background-color: #047857;">
                <h6 class="fw-bolder mb-0 text-white">Edit Lokasi / Ruangan</h6>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-slate-600 mb-1">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" class="form-control bg-slate-50 border-slate-200 shadow-sm" value="{{ $loc->nama_ruangan }}" required>
                </div>
                <div>
                    <label class="form-label small fw-bold text-slate-600 mb-1">Penanggung Jawab <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="text" name="penanggung_jawab" class="form-control bg-slate-50 border-slate-200 shadow-sm" value="{{ $loc->penanggung_jawab }}" placeholder="mis. Ahmad Fauzi">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="submit" class="btn btn-success fw-bold px-4 w-100 rounded-3 shadow-sm" style="background-color: #065f46; border-color: #065f46;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
