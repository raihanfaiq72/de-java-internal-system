@extends('Layout.main')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; }
    
    .supplier-header {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .supplier-header h2 {
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .supplier-header p {
        color: #64748b;
        margin-bottom: 0;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    #invoiceTable thead th {
        background: #f8fafc;
        border: none;
        padding: 1rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    
    #invoiceTable tbody td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    #invoiceTable tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge-overdue {
        background: #fef2f2;
        color: #dc2626;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-upcoming {
        background: #fffbeb;
        color: #d97706;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .action-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: none;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        font-size: 0.8rem;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        color: white;
    }
    
    .back-btn {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        border: none;
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    
    .back-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
        color: white;
    }
    
    @media print {
        .page-wrapper {
            padding: 0;
        }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .btn, .breadcrumb, .page-title-box {
            display: none !important;
        }
        .supplier-header {
            background: white !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        .supplier-header h2 {
            color: #000 !important;
        }
        .supplier-header p {
            color: #000 !important;
        }
        #invoiceTable {
            border: 1px solid #000 !important;
        }
        #invoiceTable th, #invoiceTable td {
            border: 1px solid #000 !important;
            padding: 8px !important;
        }
    }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Breadcrumb -->
            <div class="row mb-4">
                <div class="col-sm-12">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('report.supplier-invoices') }}">Report: Tagihan Supplier</a></li>
                        <li class="breadcrumb-item active">Detail Supplier</li>
                    </ol>
                </div>
            </div>

            <!-- Supplier Header -->
            <div class="supplier-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-2 fw-bold">{{ $supplier->nama }}</h2>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $supplier->kota ?? '-' }}
                            @if($supplier->no_hp)
                                <i class="fas fa-phone ms-3 me-2"></i>{{ $supplier->no_hp }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('report.supplier-invoices') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Total Tagihan</label>
                            <h5 class="fw-bold mb-0 text-primary">{{ number_format($totalInvoice, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 border-start border-4 border-success">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Sudah Dibayar</label>
                            <h5 class="fw-bold mb-0 text-success">{{ number_format($totalPayment, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 border-start border-4 border-danger">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Sisa Tagihan</label>
                            <h5 class="fw-bold mb-0 text-danger">{{ number_format($totalRemaining, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 border-start border-4 border-info">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Jumlah Invoice</label>
                            <h5 class="fw-bold mb-0 text-info">{{ $invoiceData->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">Detail Invoice {{ $supplier->nama }}</h6>
                        <div>
                            @if($invoiceData->count() > 0)
                                <a href="{{ route('report.supplier-invoices', request()->query() + ['export' => 'excel', 'mitra_id' => $supplier->id]) }}" 
                                   class="btn btn-success border fw-bold px-3 shadow-sm btn-sm me-2">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <!-- Date Filters -->
                    <div class="row g-2 mb-4 align-items-end bg-light p-3 rounded border">
                        <div class="col-lg-4">
                            <label class="f-label">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="f-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-lg-4 text-end">
                            <button onclick="applyFilters()" class="btn btn-primary fw-bold py-2 px-4 shadow-sm btn-sm me-2">
                                <i class="fas fa-filter me-1"></i> FILTER
                            </button>
                            <button onclick="resetFilters()" class="btn btn-light border fw-bold text-dark py-2 btn-sm">
                                RESET
                            </button>
                        </div>
                    </div>

                    <!-- Invoice Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="invoiceTable">
                            <thead class="bg-light text-uppercase text-secondary fw-bold"
                                style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th width="150">No. Invoice</th>
                                    <th width="120">Tanggal</th>
                                    <th width="120">Jatuh Tempo</th>
                                    <th width="150" class="text-end">Total Tagihan</th>
                                    <th width="150" class="text-end">Sudah Dibayar</th>
                                    <th width="150" class="text-end">Sisa Tagihan</th>
                                    <th width="120" class="text-center">Status</th>
                                    <th width="120" class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @if($invoiceData->count() > 0)
                                    @foreach($invoiceData as $invoice)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark fw-bold">{{ $invoice['nomor_invoice'] }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($invoice['tgl_invoice'])->format('d/m/Y') }}</td>
                                            <td>{{ $invoice['tgl_jatuh_tempo'] ? \Carbon\Carbon::parse($invoice['tgl_jatuh_tempo'])->format('d/m/Y') : '-' }}</td>
                                            <td class="fw-bold text-end">{{ number_format($invoice['total_akhir'], 0, ',', '.') }}</td>
                                            <td class="text-success text-end">{{ number_format($invoice['total_paid'], 0, ',', '.') }}</td>
                                            <td class="text-danger fw-bold text-end">{{ number_format($invoice['remaining'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if($invoice['days_overdue'] > 0)
                                                    <span class="badge-overdue">Terlambat {{ $invoice['days_overdue'] }} hari</span>
                                                @elseif($invoice['days_overdue'] <= 7 && $invoice['days_overdue'] >= 0)
                                                    <span class="badge-upcoming">Minggu ini</span>
                                                @else
                                                    <span class="badge bg-secondary">Hari ini</span>
                                                @endif
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="{{ route('purchase.show', $invoice['id']) }}" class="action-btn">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">Tidak Ada Tagihan</h5>
                                            <p class="text-muted">Supplier ini tidak memiliki tagihan belum lunas dalam periode yang dipilih.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            @if($invoiceData->count() > 0)
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="3" class="text-end">TOTAL:</td>
                                        <td class="text-end">{{ number_format($totalInvoice, 0, ',', '.') }}</td>
                                        <td class="text-end text-success">{{ number_format($totalPayment, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">{{ number_format($totalRemaining, 0, ',', '.') }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    function applyFilters() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        
        window.location.href = '{{ route('report.supplier-invoices.detail', $supplier->id) }}?' + params.toString();
    }

    function resetFilters() {
        window.location.href = '{{ route('report.supplier-invoices.detail', $supplier->id) }}';
    }
</script>
@endpush
