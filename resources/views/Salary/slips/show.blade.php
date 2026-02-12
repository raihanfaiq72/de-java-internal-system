@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-4">
                            <a href="{{ route('salary-periods.show', $salarySlip->salary_period_id) }}" class="btn btn-light shadow-sm me-3 rounded-circle">
                                <i class="mdi mdi-arrow-left text-primary"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Edit Slip Gaji</h4>
                                <p class="text-muted small mb-0">{{ $salarySlip->employee->name }} - {{ $salarySlip->employee->nik }}</p>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded-3">
                            <div class="card-body p-4">
                                <div class="alert alert-light border-start border-4 border-info shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="mdi mdi-information-outline fs-4 text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Info Kehadiran</h6>
                                            <p class="mb-0 text-muted small">
                                                Masuk: <span class="fw-bold text-dark">{{ $salarySlip->details['work_days'] ?? 0 }} hari</span> 
                                                (Hadir: {{ $salarySlip->details['present'] ?? 0 }}, Telat: {{ $salarySlip->details['late'] ?? 0 }})
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('salary-slips.update', $salarySlip->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-4 mt-2">
                                        <div class="col-md-12">
                                            <h6 class="fw-bold text-uppercase text-secondary small mb-3 border-bottom pb-2">Komponen Pendapatan</h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-uppercase text-muted">Gaji Pokok</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                                <input type="number" name="basic_salary" class="form-control border-start-0 ps-0" value="{{ $salarySlip->basic_salary }}">
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;">Otomatis: Hari kerja x Gaji Harian</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-uppercase text-muted">Premi</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                                <input type="number" name="premi" class="form-control border-start-0 ps-0" value="{{ $salarySlip->premi }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-uppercase text-muted">Uang Makan</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                                <input type="number" name="meal_allowance" class="form-control border-start-0 ps-0" value="{{ $salarySlip->meal_allowance }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-4">
                                            <h6 class="fw-bold text-uppercase text-secondary small mb-3 border-bottom pb-2">Komponen Potongan</h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-uppercase text-danger">Potongan Keterlambatan</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-danger">Rp</span>
                                                <input type="number" name="late_deduction" class="form-control border-start-0 ps-0 text-danger" value="{{ $salarySlip->late_deduction }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-uppercase text-danger">Potongan Lainnya</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-danger">Rp</span>
                                                <input type="number" name="other_deduction" class="form-control border-start-0 ps-0 text-danger" value="{{ $salarySlip->other_deduction }}">
                                            </div>
                                        </div>

                                        <div class="col-12 mt-5">
                                            <div class="bg-light p-4 rounded-3 text-center border border-dashed">
                                                <label class="d-block text-muted text-uppercase small fw-bold mb-2">Total Gaji Diterima</label>
                                                <h2 class="fw-bold text-primary mb-0">Rp {{ number_format($salarySlip->total_salary, 0, ',', '.') }}</h2>
                                                <small class="text-muted">Akan dihitung ulang setelah disimpan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-5">
                                        @if($salarySlip->status == 'draft')
                                            <button type="button" class="btn btn-success fw-bold px-4 shadow-sm" onclick="if(confirm('Publish slip gaji ini? Karyawan akan dapat melihatnya.')) document.getElementById('publish-form').submit()">
                                                <i class="mdi mdi-check-circle me-1"></i> Publish
                                            </button>
                                        @else
                                            <div></div>
                                        @endif
                                        
                                        <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm">
                                            <i class="mdi mdi-content-save me-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                                <form id="publish-form" action="{{ route('salary-slips.publish', $salarySlip->id) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
