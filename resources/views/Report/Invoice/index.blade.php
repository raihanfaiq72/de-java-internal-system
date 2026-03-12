@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('main')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.2/dist/apexcharts.min.js"></script>
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Laporan Invoice</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Report: Invoice</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Header & Filter -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('report.invoice') }}" method="GET" id="filterForm">
                            <div class="row align-items-end g-3">
                                <div class="col-md-3">
                                    <h4 class="fw-bold text-dark mb-1">Laporan Invoice</h4>
                                    <p class="text-muted small mb-0">Rekapitulasi penjualan, pembayaran, dan produk.</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted">Periode Pembayaran</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                        <span class="input-group-text bg-white border-start-0 border-end-0">s/d</span>
                                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Mitra</label>
                                    <select class="form-select" name="mitra_id">
                                        <option value="">Semua Mitra</option>
                                        @foreach($mitras as $m)
                                            <option value="{{ $m->id }}" {{ request('mitra_id') == $m->id ? 'selected' : '' }}>
                                                {{ $m->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Pencarian</label>
                                    <input type="text" class="form-control" name="search" placeholder="No Invoice/Mitra..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                            <i class="fa fa-filter me-1"></i> Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Extended Filters -->
                            <div class="row align-items-end g-3 mt-2">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted">Produk</label>
                                    @php
                                        $selectedProducts = (array) request('product_id', []);
                                    @endphp
                                    <select class="form-select" name="product_id[]" multiple>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" {{ in_array($p->id, $selectedProducts) ? 'selected' : '' }}>
                                                {{ $p->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Tipe Invoice</label>
                                    <select class="form-select" name="invoice_type">
                                        <option value="">Semua</option>
                                        <option value="Sales" {{ request('invoice_type') == 'Sales' ? 'selected' : '' }}>Sales
                                        </option>
                                        <option value="Purchase" {{ request('invoice_type') == 'Purchase' ? 'selected' : '' }}>Purchase</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Status Pembayaran</label>
                                    <select class="form-select" name="payment_status">
                                        <option value="">Semua</option>
                                        <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Lunas
                                            (Paid)</option>
                                        <option value="Unpaid" {{ request('payment_status') == 'Unpaid' ? 'selected' : '' }}>
                                            Belum Lunas (Unpaid)</option>
                                        <option value="Partially Paid" {{ request('payment_status') == 'Partially Paid' ? 'selected' : '' }}>Sebagian (Partial)</option>
                                        <option value="Overdue" {{ request('payment_status') == 'Overdue' ? 'selected' : '' }}>Jatuh Tempo (Overdue)</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted">Tampilkan</label>
                                    <select class="form-select" name="product_metric">
                                        @php $metric = request('product_metric', 'both'); @endphp
                                        <option value="qty" {{ $metric === 'qty' ? 'selected' : '' }}>Quantity</option>
                                        <option value="value" {{ $metric === 'value' ? 'selected' : '' }}>Value (Harga Bayar)</option>
                                        <option value="both" {{ $metric === 'both' ? 'selected' : '' }}>Quantity + Value</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabs & Content Wrapped in a Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 p-0">
                        <ul class="nav nav-tabs nav-fill border-bottom-0 m-0" id="reportTabs" role="tablist">
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold py-3 rounded-top-3" id="general-tab"
                                    data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                    <i class="iconoir-stats-report me-2"></i>Rekap Umum
                                </button>
                            </li> --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold py-3 rounded-top-3" id="payments-tab"
                                    data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                                    <i class="iconoir-wallet me-2"></i>Rekap Pembayaran
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-3 rounded-top-3" id="products-tab" data-bs-toggle="tab"
                                    data-bs-target="#products" type="button" role="tab">
                                    <i class="iconoir-box-iso me-2"></i>Laporan Produk Terjual
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold py-3 rounded-top-3" id="invoice-products-tab"
                                    data-bs-toggle="tab" data-bs-target="#invoice-products" type="button" role="tab">
                                    <i class="iconoir-list me-2"></i>Laporan Invoice Per Produk
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">

                        <div class="tab-content" id="reportTabsContent">

                            <!-- Tab 1: Rekap Umum -->
                            {{-- <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row g-4">
                                    <!-- Line Chart: Income vs Expenditure -->
                                    <div class="col-lg-8">
                                        <div class="card border-0 shadow-sm rounded-3 h-100">
                                            <div
                                                class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-0">Income vs Expenditure (Tahun {{ $year }})</h6>
                                                    <small class="text-muted">Perbandingan pemasukan dan pengeluaran per
                                                        bulan</small>
                                                </div>
                                                <div>
                                                    <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark"
                                                        onclick="window.print()">
                                                        <i class="fa fa-print me-1"></i> Print
                                                    </button>
                                                    <button
                                                        class="btn btn-white border btn-sm shadow-sm fw-bold text-dark ms-2"
                                                        onclick="exportReport('general')">
                                                        <i class="fa fa-file-excel me-1 text-success"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-4">
                                                <div id="incomeExpenseChart"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pie Chart: Receivables Composition -->
                                    <div class="col-lg-4">
                                        <div class="card border-0 shadow-sm rounded-3 h-100">
                                            <div
                                                class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between">
                                                <div>
                                                    <h6 class="fw-bold mb-0">Komposisi Piutang (Sales)</h6>
                                                    <small class="text-muted">Status pembayaran tagihan penjualan</small>
                                                </div>
                                            </div>
                                            <div class="card-body p-4 d-flex align-items-center justify-content-center">
                                                <div id="receivablesPieChart" style="width: 100%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Tab 2: Rekap Pembayaran -->
                            <div class="tab-pane fade show active" id="payments" role="tabpanel">
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">Daftar Pembayaran</h6>
                                                <small class="text-muted">
                                                    Total: <span class="fw-bold text-primary">Rp
                                                        {{ number_format($totalPaymentAmount, 0, ',', '.') }}</span>
                                                    ({{ $totalUniqueInvoices }} Invoice)
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="dropdown">
                                                    <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                                        <i class="iconoir-view-columns-3 me-1"></i> Kolom
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-payments" style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                                        <!-- Column toggles via JS -->
                                                    </ul>
                                                </div>
                                                <div class="btn-group shadow-sm">
                                                    <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('payments')">
                                                        <i class="fa fa-print me-1"></i> Print
                                                    </button>
                                                    <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('payments')">
                                                        <i class="fa fa-file-excel me-1 text-success"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" id="table-payments">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Tanggal
                                                    </th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Invoice
                                                    </th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Metode</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Mitra</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Nama Mitra</th>
                                                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">
                                                        Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($payments as $payment)
                                                    <tr>
                                                        <td class="px-4">
                                                            {{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}
                                                        </td>
                                                        <td class="fw-bold text-primary">{{ $payment->nomor_invoice }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-soft-info text-info border border-info border-opacity-25 rounded-pill px-2">
                                                                {{ $payment->metode_pembayaran }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $payment->nomor_mitra }}</td>
                                                        <td>{{ $payment->nama_mitra }}</td>
                                                        <td class="px-4 text-end fw-bold">Rp
                                                            {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-5">
                                                            <div class="mb-3"><i
                                                                    class="iconoir-file-not-found fs-1 text-muted opacity-50"></i>
                                                            </div>
                                                            <h6 class="fw-bold text-dark">Tidak ada data pembayaran</h6>
                                                            <p class="text-muted small mb-0">Sesuaikan filter periode atau
                                                                pencarian.</p>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td colspan="5" class="px-4 text-end text-uppercase text-muted small">
                                                        Total Pembayaran</td>
                                                    <td class="px-4 text-end text-dark">Rp
                                                        {{ number_format($totalPaymentAmount, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Laporan Produk Terjual -->
                            <div class="tab-pane fade" id="products" role="tabpanel">
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">Daftar Produk Terjual</h6>
                                                <small class="text-muted">
                                                    Total Qty: <span
                                                        class="fw-bold text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</span>
                                                    (Berdasarkan pembayaran periode ini)
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="dropdown">
                                                    <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                                        <i class="iconoir-view-columns-3 me-1"></i> Kolom
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-products" style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                                        <!-- Column toggles via JS -->
                                                    </ul>
                                                </div>
                                                <div class="btn-group shadow-sm">
                                                    <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('products')">
                                                        <i class="fa fa-print me-1"></i> Print
                                                    </button>
                                                    <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('sold_products')">
                                                        <i class="fa fa-file-excel me-1 text-success"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" id="table-products">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Nama
                                                        Produk</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Kode (SKU)</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Kategori</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Satuan</th>
                                                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">
                                                        Kuantitas Terjual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($soldProducts as $product)
                                                    <tr>
                                                        <td class="px-4 fw-bold text-dark">{{ $product->nama_produk }}</td>
                                                        <td class="text-muted font-monospace small">{{ $product->sku_kode }}
                                                        </td>
                                                        <td>{{ $product->nama_kategori ?? '-' }}</td>
                                                        <td>{{ $product->satuan }}</td>
                                                        <td class="px-4 text-end fw-bold text-success">
                                                            {{ number_format($product->total_qty, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5">
                                                            <div class="mb-3"><i
                                                                    class="iconoir-box-iso fs-1 text-muted opacity-50"></i>
                                                            </div>
                                                            <h6 class="fw-bold text-dark">Tidak ada produk terjual</h6>
                                                            <p class="text-muted small mb-0">Belum ada invoice lunas/terbayar
                                                                pada periode ini.</p>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td colspan="4" class="px-4 text-end text-uppercase text-muted small">
                                                        Total Kuantitas</td>
                                                    <td class="px-4 text-end text-success">
                                                        {{ number_format($totalSoldQty, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Laporan Invoice Per Produk -->
                            <div class="tab-pane fade" id="invoice-products" role="tabpanel">
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">Laporan Invoice Per Produk</h6>
                                                <small class="text-muted me-3">
                                                    Total Nilai: <span class="fw-bold text-primary">Rp
                                                        {{ number_format($summaryTotalTransaction, 0, ',', '.') }}</span>
                                                </small>
                                                <small class="text-muted">
                                                    Invoice Terlibat: <span
                                                        class="fw-bold text-dark">{{ $summaryTotalInvoices }}</span>
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="dropdown">
                                                    <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                                        <i class="iconoir-view-columns-3 me-1"></i> Kolom
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-invoice-products" style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                                        <!-- Column toggles via JS -->
                                                    </ul>
                                                </div>
                                                <div class="btn-group shadow-sm">
                                                    <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('invoice-products')">
                                                        <i class="fa fa-print me-1"></i> Print
                                                    </button>
                                                    <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('invoice_items')">
                                                        <i class="fa fa-file-excel me-1 text-success"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" id="table-invoice-products">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Status
                                                    </th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Produk</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">Mitra</th>
                                                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Invoice
                                                    </th>
                                                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Qty
                                                    </th>
                                                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Harga
                                                    </th>
                                                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Disc.
                                                        Item</th>
                                                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Tot.
                                                        Disc</th>
                                                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">
                                                        Total Akhir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($invoiceItems as $item)
                                                    <tr>
                                                        <td class="px-4">
                                                            @php
                                                                $statusClass = match ($item->status_pembayaran) {
                                                                    'Paid' => 'success',
                                                                    'Unpaid' => 'danger',
                                                                    'Partially Paid' => 'warning',
                                                                    'Overdue' => 'dark',
                                                                    default => 'secondary'
                                                                };
                                                            @endphp
                                                            <span
                                                                class="badge bg-soft-{{ $statusClass }} text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25 rounded-pill px-2">
                                                                {{ $item->status_pembayaran }}
                                                            </span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tgl_invoice)->format('d M Y') }}
                                                        </td>
                                                        <td class="fw-bold text-dark">{{ $item->nama_produk }}</td>
                                                        <td>{{ $item->nama_mitra }}</td>
                                                        <td class="text-primary fw-bold">{{ $item->nomor_invoice }}</td>
                                                        <td class="text-end">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                        <td class="text-end text-danger">
                                                            {{ number_format($item->diskon_nilai, 0, ',', '.') }}</td>
                                                        <td class="text-end text-danger">Rp
                                                            {{ number_format($item->total_diskon, 0, ',', '.') }}</td>
                                                        <td class="px-4 text-end fw-bold">Rp
                                                            {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center py-5">
                                                            <div class="mb-3"><i
                                                                    class="iconoir-list fs-1 text-muted opacity-50"></i></div>
                                                            <h6 class="fw-bold text-dark">Tidak ada data item invoice</h6>
                                                            <p class="text-muted small mb-0">Sesuaikan filter untuk melihat
                                                                data.</p>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td colspan="9" class="px-4 text-end text-uppercase text-muted small">
                                                        Total Transaksi</td>
                                                    <td class="px-4 text-end text-primary">Rp
                                                        {{ number_format($summaryTotalTransaction, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div> <!-- End Tab Content -->
            
            </div> <!-- End Card Body -->
            </div> <!-- End Tabs & Content Card -->

            <!-- Print Preview Modal -->
            <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="modalPrintPreviewLabel">
                                <i class="fa fa-print text-primary me-2"></i> Preview Cetak Laporan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe id="printFrame" style="width: 100%; height: 75vh; border: none;"></iframe>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary shadow-sm fw-bold" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary shadow-sm fw-bold" onclick="executePrint()">
                                <i class="fa fa-print me-1"></i> Cetak Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @push('js')
                <script>
                    // Declare charts globally so we can update them
                    var incomeExpenseChart = null;
                    var pieChart = null;

                    // Store hidden columns globally
                    var hiddenColumnsData = {
                        'payments': [],
                        'products': [],
                        'invoice-products': []
                    };

                    function exportReport(tab) {
                        const form = document.getElementById('filterForm');
                        const params = new URLSearchParams(new FormData(form));
                        params.append('tab', tab);
                        
                        // Append hidden columns for this specific tab
                        if (hiddenColumnsData[tab]) {
                            hiddenColumnsData[tab].forEach(colIndex => {
                                params.append('hidden_columns[]', colIndex);
                            });
                        }

                        // Remove Laravel token if present in form to avoid URL clutter
                        params.delete('_token');
                        window.location.href = "{{ route('report.invoice.export') }}?" + params.toString();
                    }

                    function printTable(tab) {
                        let tableSelector = '';
                        if (tab === 'payments') tableSelector = '#table-payments';
                        else if (tab === 'products') tableSelector = '#table-products';
                        else if (tab === 'invoice-products') tableSelector = '#table-invoice-products';
                        else return;

                        const tableEl = document.querySelector(tableSelector);
                        if (!tableEl) return;

                        // Create a clone of the table
                        const printableTable = tableEl.cloneNode(true);
                        
                        // Build HTML content for the iframe
                        const htmlContent = `
                            <html>
                            <head>
                                <title>Print Table</title>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                                <style>
                                    body { padding: 20px; font-family: 'Inter', sans-serif; }
                                    table { width: 100%; border-collapse: collapse; }
                                    th, td { padding: 8px; border: 1px solid #ddd; }
                                    .d-none { display: none !important; }
                                </style>
                            </head>
                            <body>
                                <h4>Print Laporan</h4>
                                ${printableTable.outerHTML}
                            </body>
                            </html>
                        `;
                        
                        // Get the iframe and inject content
                        const iframe = document.getElementById('printFrame');
                        const doc = iframe.contentWindow || iframe.contentDocument;
                        
                        if (doc.document) {
                            doc.document.open();
                            doc.document.write(htmlContent);
                            doc.document.close();
                        } else {
                            doc.open();
                            doc.write(htmlContent);
                            doc.close();
                        }
                        
                        // Show the modal
                        const printModal = new bootstrap.Modal(document.getElementById('modalPrintPreview'));
                        printModal.show();
                    }

                    function executePrint() {
                        const iframe = document.getElementById('printFrame');
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    }

                    function loadReportData() {
                        const form = document.getElementById('filterForm');
                        const formData = new FormData(form);
                        const params = new URLSearchParams(formData);

                        // Show loading state (optional: add spinners to tab contents)
                        document.body.style.cursor = 'wait';

                        fetch("{{ route('report.invoice') }}?" + params.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                // Update Charts
                                if (data.charts) {
                                    if (incomeExpenseChart && incomeExpenseChart.el) {
                                        incomeExpenseChart.updateOptions({
                                            series: [{
                                                data: data.charts.income
                                            }, {
                                                data: data.charts.expense
                                            }],
                                            xaxis: {
                                                categories: data.charts.labels
                                            }
                                        });
                                        // Update title manually if needed, or re-render
                                        // incomeExpenseChart.updateOptions({ title: { text: ... } }); 
                                    }

                                    if (pieChart && pieChart.el) {
                                        pieChart.updateSeries(data.charts.pieSeries);
                                    }
                                }

                                // Update Tab Contents
                                if (data.html_payments) {
                                    document.querySelector('#payments').innerHTML = data.html_payments;
                                }
                                if (data.html_products) {
                                    document.querySelector('#products').innerHTML = data.html_products;
                                }
                                if (data.html_invoice_items) {
                                    document.querySelector('#invoice-products').innerHTML = data.html_invoice_items;
                                }

                                // Re-initialize column visibility toggles after AJAX load
                                initColumnToggle('.colToggleMenu[data-target="#table-payments"]', '#table-payments', 'payments');
                                initColumnToggle('.colToggleMenu[data-target="#table-products"]', '#table-products', 'products');
                                initColumnToggle('.colToggleMenu[data-target="#table-invoice-products"]', '#table-invoice-products', 'invoice-products');

                                document.body.style.cursor = 'default';
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                document.body.style.cursor = 'default';
                                alert('Gagal memuat data. Silakan coba lagi.');
                            });
                    }

                    // Function to initialize Column Visibility Toggles
                    function initColumnToggle(menuSelector, tableSelector, tabName) {
                        const menu = document.querySelector(menuSelector);
                        const table = document.querySelector(tableSelector);
                        if (!menu || !table) return;

                        menu.innerHTML = ''; // Clear previous
                        const headers = table.querySelectorAll('thead th');
                        
                        headers.forEach((th, index) => {
                            const colName = th.textContent.trim();
                            if (!colName) return;

                            const li = document.createElement('li');
                            li.innerHTML = `
                                <div class="form-check px-3 py-1">
                                    <input class="form-check-input toggle-col-cb" type="checkbox" value="${index}" id="col-${tabName}-${index}" checked>
                                    <label class="form-check-label small" for="col-${tabName}-${index}">
                                        ${colName}
                                    </label>
                                </div>
                            `;
                            menu.appendChild(li);

                            // Add event listener
                            const cb = li.querySelector('.toggle-col-cb');
                            cb.addEventListener('change', function() {
                                const colIdx = parseInt(this.value);
                                const isChecked = this.checked;

                                // Toggle visual class
                                table.querySelectorAll(`tr > :nth-child(${colIdx + 1})`).forEach(cell => {
                                    if (isChecked) cell.classList.remove('d-none');
                                    else cell.classList.add('d-none');
                                });

                                // Update global hidden arrays
                                if (!isChecked) {
                                    if (!hiddenColumnsData[tabName].includes(colIdx)) {
                                        hiddenColumnsData[tabName].push(colIdx);
                                    }
                                } else {
                                    hiddenColumnsData[tabName] = hiddenColumnsData[tabName].filter(idx => idx !== colIdx);
                                }
                            });
                        });
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        // 1. Income vs Expenditure Chart
                        var incomeExpenseOptions = {
                            series: [{
                                name: 'Income',
                                data: @json($chartIncome)
                            }, {
                                name: 'Expenditure',
                                data: @json($chartExpense)
                            }],
                            chart: {
                                type: 'line',
                                height: 350,
                                toolbar: { show: false },
                                fontFamily: 'Inter, sans-serif'
                            },
                            colors: ['#0d6efd', '#dc3545'], // Blue for Income, Red for Expense
                            stroke: {
                                curve: 'smooth',
                                width: 3
                            },
                            dataLabels: { enabled: false },
                            xaxis: {
                                categories: @json($chartLabels),
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (value) {
                                        return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            grid: {
                                borderColor: '#f1f1f1',
                            },
                            tooltip: {
                                y: {
                                    formatter: function (value) {
                                        return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        };
                        var incomeEl = document.querySelector("#incomeExpenseChart");
                        if (incomeEl && window.ApexCharts) {
                            incomeExpenseChart = new ApexCharts(incomeEl, incomeExpenseOptions);
                            incomeExpenseChart.render();
                        }

                        // 2. Receivables Composition Pie Chart
                        var pieOptions = {
                            series: @json($pieSeries),
                            labels: @json($pieLabels),
                            chart: {
                                type: 'pie',
                                height: 350,
                                fontFamily: 'Inter, sans-serif'
                            },
                            colors: ['#ffc107', '#198754'], // Warning (Outstanding), Success (Received)
                            dataLabels: {
                                enabled: true,
                                formatter: function (val, opts) {
                                    return opts.w.globals.series[opts.seriesIndex] > 0 ? val.toFixed(1) + "%" : "";
                                }
                            },
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                y: {
                                    formatter: function (value) {
                                        return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        };
                        var pieEl = document.querySelector("#receivablesPieChart");
                        if (pieEl && window.ApexCharts) {
                            pieChart = new ApexCharts(pieEl, pieOptions);
                            pieChart.render();
                        }
                        
                        // Initialize Column Visibility
                        initColumnToggle('.colToggleMenu[data-target="#table-payments"]', '#table-payments', 'payments');
                        initColumnToggle('.colToggleMenu[data-target="#table-products"]', '#table-products', 'products');
                        initColumnToggle('.colToggleMenu[data-target="#table-invoice-products"]', '#table-invoice-products', 'invoice-products');

                        // Override Form Submit
                        const filterForm = document.getElementById('filterForm');
                        if (filterForm) {
                            filterForm.addEventListener('submit', function (e) {
                                e.preventDefault();
                                loadReportData();
                            });
                        }
                    });
                </script>
            @endpush
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.querySelector('select[name="product_id[]"]');
            if (productSelect) {
                new TomSelect(productSelect, {
                    plugins: ['remove_button'],
                    create: false,
                    persist: false,
                    placeholder: 'Pilih Produk...',
                });
            }
        });
    </script>
@endpush
