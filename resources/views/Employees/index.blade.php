@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Manajemen Karyawan</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Employees</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0 fw-bold text-dark">Daftar Karyawan</h5>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary fw-bold me-3 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <i class="fa fa-plus-circle me-1"></i> Tambah Karyawan
                                </button>
                                <form action="{{ route('employees.index') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" name="search"
                                            class="form-control form-control-sm border-end-0"
                                            placeholder="Cari nama atau NIK..." value="{{ request('search') }}">
                                        <button class="btn btn-sm btn-secondary border-start-0 ps-3 pe-3" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
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
                                                <span
                                                    class="badge bg-{{ $employee->status == 'active' ? 'success' : 'secondary' }} rounded-pill px-3">
                                                    {{ $employee->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($employee->user)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <span
                                                                class="avatar-title rounded-circle bg-light text-primary font-size-10">
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
                                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $employee->id }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->name }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                <form id="delete-{{ $employee->id }}"
                                                    action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $employee->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <form action="{{ route('employees.update', $employee->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold">Edit Karyawan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-bold small text-uppercase text-muted">NIK</label>
                                                                <input type="text" name="nik" class="form-control"
                                                                    value="{{ $employee->nik }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-bold small text-uppercase text-muted">Nama</label>
                                                                <input type="text" name="name" class="form-control"
                                                                    value="{{ $employee->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-bold small text-uppercase text-muted">Jabatan</label>
                                                                <input type="text" name="position" class="form-control"
                                                                    value="{{ $employee->position }}" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label
                                                                        class="form-label fw-bold small text-uppercase text-muted">Gaji
                                                                        Harian</label>
                                                                    <div class="input-group">
                                                                        <span
                                                                            class="input-group-text bg-light border-end-0">Rp</span>
                                                                        <input type="text"
                                                                            class="form-control border-start-0 ps-0 rupiah-input"
                                                                            value="{{ number_format($employee->daily_salary, 0, ',', '.') }}"
                                                                            placeholder="0" onkeyup="syncInput(this)">

                                                                        <input type="hidden" name="daily_salary"
                                                                            class="real-value"
                                                                            value="{{ $employee->daily_salary }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label
                                                                        class="form-label fw-bold small text-uppercase text-muted">Premi</label>
                                                                    <div class="input-group">
                                                                        <span
                                                                            class="input-group-text bg-light border-end-0">Rp</span>
                                                                        <input type="text"
                                                                            class="form-control border-start-0 ps-0 rupiah-input"
                                                                            value="{{ number_format($employee->premi, 0, ',', '.') }}"
                                                                            placeholder="0" onkeyup="syncInput(this)">

                                                                        <input type="hidden" name="premi"
                                                                            class="real-value"
                                                                            value="{{ $employee->premi }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-bold small text-uppercase text-muted">Tanggal
                                                                    Bergabung</label>
                                                                <input type="date" name="join_date"
                                                                    class="form-control"
                                                                    value="{{ $employee->join_date ? $employee->join_date->format('Y-m-d') : '' }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-bold small text-uppercase text-muted">Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="active"
                                                                        {{ $employee->status == 'active' ? 'selected' : '' }}>
                                                                        Aktif</option>
                                                                    <option value="inactive"
                                                                        {{ $employee->status == 'inactive' ? 'selected' : '' }}>
                                                                        Non-Aktif</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-bold small text-uppercase text-muted">Hubungkan
                                                                    Akun User (Opsional)</label>
                                                                <select name="user_id" class="form-select">
                                                                    <option value="">-- Pilih User --</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->id }}"
                                                                            {{ $employee->user_id == $user->id ? 'selected' : '' }}>
                                                                            {{ $user->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                                                            <button type="button" class="btn btn-light fw-bold"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary fw-bold px-4">Simpan
                                                                Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <div class="mb-2"><i
                                                        class="fa fa-user fs-1 text-secondary opacity-50"></i>
                                                </div>
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
                            <input type="text" name="nik" class="form-control" required
                                placeholder="Contoh: 2024001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nama</label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="Nama Lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Jabatan</label>
                            <input type="text" name="position" class="form-control" required
                                placeholder="Contoh: Staff Admin">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Gaji
                                    Harian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        value="0" placeholder="0" onkeyup="syncInput(this)">

                                    <input type="hidden" name="daily_salary" class="real-value" value="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Premi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        value="0" placeholder="0" onkeyup="syncInput(this)">

                                    <input type="hidden" name="premi" class="real-value" value="0">
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
                            <label class="form-label fw-bold small text-uppercase text-muted">Hubungkan Akun User
                                (Opsional)</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
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

@push('js')
    <script>
        function formatRupiah(angka) {
            if (!angka) return '';
            let number_string = String(angka).replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        function cleanNumber(angka) {
            if (!angka) return '';
            let cleaned = String(angka).replace(/[^0-9,]/g, '');
            return cleaned.replace(',', '.');
        }

        function syncInput(displayInput) {
            let formatted = formatRupiah(displayInput.value);
            displayInput.value = formatted;


            let hiddenInput = displayInput.parentElement.querySelector('.real-value');

            if (hiddenInput) {
                hiddenInput.value = cleanNumber(displayInput.value);
            }
        }

        async function deleteEmployee(id, name) {
            if (typeof macConfirm === 'function') {
                const confirmed = await macConfirm(
                    'Hapus Karyawan?',
                    `Apakah Anda yakin ingin menghapus karyawan "${name}"? Data ini tidak dapat dikembalikan.`, {
                        confirmText: 'Hapus',
                        confirmType: 'danger',
                        cancelText: 'Batal'
                    }
                );
                if (confirmed) document.getElementById(`delete-${id}`).submit();
            } else {
                if (confirm(`Hapus karyawan ${name}?`)) {
                    document.getElementById(`delete-${id}`).submit();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const createModalEl = document.getElementById('createModal');
            if (createModalEl) {
                createModalEl.addEventListener('hidden.bs.modal', function() {
                    const form = this.querySelector('form');
                    if (form) {
                        form.reset();

                        form.querySelectorAll('.rupiah-input').forEach(input => {
                            input.value = '0';
                        });

                        form.querySelectorAll('.real-value').forEach(input => {
                            input.value = '0';
                        });
                    }
                });
            }
        })
    </script>
@endpush
