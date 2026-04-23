@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <i class="iconoir-home dr-breadcrumb-icon"></i>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Data Karyawan</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Manajemen Karyawan</h1>
                            <p class="dr-subtitle">Kelola database karyawan, jabatan, dan informasi penggajian harian.</p>
                        </div>
                        <div class="dr-actions">
                            <form id="searchForm" action="{{ route('employees.index') }}" method="GET" class="d-flex gap-2">
                                <div class="position-relative" style="width: 250px;">
                                    <i class="iconoir-search text-muted position-absolute start-0 top-50 translate-middle-y ms-3" style="font-size: 16px;"></i>
                                    <input type="text" name="search" id="searchInput" class="dr-input ps-5" placeholder="Cari nama atau NIK..." value="{{ request('search') }}" autocomplete="off">
                                </div>
                                {{-- Tombol filter disembunyikan karena sudah otomatis --}}
                            </form>
                            <button type="button" class="dr-btn dr-btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="iconoir-plus-circle"></i> Tambah Karyawan
                            </button>
                        </div>
                    </div>

                    <div class="dr-card p-0 overflow-hidden bg-white">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="120" class="ps-4">NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>
                                        <th class="text-end">Gaji Harian</th>
                                        <th class="text-end">Premi</th>
                                        <th class="text-center">Status</th>
                                        <th>User Akun</th>
                                        <th width="120" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="dr-badge bg-soft-secondary text-secondary font-monospace">{{ $employee->nik }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $employee->name }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted small fw-medium">{{ $employee->position }}</span>
                                            </td>
                                            <td class="text-end fw-bold">
                                                <span class="small text-muted fw-normal me-1">Rp</span>{{ number_format($employee->daily_salary, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                <span class="small text-muted fw-normal me-1">Rp</span>{{ number_format($employee->premi, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if($employee->status == 'active')
                                                    <span class="dr-badge bg-soft-success text-success">Aktif</span>
                                                @else
                                                    <span class="dr-badge bg-soft-secondary text-secondary">Non-Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($employee->user)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar-text" style="width: 24px; height: 24px; font-size: 10px;">
                                                            {{ substr($employee->user->name, 0, 2) }}
                                                        </div>
                                                        <span class="small fw-medium text-dark">{{ $employee->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted italic small">- belum terhubung -</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button class="dr-btn-icon dr-btn-icon-edit" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $employee->id }}" title="Edit Karyawan">
                                                        <i class="iconoir-edit-pencil"></i>
                                                    </button>
                                                    <button type="button" class="dr-btn-icon dr-btn-icon-delete"
                                                        onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->name }}')" title="Hapus">
                                                        <i class="iconoir-trash"></i>
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
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content border-0 bg-white">
                                                    <form action="{{ route('employees.update', $employee->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <i class="iconoir-edit-pencil text-dark" style="font-size: 20px;"></i>
                                                                <div>
                                                                    <h5 class="dr-title mb-1" style="font-size: 16px;">Edit Data Karyawan</h5>
                                                                    <p class="dr-subtitle mb-0" style="font-size: 12px;">Perbarui profil dan detail operasional.</p>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body px-4 pb-4 pt-0 bg-white">
                                                            <div class="row g-4">
                                                                <!-- Kolom Kiri -->
                                                                <div class="col-md-6 border-end">
                                                                    <div class="d-flex align-items-center mb-4">
                                                                        <i class="iconoir-vcard text-dark me-2" style="font-size: 20px;"></i>
                                                                        <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Informasi Personal</h6>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="dr-label mb-2">NIK *</label>
                                                                        <input type="text" name="nik" class="dr-input font-monospace" value="{{ $employee->nik }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="dr-label mb-2">Nama Lengkap *</label>
                                                                        <input type="text" name="name" class="dr-input fw-bold" value="{{ $employee->name }}" required>
                                                                    </div>
                                                                    <div class="mb-0">
                                                                        <label class="dr-label mb-2">Jabatan / Posisi *</label>
                                                                        <input type="text" name="position" class="dr-input" value="{{ $employee->position }}" required>
                                                                    </div>
                                                                </div>

                                                                <!-- Kolom Kanan -->
                                                                <div class="col-md-6">
                                                                    <div class="d-flex align-items-center mb-4 ms-md-2">
                                                                        <i class="iconoir-reports text-dark me-2" style="font-size: 20px;"></i>
                                                                        <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Detail Operasional</h6>
                                                                    </div>
                                                                    <div class="ms-md-2">
                                                                        <div class="row g-3 mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="dr-label mb-2">Gaji Harian *</label>
                                                                                <div class="input-group">
                                                                                    <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                                                    <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" value="{{ number_format($employee->daily_salary, 0, ',', '.') }}" style="border-radius: 0 12px 12px 0;" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                                                                    <input type="hidden" name="daily_salary" class="real-value" value="{{ $employee->daily_salary }}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="dr-label mb-2">Premi Kehadiran *</label>
                                                                                <div class="input-group">
                                                                                    <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                                                    <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" value="{{ number_format($employee->premi, 0, ',', '.') }}" style="border-radius: 0 12px 12px 0;" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                                                                    <input type="hidden" name="premi" class="real-value" value="{{ $employee->premi }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row g-3 mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="dr-label mb-2">Join Date</label>
                                                                                <input type="date" name="join_date" class="dr-input" value="{{ $employee->join_date ? $employee->join_date->format('Y-m-d') : '' }}">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="dr-label mb-2">Status</label>
                                                                                <select name="status" class="dr-input" required>
                                                                                    <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                                                                    <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mb-0">
                                                                            <label class="dr-label mb-2">Hubungkan Akun User</label>
                                                                            <select name="user_id" class="dr-input">
                                                                                <option value="">-- Pilih User --</option>
                                                                                @foreach ($users as $user)
                                                                                    <option value="{{ $user->id }}" {{ $employee->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                                                            <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                                                                <i class="iconoir-xmark text-danger"></i> Batal
                                                            </button>
                                                            <button type="submit" class="dr-btn-modal">
                                                                <i class="iconoir-check text-success"></i> Simpan Perubahan
                                                            </button>
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

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 bg-white border-top">
                            <span id="pagination-info" class="text-muted small fw-medium">
                                Menampilkan {{ $employees->firstItem() }} sampai {{ $employees->lastItem() }} dari {{ $employees->total() }} data
                            </span>
                            <nav>
                                {{ $employees->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 bg-white">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="modal-header px-4 py-4 d-flex align-items-center justify-content-between border-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="iconoir-user-plus" style="font-size: 20px;"></i>
                            <div>
                                <h5 class="dr-title mb-1" style="font-size: 16px;">Tambah Karyawan Baru</h5>
                                <p class="dr-subtitle mb-0" style="font-size: 12px;">Lengkapi data personal dan sistem penggajian.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 pb-4 pt-0">
                        <div class="row g-4">
                            <!-- Kolom Kiri: Data Personal -->
                            <div class="col-md-6 border-end">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="iconoir-vcard me-2" style="font-size: 20px;"></i>
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Informasi Personal</h6>
                                </div>
                                <div class="mb-3">
                                    <label class="dr-label mb-2">NIK *</label>
                                    <input type="text" name="nik" class="dr-input font-monospace" required placeholder="Contoh: 2024001">
                                </div>
                                <div class="mb-3">
                                    <label class="dr-label mb-2">Nama Lengkap *</label>
                                    <input type="text" name="name" class="dr-input fw-bold" required placeholder="Nama Sesuai KTP">
                                </div>
                                <div class="mb-0">
                                    <label class="dr-label mb-2">Jabatan / Posisi *</label>
                                    <input type="text" name="position" class="dr-input" required placeholder="Contoh: Staff Produksi">
                                </div>
                            </div>

                            <!-- Kolom Kanan: Penggajian & Sistem -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4 ms-md-2">
                                    <i class="iconoir-reports text-dark me-2" style="font-size: 20px;"></i>
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Detail Operasional</h6>
                                </div>
                                <div class="ms-md-2">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2">Gaji Harian *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" value="0" style="border-radius: 0 12px 12px 0;" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                                <input type="hidden" name="daily_salary" class="real-value" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2">Premi Kehadiran *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" value="0" style="border-radius: 0 12px 12px 0;" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                                <input type="hidden" name="premi" class="real-value" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2">Join Date</label>
                                            <input type="date" name="join_date" class="dr-input" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2">Status</label>
                                            <select name="status" class="dr-input" required>
                                                <option value="active" selected>Aktif</option>
                                                <option value="inactive">Non-Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="dr-label mb-2">Hubungkan Akun User</label>
                                        <select name="user_id" class="dr-input">
                                            <option value="">-- Pilih User --</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                        <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                            <i class="iconoir-xmark text-danger"></i> Batal
                        </button>
                        <button type="submit" class="dr-btn-modal">
                            <i class="iconoir-check text-success"></i> Simpan Karyawan
                        </button>
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

        function clearZero(input) {
            if (input.value === '0') {
                input.value = '';
            }
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
        // Dynamic Search
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchForm.submit();
                }, 500); // Tunggu 500ms setelah berhenti mengetik
            });

            // Fokuskan cursor ke akhir teks saat halaman load (jika ada input search)
            if (searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }
    </script>
@endpush
