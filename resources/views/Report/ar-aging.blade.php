@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Laporan Umur Piutang</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Report: AR Aging</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4 d-flex justify-content-between align-items-end">
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


                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom p-0 d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs px-4 pt-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-ringkasan" role="tab">Ringkasan Umur Piutang</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-detail" role="tab">Detail Pelanggan</a>
                            </li>
                        </ul>
                        <div class="pe-4">
                            <button type="button" class="btn btn-white border fw-bold px-3 shadow-sm text-dark btn-sm" onclick="printActiveTab()">
                                <i class="fa fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab-ringkasan" role="tabpanel">
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
                                                <td class="py-3 text-end text-primary">{{ number_format($summary['total_amount']) }}</td>
                                                <td class="py-3 text-end text-success">{{ number_format($summary['buckets']['current']) }}</td>
                                                <td class="py-3 text-end">{{ number_format($summary['buckets']['1-15']) }}</td>
                                                <td class="py-3 text-end">{{ number_format($summary['buckets']['16-30']) }}</td>
                                                <td class="py-3 text-end">{{ number_format($summary['buckets']['31-45']) }}</td>
                                                <td class="py-3 text-end">{{ number_format($summary['buckets']['46-60']) }}</td>
                                                <td class="py-3 text-end px-4 text-danger">{{ number_format($summary['buckets']['61+']) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab-detail" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="px-4 py-3">No</th>
                                                <th class="py-3">Kode Pelanggan</th>
                                                <th class="py-3">Nama Pelanggan</th>
                                                <th class="py-3 text-end">Tagihan Invoice</th>
                                                <th class="py-3 text-end">Sudah Bayar</th>
                                                <th class="py-3 text-end">Outstanding</th>
                                                <th class="py-3">Sales Code</th>
                                                <th class="py-3">Sales Name</th>
                                                <th class="py-3 text-end">Umur Nota</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customerDetails as $i => $row)
                                                <tr>
                                                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                                                    <td class="py-3">{{ $row['kode'] }}</td>
                                                    <td class="py-3 fw-bold text-dark">{{ $row['nama'] }}</td>
                                                    <td class="py-3 text-end fw-bold">{{ number_format($row['tagihan']) }}</td>
                                                    <td class="py-3 text-end">{{ number_format($row['dibayar']) }}</td>
                                                    <td class="py-3 text-end text-primary">{{ number_format($row['outstanding']) }}</td>
                                                    <td class="py-3">{{ $row['sales_code'] }}</td>
                                                    <td class="py-3">{{ $row['sales_name'] }}</td>
                                                    <td class="py-3 text-end {{ $row['umur_nota'] > 60 ? 'text-danger' : '' }}">{{ $row['umur_nota'] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-5 text-muted">
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
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div class="modal fade" id="printPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-print me-2"></i> Preview Cetak Laporan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="card border shadow-sm mx-auto" style="max-width: 210mm; min-height: 297mm;">
                        <div class="card-body p-5 bg-white">
                            <!-- Report Header -->
                            <div class="text-center mb-4">
                                <h4 class="fw-bold text-uppercase mb-1">LAPORAN UMUR PIUTANG</h4>
                                <p class="text-muted mb-0">
                                    Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') : 'Awal' }} 
                                    s/d 
                                    {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') : 'Akhir' }}
                                </p>
                            </div>

                            <!-- Ringkasan Section -->
                            <h6 class="fw-bold border-bottom pb-2 mb-3">RINGKASAN UMUR PIUTANG</h6>
                            <table class="table table-bordered table-sm mb-5" style="border-color: #000;">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th>Total Customers</th>
                                        <th>Avg. Day Past Due</th>
                                        <th>Total Invoices</th>
                                        <th>Total Amount</th>
                                        <th><= 0 Hari</th>
                                        <th>1-15 Hari</th>
                                        <th>16-30 Hari</th>
                                        <th>31-45 Hari</th>
                                        <th>46-60 Hari</th>
                                        <th>61+ Hari</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <td>{{ $summary['total_customers'] }}</td>
                                        <td>{{ number_format($summary['avg_days_past_due'], 2) }}</td>
                                        <td>{{ $summary['total_invoices'] }}</td>
                                        <td class="text-end">{{ number_format($summary['total_amount']) }}</td>
                                        <td class="text-end">{{ number_format($summary['buckets']['current']) }}</td>
                                        <td class="text-end">{{ number_format($summary['buckets']['1-15']) }}</td>
                                        <td class="text-end">{{ number_format($summary['buckets']['16-30']) }}</td>
                                        <td class="text-end">{{ number_format($summary['buckets']['31-45']) }}</td>
                                        <td class="text-end">{{ number_format($summary['buckets']['46-60']) }}</td>
                                        <td class="text-end">{{ number_format($summary['buckets']['61+']) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Detail Section -->
                            <h6 class="fw-bold border-bottom pb-2 mb-3">DETAIL PELANGGAN</h6>
                            <table class="table table-bordered table-sm" style="border-color: #000; font-size: 12px;">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th>Kode</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Tagihan</th>
                                        <th>Bayar</th>
                                        <th>Outstanding</th>
                                        <th>Sales Code</th>
                                        <th>Sales Name</th>
                                        <th>Umur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customerDetails as $i => $row)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $row['kode'] }}</td>
                                            <td>{{ $row['nama'] }}</td>
                                            <td class="text-end">{{ number_format($row['tagihan']) }}</td>
                                            <td class="text-end">{{ number_format($row['dibayar']) }}</td>
                                            <td class="text-end fw-bold">{{ number_format($row['outstanding']) }}</td>
                                            <td>{{ $row['sales_code'] }}</td>
                                            <td>{{ $row['sales_name'] }}</td>
                                            <td class="text-center">{{ $row['umur_nota'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light fw-bold border" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-primary fw-bold">
                        <i class="fa fa-print me-2"></i> Cetak Sekarang
                    </a>
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

        function printActiveTab() {
            // Check active tab
            const activeTab = document.querySelector('.nav-link.active').getAttribute('href');
            
            // Add a class to body to indicate print mode
            document.body.classList.add('printing');
            
            // Hide all tab panes except active one via CSS or JS manipulation
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => {
                if ('#' + pane.id !== activeTab) {
                    pane.classList.add('d-print-none');
                } else {
                    pane.classList.remove('d-print-none');
                    pane.classList.add('d-print-block');
                }
            });

            window.print();

            // Cleanup after print dialog closes
            setTimeout(() => {
                document.body.classList.remove('printing');
                tabPanes.forEach(pane => {
                    pane.classList.remove('d-print-none', 'd-print-block');
                });
            }, 500);
        }
    </script>
@endpush

