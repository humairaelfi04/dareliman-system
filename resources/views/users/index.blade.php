@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

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
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center py-4 px-4 border-bottom border-slate-100">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 d-flex justify-content-center align-items-center shadow-sm">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark letter-spacing-tight">Manajemen Pengguna</h5>
                    <p class="text-slate-500 small mb-0 fw-medium">Kelola akun dan role hak akses sistem.</p>
                </div>
            </div>

            <div class="d-flex gap-3 align-items-center">
                <button type="button" class="btn btn-primary shadow-sm fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill me-2 fw-bolder"></i> Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive border-0 border-radius-0 mb-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="text-center ps-4" style="width: 60px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role / Hak Akses</th>
                            <th class="text-center pe-4" style="min-width: 120px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="text-center ps-4 text-slate-400 fw-bold">{{ $index + 1 }}</td>
                                <td class="fw-bold text-slate-800">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-2"><i class="bi bi-shield-lock-fill me-1"></i> Administrator (Super Admin)</span>
                                    @elseif($user->role === 'kepala_sekolah')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2"><i class="bi bi-person-check-fill me-1"></i> Kepala Sekolah</span>
                                    @elseif($user->role === 'bendahara')
                                        <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle rounded-pill px-3 py-2"><i class="bi bi-wallet-fill me-1"></i> Bendahara</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2"><i class="bi bi-box-seam-fill me-1"></i> Sarpras</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-warning rounded-circle" style="width: 34px; height: 34px;" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit Pengguna">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        onsubmit="event.preventDefault(); Swal.fire({title: 'Hapus Pengguna?', text: 'Anda yakin ingin menghapus akun ini secara permanen?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Hapus!'}).then((result) => { if (result.isConfirmed) { this.submit(); } })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger rounded-circle ms-1" style="width: 34px; height: 34px;" title="Hapus Pengguna">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header pt-4 pb-3 px-4 bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="addUserModalLabel">
                        <i class="bi bi-person-plus-fill fs-4 me-3"></i> Tambah Pengguna Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-white" name="name" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg bg-white" name="email" required placeholder="admin@sekolah.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-lg bg-white" name="password" required minlength="8" placeholder="Minimal 8 karakter">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Role / Hak Akses <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg bg-white" name="role" required>
                                <option value="">-- Pilih Role Akses --</option>
                                <option value="sarpras">Sarpras</option>
                                <option value="bendahara">Bendahara</option>
                                <option value="kepala_sekolah">Kepsek</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top border-slate-100 px-4 py-3 d-flex">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm flex-grow-1 ms-3">
                            <i class="bi bi-save-fill me-2"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header pt-4 pb-3 px-4 bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="editUserModalLabel{{ $user->id }}">
                        <i class="bi bi-pencil-square fs-4 me-3"></i> Edit Data Pengguna
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-white" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg bg-white" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Ubah Password <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="password" class="form-control form-control-lg bg-white" name="password" minlength="8" placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-800 fw-bold">Role / Hak Akses <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg bg-white" name="role" required>
                                @if($user->role === 'admin')
                                    <option value="admin" selected>Administrator (Super Admin)</option>
                                @endif
                                <option value="sarpras" {{ $user->role == 'sarpras' ? 'selected' : '' }}>Sarpras</option>
                                <option value="bendahara" {{ $user->role == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                <option value="kepala_sekolah" {{ $user->role == 'kepala_sekolah' ? 'selected' : '' }}>Kepsek</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top border-slate-100 px-4 py-3 d-flex">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm flex-grow-1 ms-3">
                            <i class="bi bi-save-fill me-2"></i> Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection
