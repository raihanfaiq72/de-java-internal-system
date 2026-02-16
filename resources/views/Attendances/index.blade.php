@extends('Layout.main')


@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control {
        border: none !important;
        background: transparent !important;
        padding-left: 10px !important;
        box-shadow: none !important;
        min-width: 200px;
    }

    .ts-dropdown {
        z-index: 9999 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }

    .ts-wrapper.form-select-sm .ts-control {
        padding-top: 2px !important;
        padding-bottom: 2px !important;
        min-height: auto !important;
    }

    .ts-wrapper.single .ts-control:after {
        border-color: #64748b transparent transparent transparent !important;
        border-width: 4px 4px 0 4px !important;
    }

    .ts-wrapper.dropdown-active {
        z-index: 10000 !important;
    }
</style>
@endpush


@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Data Absensi</h4>
                        <p class="text-muted small mb-0">Kelola data kehadiran, sakit, izin, dan keterlambatan karyawan.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Absensi
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="mb-0 fw-bold text-dark">Riwayat Absensi</h6>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <form action="{{ route('attendances.index') }}" method="GET" class="row g-2">
                                        <div class="col-auto">
                                            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', date('Y-m-d')) }}">
                                        </div>
                                        <div class="col-auto">
                                            <select name="employee_id" id="filter-employee" class="form-select form-select-sm">
                                                <option value="">Semua Karyawan</option>
                                                @foreach($employees->sortBy('name') as $emp)
                                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                        {{ $emp->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-sm btn-light border fw-bold" type="submit">
                                                <i class="fa fa-filter me-1"></i> Filter
                                            </button>
                                        </div>
                                    </form>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('attendances.template') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-file-excel-o me-1"></i> Download Template
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <i class="fa fa-upload me-1"></i> Import Dari Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Karyawan</th>
                                        <th>Status</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Catatan</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($attendances as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                        <td class="fw-bold text-dark">{{ $attendance->employee->name }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'present' => 'success',
                                                    'late' => 'warning',
                                                    'sick' => 'info',
                                                    'permission' => 'primary',
                                                    'alpha' => 'danger'
                                                ];
                                                $statusLabels = [
                                                    'present' => 'Hadir',
                                                    'late' => 'Terlambat',
                                                    'sick' => 'Sakit',
                                                    'permission' => 'Izin',
                                                    'alpha' => 'Alpha'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$attendance->status] ?? 'secondary' }} rounded-pill px-3">
                                                {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                                        <td class="text-muted small">{{ Str::limit($attendance->note, 30) ?: '-' }}</td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $attendance->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAttendance({{ $attendance->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-{{ $attendance->id }}" action="{{ route('attendances.destroy', $attendance->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-bottom-0 py-3 bg-dark">
                                                        <h5 class="modal-title fw-bold">Edit Absensi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Karyawan</label>
                                                            <select name="employee_id" class="form-select tom-select-edit" required>
                                                                @foreach($employees->sortBy('name') as $emp)
                                                                    <option value="{{ $emp->id }}" {{ $attendance->employee_id == $emp->id ? 'selected' : '' }}>
                                                                        {{ $emp->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal</label>
                                                            <input type="date" name="date" class="form-control" value="{{ $attendance->date->format('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Hadir</option>
                                                                <option value="late" {{ $attendance->status == 'late' ? 'selected' : '' }}>Terlambat</option>
                                                                <option value="sick" {{ $attendance->status == 'sick' ? 'selected' : '' }}>Sakit</option>
                                                                <option value="permission" {{ $attendance->status == 'permission' ? 'selected' : '' }}>Izin</option>
                                                                <option value="alpha" {{ $attendance->status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                                            </select>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label fw-bold small text-uppercase text-muted">Jam Masuk</label>
                                                                <input type="time" name="clock_in" class="form-control" value="{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label fw-bold small text-uppercase text-muted">Jam Keluar</label>
                                                                <input type="time" name="clock_out" class="form-control" value="{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Catatan</label>
                                                            <textarea name="note" class="form-control" rows="2">{{ $attendance->note }}</textarea>
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
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="fa fa-calendar fs-1 text-secondary opacity-50"></i></div>
                                            Belum ada data absensi untuk filter yang dipilih.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            {{ $attendances->links() }}
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
                <form action="{{ route('attendances.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 py-3 bg-dark">
                        <h5 class="modal-title fw-bold">Tambah Absensi Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Karyawan</label>
                            <select name="employee_id" id="create-employee" class="form-select" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($employees->sortBy('name') as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="present">Hadir</option>
                                <option value="late">Terlambat</option>
                                <option value="sick">Sakit</option>
                                <option value="permission">Izin</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Jam Masuk</label>
                                <input type="time" name="clock_in" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Jam Keluar</label>
                                <input type="time" name="clock_out" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Catatan</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
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

    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('attendances.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 py-3 bg-dark">
                        <h5 class="modal-title fw-bold">Import Absensi dari Finger</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">File Template</label>
                            <input type="file" name="file" class="form-control" accept=".csv" required>
                            <div class="form-text text-muted small">
                                Gunakan format dari tombol Download Template. Kolom: employee_nik, date (YYYY-MM-DD), clock_in, clock_out, status, note.
                            </div>
                        </div>
                        <div class="alert alert-light border-start border-4 border-info small mb-0">
                            Contoh status: present, late, sick, permission, alpha.
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#filter-employee', {
            create: false,
            placeholder: 'Semua Karyawan',
            allowEmptyOption: true,
        });

        new TomSelect('#create-employee', {
            create: false,
            placeholder: 'Pilih Karyawan',
            dropdownParent: 'body' 
        });

        document.querySelectorAll('.tom-select-edit').forEach((el) => {
            new TomSelect(el, {
                create: false,
                placeholder: 'Pilih Karyawan',
                dropdownParent: 'body' 
            });
        });
    });

    async function deleteAttendance(id) {
        if (await macConfirm('Hapus Absensi', 'Apakah Anda yakin ingin menghapus data absensi ini?')) {
            document.getElementById('delete-' + id).submit();
        }
    }
</script>
@endpush
