@extends('Layout.main')

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
                            <i class="mdi mdi-plus me-1"></i> Tambah Absensi
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
                                <form action="{{ route('attendances.index') }}" method="GET" class="row g-2 justify-content-md-end">
                                    <div class="col-auto">
                                        <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-auto">
                                        <select name="employee_id" class="form-select form-select-sm">
                                            <option value="">Semua Karyawan</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-light border fw-bold" type="submit">
                                            <i class="mdi mdi-filter-variant"></i> Filter
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
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Yakin ingin menghapus?')) document.getElementById('delete-{{ $attendance->id }}').submit()">
                                                    <i class="mdi mdi-trash-can"></i>
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
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Edit Absensi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Karyawan</label>
                                                            <select name="employee_id" class="form-select" required>
                                                                @foreach($employees as $emp)
                                                                    <option value="{{ $emp->id }}" {{ $attendance->employee_id == $emp->id ? 'selected' : '' }}>
                                                                        {{ $emp->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal</label>
                                                            <input type="date" name="date" class="form-control" value="{{ $attendance->date }}" required>
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
                                            <div class="mb-2"><i class="mdi mdi-calendar-blank-outline fs-1 text-secondary opacity-50"></i></div>
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
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Absensi Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Karyawan</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($employees as $emp)
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
@endsection
