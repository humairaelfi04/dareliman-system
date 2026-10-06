@extends('layouts.app')

@section('title', 'Arsip Aset Dilepas')

@push('styles')
    <style>
        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .text-slate-800 {
            color: #1e293b;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .border-card-green {
            border-left: 4px solid #047857 !important;
        }

        .border-card-blue {
            border-left: 4px solid #1d4ed8 !important;
        }

        .border-card-red {
            border-left: 4px solid #991b1b !important;
        }

        .table-borderless td,
        .table-borderless th {
            padding-left: 0;
            padding-right: 0;
        }

        .badge-reason {
            font-weight: 800;
            font-size: 0.65rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-radius: 50rem;
        }

        .bg-reason-gray {
            background-color: #f1f5f9;
            color: #475569;
        }

        .bg-reason-blue {
            background-color: #eff6ff;
            color: #1e40af;
        }

        .bg-reason-red {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .btn-outline-custom {
            display: inline-flex;
            align-items: center;
            background-color: #e2e8f0;
            color: #334155;
            border: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline-custom:hover {
            background-color: #cbd5e1;
            color: #0f172a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3 pb-5">

        <!-- Flash Messages -->
        <div class="row mb-3">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border border-success-subtle rounded-4 d-flex align-items-center p-3 mb-0"
                        role="alert" style="background-color: var(--emerald-50);">
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        </div>
                        <div class="flex-grow-1 fw-bold text-slate-800">{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Unified Header Section -->
        <div class="d-flex flex-column mb-4 mt-2">
            <h2 class="fw-bolder" style="color: #0f172a; font-size: 2.2rem; letter-spacing: -0.5px;">Manajemen Pelepasan
                Aset</h2>
            <p class="text-slate-500 mb-4 fw-medium" style="max-width: 600px; font-size: 0.95rem;">
                Pusat pengelolaan permohonan pelepasan aset dan rekaman arsip historis.
            </p>

            <!-- Modern Tabs -->
            <div class="bg-white p-2 rounded-pill shadow-sm d-inline-flex border border-slate-200"
                style="width: fit-content;">
                <a href="{{ route('disposal-requests.index') }}"
                    class="btn btn-light text-slate-500 rounded-pill px-4 py-2 fw-bold me-2"
                    style="border: none; background: transparent; transition: all 0.3s;">
                    <i class="bi bi-clipboard-check me-2"></i> Pengajuan & Persetujuan
                </a>
                <a href="{{ route('assets.disposed') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2 fw-bold"
                    style="background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-500) 100%);">
                    <i class="bi bi-archive-fill me-2"></i> Arsip Aset Dilepas
                </a>
            </div>
        </div>


        <!-- Main Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-white">
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0 table-hover">
                        <thead style="border-bottom: 2px solid #f8fafc;">
                            <tr>
                                <th class="py-3 px-4 small fw-bolder text-slate-500"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">NO</th>
                                <th class="py-3 small fw-bolder text-slate-500"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">INFO ASET</th>
                                <th class="py-3 small fw-bolder text-slate-500"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">TANGGAL DILEPAS</th>
                                <th class="py-3 small fw-bolder text-slate-500"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">ALASAN</th>
                                <th class="py-3 small fw-bolder text-slate-500"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">NILAI AKHIR</th>
                                <th class="py-3 pe-4 text-center small fw-bolder text-slate-500"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $index => $asset)
                                <tr style="transition: all 0.2s; cursor: pointer;"
                                    onclick="window.location='{{ route('assets.show', $asset->id) }}'">
                                    <td class="py-4 px-4 text-slate-400 fw-medium">
                                        {{ str_pad($assets->firstItem() + $index, 2, "0", STR_PAD_LEFT) }}</td>
                                    <td class="py-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-slate-50 text-slate-500 p-2 rounded-2 d-flex align-items-center justify-content-center"
                                                style="width: 45px; height: 45px;">
                                                <i
                                                    class="bi {{ ($asset->category->nama_kategori ?? '') == 'Elektronik' ? 'bi-laptop' : (($asset->category->nama_kategori ?? '') == 'Kendaraan' ? 'bi-car-front' : (($asset->category->nama_kategori ?? '') == 'Mebel' ? 'bi-lamp' : 'bi-box-seam')) }} fs-4"></i>
                                            </div>
                                            <div class="ms-1">
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">
                                                    {{ Str::limit($asset->nama_aset, 30) }}</h6>
                                                <span class="text-slate-400 fw-bold d-block"
                                                    style="font-size: 0.70rem; letter-spacing: 0.5px;">SN:
                                                    {{ $asset->kode_aset ?? 'AST-NR-' . str_pad($asset->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            @if($asset->disposal && $asset->disposal->tanggal_keluar)
                                                {{ \Carbon\Carbon::parse($asset->disposal->tanggal_keluar)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        @php
                                            $reason = $asset->disposal ? $asset->disposal->jenis_transaksi_keluar : 'None';
                                            $reasonDisplay = strtoupper($reason);

                                            if (str_contains(strtolower($reason), 'penghapusan') || str_contains(strtolower($reason), 'musnah')) {
                                                $badgeClass = 'bg-reason-red';
                                                $reasonDisplay = 'TIDAK DAPAT DIPERBAIKI';
                                            } elseif (str_contains(strtolower($reason), 'pemindah')) {
                                                $badgeClass = 'bg-reason-gray';
                                                $reasonDisplay = 'PENGGANTIAN';
                                            } elseif (str_contains(strtolower($reason), 'hibah')) {
                                                $badgeClass = 'bg-reason-blue';
                                                $reasonDisplay = 'DIHIBAHKAN';
                                            } else {
                                                $badgeClass = 'bg-reason-red';
                                                $reasonDisplay = 'RUSAK';
                                            }
                                        @endphp
                                        <span class="badge badge-reason {{ $badgeClass }} px-3 py-2">{{ $reasonDisplay }}</span>
                                    </td>
                                    <td class="py-4 fw-bold text-dark" style="font-size: 0.9rem;">
                                        @if($asset->disposal && $asset->disposal->harga_jual > 0)
                                            Rp {{ number_format($asset->disposal->harga_jual, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </td>
                                    <td class="py-4 pe-4 text-center">
                                        <a href="{{ route('assets.show', $asset->id) }}" class="text-slate-400">
                                            <i class="bi bi-chevron-right fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center text-slate-500">
                                        <i class="bi bi-archive display-4 mb-3 d-block text-slate-300"></i>
                                        <h5 class="fw-bold text-slate-600">Tidak Ada Aset Dilepas</h5>
                                        <p class="small fw-medium">Semua riwayat aset yang telah dilepas akan tampil di sini.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Area -->
                <div class="px-4 py-4 d-flex justify-content-between align-items-center flex-wrap"
                    style="background-color: #fff;">
                    <span class="text-slate-500 fw-medium mb-2 mb-md-0" style="font-size: 0.85rem;">Menampilkan <strong
                            class="text-dark">{{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ number_format($assets->total(), 0, ',', '.') }}</strong> aset</span>
                    <div>
                        {{ $assets->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>



    </div>
@endsection