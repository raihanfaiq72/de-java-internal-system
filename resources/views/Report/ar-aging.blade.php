@extends('Layout.main')

@section('main')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">

    <div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content py-4">
            <div class="container-fluid">
                <!-- Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Laporan Umur Piutang</h4>
                        <p class="text-muted small mb-0">Pantau invoice yang akan jatuh tempo atau sudah lewat jatuh tempo.
                        </p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2">
                            <i class="iconoir-download me-1"></i> Unduh
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('report.ar-aging') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Pelanggan</label>
                                <select name="mitra_id" id="select-mitra" class="form-select">
                                    <option value="">Semua Mitra</option>
                                    @foreach ($mitras as $mitra)
                                        <option value="{{ $mitra->id }}"
                                            {{ request('mitra_id') == $mitra->id ? 'selected' : '' }}>
                                            {{ $mitra->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary fw-bold w-100">
                                    Hasilkan Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Summary Section -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 text-uppercase text-muted" style="letter-spacing: 0.5px;">Ringkasan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0 align-middle">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3">Total Customers</th>
                                        <th class="py-3">Avg. Day Past Due</th>
                                        <th class="py-3">Total Invoices</th>
                                        <th class="py-3 text-end">Total Amount</th>
                                        <th class="py-3 text-end">
                                            <= 0 Hari</th>
                                        <th class="py-3 text-end">1-15 Hari</th>
                                        <th class="py-3 text-end">16-30 Hari</th>
                                        <th class="py-3 text-end">31-45 Hari</th>
                                        <th class="py-3 text-end">46-60 Hari</th>
                                        <th class="py-3 text-end px-4">61+ Hari</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="fw-bold fs-5">
                                        <td class="px-4 py-3">{{ $summary['total_customers'] }}</td>
                                        <td class="py-3">{{ number_format($summary['avg_days_past_due'], 2) }}</td>
                                        <td class="py-3">{{ $summary['total_invoices'] }}</td>
                                        <td class="py-3 text-end text-primary">{{ number_format($summary['total_amount']) }}
                                        </td>
                                        <td class="py-3 text-end text-success">
                                            {{ number_format($summary['buckets']['current']) }}</td>
                                        <td class="py-3 text-end">{{ number_format($summary['buckets']['1-15']) }}</td>
                                        <td class="py-3 text-end">{{ number_format($summary['buckets']['16-30']) }}</td>
                                        <td class="py-3 text-end">{{ number_format($summary['buckets']['31-45']) }}</td>
                                        <td class="py-3 text-end">{{ number_format($summary['buckets']['46-60']) }}</td>
                                        <td class="py-3 text-end px-4 text-danger">
                                            {{ number_format($summary['buckets']['61+']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detailed Table -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0 text-uppercase text-muted" style="letter-spacing: 0.5px;">Rincian Per
                            Pelanggan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3">Pelanggan</th>
                                        <th class="py-3">Avg. Day Past Due</th>
                                        <th class="py-3">Total Invoices</th>
                                        <th class="py-3 text-end">Total</th>
                                        <th class="py-3 text-end">
                                            <= 0 Hari</th>
                                        <th class="py-3 text-end">1-15 Hari</th>
                                        <th class="py-3 text-end">16-30 Hari</th>
                                        <th class="py-3 text-end">31-45 Hari</th>
                                        <th class="py-3 text-end">46-60 Hari</th>
                                        <th class="py-3 text-end px-4">61+ Hari</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($agingData as $data)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-dark">{{ $data['mitra_name'] }}</td>
                                            <td class="py-3">{{ number_format($data['avg_days'], 2) }}</td>
                                            <td class="py-3">{{ $data['count'] }}</td>
                                            <td class="py-3 text-end fw-bold">{{ number_format($data['total']) }}</td>
                                            <td class="py-3 text-end text-muted">
                                                {{ number_format($data['buckets']['current']) }}</td>
                                            <td class="py-3 text-end text-muted">
                                                {{ number_format($data['buckets']['1-15']) }}</td>
                                            <td class="py-3 text-end text-muted">
                                                {{ number_format($data['buckets']['16-30']) }}</td>
                                            <td class="py-3 text-end text-muted">
                                                {{ number_format($data['buckets']['31-45']) }}</td>
                                            <td class="py-3 text-end text-muted">
                                                {{ number_format($data['buckets']['46-60']) }}</td>
                                            <td class="py-3 text-end px-4 text-danger">
                                                {{ number_format($data['buckets']['61+']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="iconoir-file-not-found fs-1 d-inline-block mb-2"></i>
                                                <p class="mb-0">Tidak ada data piutang untuk periode ini.</p>
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

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-wrapper.form-select {
            border: none;
            padding: 0;
        }

        .ts-dropdown {
            z-index: 1050;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect('#select-mitra', {
                create: false,
                allowEmptyOption: true,
                placeholder: "Cari Pelanggan...",
                dropdownParent: 'body'
            });
        });
    </script>
@endpush
