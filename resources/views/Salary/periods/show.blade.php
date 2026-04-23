@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-control { border-radius: 12px !important; }
    </style>
@endpush

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
                        <a href="{{ route('salary-periods.index') }}" class="text-decoration-none text-muted">Periode Gaji</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Detail Slip Gaji</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h4 class="dr-title">{{ $salaryPeriod->name }}</h4>
                            <p class="dr-subtitle">
                                <i class="iconoir-calendar me-1"></i>
                                {{ $salaryPeriod->start_date->format('d M Y') }} — {{ $salaryPeriod->end_date->format('d M Y') }}
                                <span class="mx-2">•</span>
                                @if($salaryPeriod->status == 'open')
                                    <span class="dr-badge bg-soft-success">Open</span>
                                @else
                                    <span class="dr-badge bg-soft-secondary">Closed</span>
                                @endif
                            </p>
                        </div>
                        <div class="dr-actions">
                            <form id="searchForm" action="{{ route('salary-periods.show', $salaryPeriod->id) }}" method="GET" class="d-flex gap-2">
                                <div class="position-relative" style="width: 250px;">
                                    <i class="iconoir-search text-muted position-absolute start-0 top-50 translate-middle-y ms-3" style="font-size: 16px;"></i>
                                    <input type="text" name="search" id="searchInput" class="dr-input ps-5" placeholder="Cari karyawan..." value="{{ request('search') }}" autocomplete="off">
                                </div>
                            </form>
                            @if ($salaryPeriod->salarySlips->count() > 0)
                                <button type="button" class="dr-btn dr-btn-outline" onclick="openBulkPrint()">
                                    <i class="iconoir-printing-page"></i> Cetak Massal
                                </button>
                            @endif
                            @if ($salaryPeriod->status == 'open')
                                <button class="dr-btn dr-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateSlipOne">
                                    <i class="iconoir-plus-circle"></i> Buat Gaji Karyawan
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Main Content Card -->
                    <div class="dr-card p-0 overflow-hidden bg-white mt-4">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Karyawan</th>
                                        <th>Jabatan</th>
                                        <th class="text-end">Gaji Pokok</th>
                                        <th class="text-end">Total Diterima</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $displaySlips = $salaryPeriod->salarySlips;
                                        if(request('search')) {
                                            $search = strtolower(request('search'));
                                            $displaySlips = $displaySlips->filter(function($s) use ($search) {
                                                return str_contains(strtolower($s->employee->name), $search) || 
                                                       str_contains(strtolower($s->employee->nik), $search);
                                            });
                                        }
                                    @endphp
                                    @forelse($displaySlips as $slip)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft-primary p-2 rounded-3 me-3">
                                                        <i class="iconoir-user text-primary" style="font-size: 18px;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark d-block">{{ $slip->employee->name }}</span>
                                                        <small class="text-muted font-monospace">{{ $slip->employee->nik }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted small fw-bold text-uppercase">{{ $slip->employee->position }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-dark fw-medium">Rp {{ number_format($slip->basic_salary, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-success fw-bold">Rp {{ number_format($slip->total_salary, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($slip->status == 'published')
                                                    <span class="dr-badge bg-soft-success">Published</span>
                                                @elseif($slip->status == 'paid')
                                                    <span class="dr-badge bg-soft-primary">Paid</span>
                                                @else
                                                    <span class="dr-badge bg-soft-warning">Draft</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="dr-btn-icon dr-btn-icon-edit"
                                                        onclick='openEditSlip(@json($slip))' title="Edit">
                                                        <i class="iconoir-edit-pencil"></i>
                                                    </button>
                                                    <button type="button" onclick="openPrintPreview({{ $slip->id }})"
                                                        class="dr-btn-icon dr-btn-icon-view" title="Cetak Preview">
                                                        <i class="iconoir-printing-page"></i>
                                                    </button>
                                                    @if ($salaryPeriod->status == 'open' && $slip->status !== 'paid')
                                                        <button type="button" class="dr-btn-icon dr-btn-icon-delete"
                                                            onclick="deleteSlip('{{ $slip->id }}', '{{ $slip->employee->name }}')" title="Hapus">
                                                            <i class="iconoir-trash"></i>
                                                        </button>
                                                        <form id="delete-slip-{{ $slip->id }}"
                                                            action="{{ route('salary-slips.destroy', $slip->id) }}"
                                                            method="POST" class="d-none">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="iconoir-reports text-muted mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                                                    <p class="text-muted mb-0">Data tidak ditemukan.</p>
                                                </div>
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
    <div class="modal fade" id="modalCreateSlipOne" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <form id="form-create-slip-one" method="POST"
                    action="{{ route('salary-periods.slips.store-one', $salaryPeriod->id) }}">
                    @csrf
                    <div class="modal-header bg-white border-bottom px-4 sticky-top d-flex align-items-center" 
                         style="padding-top: 20px !important; padding-bottom: 20px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary p-2 rounded-3 me-3">
                                <i class="iconoir-reports text-primary" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="dr-title mb-0" style="font-size: 18px;">Buat Gaji Karyawan</h5>
                                <p class="dr-subtitle mb-0" style="font-size: 13px;">Pilih karyawan dan tentukan rincian pendapatannya.</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="row g-4">
                            <!-- Kolom Kiri: Input Dasar -->
                            <div class="col-md-6 border-end">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="iconoir-vcard text-primary me-2" style="font-size: 20px;"></i>
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Informasi Dasar</h6>
                                </div>
                                <div class="mb-3">
                                    <label class="dr-label mb-2">Pilih Karyawan *</label>
                                    <select name="employee_id" id="create_employee_id" required>
                                        <option value="">- Cari Nama / NIK -</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}" data-position="{{ $emp->position }}"
                                                data-daily="{{ $emp->daily_salary ?? 0 }}"
                                                data-premi="{{ $emp->premi ?? 0 }}">{{ $emp->name }} ({{ $emp->nik }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="dr-label mb-2">Hari Kerja *</label>
                                        <input type="number" min="0" class="dr-input font-monospace" name="work_days"
                                            id="create_work_days" placeholder="Contoh: 26">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="dr-label mb-2">Gaji Per Hari *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input"
                                                id="create_daily_basic_salary_display" value="0" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                            <input type="hidden" name="daily_basic_salary" id="create_daily_basic_salary" class="real-value" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="dr-label mb-2">Premi Kehadiran</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                        <input type="text" class="dr-input border-start-0 ps-3 rupiah-input"
                                            id="create_premi_display" value="0" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                        <input type="hidden" name="premi" id="create_premi" class="real-value" data-field="premi" value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Tunjangan & Potongan -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4 ms-md-2">
                                    <i class="iconoir-reports text-primary me-2" style="font-size: 20px;"></i>
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Tambahan & Potongan</h6>
                                </div>
                                <div class="ms-md-2">
                                    <div class="mb-3">
                                        <label class="dr-label mb-2">Uang Makan / Lainnya</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" value="0" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                            <input type="hidden" name="meal_allowance" class="real-value" data-field="meal_allowance" value="0">
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2 text-danger">Potongan Telat</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 px-3 text-danger" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                <input type="text" class="dr-input border-start-0 ps-3 rupiah-input text-danger" value="0" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                                <input type="hidden" name="late_deduction" class="real-value" data-field="late_deduction" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2 text-danger">Potongan Lain</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 px-3 text-danger" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                <input type="text" class="dr-input border-start-0 ps-3 rupiah-input text-danger" value="0" onkeyup="syncInput(this)" onfocus="clearZero(this)">
                                                <input type="hidden" name="other_deduction" class="real-value" data-field="other_deduction" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Banner -->
                                    <div class="bg-soft-success p-3 rounded-4 border border-success border-opacity-10 d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1" style="font-size: 10px;">Total Diterima (Take Home Pay)</small>
                                            <span id="create_take_home" class="fs-4 fw-bold text-success">Rp 0</span>
                                        </div>
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" id="create_confirm" required>
                                            <label class="form-check-label small fw-bold text-dark" for="create_confirm">Konfirmasi Benar</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-end gap-2">
                        <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                            <i class="iconoir-xmark text-danger"></i> Batal
                        </button>
                        <button type="submit" class="dr-btn-modal">
                            <i class="iconoir-check text-success"></i> Simpan Slip Gaji
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Slip Modal -->
    <div class="modal fade" id="modalEditSlip" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <form id="form-edit-slip" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-white border-bottom px-4 sticky-top d-flex align-items-center" 
                         style="padding-top: 20px !important; padding-bottom: 20px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary p-2 rounded-3 me-3">
                                <i class="iconoir-edit-pencil text-primary" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="dr-title mb-0" style="font-size: 18px;" id="editSlipTitle">Edit Gaji Karyawan</h5>
                                <p class="dr-subtitle mb-0" style="font-size: 13px;">Perbarui rincian pendapatan dan potongan karyawan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-6 border-end">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="iconoir-vcard text-primary me-2" style="font-size: 20px;"></i>
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Rincian Kerja</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="dr-label mb-2">Hari Kerja *</label>
                                        <input type="number" min="0" id="edit_work_days" name="work_days" class="dr-input font-monospace">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="dr-label mb-2">Gaji Per Hari *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" id="edit_daily_basic_salary_display" onkeyup="syncInput(this)">
                                            <input type="hidden" name="daily_basic_salary" id="edit_daily_basic_salary" class="real-value">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="dr-label mb-2">Premi Kehadiran</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" id="edit_premi_display" onkeyup="syncInput(this)">
                                            <input type="hidden" name="premi" id="edit_premi" class="real-value">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4 ms-md-2">
                                    <i class="iconoir-reports text-primary me-2" style="font-size: 20px;"></i>
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Lain-lain</h6>
                                </div>
                                <div class="ms-md-2">
                                    <div class="mb-3">
                                        <label class="dr-label mb-2">Uang Makan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input" id="edit_meal_allowance_display" onkeyup="syncInput(this)">
                                            <input type="hidden" name="meal_allowance" id="edit_meal_allowance" class="real-value">
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2 text-danger">Pot. Telat</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 px-3 text-danger" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                <input type="text" class="dr-input border-start-0 ps-3 rupiah-input text-danger" id="edit_late_deduction_display" onkeyup="syncInput(this)">
                                                <input type="hidden" name="late_deduction" id="edit_late_deduction" class="real-value">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label mb-2 text-danger">Pot. Lain</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 px-3 text-danger" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                <input type="text" class="dr-input border-start-0 ps-3 rupiah-input text-danger" id="edit_other_deduction_display" onkeyup="syncInput(this)">
                                                <input type="hidden" name="other_deduction" id="edit_other_deduction" class="real-value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-soft-success p-3 rounded-4 border border-success border-opacity-10">
                                        <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1" style="font-size: 10px;">Total Diterima</small>
                                        <span id="edit_take_home" class="fs-4 fw-bold text-success">Rp 0</span>
                                        <input type="hidden" name="basic_salary" id="edit_basic_salary_hidden" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-end gap-2">
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

    <!-- Bulk Print Modal -->
    <div class="modal fade" id="modalBulkPrint" tabindex="-1" aria-labelledby="modalBulkPrintLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-bottom px-4 py-3 d-flex align-items-center"
                     style="box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div class="bg-soft-success p-2 rounded-3 me-3">
                        <i class="iconoir-printing-page text-success" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="dr-title mb-0" style="font-size: 18px;">Cetak Massal Slip Gaji</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 13px;">Preview seluruh slip gaji periode ini.</p>
                    </div>
                </div>
                <div class="modal-body p-0 bg-light">
                    <div class="ratio ratio-16x9" style="min-height: 70vh;">
                        <iframe id="bulk-print-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-end gap-2">
                    <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                        <i class="iconoir-xmark text-danger"></i> Tutup
                    </button>
                    <button type="button" class="dr-btn-modal" onclick="triggerBulkPrint()">
                        <i class="iconoir-printing-page text-success"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-bottom px-4 py-3 d-flex align-items-center"
                     style="box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div class="bg-soft-primary p-2 rounded-3 me-3">
                        <i class="iconoir-printing-page text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="dr-title mb-0" style="font-size: 18px;">Preview Cetak Slip Gaji</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 13px;">Pastikan data sudah benar sebelum dicetak.</p>
                    </div>
                </div>
                <div class="modal-body p-0 bg-light">
                    <div class="ratio ratio-16x9" style="min-height: 70vh;">
                        <iframe id="print-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-end gap-2">
                    <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                        <i class="iconoir-xmark text-danger"></i> Tutup
                    </button>
                    <button type="button" class="dr-btn-modal" onclick="triggerPrint()">
                        <i class="iconoir-printing-page text-primary"></i> Cetak Slip
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
            if (!angka) return '0';
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
            if (!angka) return '0';
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
                hiddenInput.value = cleanNumber(displayInput.value) || 0;
            }

            if (displayInput.closest('#modalCreateSlipOne')) recalcCreate();
            if (displayInput.closest('#modalEditSlip')) recalcEdit();
        }

        function openBulkPrint() {
            const url = `{{ url('salary-periods/:id/bulk-print') }}/`.replace(':id', {{ $salaryPeriod->id }});
            const iframe = document.getElementById('bulk-print-iframe');
            if (iframe) iframe.src = url;
            const bModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalBulkPrint'));
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
            const iframe = document.getElementById('print-iframe');
            if (iframe) iframe.src = url;
            const bModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPrintPreview'));
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
            const premi = parseFloat(document.querySelector('#modalCreateSlipOne [data-field="premi"]')?.value || 0);
            const meal = parseFloat(document.querySelector('#modalCreateSlipOne [data-field="meal_allowance"]')?.value || 0);
            const late = parseFloat(document.querySelector('#modalCreateSlipOne [data-field="late_deduction"]')?.value || 0);
            const other = parseFloat(document.querySelector('#modalCreateSlipOne [data-field="other_deduction"]')?.value || 0);
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
                    sortField: { field: 'text', direction: 'asc' }
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

        function openEditSlip(slip) {
            const det = slip.details || {};
            document.getElementById('editSlipTitle').textContent = `Edit Gaji: ${slip.employee.name}`;
            
            // Set values and formats
            document.getElementById('edit_work_days').value = det.work_days ?? 0;
            
            const fields = [
                {id: 'edit_daily_basic_salary', val: det.daily_rate ?? 0},
                {id: 'edit_premi', val: slip.premi ?? 0},
                {id: 'edit_meal_allowance', val: slip.meal_allowance ?? 0},
                {id: 'edit_late_deduction', val: slip.late_deduction ?? 0},
                {id: 'edit_other_deduction', val: slip.other_deduction ?? 0}
            ];

            fields.forEach(f => {
                const h = document.getElementById(f.id);
                const d = document.getElementById(f.id + '_display');
                if(h) h.value = f.val;
                if(d) d.value = formatRupiah(String(Math.round(f.val)));
            });

            recalcEdit();
            const modal = new bootstrap.Modal(document.getElementById('modalEditSlip'));
            modal.show();
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

        document.getElementById('edit_work_days').addEventListener('input', recalcEdit);
        document.querySelectorAll('#modalEditSlip .rupiah-input').forEach(el => {
            el.addEventListener('input', recalcEdit);
        });
        // Dynamic Search
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            });

            if (searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }
    </script>
@endpush
