@extends('layouts.app')

@section('title', 'Kartu Pemeliharaan Ruangan')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-slate-800 mb-1">Kartu Pemeliharaan Ruangan (KIR)</h4>
            <p class="text-slate-500 fw-medium small mb-0">Kelola dan cetak Kartu Pemeliharaan aset berdasarkan ruangan per semester.</p>
        </div>
        <div>
            <button class="btn btn-success fw-bold px-4 rounded-pill shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#newKIRModal">
                <i class="bi bi-plus-circle me-2"></i> Buat KIR Baru
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8fafc;" class="text-nowrap">
                    <tr>
                        <th class="text-slate-500 fw-bold fs-7 py-3 px-4">RUANGAN</th>
                        <th class="text-slate-500 fw-bold fs-7 py-3 px-4">PERIODE</th>
                        <th class="text-slate-500 fw-bold fs-7 py-3 px-4">PENANGGUNG JAWAB</th>
                        <th class="text-end text-slate-500 fw-bold fs-7 py-3 px-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                    <tr>
                        <td class="px-4 text-dark fw-bold">{{ $card->ruangan_name }}</td>
                        <td class="px-4 text-emerald-700 fw-bold">Semester {{ $card->semester }} - Tahun {{ $card->tahun }}</td>
                        <td class="px-4 text-slate-600">{{ $card->penanggung_jawab ?? '-' }}</td>
                        <td class="px-4 text-end">
                            <a href="{{ route('room-maintenances.edit', $card->id) }}" class="btn btn-sm btn-primary px-3 fs-7 fw-bold rounded-pill shadow-sm me-1">
                                <i class="bi bi-pencil-square me-1"></i> Isi Form
                            </a>
                            <a href="{{ route('room-maintenances.export-pdf', $card->id) }}" class="btn btn-sm btn-outline-secondary px-3 fs-7 fw-bold rounded-pill">
                                <i class="bi bi-printer me-1"></i> Cetak PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-slate-500">
                            <i class="bi bi-card-checklist fs-1 text-slate-300 d-block mb-3"></i>
                            Belum ada Kartu Pemeliharaan Ruangan yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{ $cards->links() }}
</div>

<!-- Modal New KIR -->
<div class="modal fade" id="newKIRModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('room-maintenances.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-plus-circle me-2"></i> Buat Kartu Pemeliharaan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="mb-3">
                    <label class="form-label fw-bold text-slate-700 small">Pilih Ruangan <span class="text-danger">*</span></label>
                    <select name="ruangan_name" class="form-select bg-white" required>
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->nama_ruangan }}">{{ $loc->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-slate-700 small">Tahun <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tahun" value="{{ date('Y') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-slate-700 small">Semester <span class="text-danger">*</span></label>
                    <select name="semester" class="form-select bg-white" required>
                        <option value="2" {{ date('m') <= 6 ? 'selected' : '' }}>Semester 2 (Jan - Jun)</option>
                        <option value="1" {{ date('m') > 6 ? 'selected' : '' }}>Semester 1 (Jul - Des)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-3">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Buat Kartu</button>
            </div>
        </form>
    </div>
</div>
@endsection
