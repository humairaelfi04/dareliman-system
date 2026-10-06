@extends('layouts.app')

@section('title', 'Catatan Pemeliharaan')

@push('styles')
<style>
    .bg-slate-50 { background-color: #f8fafc; }
    .text-slate-800 { color: #1e293b; }
    .text-emerald-700 { color: #047857; }
    .text-emerald-800 { color: #065f46; }
    .bg-emerald-700 { background-color: #047857; }
    .bg-emerald-50 { background-color: #ecfdf5; }
    .bg-soft-emerald { background-color: #d1fae5; }
    .border-emerald-700 { border-color: #047857 !important; }
    
    .table-borderless td, .table-borderless th {
        padding-left: 0;
        padding-right: 0;
    }
    
    .filter-btn {
        background-color: #1e293b;
        color: white;
    }
    .filter-btn:hover {
        background-color: #0f172a;
        color: white;
    }
    
    .badge-completed {
        background-color: #d1fae5;
        color: #047857 !important;
        font-weight: 700;
        border: 1px solid #a7f3d0;
    }
    .badge-pending {
        background-color: #fef08a;
        color: #854d0e !important;
        font-weight: 700;
        border: 1px solid #fde047;
    }
    
    .card-stat {
        flex: 1;
        min-width: 250px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 pb-5">

    <!-- Flash Messages (jika ada alert) -->
    <div class="row">
        <div class="col-12 mb-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border border-success-subtle rounded-4 d-flex align-items-center p-3" role="alert" style="background-color: var(--emerald-50);">
                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    </div>
                    <div class="flex-grow-1 fw-bold text-slate-800">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border border-danger-subtle rounded-4 d-flex align-items-center p-3" role="alert" style="background-color: #fef2f2;">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                    </div>
                    <div class="flex-grow-1 fw-bold text-slate-800">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bolder text-slate-800 mb-1" style="color: #0f172a;">Catatan Pemeliharaan</h3>
            <p class="text-slate-500 mb-0 fw-medium small">Tampilan Arsip Tahun Anggaran {{ date('Y')-1 }}-{{ date('Y') }}</p>
        </div>
        <div class="d-flex gap-2">

            <a href="{{ route('assets.index') }}" class="btn fw-bold px-4 py-2 rounded-3 shadow-sm text-white" style="background-color: #047857;">
                <i class="bi bi-plus me-1 fs-5 align-middle"></i> Catat Servis Baru
            </a>
        </div>
    </div>

    <!-- 3 Top Cards -->
    <div class="d-flex flex-wrap gap-4 mb-4">
        <!-- Card 1 -->
        <div class="card card-stat border-0 shadow-sm rounded-4 position-relative overflow-hidden bg-white px-4 py-4" style="border-left: 4px solid #047857 !important; flex: 1.5;">
            <div class="position-absolute top-50 end-0 translate-middle-y opacity-25 pe-4">
                <i class="bi bi-cash-stack text-success" style="font-size: 4.5rem;"></i>
            </div>
            <div class="position-relative z-1">
                <p class="text-muted small fw-bold mb-2 text-uppercase letter-spacing-1" style="font-size: 0.70rem;">TOTAL PENGELUARAN PEMELIHARAAN</p>
                <h1 class="fw-bolder text-emerald-800 mb-3" style="color: #065f46; letter-spacing: -1px;">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h1>
                <div class="d-inline-flex align-items-center bg-soft-emerald px-2 py-1 rounded">
                    <i class="bi bi-graph-up-arrow text-emerald-700 me-2 small"></i>
                    <span class="text-emerald-700 fw-bold" style="font-size: 0.75rem;">+12% dari periode sebelumnya</span>
                </div>
            </div>
        </div>


        <!-- Card 3 -->
        <div class="card card-stat border-0 shadow-sm rounded-4 px-4 py-4" style="background-color: #f8fafc; flex: 1;">
            <p class="text-muted small fw-bold mb-2 text-uppercase letter-spacing-1" style="font-size: 0.70rem;">30 HARI KE DEPAN</p>
            <h2 class="fw-bolder text-slate-800 mb-2">{{ $next30DaysSchedulesCount ?? 0 }} Jadwal</h2>
            <p class="text-slate-500 small fw-medium mb-0">{{ $urgentSchedulesCount ?? 0 }} Inspeksi Mendesak</p>
        </div>
    </div>



    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0 table-hover">
                <thead style="background-color: #e2e8f0; color: #475569;" class="text-nowrap">
                    <tr>
                        <th class="py-3 px-4 small fw-bolder text-uppercase letter-spacing-1">NO.</th>
                        <th class="py-3 small fw-bolder text-uppercase letter-spacing-1">INFO ASET</th>
                        <th class="py-3 small fw-bolder text-uppercase letter-spacing-1">TANGGAL SERVIS</th>
                        <th class="py-3 small fw-bolder text-uppercase letter-spacing-1" style="min-width:250px;">DETAIL SERVIS</th>
                        <th class="py-3 small fw-bolder text-uppercase letter-spacing-1">BIAYA</th>
                        <th class="py-3 small fw-bolder text-uppercase letter-spacing-1">JADWAL BERIKUTNYA</th>
                        <th class="py-3 pe-4 small fw-bolder text-uppercase letter-spacing-1">STATUS</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($logs as $index => $log)
                    <tr class="border-bottom border-slate-100 cursor-pointer" onclick="window.location='{{ route('assets.show', $log->asset_id) }}'">
                        <td class="py-4 px-4 text-slate-400 fw-bold small">#{{ str_pad($logs->firstItem() + $index, 3, "0", STR_PAD_LEFT) }}</td>
                        <td class="py-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #e0f2fe; color: #0284c7;">
                                    @if(($log->asset->category->nama_kategori ?? '') == 'Elektronik')
                                    <i class="bi bi-display fs-5"></i>
                                    @elseif(($log->asset->category->nama_kategori ?? '') == 'Kendaraan')
                                    <i class="bi bi-car-front-fill fs-5"></i>
                                    @elseif(($log->asset->category->nama_kategori ?? '') == 'Mebel')
                                    <i class="bi bi-inbox-fill fs-5"></i>
                                    @else
                                    <i class="bi bi-box-fill fs-5"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fw-bold text-slate-800 mb-1" style="font-size: 0.9rem;">{{ Str::limit($log->asset->nama_aset, 30) }}</h6>
                                    <span class="text-slate-500 fw-medium d-block" style="font-size: 0.70rem;">ID: {{ $log->asset->kode_aset ?? 'AST-NR-'.str_pad($log->asset_id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <h6 class="fw-bold text-slate-800 mb-1" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($log->tanggal_servis)->format('M d,') }}</h6>
                            <span class="text-slate-800 fw-bold" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($log->tanggal_servis)->format('Y') }}</span>
                        </td>
                        <td class="py-4 text-slate-700 fw-medium" style="max-width: 250px; font-size: 0.85rem;">
                            {{ Str::limit($log->deskripsi_kerusakan, 60) }}
                        </td>
                        <td class="py-4 fw-bolder text-slate-800" style="font-size: 0.9rem;">
                            @if($log->biaya_perbaikan > 0)
                                Rp {{ number_format($log->biaya_perbaikan, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-4">
                            @php
                                $nextService = \Carbon\Carbon::parse($log->next_service_date);
                                $isDue = now()->diffInDays($nextService, false) <= 7;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                @if($isDue)
                                <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                @else
                                <i class="bi bi-calendar4-week text-slate-400"></i>
                                @endif
                                <div>
                                    <span class="d-block fw-bold {{ $isDue ? 'text-danger' : 'text-slate-600' }}" style="font-size: 0.85rem;">{{ $nextService->format('M d,') }}</span>
                                    <span class="d-block fw-bold {{ $isDue ? 'text-danger' : 'text-slate-600' }}" style="font-size: 0.85rem;">{{ $nextService->format('Y') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 pe-4">
                            @if(str_contains(strtolower($log->deskripsi_kerusakan), 'pending'))
                                <span class="badge badge-pending px-3 py-1 rounded-pill" style="font-size: 0.65rem; letter-spacing: 0.5px;">BELUM</span>
                            @else
                                <span class="badge badge-completed px-3 py-1 rounded-pill" style="font-size: 0.65rem; letter-spacing: 0.5px;">SELESAI</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-slate-500">
                            <i class="bi bi-folder2-open display-4 mb-3 d-block text-slate-300"></i>
                            <h5 class="fw-bold text-slate-600">Tidak Ada Catatan Pemeliharaan</h5>
                            <p class="small fw-medium">Semua log servis dan entri pemeliharaan akan tampil di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Area -->
        <div class="px-4 py-3 border-top border-slate-100 d-flex justify-content-between align-items-center flex-wrap" style="background-color: #f8fafc;">
            <span class="text-slate-500 fw-medium small mb-2 mb-md-0">Menampilkan {{ $logs->count() }} dari {{ $logs->total() }} catatan pemeliharaan</span>
            <div>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>


</div>
@endsection
