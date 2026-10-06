@extends('layouts.app')

@section('title', 'Persetujuan Pelepasan Aset')

@section('content')
    <div class="container-fluid px-2 pb-5">
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
                    class="btn btn-primary shadow-sm rounded-pill px-4 py-2 fw-bold me-2"
                    style="background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-500) 100%);">
                    <i class="bi bi-clipboard-check me-2"></i> Pengajuan & Persetujuan
                </a>
                <a href="{{ route('assets.disposed') }}" class="btn btn-light text-slate-500 rounded-pill px-4 py-2 fw-bold"
                    style="border: none; background: transparent; transition: all 0.3s;">
                    <i class="bi bi-archive-fill me-2"></i> Arsip Aset Dilepas
                </a>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc;" class="text-nowrap text-slate-500 fs-7">
                        <tr>
                            <th class="py-3 px-4">ID ASET</th>
                            <th class="py-3 px-4">NAMA ASET</th>
                            <th class="py-3 px-4">PEMOHON</th>
                            <th class="py-3 px-4">TGL PENGAJUAN</th>
                            <th class="py-3 px-4">STATUS</th>
                            <th class="text-end py-3 px-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td class="fw-bold">
                                    {{ $req->asset->kode_aset ?? 'EDU-' . str_pad($req->asset_id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <a href="{{ route('assets.show', $req->asset_id) }}"
                                        class="text-decoration-none fw-bold text-emerald-700">
                                        {{ Str::limit($req->asset->nama_aset, 25) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="d-block fw-bold text-slate-800">{{ $req->requester?->name ?? ($req->requester_name ?? 'Sistem / Lama') }}</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 mt-1" style="font-size: 0.65rem;">{{ match($req->requester?->role ?? ($req->requester_role ?? '')) { 'kepala_sekolah' => 'Kepala Sekolah', 'admin' => 'Administrator', 'sarpras' => 'Sarpras', 'bendahara' => 'Bendahara', default => '-' } }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis">Menunggu Kepsek</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Disetujui (Menunggu
                                            Finalisasi)</span>
                                    @elseif($req->status === 'completed')
                                        <span class="badge bg-success bg-opacity-10 text-success">Selesai Dilepas</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($req->status === 'pending')
                                        @if(auth()->check() && auth()->user()->role === 'kepala_sekolah')
                                            <button type="button" class="btn btn-sm btn-primary px-3 fs-7" data-bs-toggle="modal"
                                                data-bs-target="#reviewModal{{ $req->id }}">
                                                <i class="bi bi-search me-1"></i> Review
                                            </button>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i
                                                    class="bi bi-hourglass-split me-1"></i> Menunggu Otorisasi Kepsek</span>
                                        @endif
                                    @elseif($req->status === 'approved')
                                        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara']))
                                            <button type="button" class="btn btn-sm text-white px-3 fs-7"
                                                style="background-color: #047857;" data-bs-toggle="modal"
                                                data-bs-target="#finalizeModal{{ $req->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> Catat Pelepasan
                                            </button>
                                        @else
                                            <span class="text-muted small"><i class="bi bi-hourglass"></i> Menunggu Admin
                                                Finalisasi</span>
                                        @endif
                                    @elseif($req->status === 'rejected')
                                        <span class="text-muted small" title="{{ $req->alasan_penolakan }}">Ditolak:
                                            {{ Str::limit($req->alasan_penolakan, 20) }}</span>
                                    @elseif($req->status === 'completed')
                                        <span class="text-emerald-700 small fw-bold" style="color: #047857;"><i
                                                class="bi bi-check2-all"></i> Telah Diselesaikan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada pengajuan pelepasan aset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Modals outside the table and overflow container -->
    @foreach($requests as $req)

        <!-- Modal Review Kepsek -->
        @if($req->status === 'pending' && auth()->check() && auth()->user()->role === 'kepala_sekolah')
            <div class="modal fade" id="reviewModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content text-start border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header pt-4 pb-3 px-4" style="background-color: #047857;">
                            <h5 class="modal-title fw-bold text-white d-flex align-items-center">
                                <i class="bi bi-clipboard-data fs-4 me-3 text-warning"></i> Review Pengajuan Pelepasan
                            </h5>
                            <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4" style="background-color: #f8fafc;">
                            <!-- Data Aset -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border border-slate-200 mb-3 row g-0">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <span class="d-block text-muted small fw-bold text-uppercase"
                                        style="letter-spacing: 0.5px;">Aset</span>
                                    <span class="fw-bold text-slate-800">{{ $req->asset->nama_aset }}
                                        ({{ $req->asset->kode_aset ?? '-' }})</span>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <span class="d-block text-muted small fw-bold text-uppercase"
                                        style="letter-spacing: 0.5px;">Umur Ekonomis</span>
                                    <span class="fw-bold text-slate-800">{{ $req->asset->umur_ekonomis_tahun }} Tahun</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="d-block text-muted small fw-bold text-uppercase"
                                        style="letter-spacing: 0.5px;">Harga Beli</span>
                                    <span class="fw-bold text-slate-800">Rp
                                        {{ number_format($req->asset->harga_perolehan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Alasan Pelepasan & Foto -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border border-slate-200 mb-3">
                                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-chat-quote-fill me-2"></i>Alasan Pengajuan
                                    Pelepasan</h6>
                                <p class="text-slate-700 mb-3">{{ $req->alasan_pengajuan ?? 'Tidak ada alasan yang dituliskan.' }}
                                </p>

                                @if($req->foto_kondisi)
                                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-image me-2"></i>Foto Bukti Kerusakan / Kondisi
                                        Terakhir</h6>
                                    <div class="text-center bg-slate-100 p-2 rounded-3">
                                        <img src="{{ asset('storage/' . $req->foto_kondisi) }}" class="img-fluid rounded"
                                            style="max-height: 250px; object-fit: contain;" alt="Foto Kondisi Aset">
                                    </div>
                                @endif
                            </div>

                            <!-- Riwayat Servis -->
                            <div class="bg-white p-3 rounded-4 shadow-sm border border-slate-200 mb-3">
                                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-tools me-2"></i>Riwayat Servis / Pemeliharaan
                                </h6>
                                @if($req->asset->maintenanceLogs && $req->asset->maintenanceLogs->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0 fs-7">
                                            <thead class="bg-slate-100 text-slate-600">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Kerusakan/Tindakan</th>
                                                    <th>Biaya</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($req->asset->maintenanceLogs->sortByDesc('tanggal_servis')->take(3) as $log)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($log->tanggal_servis)->format('d M Y') }}</td>
                                                        <td>{{ $log->deskripsi_kerusakan }}</td>
                                                        <td>Rp {{ number_format($log->biaya_perbaikan, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-muted small mt-1"><em>* Menampilkan maksimal 3 riwayat servis terakhir.</em></div>
                                @else
                                    <p class="text-muted small mb-0">Belum ada riwayat servis untuk aset ini.</p>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-top border-slate-100 px-4 py-3 d-flex justify-content-between">
                            <!-- Tolak Action -->
                            <button type="button" class="btn btn-outline-danger fw-bold px-4 rounded-pill shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle-fill me-1"></i> Tolak
                            </button>

                            <!-- Setujui Action -->
                            <form action="{{ route('disposal-requests.approve', $req->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Setujui Pelepasan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nested Modal untuk Alasan Penolakan -->
            <div class="modal fade text-start" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('disposal-requests.reject', $req->id) }}" method="POST"
                        class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white">Tolak Pengajuan Pelepasan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-slate-50">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-slate-700 small">Alasan Penolakan <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="alasan_penolakan" rows="3" required
                                    placeholder="Tuliskan alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-subtle px-4 py-3">
                            <button type="button" class="btn btn-light rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                                data-bs-target="#reviewModal{{ $req->id }}" data-bs-dismiss="modal">Kembali ke Review</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-3 fw-bold">Konfirmasi Penolakan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Modal Finalisasi (Catat Pelepasan) untuk Admin -->
        @if($req->status === 'approved' && auth()->check() && in_array(auth()->user()->role, ['admin', 'bendahara']))
            <div class="modal fade" id="finalizeModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content text-start border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header pt-4 pb-3 px-4" style="background-color: #047857;">
                            <h5 class="modal-title fw-bold text-white d-flex align-items-center">
                                <i class="bi bi-journal-check fs-4 me-3 text-warning"></i> Catat Pelepasan Final
                            </h5>
                            <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('disposal-requests.finalize', $req->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4 bg-slate-50">

                                <div class="bg-white p-4 rounded-4 shadow-sm border border-slate-200">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label text-slate-800 fw-bold">Tanggal Eksekusi Pelepasan <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control form-control-lg bg-slate-50"
                                                name="tanggal_keluar" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-slate-800 fw-bold">Metode Pelepasan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select form-select-lg bg-slate-50 selector-jenis-keluar"
                                                data-target="{{ $req->id }}" name="jenis_transaksi_keluar" required>
                                                <option value="">-- Tentukan Metode --</option>
                                                <option value="Penjualan">Penjualan</option>
                                                <option value="Penghapusan">Penghapusan (Dibuang/Dihancurkan)</option>
                                                <option value="Hibah keluar">Hibah Keluar</option>
                                                <option value="Pemindahan keluar">Pemindahan</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label text-slate-800 fw-bold">Nominal Pelepasan / Penjualan (Rp)
                                                <span class="badge bg-danger ms-1" id="labelWajibHarga{{ $req->id }}"
                                                    style="display:none;">Wajib</span></label>
                                            <div
                                                class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                                <span class="input-group-text bg-light border-0 text-muted fw-bold ps-4">Rp</span>
                                                <input type="text"
                                                    class="form-control bg-white border-0 fw-semibold text-end pe-4 harga-jual-formatted"
                                                    id="hargaJualVisible{{ $req->id }}" data-target="{{ $req->id }}" placeholder="0">
                                                <input type="hidden" id="hargaJual{{ $req->id }}" name="harga_jual">
                                            </div>
                                            <small class="text-muted mt-2 d-block fw-medium"><i
                                                    class="bi bi-info-circle me-1 text-primary"></i>Hanya diisi jika menghasilkan
                                                dana balik (Nilai Jual/Lelang).</small>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label text-slate-800 fw-bold">Dokumen Berita Acara <span class="text-danger">*</span></label>
                                            <div
                                                class="p-3 border border-dashed border-2 border-slate-300 rounded-4 bg-slate-50 d-flex flex-column align-items-center justify-content-center">
                                                <i class="bi bi-cloud-arrow-up-fill text-primary fs-3 mb-2"></i>
                                                <input type="file" class="form-control border-0 bg-transparent w-75"
                                                    name="bukti_transaksi_keluar" accept=".jpg,.jpeg,.png,.pdf" required>
                                            </div>
                                            <small class="text-muted mt-2 d-block fw-medium"><i
                                                    class="bi bi-paperclip me-1 text-primary"></i>Upload scan BA Serah Terima /
                                                Kwitansi (.pdf / .jpg).</small>
                                        </div>
                                    </div>

                                    <!-- Detail Pihak Kedua KELUAR -->
                                    <div class="p-4 bg-slate-50 rounded-4 border border-slate-200 mt-4">
                                        <label class="form-label fw-bold text-primary mb-3 d-flex align-items-center">
                                            <div class="bg-white p-2 rounded-circle shadow-sm me-3">
                                                <i class="bi bi-person-bounding-box fs-5"></i>
                                            </div>
                                            Informasi Pihak Penerima (Pihak Kedua)
                                        </label>

                                        <div class="mb-4 mt-2 d-flex gap-4 p-3 bg-white border rounded-3 shadow-sm">
                                            <div class="form-check custom-radio d-flex align-items-center">
                                                <input class="form-check-input handler-radio-keluar me-2" type="radio"
                                                    name="is_pihak_kedua_karyawan_keluar" id="karyawanYaKeluar{{ $req->id }}"
                                                    value="1" data-target="{{ $req->id }}" style="transform: scale(1.2);">
                                                <label class="form-check-label fw-bold text-slate-700 mt-1"
                                                    for="karyawanYaKeluar{{ $req->id }}">Internal Yayasan (Pegawai)</label>
                                            </div>
                                            <div class="form-check custom-radio d-flex align-items-center">
                                                <input class="form-check-input handler-radio-keluar me-2" type="radio"
                                                    name="is_pihak_kedua_karyawan_keluar" id="karyawanTidakKeluar{{ $req->id }}"
                                                    value="0" data-target="{{ $req->id }}" checked style="transform: scale(1.2);">
                                                <label class="form-check-label fw-bold text-slate-700 mt-1"
                                                    for="karyawanTidakKeluar{{ $req->id }}">Pihak Eksternal</label>
                                            </div>
                                        </div>

                                        <div id="formKaryawan{{ $req->id }}" style="display: none;" class="px-2">
                                            <div class="mb-2">
                                                <label class="form-label small text-primary fw-bold">Nomor Induk Yayasan (NIY) / NIK
                                                    Pegawai</label>
                                                <input type="text" class="form-control form-control-lg bg-white shadow-sm"
                                                    name="nik_pihak_kedua_keluar" placeholder="Ketikkan NIY ex: 19827391">
                                            </div>
                                        </div>

                                        <div id="formEksternal{{ $req->id }}" class="px-2">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-slate-700">Nama Lengkap Institusi /
                                                        Individu</label>
                                                    <input type="text" class="form-control form-control-lg bg-white shadow-sm"
                                                        name="nama_pihak_kedua_keluar"
                                                        placeholder="Contoh: CV. Mandiri Teknologi / Bapak Budi...">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small fw-bold text-slate-700">Alamat Pihak
                                                        Penerima</label>
                                                    <textarea class="form-control bg-white shadow-sm"
                                                        name="alamat_pihak_kedua_keluar" rows="3"
                                                        placeholder="Tuliskan alamat lengkap korespondensi..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-white border-top border-slate-100 px-4 py-4 d-flex">
                                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm"
                                    data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn text-white fw-bold px-4 rounded-pill shadow-sm flex-grow-1 ms-3"
                                    style="background-color: #047857;">
                                    <i class="bi bi-floppy-fill me-2"></i> Simpan Pencatatan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Format angka ribuan
            const formatRupiah = (number) => {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            };

            const inputsFormatted = document.querySelectorAll('.harga-jual-formatted');
            inputsFormatted.forEach(input => {
                input.addEventListener('input', function (e) {
                    let rawValue = this.value.replace(/[^0-9]/g, '');
                    const targetId = this.getAttribute('data-target');
                    const hiddenInput = document.getElementById('hargaJual' + targetId);
                    
                    hiddenInput.value = rawValue;
                    this.value = rawValue ? formatRupiah(rawValue) : '';
                });
            });

            // Handler untuk select Metode Pelepasan (Tampilkan wajib Harga Jual jika 'Penjualan')
            const selectJenisKeluar = document.querySelectorAll('.selector-jenis-keluar');
            selectJenisKeluar.forEach(select => {
                select.addEventListener('change', function () {
                    const id = this.getAttribute('data-target');
                    const labelWajib = document.getElementById('labelWajibHarga' + id);
                    const inputHarga = document.getElementById('hargaJual' + id);
                    const inputFormatted = document.getElementById('hargaJualVisible' + id);

                    if (this.value === 'Penjualan') {
                        labelWajib.style.display = 'inline-block';
                        if (inputFormatted) inputFormatted.setAttribute('required', 'required');
                    } else {
                        labelWajib.style.display = 'none';
                        if (inputFormatted) {
                            inputFormatted.removeAttribute('required');
                            inputFormatted.value = '';
                        }
                        inputHarga.value = ''; // Reset nilai
                    }
                });
            });

            // Handler Pihak Kedua Keluar
            const radiosKeluar = document.querySelectorAll('.handler-radio-keluar');
            radiosKeluar.forEach(radio => {
                radio.addEventListener('change', function () {
                    const id = this.getAttribute('data-target');
                    const formKaryawan = document.getElementById('formKaryawan' + id);
                    const formEksternal = document.getElementById('formEksternal' + id);

                    if (this.value == '1') {
                        formKaryawan.style.display = 'block';
                        formEksternal.style.display = 'none';
                        // Toggle required
                        formKaryawan.querySelector('input').setAttribute('required', 'required');
                        formEksternal.querySelectorAll('input, textarea').forEach(el => el.removeAttribute('required'));
                    } else {
                        formKaryawan.style.display = 'none';
                        formEksternal.style.display = 'block';
                        // Toggle required
                        formKaryawan.querySelector('input').removeAttribute('required');
                        formEksternal.querySelectorAll('input, textarea').forEach(el => el.setAttribute('required', 'required'));
                    }
                });
            });
        });
    </script>
@endpush