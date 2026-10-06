@extends('layouts.app')

@section('title', 'Perubahan Data Inventaris')

@section('content')

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11 text-center mb-4">
            <h3 class="fw-bold text-dark mt-2">Pembaruan Data Aset</h3>
            <p class="text-slate-500">Edit informasi aset dengan teliti. Nilai penyusutan otomatis menyesuaikan jika ada
                perubahan.</p>
        </div>
        <div class="col-xl-10 col-lg-11">
            <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">

                    <div class="card-body px-5 pb-5 pt-5">
                        @if ($errors->any())
                            <div
                                class="alert alert-danger bg-danger bg-opacity-10 border border-danger-subtle rounded-4 d-flex align-items-start mb-5">
                                <i class="bi bi-exclamation-octagon-fill text-danger fs-4 me-3 mt-1"></i>
                                <div>
                                    <strong class="d-block text-danger mb-2">Validasi Pembaruan Gagal!</strong>
                                    <ul class="mb-0 text-slate-700 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-200 mb-5 relative">
                            <h5 class="fw-bold text-slate-800 mb-4 d-flex align-items-center">
                                <div class="bg-success text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 36px; height: 36px;">1</div>
                                Informasi Umum Aset
                            </h5>
                            <div class="row g-4">

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Kode Aset / Barang</label>
                                    <input type="text"
                                        class="form-control form-control-lg bg-white shadow-sm border-slate-200 font-monospace fw-medium text-slate-500"
                                        name="kode_aset" placeholder="Contoh: AST-..."
                                        value="{{ old('kode_aset', $asset->kode_aset) }}">
                                    <small class="text-slate-500 mt-2 d-block"><i
                                            class="bi bi-info-circle text-primary me-1"></i>Bisa dibiarkan atau
                                        diubah</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Nama Aset <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-lg bg-white shadow-sm border-slate-200"
                                        name="nama_aset" required placeholder="Contoh: Laptop Mengajar"
                                        value="{{ old('nama_aset', $asset->nama_aset) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Merek/Brand Aset <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-lg bg-white shadow-sm border-slate-200"
                                        name="merek_aset" required placeholder="Contoh: Asus / Sharp"
                                        value="{{ old('merek_aset', $asset->merek_aset) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Ruangan Aset <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200"
                                        name="location_id" required>
                                        <option value="">Pilih Ruangan...</option>
                                        @foreach($locations as $lokasi)
                                            <option value="{{ $lokasi->id }}" {{ old('location_id', $asset->location_id) == $lokasi->id ? 'selected' : '' }}>
                                                {{ $lokasi->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Kategori Aset <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200"
                                        name="category_id" required>
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($categories as $kat)
                                            <option value="{{ $kat->id }}" {{ old('category_id', $asset->category_id) == $kat->id ? 'selected' : '' }}>
                                                {{ $kat->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Unit Penempatan <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-lg bg-white shadow-sm border-slate-200" name="unit"
                                        required value="{{ old('unit', $asset->unit) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Kondisi Fisik Saat Diregistrasi <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200"
                                        name="kondisi" required>
                                        <option value="Baik" {{ old('kondisi', $asset->kondisi) == 'Baik' ? 'selected' : '' }}>Baik (Layak Pakai)</option>
                                        <option value="Rusak Ringan" {{ old('kondisi', $asset->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                        <option value="Rusak Berat" {{ old('kondisi', $asset->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Vendor / Toko Pembelian</label>
                                    <div
                                        class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                        <span class="input-group-text bg-white border-0 text-muted ps-4"><i
                                                class="bi bi-shop"></i></span>
                                        <input type="text" class="form-control bg-white border-0" name="vendor_aset"
                                            placeholder="Nama Toko / Distributor"
                                            value="{{ old('vendor_aset', $asset->vendor_aset) }}">
                                    </div>
                                </div>

                                <!-- Pilihan Servis Rutin -->
                                <div class="col-12 mt-4">
                                    <div
                                        class="p-4 bg-white shadow-sm rounded-4 border border-slate-200 d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center mb-3 mb-md-0">
                                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
                                                <i class="bi bi-tools fs-4"></i>
                                            </div>
                                            <div>
                                                <label class="form-label fw-bold mb-0 text-slate-800">Perawatan Rutin
                                                    Berkala</label>
                                                <p class="small text-slate-500 mb-0 fw-medium">Aktifkan untuk perangkat (AC,
                                                    Mobil, Mesin Cuci)</p>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg ms-md-4">
                                            <input class="form-check-input" type="checkbox" id="is_rutin_servis"
                                                name="is_rutin_servis" value="1" {{ old('is_rutin_servis', $asset->is_rutin_servis) ? 'checked' : '' }} style="transform: scale(1.5);">
                                        </div>
                                    </div>

                                    <div id="interval_servis_container" class="mt-3 ps-3"
                                        style="{{ old('is_rutin_servis', $asset->is_rutin_servis) ? 'display: block;' : 'display: none;' }} border-left: 3px solid var(--emerald-400);">
                                        <label class="form-label fw-bold text-slate-700">Interval Servis (Per Bulan) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200"
                                            style="max-width:350px;">
                                            <input type="number" class="form-control bg-white border-0" id="interval_servis"
                                                name="interval_servis" min="1" max="60"
                                                placeholder="Contoh: 3 untuk per 3 bulan"
                                                value="{{ old('interval_servis', $asset->interval_servis) }}">
                                            <span
                                                class="input-group-text bg-light border-0 fw-bold text-slate-600 pe-4">Bulan</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-200 mb-5 relative">
                            <h5 class="fw-bold text-slate-800 mb-4 d-flex align-items-center">
                                <div class="bg-success text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 36px; height: 36px;">2</div>
                                Rincian Harga & Nilai Aset
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Sumber Pendanaan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200"
                                        name="sumber_dana" required>
                                        <option value="Yayasan" {{ old('sumber_dana', $asset->sumber_dana) == 'Yayasan' ? 'selected' : '' }}>Yayasan Dar El-Iman</option>
                                        <option value="BOS" {{ old('sumber_dana', $asset->sumber_dana) == 'BOS' ? 'selected' : '' }}>BOS (Bantuan Operasional Sekolah)</option>
                                        <option value="Wakaf" {{ old('sumber_dana', $asset->sumber_dana) == 'Wakaf' ? 'selected' : '' }}>Sumbangan Wakaf</option>
                                        <option value="Hibah" {{ old('sumber_dana', $asset->sumber_dana) == 'Hibah' ? 'selected' : '' }}>Hibah Eksternal</option>
                                        <option value="Lainnya" {{ old('sumber_dana', $asset->sumber_dana) == 'Lainnya' ? 'selected' : '' }}>Sumber Lainnya</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Harga Perolehan Aset (Rp) <span
                                            class="text-danger">*</span></label>
                                    <div
                                        class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                        <span
                                            class="input-group-text bg-light border-0 fw-bold text-slate-500 ps-4">Rp</span>
                                        <input type="text"
                                            class="form-control bg-white border-0 fw-semibold text-emerald placeholder-slate-400"
                                            id="harga_formatted" placeholder="0" required
                                            value="{{ old('harga_perolehan', $asset->harga_perolehan) ? number_format(old('harga_perolehan', $asset->harga_perolehan), 0, '', '.') : '' }}">
                                        <input type="hidden" name="harga_perolehan" id="harga_perolehan_real"
                                            value="{{ old('harga_perolehan', $asset->harga_perolehan) }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Umur Ekonomis <span
                                            class="text-danger">*</span></label>
                                    <div
                                        class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                        <input type="number"
                                            class="form-control bg-white border-0 fw-semibold placeholder-slate-400"
                                            name="umur_ekonomis_tahun" required min="1"
                                            value="{{ old('umur_ekonomis_tahun', $asset->umur_ekonomis_tahun) }}">
                                        <span
                                            class="input-group-text bg-light border-0 fw-bold text-slate-500 pe-4">Tahun</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-200 relative mb-2">
                            <h5 class="fw-bold text-slate-800 mb-4 d-flex align-items-center">
                                <div class="bg-success text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 36px; height: 36px;">3</div>
                                Detail Penerimaan / Perolehan
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Tanggal Transaksi Masuk <span
                                            class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control form-control-lg bg-white shadow-sm border-slate-200 text-slate-700 fw-medium"
                                        name="tanggal_masuk"
                                        value="{{ old('tanggal_masuk', $asset->perolehanAset->tanggal_masuk ?? '') }}"
                                        required>
                                    <small class="text-danger mt-2 d-block fw-medium"><i
                                            class="bi bi-exclamation-triangle-fill me-1"></i>Hati-hati mengubah tanggal
                                        ini.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Sifat Pemerolehan Aset <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200"
                                        name="jenis_transaksi_masuk" required>
                                        <option value="Hibah masuk" {{ old('jenis_transaksi_masuk', $asset->perolehanAset->jenis_transaksi_masuk ?? '') == 'Hibah masuk' ? 'selected' : '' }}>Penerimaan Hibah Masuk</option>
                                        <option value="Pembelian" {{ old('jenis_transaksi_masuk', $asset->perolehanAset->jenis_transaksi_masuk ?? '') == 'Pembelian' ? 'selected' : '' }}>Pembelian Resmi Instansi</option>
                                        <option value="Pemindahan masuk" {{ old('jenis_transaksi_masuk', $asset->perolehanAset->jenis_transaksi_masuk ?? '') == 'Pemindahan masuk' ? 'selected' : '' }}>Mutasi / Pemindahan Masuk</option>
                                        <option value="Pinjaman" {{ old('jenis_transaksi_masuk', $asset->perolehanAset->jenis_transaksi_masuk ?? '') == 'Pinjaman' ? 'selected' : '' }}>Peminjaman (Berbatas Waktu)</option>
                                        <option value="Sewa" {{ old('jenis_transaksi_masuk', $asset->perolehanAset->jenis_transaksi_masuk ?? '') == 'Sewa' ? 'selected' : '' }}>Penyewaan Berkala</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700 d-block">Ganti Bukti Dokumen <small
                                            class="text-muted fw-normal">(Opsional)</small></label>
                                    @if($asset->perolehanAset && $asset->perolehanAset->bukti_transaksi_masuk)
                                        <div class="mb-3">
                                            <a href="{{ asset('storage/' . $asset->perolehanAset->bukti_transaksi_masuk) }}"
                                                target="_blank"
                                                class="badge bg-success text-white text-decoration-none px-3 py-2 rounded-pill shadow-sm"><i
                                                    class="bi bi-file-earmark-check me-1"></i> Lihat Dokumen Saat Ini</a>
                                        </div>
                                    @endif
                                    <label
                                        class="form-control form-control-lg shadow-sm border-2 border-dashed border-slate-300 bg-white d-flex align-items-center justify-content-center gap-2 cursor-pointer transition-all mb-0"
                                        style="cursor: pointer;">
                                        <i class="bi bi-cloud-arrow-up text-primary fs-5"></i>
                                        <span class="text-slate-500 fw-medium fs-6">Upload Dokumen Baru</span>
                                        <input type="file" class="d-none" name="bukti_transaksi_masuk"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            onchange="this.previousElementSibling.textContent = this.files[0].name">
                                    </label>
                                </div>

                                <!-- Detail Pihak Kedua MASUK -->
                                <div class="col-12 mt-5">
                                    <div class="p-4 bg-white shadow-sm rounded-4 border border-slate-200 relative">
                                        <label class="form-label fw-bold text-dark mb-4 d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-3">
                                                <i class="bi bi-person-bounding-box fs-5"></i>
                                            </div>
                                            Tentukan Sumber Pihak yang Menyerahkan <span class="text-danger ms-1">*</span>
                                        </label>

                                        <div
                                            class="mb-4 mt-2 d-flex flex-wrap gap-4 px-3 py-3 rounded-3 bg-slate-50 border border-slate-100">
                                            <div class="form-check custom-radio d-flex align-items-center">
                                                <input class="form-check-input me-2" type="radio"
                                                    name="is_pihak_kedua_karyawan_masuk" id="karyawanYaMasuk" value="1" {{ old('is_pihak_kedua_karyawan_masuk', $asset->perolehanAset->is_pihak_kedua_karyawan_masuk ?? '0') == '1' ? 'checked' : '' }} required style="transform: scale(1.3);">
                                                <label class="form-check-label fw-bold text-slate-700 mt-1 cursor-pointer"
                                                    for="karyawanYaMasuk">Pihak Internal Yayasan (Pegawai)</label>
                                            </div>
                                            <div class="form-check custom-radio d-flex align-items-center ms-md-4">
                                                <input class="form-check-input me-2" type="radio"
                                                    name="is_pihak_kedua_karyawan_masuk" id="karyawanTidakMasuk" value="0"
                                                    {{ old('is_pihak_kedua_karyawan_masuk', $asset->perolehanAset->is_pihak_kedua_karyawan_masuk ?? '0') == '0' ? 'checked' : '' }} required style="transform: scale(1.3);">
                                                <label class="form-check-label fw-bold text-slate-700 mt-1 cursor-pointer"
                                                    for="karyawanTidakMasuk">Entitas Eksternal (Toko / Mutasi Luar)</label>
                                            </div>
                                        </div>

                                        <div id="formPihakKeduaDalamMasuk"
                                            style="display: none; transition: all 0.3s ease;">
                                            <div class="row">
                                                <div class="col-md-6 border-start border-4 border-emerald ps-4 ms-3">
                                                    <label class="form-label fw-bold text-emerald mb-2">Nomor Induk Yayasan
                                                        (NIY) / NIK <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control form-control-lg bg-light border-slate-200"
                                                        id="inputNikPihakMasuk" name="nik_pihak_kedua_masuk"
                                                        value="{{ old('nik_pihak_kedua_masuk', $asset->perolehanAset->nik_pihak_kedua_masuk ?? '') }}"
                                                        placeholder="Input otomatis sistem NIK Staff...">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="formPihakKeduaLuarMasuk" style="transition: all 0.3s ease;">
                                            <div class="row g-4 border-start border-4 border-slate-300 ps-4 ms-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-slate-700 mb-2">Nama Instansi / Lembaga <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control form-control-lg bg-light border-slate-200"
                                                        id="inputNamaPihakMasuk" name="nama_pihak_kedua_masuk"
                                                        value="{{ old('nama_pihak_kedua_masuk', $asset->perolehanAset->nama_pihak_kedua_masuk ?? '') }}"
                                                        placeholder="PT. ABC Nusantara...">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-slate-700 mb-2">Alamat Lengkap
                                                        Eksternal <span class="text-danger">*</span></label>
                                                    <textarea class="form-control bg-light border-slate-200 rounded-3"
                                                        id="inputAlamatPihakMasuk" name="alamat_pihak_kedua_masuk" rows="2"
                                                        placeholder="Jl. Raya Kemerdekaan No.12...">{{ old('alamat_pihak_kedua_masuk', $asset->perolehanAset->alamat_pihak_kedua_masuk ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-end gap-3 pb-5 align-items-center">
                    <a href="{{ route('assets.index') }}"
                        class="btn btn-light fw-bold px-5 py-3 rounded-pill shadow-sm border border-slate-200 text-slate-600 w-100 w-md-auto">
                        <i class="bi bi-x-lg me-2"></i> Batalkan Modifikasi
                    </a>
                    <button type="submit"
                        class="btn fw-bold px-5 py-3 rounded-pill shadow-lg w-100 w-md-auto fs-6 text-white"
                        style="background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-800) 100%); border: none;">
                        <i class="bi bi-pencil-square me-2"></i> Simpan Data Aset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Toggle Pihak Kedua -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Masuk
            const radKaryawanYaMasuk = document.getElementById('karyawanYaMasuk');
            const radKaryawanTidakMasuk = document.getElementById('karyawanTidakMasuk');
            const formPihakKeduaLuarMasuk = document.getElementById('formPihakKeduaLuarMasuk');
            const formPihakKeduaDalamMasuk = document.getElementById('formPihakKeduaDalamMasuk');
            const inputNikPihakMasuk = document.getElementById('inputNikPihakMasuk');
            const inputNamaPihakMasuk = document.getElementById('inputNamaPihakMasuk');
            const inputAlamatPihakMasuk = document.getElementById('inputAlamatPihakMasuk');

            function togglePihakKeduaMasuk() {
                if (radKaryawanYaMasuk.checked) {
                    formPihakKeduaDalamMasuk.style.display = 'block';
                    inputNikPihakMasuk.required = true;

                    formPihakKeduaLuarMasuk.style.display = 'none';
                    inputNamaPihakMasuk.required = false;
                    inputAlamatPihakMasuk.required = false;
                } else {
                    formPihakKeduaDalamMasuk.style.display = 'none';
                    inputNikPihakMasuk.required = false;

                    formPihakKeduaLuarMasuk.style.display = 'block';
                    inputNamaPihakMasuk.required = true;
                    inputAlamatPihakMasuk.required = true;
                }
            }

            radKaryawanYaMasuk.addEventListener('change', togglePihakKeduaMasuk);
            radKaryawanTidakMasuk.addEventListener('change', togglePihakKeduaMasuk);
            togglePihakKeduaMasuk(); // Init

            // Script Servis AC
            const isRutinServis = document.getElementById('is_rutin_servis');
            const intervalServisContainer = document.getElementById('interval_servis_container');
            const intervalServisInput = document.getElementById('interval_servis');

            function toggleServis() {
                if (isRutinServis.checked) {
                    intervalServisContainer.style.display = 'block';
                    intervalServisInput.required = true;
                } else {
                    intervalServisContainer.style.display = 'none';
                    intervalServisInput.required = false;
                }
            }

            isRutinServis.addEventListener('change', toggleServis);
            toggleServis();
            // Script Format Rupiah
            const hrgInput = document.getElementById('harga_formatted');
            const hrgHidden = document.getElementById('harga_perolehan_real');
            if (hrgInput && hrgHidden) {
                hrgInput.addEventListener('input', function (e) {
                    let rawValue = this.value.replace(/[^0-9]/g, '');
                    rawValue = rawValue.replace(/^0+/, '');
                    if (rawValue === '') {
                        this.value = '';
                        hrgHidden.value = '';
                    } else {
                        let formatted = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        this.value = formatted;
                        hrgHidden.value = rawValue;
                    }
                });
            }
        });
    </script>

@endsection