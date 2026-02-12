@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('salary-periods.index') }}" class="btn btn-light shadow-sm me-3 rounded-circle">
                                <i class="mdi mdi-arrow-left text-primary"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Detail Periode Gaji</h4>
                                <p class="text-muted small mb-0">
                                    {{ $salaryPeriod->name }} 
                                    <span class="mx-2">•</span> 
                                    {{ $salaryPeriod->start_date->format('d M Y') }} - {{ $salaryPeriod->end_date->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                         <span class="badge bg-{{ $salaryPeriod->status == 'open' ? 'success' : 'secondary' }} rounded-pill px-3 py-2 me-2">
                            {{ ucfirst($salaryPeriod->status) }}
                        </span>
                        
                        @if($salaryPeriod->status == 'open')
                        <form action="{{ route('salary-periods.generate', $salaryPeriod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Generate slip gaji untuk semua karyawan aktif?')">
                            @csrf
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                                <i class="mdi mdi-calculator me-1"></i> Generate Slip Gaji
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark">Daftar Slip Gaji Karyawan</h6>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
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
                                        <td class="fw-bold text-success">Rp {{ number_format($slip->total_salary, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $slip->status == 'published' ? 'success' : ($slip->status == 'paid' ? 'primary' : 'warning') }} rounded-pill px-3">
                                                {{ ucfirst($slip->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="{{ route('salary-slips.show', $slip->id) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                                    Detail
                                                </a>
                                                <a href="{{ route('salary-slips.print', $slip->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="mdi mdi-file-document-outline fs-1 text-secondary opacity-50"></i></div>
                                            Belum ada slip gaji yang digenerate.<br>Silakan klik tombol Generate di atas.
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
@endsection
