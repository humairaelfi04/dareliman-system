@extends('layouts.app')

@section('title', 'Tambah Data Aset')

@section('content')

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11 text-center mb-4">
            <h3 class="fw-bold text-dark mt-2">Pendaftaran Aset</h3>
            <p class="text-slate-500">Gunakan form ini untuk mendaftarkan aset baru. Anda bisa mendaftarkan 1 aset, atau banyak aset sekaligus.</p>
        </div>
        <div class="col-xl-10 col-lg-11">
            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
                    
                    <div class="card-body px-5 pb-5 pt-5">
                        @if ($errors->any())
                            <div class="alert alert-danger bg-danger bg-opacity-10 border border-danger-subtle rounded-4 d-flex align-items-start mb-5">
                                <i class="bi bi-exclamation-octagon-fill text-danger fs-4 me-3 mt-1"></i>
                                <div>
                                    <strong class="d-block text-danger mb-2">Validasi Gagal!</strong>
                                    <ul class="mb-0 text-slate-700 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Bagian Khusus Bulk Add -->
                        <div class="p-4 rounded-4 mb-5 relative shadow-sm" style="background-color: var(--emerald-50); border: 1px solid var(--emerald-100);">
                            <h5 class="fw-bold mb-4 d-flex align-items-center" style="color: var(--emerald-800);">
                                <i class="bi bi-layers-fill fs-3 me-3"></i> Opsi Tambah Banyak Sekaligus 
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-primary fs-5">Kuantitas Aset <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm border border-slate-200 rounded-3">
                                        <span class="input-group-text bg-white border-0 fw-bold text-slate-400"><i class="bi bi-box-seam"></i></span>
                                        <input type="number" class="form-control border-0 fw-bolder fs-4 text-primary bg-light" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="500" required>
                                        <span class="input-group-text bg-white border-0 fw-bold text-slate-400">Unit</span>
                                    </div>
                                    <small class="text-muted mt-2 d-block">Berapa banyak aset ini yang akan dimasukkan?</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: var(--emerald-800);">Awalan Kode (Base Kode Aset)</label>
                                    <input type="text" class="form-control form-control-lg shadow-sm border" style="background-color: white; border-color: var(--emerald-500); color: var(--emerald-900);" name="base_kode_aset" placeholder="Contoh: MS-K1A" value="{{ old('base_kode_aset') }}">
                                    <small class="d-block mt-2" style="color: var(--emerald-700);"><i class="bi bi-magic me-1"></i> Sistem akan membuatkan kode <strong>MS-K1A-001</strong>, <strong>MS-K1A-002</strong>, dst.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Data Dasar Aset (Identik) -->
                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-200 mb-5 relative">
                            <h5 class="fw-bold text-slate-800 mb-4 d-flex align-items-center">
                                <div class="bg-success text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">1</div> 
                                Informasi Umum Aset
                            </h5>
                            <div class="row g-4">
                            
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Nama Aset Induk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg bg-white shadow-sm border-slate-200" name="nama_aset" required placeholder="Contoh: Kursi Siswa" value="{{ old('nama_aset') }}">
                                    <small class="text-slate-500 mt-2 d-block">Sistem akan menambahkan indeks tambahan nama (Contoh: Kursi Siswa - 1).</small>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Merek/Brand Aset <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg bg-white shadow-sm border-slate-200" name="merek_aset" required placeholder="Contoh: Chitose / Olympic" value="{{ old('merek_aset') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Ruangan Aset <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200" name="location_id" required>
                                        <option value="">Pilih Ruangan...</option>
                                        @foreach($locations as $lokasi)
                                            <option value="{{ $lokasi->id }}" {{ old('location_id') == $lokasi->id ? 'selected' : '' }}>
                                                {{ $lokasi->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Kategori Aset <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200" name="category_id" required>
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($categories as $kat)
                                            <option value="{{ $kat->id }}" {{ old('category_id') == $kat->id ? 'selected' : '' }}>
                                                {{ $kat->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Unit Penempatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg bg-white shadow-sm border-slate-200" name="unit" required value="{{ old('unit', 'SDIT 2 Dar El-Iman') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Kondisi Fisik Saat Diregistrasi <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200" name="kondisi" required>
                                        <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik (Layak Pakai)</option>
                                        <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                        <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Vendor / Toko Pembelian</label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                        <span class="input-group-text bg-white border-0 text-muted ps-4"><i class="bi bi-shop"></i></span>
                                        <input type="text" class="form-control bg-white border-0" name="vendor_aset" placeholder="Nama Toko / Distributor" value="{{ old('vendor_aset') }}">
                                    </div>
                                </div>

                                <!-- Pilihan Servis Rutin -->
                                <div class="col-12 mt-4">
                                    <div class="p-4 bg-white shadow-sm rounded-4 border border-slate-200 d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center mb-3 mb-md-0">
                                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
                                                <i class="bi bi-tools fs-4"></i>
                                            </div>
                                            <div>
                                                <label class="form-label fw-bold mb-0 text-slate-800">Perawatan Rutin Berkala</label>
                                                <p class="small text-slate-500 mb-0 fw-medium">Aktifkan jika perangkat memerlukan servis terjadwal.</p>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg ms-md-4">
                                            <input class="form-check-input" type="checkbox" id="is_rutin_servis" name="is_rutin_servis" value="1" {{ old('is_rutin_servis') ? 'checked' : '' }} style="transform: scale(1.5);">
                                        </div>
                                    </div>
                                    
                                    <div id="interval_servis_container" class="mt-3 ps-3" style="display: none; border-left: 3px solid var(--emerald-400);">
                                        <label class="form-label fw-bold text-slate-700">Interval Servis (Per Bulan) <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200" style="max-width:350px;">
                                            <input type="number" class="form-control bg-white border-0" id="interval_servis" name="interval_servis" min="1" max="60" placeholder="Contoh: 3" value="{{ old('interval_servis') }}">
                                            <span class="input-group-text bg-light border-0 fw-bold text-slate-600 pe-4">Bulan</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-200 mb-5 relative">
                            <h5 class="fw-bold text-slate-800 mb-4 d-flex align-items-center">
                                <div class="bg-success text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">2</div> 
                                Rincian Harga & Nilai Aset
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Sumber Pendanaan <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200" name="sumber_dana" required>
                                        <option value="">Pilih Sumber...</option>
                                        <option value="Yayasan" {{ old('sumber_dana') == 'Yayasan' ? 'selected' : '' }}>Yayasan</option>
                                        <option value="BOS" {{ old('sumber_dana') == 'BOS' ? 'selected' : '' }}>BOS</option>
                                        <option value="Wakaf" {{ old('sumber_dana') == 'Wakaf' ? 'selected' : '' }}>Wakaf</option>
                                        <option value="Hibah" {{ old('sumber_dana') == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                                        <option value="Lainnya" {{ old('sumber_dana') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Harga Beli / Perolehan PER ASET (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                        <span class="input-group-text bg-light border-0 fw-bold text-slate-500 ps-4">Rp</span>
                                        <input type="text" class="form-control bg-white border-0 fw-semibold text-emerald placeholder-slate-400" id="harga_formatted" placeholder="0" required value="{{ old('harga_perolehan') ? number_format(old('harga_perolehan'), 0, '', '.') : '' }}">
                                        <input type="hidden" name="harga_perolehan" id="harga_perolehan_real" value="{{ old('harga_perolehan') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700">Perkiraan Umur Pemakaian <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-slate-200">
                                        <input type="number" class="form-control bg-white border-0 fw-semibold placeholder-slate-400" name="umur_ekonomis_tahun" required min="1" placeholder="Contoh: 4" value="{{ old('umur_ekonomis_tahun') }}">
                                        <span class="input-group-text bg-light border-0 fw-bold text-slate-500 pe-4">Tahun</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-4 border border-slate-200 mb-2 relative">
                            <h5 class="fw-bold text-slate-800 mb-4 d-flex align-items-center">
                                <div class="bg-success text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">3</div> 
                                Detail Penerimaan / Perolehan
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Tanggal Transaksi Masuk <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg bg-white shadow-sm border-slate-200 text-slate-700 fw-medium" name="tanggal_masuk" required value="{{ old('tanggal_masuk') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700">Jenis Transaksi Masuk <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-white shadow-sm border-slate-200" name="jenis_transaksi_masuk" required>
                                        <option value="">Pilih Sifat Masuk...</option>
                                        <option value="Pembelian" {{ old('jenis_transaksi_masuk') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                                        <option value="Hibah masuk" {{ old('jenis_transaksi_masuk') == 'Hibah masuk' ? 'selected' : '' }}>Hibah Masuk</option>
                                        <option value="Pemindahan masuk" {{ old('jenis_transaksi_masuk') == 'Pemindahan masuk' ? 'selected' : '' }}>Pemindahan Masuk</option>
                                        <option value="Pinjaman" {{ old('jenis_transaksi_masuk') == 'Pinjaman' ? 'selected' : '' }}>Pinjaman</option>
                                        <option value="Sewa" {{ old('jenis_transaksi_masuk') == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-slate-700">Lampiran Bukti (Kuitansi / BA) <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <label class="d-flex align-items-center bg-white border border-slate-200 rounded-3 p-3 cursor-pointer shadow-sm w-100 hover-border-primary transition-all">
                                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-cloud-arrow-up-fill fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="d-block fw-bold text-slate-700">Pilih File...</span>
                                                <span class="d-block small text-slate-500">Maksimal 2MB. Format: JPG, PNG, PDF</span>
                                            </div>
                                            <input type="file" class="d-none" name="bukti_transaksi_masuk" accept=".jpg,.jpeg,.png,.pdf" onchange="this.previousElementSibling.firstElementChild.textContent = this.files[0].name">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-5">
                                    <div class="p-4 bg-white shadow-sm rounded-4 border border-slate-200 relative">
                                        <label class="form-label fw-bold text-dark mb-4 d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-3">
                                                <i class="bi bi-person-bounding-box fs-5"></i>
                                            </div>
                                            Pihak yang Menyerahkan Aset <span class="text-danger ms-1">*</span>
                                        </label>
                                        
                                        <div class="mb-4 mt-2 d-flex flex-wrap gap-4 px-3 py-3 rounded-3 bg-slate-50 border border-slate-100">
                                            <div class="form-check custom-radio d-flex align-items-center">
                                                <input class="form-check-input me-2" type="radio" name="is_pihak_kedua_karyawan_masuk" id="karyawanYa" value="1" {{ old('is_pihak_kedua_karyawan_masuk') == '1' ? 'checked' : '' }} required style="transform: scale(1.3);">
                                                <label class="form-check-label fw-bold text-slate-700 mt-1 cursor-pointer" for="karyawanYa">Pegawai (Internal Yayasan)</label>
                                            </div>
                                            <div class="form-check custom-radio d-flex align-items-center ms-md-4">
                                                <input class="form-check-input me-2" type="radio" name="is_pihak_kedua_karyawan_masuk" id="karyawanTidak" value="0" {{ old('is_pihak_kedua_karyawan_masuk', '0') == '0' ? 'checked' : '' }} required style="transform: scale(1.3);">
                                                <label class="form-check-label fw-bold text-slate-700 mt-1 cursor-pointer" for="karyawanTidak">Toko / Pihak Luar</label>
                                            </div>
                                        </div>

                                        <div id="formPihakKeduaDalam" style="display: none; transition: all 0.3s ease;">
                                            <div class="row">
                                                <div class="col-md-6 border-start border-4 border-emerald ps-4 ms-3">
                                                    <label class="form-label fw-bold text-emerald mb-2">Nomor Induk Yayasan (NIY) / NIK <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-lg bg-light border-slate-200" id="inputNikPihak" name="nik_pihak_kedua_masuk" placeholder="Masukkan NIK / NIY Pegawai..." value="{{ old('nik_pihak_kedua_masuk') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="formPihakKeduaLuar" style="transition: all 0.3s ease;">
                                            <div class="row g-4 border-start border-4 border-slate-300 ps-4 ms-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-slate-700 mb-2">Nama Toko / Pihak Luar <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-lg bg-light border-slate-200" id="inputNamaPihak" name="nama_pihak_kedua_masuk" placeholder="PT. ABC Nusantara..." value="{{ old('nama_pihak_kedua_masuk') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-slate-700 mb-2">Alamat Lengkap <span class="text-danger">*</span></label>
                                                    <textarea class="form-control bg-light border-slate-200 rounded-3" id="inputAlamatPihak" name="alamat_pihak_kedua_masuk" rows="2" placeholder="Jl. Raya Kemerdekaan No.12...">{{ old('alamat_pihak_kedua_masuk') }}</textarea>
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
                    <a href="{{ route('assets.index') }}" class="btn btn-light fw-bold px-5 py-3 rounded-pill shadow-sm border border-slate-200 text-slate-600 w-100 w-md-auto">
                        <i class="bi bi-x-lg me-2"></i> Batalkan & Kembali
                    </a>
                    <button type="submit" class="btn fw-bold px-5 py-3 rounded-pill shadow-lg w-100 w-md-auto fs-6 text-white" style="background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-800) 100%); border: none;">
                        <i class="bi bi-floppy me-2"></i> Simpan Data Aset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Toggle Pihak Kedua -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radKaryawanYa = document.getElementById('karyawanYa');
            const radKaryawanTidak = document.getElementById('karyawanTidak');
            const formPihakKeduaLuar = document.getElementById('formPihakKeduaLuar');
            const formPihakKeduaDalam = document.getElementById('formPihakKeduaDalam');
            
            const inputNikPihak = document.getElementById('inputNikPihak');
            const inputNamaPihak = document.getElementById('inputNamaPihak');
            const inputAlamatPihak = document.getElementById('inputAlamatPihak');

            function togglePihakKedua() {
                if (radKaryawanYa.checked) {
                    formPihakKeduaDalam.style.display = 'block';
                    inputNikPihak.required = true;
                    
                    formPihakKeduaLuar.style.display = 'none';
                    inputNamaPihak.required = false;
                    inputAlamatPihak.required = false;
                } else {
                    formPihakKeduaDalam.style.display = 'none';
                    inputNikPihak.required = false;
                    
                    formPihakKeduaLuar.style.display = 'block';
                    inputNamaPihak.required = true;
                    inputAlamatPihak.required = true;
                }
            }

            radKaryawanYa.addEventListener('change', togglePihakKedua);
            radKaryawanTidak.addEventListener('change', togglePihakKedua);
            togglePihakKedua();

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
                hrgInput.addEventListener('input', function(e) {
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
