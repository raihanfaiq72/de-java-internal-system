@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Data Karyawan</h4>
                        <p class="text-muted small mb-0">Kelola data karyawan, gaji, dan akun user terkait.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="mdi mdi-plus me-1"></i> Tambah Karyawan
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-0 fw-bold text-dark">Daftar Karyawan</h6>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('employees.index') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control form-control-sm border-end-0" placeholder="Cari nama atau NIK..." value="{{ request('search') }}">
                                        <button class="btn btn-sm btn-outline-secondary border-start-0 ps-3 pe-3" type="submit">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Gaji Harian</th>
                                        <th>Premi</th>
                                        <th>Status</th>
                                        <th>User Akun</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($employees as $employee)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $employee->nik }}</td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->position }}</td>
                                        <td>Rp {{ number_format($employee->daily_salary, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($employee->premi, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }} rounded-pill px-3">
                                                {{ $employee->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($employee->user)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-light text-primary font-size-10">
                                                            {{ substr($employee->user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span>{{ $employee->user->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $employee->id }}">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Yakin ingin menghapus?')) document.getElementById('delete-{{ $employee->id }}').submit()">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                            <form id="delete-{{ $employee->id }}" action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Karyawan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">NIK</label>
                                                            <input type="text" name="nik" class="form-control" value="{{ $employee->nik }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Nama</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $employee->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Jabatan</label>
                                                            <input type="text" name="position" class="form-control" value="{{ $employee->position }}" required>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label fw-bold small text-uppercase text-muted">Gaji Harian</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                                                    <input type="number" name="daily_salary" class="form-control border-start-0 ps-0" value="{{ $employee->daily_salary }}" required min="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label fw-bold small text-uppercase text-muted">Premi</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                                                    <input type="number" name="premi" class="form-control border-start-0 ps-0" value="{{ $employee->premi }}" min="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Bergabung</label>
                                                            <input type="date" name="join_date" class="form-control" value="{{ $employee->join_date ? $employee->join_date->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                                                <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Hubungkan Akun User (Opsional)</label>
                                                            <select name="user_id" class="form-select">
                                                                <option value="">-- Pilih User --</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}" {{ $employee->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                                                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="mdi mdi-account-off-outline fs-1 text-secondary opacity-50"></i></div>
                                            Belum ada data karyawan yang ditemukan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Karyawan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">NIK</label>
                            <input type="text" name="nik" class="form-control" required placeholder="Contoh: 2024001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nama</label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama Lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Jabatan</label>
                            <input type="text" name="position" class="form-control" required placeholder="Contoh: Staff Admin">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Gaji Harian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" name="daily_salary" class="form-control border-start-0 ps-0" required min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Premi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" name="premi" class="form-control border-start-0 ps-0" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Bergabung</label>
                            <input type="date" name="join_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Hubungkan Akun User (Opsional)</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
