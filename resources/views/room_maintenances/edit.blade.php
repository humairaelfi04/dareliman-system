@extends('layouts.app')

@section('title', 'Isi Kartu Pemeliharaan')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('room-maintenances.index') }}"
                    class="btn btn-sm btn-light border mb-2 rounded-pill fw-bold text-slate-600">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <h4 class="fw-bold text-slate-800 mb-1 d-flex align-items-center">
                    <div class="bg-emerald bg-opacity-10 text-emerald p-2 rounded-circle me-3 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-clipboard2-check fs-5"></i>
                    </div>
                    Pengisian Kartu Pemeliharaan
                </h4>
                <p class="text-slate-500 fw-medium small mb-0 ms-4 ps-4">
                    Ruang: <span class="fw-bold text-emerald">{{ $roomMaintenance->ruangan_name }}</span> | Semester: <span
                        class="fw-bold text-slate-700">{{ $roomMaintenance->semester }}</span> | Tahun: <span
                        class="fw-bold text-slate-700">{{ $roomMaintenance->tahun }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('room-maintenances.export-pdf', $roomMaintenance->id) }}"
                    class="btn fw-bold px-4 py-2 rounded-pill shadow-sm d-flex align-items-center border-0 text-white"
                    style="background: linear-gradient(135deg, var(--emerald-500) 0%, var(--emerald-700) 100%);">
                    <i class="bi bi-printer-fill me-2 fs-5"></i> Cetak PDF
                </a>
            </div>
        </div>

        <form action="{{ route('room-maintenances.update', $roomMaintenance->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4 mb-4">
                <!-- Penanggung Jawab -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div
                            class="card-body p-4 bg-white rounded-4 border border-slate-200 d-flex flex-column justify-content-center">
                            <label class="form-label fw-bold text-slate-700 mb-3 d-flex align-items-center">
                                <i class="bi bi-person-badge fs-4 me-2 text-emerald"></i> Penanggung Jawab Ruangan
                            </label>
                            <input type="text"
                                class="form-control form-control-lg bg-slate-50 border-slate-200 fs-6 fw-medium text-slate-800"
                                name="penanggung_jawab" placeholder="Masukkan nama penanggung jawab..."
                                value="{{ $roomMaintenance->penanggung_jawab }}">
                        </div>
                    </div>
                </div>

                <!-- Pemilihan Bulan -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100 relative"
                        style="background-color: var(--emerald-50); border: 1px solid var(--emerald-200) !important;">

                        <div class="card-body p-4 d-flex flex-column justify-content-center relative z-10">
                            <div class="d-flex align-items-md-center flex-column flex-md-row gap-4">
                                <div class="bg-white p-3 rounded-circle shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <i class="bi bi-calendar-check-fill fs-3" style="color: var(--emerald-600);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="fw-bold mb-2 fs-6" style="color: var(--emerald-900);">Pilih Bulan
                                        Pemeriksaan</label>
                                    <select class="form-select form-select-lg border-0 shadow-sm fw-bold text-emerald"
                                        name="active_month" id="monthSelector" onchange="changeMonth()"
                                        style="cursor: pointer;">
                                        @if($roomMaintenance->semester == 1)
                                            <option value="1">Bulan 1 - Juli</option>
                                            <option value="2">Bulan 2 - Agustus</option>
                                            <option value="3">Bulan 3 - September</option>
                                            <option value="4">Bulan 4 - Oktober</option>
                                            <option value="5">Bulan 5 - November</option>
                                            <option value="6">Bulan 6 - Desember</option>
                                        @else
                                            <option value="1">Bulan 1 - Januari</option>
                                            <option value="2">Bulan 2 - Februari</option>
                                            <option value="3">Bulan 3 - Maret</option>
                                            <option value="4">Bulan 4 - April</option>
                                            <option value="5">Bulan 5 - Mei</option>
                                            <option value="6">Bulan 6 - Juni</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <p class="mb-0 mt-3 small fw-semibold" style="color: var(--emerald-800);">
                                <i class="bi bi-info-circle-fill me-1"></i> Data dalam tabel di bawah akan menyesuaikan
                                otomatis dengan bulan yang dipilih.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Aset -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border border-slate-200">
                <div class="card-header bg-white p-4 border-bottom border-slate-200 d-flex align-items-center">
                    <h5 class="fw-bold text-slate-800 mb-0">
                        <i class="bi bi-list-columns-reverse me-2 text-emerald"></i> Rincian Aset dan Pengecekan
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th class="py-3 px-4 text-center text-slate-600 fw-bold">No</th>
                                <th class="py-3 px-3 text-slate-600 fw-bold border-end">Nama Sarana/Perabot</th>
                                <th class="py-3 px-3 text-center text-emerald fw-bold bg-emerald bg-opacity-10"
                                    style="white-space: nowrap;">Diperiksa Bulan Ini?</th>
                                <th class="py-3 px-3 text-slate-600 fw-bold" style="white-space: nowrap;">Kondisi Barang
                                </th>
                                <th class="py-3 px-3 text-slate-600 fw-bold" style="white-space: nowrap;">Status Keberadaan
                                </th>
                                <th class="py-3 px-3 text-slate-600 fw-bold border-end">Catatan Temuan</th>
                                <th class="py-3 px-4 text-center text-slate-600 fw-bold" style="min-width: 170px;">1
                                    Semester Recap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomMaintenance->details as $index => $detail)
                                <tr class="detail-row" data-detail-id="{{ $detail->id }}"
                                    data-m1-kondisi="{{ $detail->kondisi_b1 ?? $detail->kondisi }}"
                                    data-m1-status="{{ $detail->status_temuan_b1 ?? $detail->status_temuan }}"
                                    data-m1-catatan="{{ $detail->catatan_temuan_b1 }}" data-m1-checked="{{ $detail->b1 }}"
                                    data-m2-kondisi="{{ $detail->kondisi_b2 ?? $detail->kondisi }}"
                                    data-m2-status="{{ $detail->status_temuan_b2 ?? $detail->status_temuan }}"
                                    data-m2-catatan="{{ $detail->catatan_temuan_b2 }}" data-m2-checked="{{ $detail->b2 }}"
                                    data-m3-kondisi="{{ $detail->kondisi_b3 ?? $detail->kondisi }}"
                                    data-m3-status="{{ $detail->status_temuan_b3 ?? $detail->status_temuan }}"
                                    data-m3-catatan="{{ $detail->catatan_temuan_b3 }}" data-m3-checked="{{ $detail->b3 }}"
                                    data-m4-kondisi="{{ $detail->kondisi_b4 ?? $detail->kondisi }}"
                                    data-m4-status="{{ $detail->status_temuan_b4 ?? $detail->status_temuan }}"
                                    data-m4-catatan="{{ $detail->catatan_temuan_b4 }}" data-m4-checked="{{ $detail->b4 }}"
                                    data-m5-kondisi="{{ $detail->kondisi_b5 ?? $detail->kondisi }}"
                                    data-m5-status="{{ $detail->status_temuan_b5 ?? $detail->status_temuan }}"
                                    data-m5-catatan="{{ $detail->catatan_temuan_b5 }}" data-m5-checked="{{ $detail->b5 }}"
                                    data-m6-kondisi="{{ $detail->kondisi_b6 ?? $detail->kondisi }}"
                                    data-m6-status="{{ $detail->status_temuan_b6 ?? $detail->status_temuan }}"
                                    data-m6-catatan="{{ $detail->catatan_temuan_b6 }}" data-m6-checked="{{ $detail->b6 }}">

                                    <td class="px-4 text-center fw-medium text-slate-500">{{ $index + 1 }}</td>

                                    <td class="px-3 border-end">
                                        <div class="fw-bold text-slate-800 fs-6">
                                            {{ $detail->asset ? $detail->asset->nama_aset : 'Aset Dihapus' }}
                                        </div>
                                        @if($detail->asset)
                                            <div class="small text-emerald fw-medium mt-1"><i class="bi bi-tag-fill me-1"></i>
                                                {{ $detail->asset->merek_aset }}</div>
                                        @endif
                                    </td>

                                    <td class="px-3 text-center bg-emerald bg-opacity-10 position-relative">
                                        <div class="form-check d-flex justify-content-center p-0 m-0">
                                            <input class="form-check-input check-month m-0 shadow-sm border-emerald"
                                                type="checkbox" name="details[{{ $detail->id }}][is_checked]" value="1"
                                                style="transform: scale(1.6); cursor: pointer;">
                                        </div>
                                    </td>

                                    <td class="px-3" style="width: 180px;">
                                        <select name="details[{{ $detail->id }}][kondisi]"
                                            class="form-select form-select-sm bg-white shadow-sm border-slate-200 fw-medium input-kondisi">
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </td>

                                    <td class="px-3" style="width: 180px;">
                                        <select name="details[{{ $detail->id }}][status_temuan]"
                                            class="form-select form-select-sm bg-white shadow-sm border-slate-200 fw-medium input-status">
                                            <option value="Sesuai">Sesuai/Ada</option>
                                            <option value="Hilang">Hilang</option>
                                            <option value="Tidak Sesuai Ruangan">Tidak Sesuai Ruangan</option>
                                        </select>
                                    </td>

                                    <td class="px-3 border-end" style="width: 250px;">
                                        <input type="text"
                                            class="form-control form-control-sm bg-white shadow-sm border-slate-200 input-catatan"
                                            name="details[{{ $detail->id }}][catatan_temuan]" placeholder="Catat keluhan...">
                                    </td>

                                    <td class="px-4">
                                        <div class="d-flex justify-content-center gap-1">
                                            @for($i = 1; $i <= 6; $i++)
                                                @php $col = 'b' . $i; @endphp
                                                @if($detail->$col)
                                                    <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background-color: #10b981; color: white; width:24px;height:24px;font-size:0.75rem;cursor:help;transition:all 0.2s;"
                                                        data-bs-toggle="tooltip" title="Bulan {{$i}} Telah Diperiksa">
                                                        {{ $i }}
                                                    </span>
                                                @else
                                                    <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; width:24px;height:24px;font-size:0.75rem;cursor:help;transition:all 0.2s; font-weight: 800;"
                                                        data-bs-toggle="tooltip" title="Bulan {{$i}} Belum Diperiksa">
                                                        {{ $i }}
                                                    </span>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div
                    class="card-footer bg-slate-50 border-top border-slate-200 px-4 py-4 d-flex justify-content-between align-items-center">
                    <div class="text-slate-500 small fw-medium">
                        <i class="bi bi-shield-check me-1 text-emerald"></i> Pastikan data telah terisi dengan benar.
                    </div>
                    <button type="submit"
                        class="btn fw-bold px-5 py-3 rounded-pill shadow-lg text-white d-flex align-items-center"
                        style="background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-800) 100%); border: none;">
                        <i class="bi bi-floppy-fill me-2 fs-5"></i> Simpan Data Kartu
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function changeMonth() {
            const month = document.getElementById('monthSelector').value;
            const rows = document.querySelectorAll('tr.detail-row');

            rows.forEach(tr => {
                const kondisi = tr.getAttribute(`data-m${month}-kondisi`) || 'Baik';
                const status = tr.getAttribute(`data-m${month}-status`) || 'Sesuai';
                const catatan = tr.getAttribute(`data-m${month}-catatan`) || '';
                const checked = tr.getAttribute(`data-m${month}-checked`) === "1";

                tr.querySelector('.input-kondisi').value = kondisi;
                tr.querySelector('.input-status').value = status;
                tr.querySelector('.input-catatan').value = catatan;
                tr.querySelector('.check-month').checked = checked;
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            changeMonth();

            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush