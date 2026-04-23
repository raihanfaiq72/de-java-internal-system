@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <i class="iconoir-home dr-breadcrumb-icon"></i>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <a href="{{ route('salary-periods.index') }}" class="text-decoration-none text-muted">Periode Gaji</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <a href="{{ route('salary-periods.show', $salarySlip->salary_period_id) }}" class="text-decoration-none text-muted">Detail Periode</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Edit Slip Gaji</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('salary-periods.show', $salarySlip->salary_period_id) }}"
                                class="dr-btn-icon me-3" title="Kembali">
                                <i class="iconoir-nav-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="dr-title">Edit Slip Gaji: {{ $salarySlip->employee->name }}</h4>
                                <p class="dr-subtitle">NIK: {{ $salarySlip->employee->nik }} • {{ $salarySlip->employee->position }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="dr-card">
                                <div class="dr-card-header">
                                    <h6 class="dr-card-title">Rincian Pendapatan & Potongan</h6>
                                </div>
                                
                                <div class="card-body p-4 bg-white">
                                    <!-- Info Kehadiran Summary -->
                                    <div class="bg-soft-primary p-3 rounded-4 mb-4 border border-primary border-opacity-10 d-flex align-items-center">
                                        <div class="bg-white p-2 rounded-3 me-3 shadow-sm">
                                            <i class="iconoir-calendar text-primary" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 small text-uppercase">Statistik Kehadiran</h6>
                                            <p class="mb-0 text-muted small">
                                                Total Masuk: <span class="fw-bold text-primary">{{ $salarySlip->details['work_days'] ?? 0 }} hari</span>
                                                <span class="mx-2 opacity-50">|</span>
                                                Tepat Waktu: <span class="text-success">{{ $salarySlip->details['present'] ?? 0 }}</span>
                                                <span class="mx-2 opacity-50">|</span>
                                                Terlambat: <span class="text-danger">{{ $salarySlip->details['late'] ?? 0 }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <form action="{{ route('salary-slips.update', $salarySlip->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-4">
                                            <!-- Pendapatan -->
                                            <div class="col-md-6 border-end">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="iconoir-reports text-primary me-2" style="font-size: 18px;"></i>
                                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase">Komponen Pendapatan</h6>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="dr-label mb-2">Gaji Pokok (Total)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                        <input type="text" class="dr-input border-start-0 ps-3 rupiah-input"
                                                            value="{{ number_format($salarySlip->basic_salary, 0, ',', '.') }}"
                                                            onkeyup="syncInput(this)">
                                                        <input type="hidden" name="basic_salary" class="real-value"
                                                            value="{{ $salarySlip->basic_salary }}">
                                                    </div>
                                                    <small class="text-muted mt-1 d-block" style="font-size: 11px;">Hasil kali hari kerja x gaji harian.</small>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="dr-label mb-2">Premi Kehadiran</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input"
                                                                value="{{ number_format($salarySlip->premi, 0, ',', '.') }}"
                                                                onkeyup="syncInput(this)">
                                                            <input type="hidden" name="premi" class="real-value"
                                                                value="{{ $salarySlip->premi }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="dr-label mb-2">Uang Makan / Lain</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0 px-3" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input"
                                                                value="{{ number_format($salarySlip->meal_allowance, 0, ',', '.') }}"
                                                                onkeyup="syncInput(this)">
                                                            <input type="hidden" name="meal_allowance" class="real-value"
                                                                value="{{ $salarySlip->meal_allowance }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Potongan -->
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-3 ms-md-2">
                                                    <i class="iconoir-warning-triangle text-danger me-2" style="font-size: 18px;"></i>
                                                    <h6 class="fw-bold text-danger mb-0 small text-uppercase">Komponen Potongan</h6>
                                                </div>
                                                
                                                <div class="ms-md-2">
                                                    <div class="mb-3">
                                                        <label class="dr-label mb-2 text-danger">Potongan Keterlambatan</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0 px-3 text-danger" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input text-danger"
                                                                value="{{ number_format($salarySlip->late_deduction, 0, ',', '.') }}"
                                                                onkeyup="syncInput(this)">
                                                            <input type="hidden" name="late_deduction" class="real-value"
                                                                value="{{ $salarySlip->late_deduction }}">
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="dr-label mb-2 text-danger">Potongan Lainnya</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0 px-3 text-danger" style="border-radius: 12px 0 0 12px; border: 1.5px solid var(--dr-line); font-size: 11px;">Rp</span>
                                                            <input type="text" class="dr-input border-start-0 ps-3 rupiah-input text-danger"
                                                                value="{{ number_format($salarySlip->other_deduction, 0, ',', '.') }}"
                                                                onkeyup="syncInput(this)">
                                                            <input type="hidden" name="other_deduction" class="real-value"
                                                                value="{{ $salarySlip->other_deduction }}">
                                                        </div>
                                                    </div>

                                                    <!-- Summary -->
                                                    <div class="bg-soft-success p-3 rounded-4 border border-success border-opacity-10">
                                                        <small class="text-muted d-block text-uppercase ls-1 fw-bold mb-1" style="font-size: 10px;">Total Bersih Diterima</small>
                                                        <h3 class="fw-bold text-success mb-0">Rp {{ number_format($salarySlip->total_salary, 0, ',', '.') }}</h3>
                                                        <small class="text-muted italic" style="font-size: 11px;">* Akan dihitung ulang saat disimpan.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                                            @if($salarySlip->status == 'draft')
                                                <button type="button" class="dr-btn dr-btn-outline text-success border-success"
                                                    onclick="publishSlip()">
                                                    <i class="iconoir-check-circle"></i> Publish Slip Gaji
                                                </button>
                                            @else
                                                <div></div>
                                            @endif

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('salary-periods.show', $salarySlip->salary_period_id) }}" class="dr-btn dr-btn-modal">
                                                    <i class="iconoir-xmark text-danger"></i> Batal
                                                </a>
                                                <button type="submit" class="dr-btn dr-btn-primary px-5">
                                                    <i class="iconoir-check"></i> Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <form id="publish-form" action="{{ route('salary-slips.publish', $salarySlip->id) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
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
                if (!angka) return 0;
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

            async function publishSlip() {
                if (typeof macConfirm === 'function') {
                    const confirmed = await macConfirm(
                        'Publish Slip Gaji', 
                        'Apakah Anda yakin ingin mempublikasikan slip gaji ini? Karyawan akan dapat melihatnya di sistem.', 
                        {
                            confirmText: 'Publish',
                            confirmType: 'success',
                            cancelText: 'Batal'
                        }
                    );
                    if (confirmed) document.getElementById('publish-form').submit();
                } else {
                    if (confirm('Publish slip gaji ini?')) {
                        document.getElementById('publish-form').submit();
                    }
                }
            }
        </script>
    @endpush
@endsection