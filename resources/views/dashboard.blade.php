@extends('layouts.app')

@section('title', 'Dasbor Ringkasan')

@section('content')

    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="card h-100 position-relative overflow-hidden" style="border:none; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);">
                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="transform: translate(10%, -10%);">
                    <i class="bi bi-cpu" style="font-size: 8rem; display: block; color: rgba(255,255,255,0.7);"></i>
                </div>
                <div class="card-body p-4 position-relative z-1 text-white">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                            <i class="bi bi-boxes fs-4 text-white" style="opacity: 1 !important;"></i>
                        </div>
                        <div>
                            <h6 class="card-title fw-bold mb-0 text-white-50 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.1em;">Jumlah Aset Aktif</h6>
                            <span class="fs-6 fw-medium text-white opacity-90">Total Unit</span>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <h2 class="display-4 fw-bolder mb-0 text-white lh-1">{{ $totalUnitAktif }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card h-100 position-relative overflow-hidden" style="border:none; background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%);">
                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="transform: translate(10%, -10%);">
                    <i class="bi bi-cash-stack" style="font-size: 8rem; display: block; color: rgba(255,255,255,0.7);"></i>
                </div>
                <div class="card-body p-4 position-relative z-1 text-white">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                            <i class="bi bi-wallet2 fs-4 text-white" style="opacity: 1 !important;"></i>
                        </div>
                        <div>
                            <h6 class="card-title fw-bold mb-0 text-white-50 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.1em;">Total Nilai Buku</h6>
                            <span class="fs-6 fw-medium text-white opacity-90">Total Nilai Aset</span>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <h3 class="fw-bolder text-white mb-0 lh-1" style="font-size: clamp(1.5rem, 2vw, 2rem); letter-spacing: -0.02em;">
                            Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card bg-warning h-100 position-relative border-0 overflow-hidden shadow-sm">
                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="transform: translate(10%, -10%);">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 8rem; display: block; color: rgba(0,0,0,0.15);"></i>
                </div>
                <div class="card-body p-4 position-relative z-1 text-dark">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-dark bg-opacity-10 rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                            <i class="bi bi-wrench-adjustable text-dark fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title fw-bold mb-0 text-dark opacity-75 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.1em;">Perlu Tindakan</h6>
                            <span class="fs-6 fw-medium text-dark opacity-90">Menunggu Perawatan</span>
                        </div>
                    </div>
                    <div class="mt-auto d-flex align-items-end justify-content-between">
                        <h2 class="display-4 fw-bolder mb-0 text-dark lh-1">{{ count($earlyWarnings) + count($rutinServiceWarnings) }}</h2>
                        <span class="badge bg-dark bg-opacity-10 text-dark border-0 px-3 py-2 fw-semibold"><i class="bi bi-calendar-event me-1"></i> Mendekati Jadwal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'kepala_sekolah' && isset($pendingDisposalsCount) && $pendingDisposalsCount > 0)
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                            <i class="bi bi-bell-fill fs-4 animation-blink"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-danger mb-1">{{ $pendingDisposalsCount }} Pengajuan Pelepasan Menunggu Persetujuan</h5>
                            <p class="mb-0 text-danger opacity-75 small fw-medium">Terdapat pengajuan pelepasan aset baru dari Admin/Staf yang memerlukan review dan keputusan Anda.</p>
                        </div>
                    </div>
                    <a href="{{ route('disposal-requests.index') }}" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm">Tinjau Sekarang <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i></a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Banner Servis Rutin Digabung ke Radar Peringatan Dini -->
    <!-- Modals Servis (di luar table) -->
    @foreach($rutinServiceWarnings as $asset)
    <div class="modal fade" id="servisModal{{$asset->id}}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-start">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success px-4 pt-4 pb-3">
                    <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center">
                        <i class="bi bi-tools me-2 fs-5"></i> Selesaikan Perawatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('assets.complete-service', $asset->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="alert d-flex align-items-start border border-emerald rounded-3 mb-4 bg-white shadow-sm" style="color: #064e3b;">
                            <i class="bi bi-info-circle-fill text-emerald fs-4 me-3 mt-1 flex-shrink-0"></i>
                            <div class="small fw-medium lh-base">Tindakan ini akan <strong>secara otomatis menjadwalkan ulang</strong> tenggat servis sesuai dengan interval mesin/aset ini di masa depan.</div>
                        </div>

                        <div class="bg-white rounded-4 p-4 shadow-sm border border-slate-200">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                                    <i class="bi bi-hdd-network fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $asset->nama_aset }}</h6>
                                    <div class="text-muted small font-monospace d-flex align-items-center gap-2">
                                        <span>{{ $asset->kode_aset ?? '-' }}</span>
                                        <span class="badge bg-secondary p-1">{{ $asset->location->nama_ruangan ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <label class="form-label fw-bold text-slate-700">Biaya Servis / Perawatan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                <span class="input-group-text bg-light border-0 fw-bold text-muted ps-3 pe-3">Rp</span>
                                <input type="number" class="form-control bg-white border-0 fw-semibold text-end pe-3" name="biaya_servis" min="0" value="0" required placeholder="0">
                            </div>
                            <small class="text-slate-500 d-block mt-2 fw-medium"><i class="bi bi-exclamation-triangle ms-1 me-1 text-warning"></i> Biarkan <span class="badge bg-light text-dark shadow-sm border">0</span> jika dikerjakan mandiri.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top border-slate-100 px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-bold px-4 flex-grow-1 ms-3 rounded-pill shadow-sm">
                            <i class="bi bi-clipboard2-check-fill me-2"></i> Konfirmasi Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Visualisasi Kondisi Aset dan Peringatan Dini -->
    <div class="row align-items-stretch g-4 mb-4">
        <!-- Chart Kondisi -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-bottom-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Kondisi Fisik</h5>
                        <p class="text-slate-500 small mt-1 mb-0 fw-medium">Persentase kelayakan aset</p>
                    </div>
                    <div class="bg-soft-emerald p-2 rounded-circle shadow-sm border border-emerald">
                        <i class="bi bi-pie-chart-fill text-emerald fs-5"></i>
                    </div>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center pt-2">
                    @if($layakPakaiCount == 0 && $rusakCount == 0)
                        <div class="text-muted text-center my-5">
                            <i class="bi bi-inboxes display-4 text-light"></i>
                            <p class="mt-3 fw-semibold text-slate-400">Belum ada data visualisasi.</p>
                        </div>
                    @else
                        <div style="position: relative; height:240px; width:100%" class="my-3">
                            <canvas id="kondisiChart"></canvas>
                        </div>
                        <div class="mt-3 w-100 px-3">
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background-color: var(--emerald-50);">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: var(--emerald-500);"></div>
                                    <span class="fw-bold text-emerald">Layak Pakai</span>
                                </div>
                                <span class="badge bg-white text-dark shadow-sm border border-emerald fs-6">{{ $layakPakaiCount }} Unit</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #fef2f2;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: #ef4444;"></div>
                                    <span class="fw-bold text-danger">Rusak</span>
                                </div>
                                <span class="badge bg-white text-danger shadow-sm border border-danger-subtle fs-6">{{ $rusakCount }} Unit</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Peringatan Dini (Early Warning System) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-slate-100 pb-3 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            Pengingat Jadwal Perawatan <span class="badge bg-danger bg-opacity-10 text-danger border border-danger ms-3 rounded-pill shadow-sm" style="font-size: 0.7rem;"><i class="bi bi-record-circle-fill me-1 animation-blink"></i> LIVE</span>
                        </h5>
                        <p class="text-slate-500 small mt-1 mb-0 fw-medium">Servis yang hampir jatuh tempo</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($earlyWarnings) > 0 || count($rutinServiceWarnings) > 0)
                        <div class="table-responsive border-0 border-radius-0 mb-0 h-100">
                            <table class="table table-hover align-middle mb-0 border-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="ps-4">Nama Aset</th>
                                        <th>Kategori</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop over Elektronik Maintenance Logs --}}
                                    @foreach($earlyWarnings as $warning)
                    @php 
                        $sekarang = \Carbon\Carbon::now()->startOfDay();
                        $jadwal = \Carbon\Carbon::parse($warning->next_service_date)->startOfDay();
                        $selisihHari = $sekarang->diffInDays($jadwal, false);
                    @endphp
                                    <tr class="{{ $selisihHari <= 0 ? 'bg-danger bg-opacity-10' : '' }}">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold {{ $selisihHari <= 0 ? 'text-danger' : 'text-slate-800' }}">{{ $warning->asset->nama_aset }}</div>
                                            <div class="small {{ $selisihHari <= 0 ? 'text-danger opacity-75' : 'text-muted' }}">{{ $warning->asset->merek_aset }} <span class="badge bg-primary bg-opacity-10 text-primary ms-1">Log</span></div>
                                        </td>
                                        <td class="py-3"><span class="badge bg-secondary border-0">{{ $warning->asset->category->nama_kategori ?? '-' }}</span></td>
                                        <td class="py-3 fw-medium text-slate-600">{{ $jadwal->format('d M Y') }}</td>
                                        <td class="py-3">
                                            @if($selisihHari < 0)
                                                <span class="badge bg-danger shadow-sm rounded-pill px-3 py-2 fw-bold"><i class="bi bi-exclamation-octagon me-1"></i> Terlewat {{ abs(round($selisihHari)) }} Hari</span>
                                            @elseif($selisihHari == 0)
                                                <span class="badge bg-warning text-dark shadow-sm rounded-pill px-3 py-2 fw-bold"><i class="bi bi-hourglass-bottom me-1"></i> Hari Ini</span>
                                            @else
                                                <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 fw-semibold">H-{{ round($selisihHari) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <button type="button"
                                                class="btn btn-sm fw-bold px-3 rounded-pill shadow-sm"
                                                style="background-color:#047857; color:white;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#konfirmasiServisModal"
                                                onclick="setKonfirmasiWarning({{ $warning->asset_id }}, '{{ addslashes($warning->asset->nama_aset) }}', '{{ $jadwal->format('d M Y') }}', {{ $warning->asset->is_rutin_servis ? 'true' : 'false' }})"
                                            >
                                                <i class="bi bi-check2-circle me-1"></i> Konfirmasi Servis
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    {{-- Loop over Rutin Servis Assets (Kendaraan/AC) --}}
                                    @foreach($rutinServiceWarnings as $rutin)
                                    @php 
                                        $sekarang = \Carbon\Carbon::now();
                                        $jadwal = \Carbon\Carbon::parse($rutin->tanggal_servis_berikutnya);
                                        $selisihHari = $sekarang->diffInDays($jadwal, false);
                                    @endphp
                                    <tr class="{{ $selisihHari <= 0 ? 'bg-danger bg-opacity-10' : '' }}">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold {{ $selisihHari <= 0 ? 'text-danger' : 'text-slate-800' }}">{{ $rutin->nama_aset }}</div>
                                            <div class="small {{ $selisihHari <= 0 ? 'text-danger opacity-75' : 'text-muted' }}">{{ $rutin->merek_aset }} <span class="badge bg-warning bg-opacity-10 text-warning-emphasis ms-1">Wajib Servis</span></div>
                                        </td>
                                        <td class="py-3"><span class="badge bg-secondary border-0">{{ $rutin->category->nama_kategori ?? '-' }}</span></td>
                                        <td class="py-3 fw-medium text-slate-600">{{ $jadwal->format('d M Y') }}</td>
                                        <td class="py-3">
                                            @if($selisihHari < 0)
                                                <span class="badge bg-danger shadow-sm rounded-pill px-3 py-2 fw-bold"><i class="bi bi-exclamation-octagon me-1"></i> Terlewat {{ abs(round($selisihHari)) }} Hari</span>
                                            @elseif($selisihHari == 0)
                                                <span class="badge bg-warning text-dark shadow-sm rounded-pill px-3 py-2 fw-bold"><i class="bi bi-hourglass-bottom me-1"></i> Hari Ini</span>
                                            @else
                                                <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 fw-semibold">H-{{ round($selisihHari) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <button type="button"
                                                class="btn btn-sm fw-bold px-3 rounded-pill shadow-sm"
                                                style="background-color:#047857; color:white;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#konfirmasiServisModal"
                                                onclick="setKonfirmasiRutin({{ $rutin->id }}, '{{ addslashes($rutin->nama_aset) }}', '{{ $jadwal->format('d M Y') }}', {{ $rutin->is_rutin_servis ? 'true' : 'false' }})"
                                            >
                                                <i class="bi bi-check2-circle me-1"></i> Konfirmasi Servis
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5">
                            <div class="bg-success bg-opacity-10 p-4 rounded-circle mb-3">
                                <i class="bi bi-shield-check text-success display-4"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Kondisi Aman</h5>
                            <p class="text-center text-muted fw-medium mb-0">Tidak ada jadwal mendesak dalam 30 hari ke depan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Umur Ekonomis (Expiring Assets) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm overflow-hidden border-top border-warning border-4 rounded-4">
                <div class="card-header bg-white pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Status Umur Pemakaian</h5>
                            <p class="text-slate-500 small mt-1 mb-0 fw-medium">Aset yang akan atau sudah habis masa pemanfaatannya</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($expiringAssets) > 0)
                        <div class="table-responsive border-0 border-radius-0 mb-0">
                            <table class="table table-hover align-middle border-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="ps-4">Nama Aset & Merek</th>
                                        <th>Kategori</th>
                                        <th>Tgl Masuk</th>
                                        <th>Umur Default</th>
                                        <th>Status Waktu</th>
                                        <th class="text-center pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringAssets as $asset)
                                    <tr class="{{ $asset->sisa_hari_umur <= 0 ? 'bg-danger bg-opacity-10' : '' }}">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold {{ $asset->sisa_hari_umur <= 0 ? 'text-danger' : 'text-slate-800' }}">{{ $asset->nama_aset }}</div>
                                            <div class="small {{ $asset->sisa_hari_umur <= 0 ? 'text-danger opacity-75' : 'text-muted' }}">{{ $asset->merek_aset }}</div>
                                        </td>
                                        <td class="py-3"><span class="badge bg-secondary border-0">{{ $asset->category->nama_kategori ?? '-' }}</span></td>
                                        <td class="py-3 fw-medium text-slate-600">{{ \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->format('d M Y') }}</td>
                                        <td class="py-3 fw-bold text-dark">{{ $asset->umur_ekonomis_tahun }} Tahun</td>
                                        <td class="py-3">
                                            @if($asset->sisa_hari_umur < 0)
                                                <span class="badge bg-danger shadow-sm rounded-pill"><i class="bi bi-x-circle me-1"></i> Terlewat {{ abs(round($asset->sisa_hari_umur / 30)) }} Bulan</span>
                                            @elseif($asset->sisa_hari_umur == 0)
                                                <span class="badge bg-danger shadow-sm rounded-pill">Habis Hari Ini</span>
                                            @else
                                                <span class="badge bg-warning text-dark border shadow-sm rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Sisa {{ round($asset->sisa_hari_umur / 30) }} Bulan</span>
                                            @endif
                                        </td>
                                        <td class="text-center pe-4 py-3">
                                            @if($asset->sisa_hari_umur <= 0 && !$asset->is_layak_pakai)
                                            <form action="{{ route('assets.verify-layak', $asset->id) }}" method="POST" class="d-inline" id="form-layak-{{ $asset->id }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" class="btn btn-sm btn-success shadow-sm rounded-pill fw-bold px-3 me-1 mb-1" 
                                                    onclick="Swal.fire({title: 'Verifikasi Aset?', text: 'Apakah Anda yakin verifikasi aset ini masih layak pakai?', icon: 'question', showCancelButton: true, confirmButtonColor: '#10b981', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Layak!'}).then((result) => { if (result.isConfirmed) { document.getElementById('form-layak-{{ $asset->id }}').submit(); } })">
                                                    <i class="bi bi-check-circle"></i> Layak
                                                </button>
                                            </form>
                                            @endif
                                            <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-light border text-primary shadow-sm rounded-pill fw-bold px-3 mb-1">
                                                Detail <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-4 py-2 rounded-pill fs-6 border border-success">
                                <i class="bi bi-check-circle-fill me-2"></i> Kinerja Siklus Aset Optimal
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

{{-- ====== MODAL KONFIRMASI SERVIS (shared untuk earlyWarnings & rutinServiceWarnings) ====== --}}
<div class="modal fade" id="konfirmasiServisModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 px-4 pt-4 pb-2" style="background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-700) 100%);">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0">
                        <i class="bi bi-tools me-2"></i>Konfirmasi Pelaksanaan Servis
                    </h5>
                    <p class="text-white text-opacity-60 small mb-0 mt-1" id="konfirmasiSubtitle">Aset: —</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('maintenance-logs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="asset_id" id="konfirmasiAssetId">
                <div class="modal-body px-4 py-4" style="background-color:#f8fafc;">

                    {{-- Konfirmasi banner --}}
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background-color:#ecfdf5; border:1px solid #a7f3d0;">
                        <div class="bg-emerald-700 text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px;background-color:#047857;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-emerald-800" style="color:#065f46; font-size:0.85rem;">Apakah servis sudah dilaksanakan?</div>
                            <div class="text-muted small">Jadwal: <span id="konfirmasiJadwal" class="fw-semibold text-dark">—</span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-700 small">Tanggal Dilaksanakan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_servis" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-700 small">Deskripsi Tindakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi_kerusakan" rows="2" placeholder="mis. Servis rutin berkala, pengecekan komponen" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-700 small">Biaya Servis (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border text-muted fw-medium">Rp</span>
                            <input type="text" class="form-control" id="biaya_perbaikan_formatted" value="0" required placeholder="0">
                            <input type="hidden" name="biaya_perbaikan" id="biaya_perbaikan_real" value="0">
                        </div>
                        <div class="text-muted mt-1" style="font-size:0.75rem;">Isi 0 jika servis dilakukan oleh staf internal (tanpa biaya eksternal)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-700 small">Kondisi Setelah Servis <span class="text-danger">*</span></label>
                        <select class="form-select" name="kondisi_setelah_servis" required>
                            <option value="Baik" selected>Kembali Baik</option>
                            <option value="Rusak Ringan">Masih Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat (Gagal Servis)</option>
                        </select>
                        <div class="text-muted mt-1" style="font-size:0.75rem;">Sistem akan otomatis mengupdate kondisi aset ini.</div>
                    </div>

                    {{-- Toggle jadwal servis rutin berikutnya --}}
                    <div class="p-3 bg-white rounded-3 border border-slate-100 mb-2" id="wrapToggleRutinDashboard">
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="toggleRutinDashboard"
                                onchange="toggleNextServiceDashboard(this.checked)">
                            <label class="form-check-label fw-bold text-slate-700 small mb-0" for="toggleRutinDashboard">
                                Perlu dijadwalkan servis berikutnya?
                            </label>
                        </div>
                        <p class="text-muted mb-0 mt-1" style="font-size:0.75rem;">Aktifkan jika aset ini perlu servis rutin berkala.</p>
                    </div>
                    <div class="mb-1" id="wrapNextServiceDashboard" style="display:none;">
                        <label class="form-label fw-bold text-slate-700 small">Jadwal Servis Berikutnya</label>
                        <input type="date" class="form-control" name="next_service_date" id="nextServiceDashboard">
                    </div>

                </div>
                <div class="modal-footer bg-white border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn fw-bold px-4 rounded-pill text-white" style="background-color:#047857;">
                        <i class="bi bi-check-circle-fill me-1"></i> Ya, Sudah Diservis!
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    @keyframes blink { 50% { opacity: 0.3; } }
    .animation-blink { animation: blink 2s infinite ease-in-out; }
</style>
<script>
    // ── Konfirmasi Servis Modal helpers ─────────────────────────────────
    // Untuk earlyWarnings (dari MaintenanceLog — asset_id langsung)
    function setKonfirmasiWarning(assetId, namaAset, jadwal, isRutin = false) {
        document.getElementById('konfirmasiAssetId').value = assetId;
        document.getElementById('konfirmasiSubtitle').textContent = 'Aset: ' + namaAset;
        document.getElementById('konfirmasiJadwal').textContent = jadwal;
        
        const toggleWrap = document.getElementById('wrapToggleRutinDashboard');
        if (isRutin) {
            toggleWrap.style.display = 'none';
        } else {
            toggleWrap.style.display = 'block';
        }
    }

    // Untuk rutinServiceWarnings (dari Asset — id adalah asset id)
    function setKonfirmasiRutin(assetId, namaAset, jadwal, isRutin = false) {
        document.getElementById('konfirmasiAssetId').value = assetId;
        document.getElementById('konfirmasiSubtitle').textContent = 'Aset: ' + namaAset;
        document.getElementById('konfirmasiJadwal').textContent = jadwal;
        
        const toggleWrap = document.getElementById('wrapToggleRutinDashboard');
        if (isRutin) {
            toggleWrap.style.display = 'none';
        } else {
            toggleWrap.style.display = 'block';
        }
    }

    // Toggle field jadwal servis berikutnya
    function toggleNextServiceDashboard(checked) {
        const wrap  = document.getElementById('wrapNextServiceDashboard');
        const input = document.getElementById('nextServiceDashboard');
        wrap.style.display = checked ? 'block' : 'none';
        input.required = checked;
        if (!checked) input.value = '';
    }

    // Reset modal saat ditutup agar tidak ada data sisa
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('konfirmasiServisModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                document.getElementById('konfirmasiAssetId').value        = '';
                document.getElementById('konfirmasiSubtitle').textContent = 'Aset: \u2014';
                document.getElementById('konfirmasiJadwal').textContent   = '\u2014';
                document.getElementById('toggleRutinDashboard').checked   = false;
                document.getElementById('wrapToggleRutinDashboard').style.display = 'block';
                document.getElementById('biaya_perbaikan_formatted').value = '0';
                document.getElementById('biaya_perbaikan_real').value = '0';
                toggleNextServiceDashboard(false);
            });
        }
        
        // Script Format Rupiah Modal Dashboard
        const biayaInput = document.getElementById('biaya_perbaikan_formatted');
        const biayaHidden = document.getElementById('biaya_perbaikan_real');
        if (biayaInput && biayaHidden) {
            biayaInput.addEventListener('input', function(e) {
                let rawValue = this.value.replace(/[^0-9]/g, '');
                rawValue = rawValue.replace(/^0+/, '');
                if (rawValue === '') {
                    this.value = '';
                    biayaHidden.value = '0';
                } else {
                    let formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    this.value = formatted;
                    biayaHidden.value = rawValue;
                }
            });
            // Handle when left empty completely to restore '0'
            biayaInput.addEventListener('blur', function(e) {
                if (this.value === '') {
                    this.value = '0';
                    biayaHidden.value = '0';
                }
            });
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const layakPakaiCount = {{ $layakPakaiCount }};
        const rusakCount = {{ $rusakCount }};

        if (layakPakaiCount > 0 || rusakCount > 0) {
            Chart.defaults.font.family = "'Inter', sans-serif";
            const ctx = document.getElementById('kondisiChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Layak Pakai', 'Rusak'],
                    datasets: [{
                        label: 'Kondisi Aset',
                        data: [layakPakaiCount, rusakCount],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.85)', // Tailwind Emerald 500
                            'rgba(239, 68, 68, 0.85)'  // Tailwind Red 500
                        ],
                        borderColor: [
                            '#10b981', 
                            '#ef4444'
                        ],
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 14, family: "'Plus Jakarta Sans', sans-serif" },
                            bodyFont: { size: 14, family: "'Inter', sans-serif", weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
