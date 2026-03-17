@extends('Layout.main')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    #supplierTable thead th {
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
    
    #supplierTable tbody td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    #supplierTable tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge-invoice {
        background: #fef3c7;
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
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
        #supplierTable {
            border: 1px solid #000 !important;
        }
        #supplierTable th, #supplierTable td {
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
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title mb-1">Daftar Tagihan Supplier</h4>
                            <p class="text-muted mb-0">Supplier dengan tagihan belum lunas atau pembayaran sebagian</p>
                        </div>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Report: Tagihan Supplier</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            @if($suppliersData->count() > 0)
                <div class="row g-3 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3 border-start border-4 border-primary">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Total Tagihan</label>
                                <h5 class="fw-bold mb-0 text-primary">{{ number_format($grandTotalInvoice, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3 border-start border-4 border-success">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Sudah Dibayar</label>
                                <h5 class="fw-bold mb-0 text-success">{{ number_format($grandTotalPayment, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3 border-start border-4 border-danger">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Sisa Tagihan</label>
                                <h5 class="fw-bold mb-0 text-danger">{{ number_format($grandTotalRemaining, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">Daftar Tagihan Supplier</h6>
                        <div>
                            @if($suppliersData->count() > 0)
                                <a href="{{ route('report.supplier-invoices', request()->query() + ['export' => 'excel']) }}" 
                                   class="btn btn-success border fw-bold px-3 shadow-sm btn-sm me-2">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <!-- Filter Section -->
                    <div class="row g-2 mb-4 align-items-end bg-light p-3 rounded border">
                        <div class="col-lg-3">
                            <label class="f-label">Supplier</label>
                            <select name="mitra_id" id="select-mitra" class="form-select">
                                <option value="">Semua Supplier</option>
                                @foreach ($suppliersData as $supplier)
                                    <option value="{{ $supplier['id'] }}"
                                        {{ request('mitra_id') == $supplier['id'] ? 'selected' : '' }}>
                                        {{ $supplier['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="f-label">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-lg-2">
                            <label class="f-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-lg-5 text-end">
                            <button onclick="applyFilters()" class="btn btn-primary fw-bold py-2 px-4 shadow-sm btn-sm me-2">
                                <i class="fas fa-filter me-1"></i> FILTER
                            </button>
                            <button onclick="resetFilters()" class="btn btn-light border fw-bold text-dark py-2 btn-sm">
                                RESET
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="supplierTable">
                            <thead class="bg-light text-uppercase text-secondary fw-bold"
                                style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th width="50" class="ps-4 text-center">#</th>
                                    <th width="200">Nama Supplier</th>
                                    <th width="120">Kota</th>
                                    <th width="100" class="text-center">Jumlah Invoice</th>
                                    <th width="150" class="text-end">Total Tagihan</th>
                                    <th width="150" class="text-end">Sudah Dibayar</th>
                                    <th width="150" class="text-end">Sisa Tagihan</th>
                                    <th width="120" class="text-center">Terakhir Invoice</th>
                                    <th width="120" class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @if($suppliersData->count() > 0)
                                    @foreach($suppliersData as $index => $supplier)
                                        <tr>
                                            <td class="ps-4 text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $supplier['nama'] }}</div>
                                            </td>
                                            <td>{{ $supplier['kota'] }}</td>
                                            <td class="text-center">
                                                <span class="badge-invoice">{{ $supplier['invoice_count'] }}</span>
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($supplier['total_invoice'], 0, ',', '.') }}</td>
                                            <td class="text-end text-success">{{ number_format($supplier['total_paid'], 0, ',', '.') }}</td>
                                            <td class="text-end text-danger fw-bold">{{ number_format($supplier['total_remaining'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <small>{{ \Carbon\Carbon::parse($supplier['latest_invoice_date'])->format('d/m/Y') }}</small>
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="{{ route('report.supplier-invoices.detail', $supplier['id']) }}" class="action-btn">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">Tidak Ada Tagihan Supplier</h5>
                                            <p class="text-muted">Semua tagihan supplier telah lunas atau tidak ada data tagihan dalam periode yang dipilih.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
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
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize TomSelect
        const supplierSelect = document.getElementById('select-mitra');
        if (supplierSelect) {
            new TomSelect(supplierSelect, { 
                create: false, 
                maxOptions: 5000, 
                sortField: { field: 'text', direction: 'asc' } 
            });
        }
    });

    function applyFilters() {
        const mitraId = document.getElementById('select-mitra').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        const params = new URLSearchParams();
        if (mitraId) params.append('mitra_id', mitraId);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        
        window.location.href = '{{ route('report.supplier-invoices') }}?' + params.toString();
    }

    function resetFilters() {
        window.location.href = '{{ route('report.supplier-invoices') }}';
    }
</script>
@endpush
