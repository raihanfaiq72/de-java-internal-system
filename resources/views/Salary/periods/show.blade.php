@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title">Detail Periode Gaji</h4>
                                <p class="text-muted small mb-0">
                                    {{ $salaryPeriod->name }} •
                                    {{ $salaryPeriod->start_date->format('d M Y') }} -
                                    {{ $salaryPeriod->end_date->format('d M Y') }}
                                </p>
                            </div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('salary-periods.index') }}">Periode Gaji</a>
                                </li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div
                        class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">Daftar Slip Gaji Karyawan</h6>
                        <div class="d-flex gap-2">
                            @if ($salaryPeriod->salarySlips->count() > 0)
                                <button type="button" class="btn btn-success fw-bold px-4 shadow-sm"
                                    onclick="openBulkPrint()">
                                    <i class="fa fa-print me-1"></i> Cetak Massal
                                </button>
                            @endif
                            @if ($salaryPeriod->status == 'open')
                                <button class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalCreateSlipOne">
                                    <i class="fa fa-plus-circle me-1"></i> Buat Gaji Karyawan
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Jabatan</th>
                                        <th>Gaji Pokok</th>
                                        <th>Total Gaji</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($salaryPeriod->salarySlips as $slip)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $slip->employee->name }}</div>
                                                <small class="text-muted">{{ $slip->employee->nik }}</small>
                                            </td>
                                            <td>{{ $slip->employee->position }}</td>
                                            <td>Rp {{ number_format($slip->basic_salary, 0, ',', '.') }}</td>
                                            <td class="fw-bold text-success">Rp
                                                {{ number_format($slip->total_salary, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $slip->status == 'published' ? 'success' : ($slip->status == 'paid' ? 'primary' : 'warning') }} rounded-pill px-3">
                                                    {{ ucfirst($slip->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold"
                                                        onclick='openEditSlip(@json($slip))'>
                                                        Edit
                                                    </button>
                                                    <button type="button" onclick="openPrintPreview({{ $slip->id }})"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fa fa-print"></i>
                                                    </button>
                                                    @if ($salaryPeriod->status == 'open' && $slip->status !== 'paid')
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteSlip('{{ $slip->id }}', '{{ $slip->employee->name }}')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                @if ($salaryPeriod->status == 'open' && $slip->status !== 'paid')
                                                    <form id="delete-slip-{{ $slip->id }}"
                                                        action="{{ route('salary-slips.destroy', $slip->id) }}"
                                                        method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <div class="mb-2"><i
                                                        class="fa fa-file-lines fs-1 text-secondary opacity-50"></i>
                                                </div>
                                                Belum ada slip gaji.<br>Silakan klik tombol "Buat Gaji Karyawan".
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Single Slip Modal -->
    <div class="modal-fade" id="modalCreateSlipOne" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="form-create-slip-one" method="POST"
                    action="{{ route('salary-periods.slips.store-one', $salaryPeriod->id) }}">
                    @csrf
                    <div class="modal-header px-5 py-4">
                        <h5 class="fw-bold mb-0">Buat Gaji Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Nama Karyawan</label>
                                <select class="form-select" name="employee_id" id="create_employee_id" required>
                                    <option value="">-Pilih karyawan-</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" data-position="{{ $emp->position }}"
                                            data-daily="{{ $emp->daily_salary ?? 0 }}"
                                            data-premi="{{ $emp->premi ?? 0 }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Hari Kerja</label>
                                <input type="number" min="0" class="form-control" name="work_days"
                                    id="create_work_days" placeholder="Hari Kerja">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Gaji/Hari</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        id="create_daily_basic_salary_display" value="0" placeholder="0"
                                        onkeyup="syncInput(this)">
                                    <input type="hidden" min="0" step="0.01"
                                        class="real-value slip-one-input" data-field="daily_basic_salary"
                                        name="daily_basic_salary" id="create_daily_basic_salary" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Tunjangan Makan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        value="0" placeholder="0" onkeyup="syncInput(this)">
                                    <input type="hidden" min="0" step="0.01"
                                        class="real-value slip-one-input" data-field="meal_allowance"
                                        name="meal_allowance" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Premi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        id="create_premi_display" value="0" placeholder="0"
                                        onkeyup="syncInput(this)">
                                    <input type="hidden" min="0" step="0.01"
                                        class="real-value slip-one-input" data-field="premi" name="premi"
                                        id="create_premi" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Pot. Telat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        value="0" placeholder="0" onkeyup="syncInput(this)">
                                    <input type="hidden" min="0" step="0.01"
                                        class="real-value slip-one-input" data-field="late_deduction"
                                        name="late_deduction" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Pot. Lainnya</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0 ps-0 rupiah-input"
                                        value="0" placeholder="0" onkeyup="syncInput(this)">
                                    <input type="hidden" min="0" step="0.01"
                                        class="real-value slip-one-input" data-field="other_deduction"
                                        name="other_deduction" value="0">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_confirm" required>
                                    <label class="form-check-label" for="create_confirm">Data Sudah Benar</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-light p-3 rounded-3 text-end border">
                                    <label class="small fw-bold text-muted me-2">Diterima</label>
                                    <span id="create_take_home" class="fs-5 fw-bold text-success">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Slip Modal -->
    <div class="modal fade" id="modalEditSlip" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="form-edit-slip" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold mb-0" id="editSlipTitle">Edit Gaji Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Hari Kerja</label>
                                <input type="number" min="0" id="edit_work_days" name="work_days"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Gaji/Hari</label>
                                <input type="number" min="0" step="0.01" id="edit_daily_basic_salary"
                                    name="daily_basic_salary" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Premi</label>
                                <input type="number" min="0" step="0.01" id="edit_premi" name="premi"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Uang Makan</label>
                                <input type="number" min="0" step="0.01" id="edit_meal_allowance"
                                    name="meal_allowance" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Pot. Telat</label>
                                <input type="number" min="0" step="0.01" id="edit_late_deduction"
                                    name="late_deduction" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Pot. Lainnya</label>
                                <input type="number" min="0" step="0.01" id="edit_other_deduction"
                                    name="other_deduction" class="form-control">
                            </div>
                            <div class="col-12 text-end">
                                <label class="small fw-bold text-muted">Diterima</label>
                                <div id="edit_take_home" class="fs-5 fw-bold text-success">Rp 0</div>
                                <input type="hidden" name="basic_salary" id="edit_basic_salary_hidden" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Print Modal -->
    <div class="modal fade" id="modalBulkPrint" tabindex="-1" aria-labelledby="modalBulkPrintLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalBulkPrintLabel">
                        <i class="fa fa-print text-success me-2"></i> Cetak Massal Slip Gaji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 ">
                    <div class="ratio ratio-16x9 border rounded bg-light" style="min-height: 70vh;">
                        <iframe id="bulk-print-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success fw-bold px-4 shadow-sm" onclick="triggerBulkPrint()">
                        <i class="fa fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-9 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalPrintPreviewLabel">
                        <i class="iconoir-printing-page text-primary me-2"></i> Preview Cetak Slip Gaji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 ">
                    <div class="ratio ratio-16x9 border rounded bg-light" style="min-height: 70vh;">
                        <iframe id="print-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" onclick="triggerPrint()">
                        <i class="fa fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        function fmtIDR(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v));
        }

        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
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
                hiddenInput.value = cleanNumber(displayInput.value) || 0;
            }

            if (typeof recalcCreate === 'function') {
                recalcCreate();
            }
        }

        function openBulkPrint() {
            const url = `{{ url('salary-periods/:id/bulk-print') }}/`.replace(':id', {{ $salaryPeriod->id }});
            const modalContainer = document.getElementById('modalBulkPrint');

            if (!modalContainer) {
                console.error('Modal container tidak ditemukan di halaman ini.');
                return;
            }

            const iframe = modalContainer.querySelector('iframe');

            if (!iframe) {
                console.error('Elemen iframe tidak ditemukan di dalam modal.');
                alert('Gagal memuat preview cetak massal.');
                return;
            }

            iframe.src = url;

            const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
            bModal.show();
        }

        function triggerBulkPrint() {
            const iframe = document.getElementById('bulk-print-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        function openPrintPreview(id) {
            const url = `{{ url('salary-slips/:id/print') }}/`.replace(':id', id);
            const modalContainer = document.getElementById('modalPrintPreview');

            if (!modalContainer) {
                console.error('Modal container tidak ditemukan di halaman ini.');
                return;
            }

            const iframe = modalContainer.querySelector('iframe');

            if (!iframe) {
                console.error('Elemen iframe tidak ditemukan di dalam modal.');
                alert('Gagal memuat preview cetak.');
                return;
            }

            iframe.src = url;

            const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
            bModal.show();
        }

        function triggerPrint() {
            const iframe = document.getElementById('print-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        function recalcCreate() {
            const workDays = parseFloat(document.getElementById('create_work_days')?.value || 0);
            const daily = parseFloat(document.getElementById('create_daily_basic_salary')?.value || 0);
            const premi = parseFloat(document.querySelector('[data-field="premi"]')?.value || 0);
            const meal = parseFloat(document.querySelector('[data-field="meal_allowance"]')?.value || 0);
            const late = parseFloat(document.querySelector('[data-field="late_deduction"]')?.value || 0);
            const other = parseFloat(document.querySelector('[data-field="other_deduction"]')?.value || 0);
            const basicTotal = workDays * daily;
            const takeHome = basicTotal + premi + meal - late - other;
            document.getElementById('create_take_home').textContent = fmtIDR(takeHome);
        }

        function applyEmployeeDefaults() {
            const select = document.getElementById('create_employee_id');
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            if (!opt) return;

            const daily = parseFloat(opt.dataset.daily || 0);
            const premi = parseFloat(opt.dataset.premi || 0);

            const dailyHidden = document.getElementById('create_daily_basic_salary');
            const dailyDisplay = document.getElementById('create_daily_basic_salary_display');
            if (dailyHidden && dailyDisplay) {
                dailyHidden.value = daily;
                dailyDisplay.value = formatRupiah(String(Math.round(daily)));
            }

            const premiHidden = document.getElementById('create_premi');
            const premiDisplay = document.getElementById('create_premi_display');
            if (premiHidden && premiDisplay) {
                premiHidden.value = premi;
                premiDisplay.value = formatRupiah(String(Math.round(premi)));
            }

            recalcCreate();
        }

        async function deleteSlip(id, name) {
            if (typeof macConfirm === 'function') {
                const confirmed = await macConfirm(
                    'Hapus Slip Gaji?',
                    `Apakah Anda yakin ingin menghapus slip gaji untuk "${name}"?`, {
                        confirmText: 'Hapus',
                        confirmType: 'danger',
                        cancelText: 'Batal'
                    }
                );
                if (confirmed) document.getElementById(`delete-slip-${id}`)?.submit();
            } else {
                if (confirm(`Hapus slip gaji untuk ${name}?`)) {
                    document.getElementById(`delete-slip-${id}`)?.submit();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('create_employee_id');
            if (employeeSelect) {
                new TomSelect(employeeSelect, {
                    create: false,
                    maxOptions: 5000,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    }
                });
                employeeSelect.addEventListener('change', applyEmployeeDefaults);
            }

            document.getElementById('create_work_days')?.addEventListener('input', recalcCreate);
            document.querySelectorAll('#modalCreateSlipOne .rupiah-input').forEach((el) => {
                el.addEventListener('input', recalcCreate);
            });

            applyEmployeeDefaults();
            recalcCreate();
        });

        // Edit Slip modal
        let editSlipId = null;

        function openEditSlip(slip) {
            editSlipId = slip.id;
            document.getElementById('editSlipTitle').textContent = `Edit Gaji: ${slip.employee.name}`;
            const det = slip.details || {};
            document.getElementById('edit_work_days').value = det.work_days ?? 0;
            document.getElementById('edit_daily_basic_salary').value = det.daily_rate ?? 0;
            document.getElementById('edit_premi').value = slip.premi ?? 0;
            document.getElementById('edit_meal_allowance').value = slip.meal_allowance ?? 0;
            document.getElementById('edit_late_deduction').value = slip.late_deduction ?? 0;
            document.getElementById('edit_other_deduction').value = slip.other_deduction ?? 0;
            recalcEdit();
            const modal = new bootstrap.Modal(document.getElementById('modalEditSlip'));
            modal.show();
            // Set form action
            document.getElementById('form-edit-slip').action = `{{ url('salary-slips') }}/${slip.id}`;
        }

        function recalcEdit() {
            const workDays = parseFloat(document.getElementById('edit_work_days').value || 0);
            const daily = parseFloat(document.getElementById('edit_daily_basic_salary').value || 0);
            const premi = parseFloat(document.getElementById('edit_premi').value || 0);
            const meal = parseFloat(document.getElementById('edit_meal_allowance').value || 0);
            const late = parseFloat(document.getElementById('edit_late_deduction').value || 0);
            const other = parseFloat(document.getElementById('edit_other_deduction').value || 0);
            const basicTotal = workDays * daily;
            const takeHome = basicTotal + premi + meal - late - other;
            document.getElementById('edit_take_home').textContent = fmtIDR(takeHome);
            document.getElementById('edit_basic_salary_hidden').value = basicTotal;
        }
        ['edit_work_days', 'edit_daily_basic_salary', 'edit_premi', 'edit_meal_allowance', 'edit_late_deduction',
            'edit_other_deduction'
        ].forEach(id => {
            document.getElementById(id).addEventListener('input', recalcEdit);
        });
    </script>
@endpush
