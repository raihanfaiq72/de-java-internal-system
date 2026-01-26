@extends('Layout.main')

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('sales') }}" class="btn btn-light border me-3 shadow-sm rounded-circle"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Detail Invoice</h4>
                                <p class="text-muted small mb-0" id="header-subtitle">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span id="header-status-badge" class="badge bg-secondary fs-6 px-3 py-2 rounded-pill me-2">Loading...</span>
                        <a id="btn-print" href="#" target="_blank"
                            class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2 d-none">
                            <i class="fa fa-print me-1"></i> Cetak
                        </a>
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="alert('Fitur Edit akan segera hadir')">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </button>
                    </div>
                </div>

                <div class="row" id="main-content" style="display:none;">
                    <!-- Left Column: Invoice Visual -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-5 bg-white">
                                <!-- Reusing SalesNota structure but styled for Web -->
                                <div class="d-flex justify-content-between mb-5">
                                    <div>
                                        <h3 class="fw-bold text-primary mb-1">INVOICE</h3>
                                        <p class="text-muted mb-0" id="inv-no">-</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold text-dark" id="inv-date">-</h5>
                                        <p class="text-muted small">Tanggal Invoice</p>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Ditagihkan Kepada:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="mitra-name">-</h6>
                                        <p class="text-muted small mb-0" id="mitra-address">-</p>
                                        <p class="text-muted small" id="mitra-phone">-</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Detail Pengiriman:</label>
                                        <p class="mb-1"><span class="text-muted">Salesman/Ref:</span> <span class="fw-bold" id="inv-ref">-</span></p>
                                        <p class="mb-1"><span class="text-muted">Jatuh Tempo:</span> <span class="fw-bold text-danger" id="inv-due">-</span></p>
                                    </div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-borderless align-middle">
                                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                            <tr>
                                                <th class="py-3 ps-4 rounded-start">Item Produk</th>
                                                <th class="py-3 text-center">Qty</th>
                                                <th class="py-3 text-end">Harga</th>
                                                <th class="py-3 text-end pe-4 rounded-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-body">
                                            <!-- JS populated -->
                                        </tbody>
                                        <tfoot class="border-top">
                                            <tr>
                                                <td colspan="3" class="text-end pt-4 pb-2 text-muted">Subtotal</td>
                                                <td class="text-end pt-4 pb-2 fw-bold pe-4" id="val-subtotal">Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-2 text-muted">Diskon</td>
                                                <td class="text-end py-2 fw-bold text-danger pe-4" id="val-discount">- Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-2 text-muted">Pajak (PPN)</td>
                                                <td class="text-end py-2 fw-bold pe-4" id="val-tax">Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-3 fs-5 fw-bold text-dark">Total Tagihan</td>
                                                <td class="text-end py-3 fs-5 fw-bold text-primary pe-4" id="val-total">Rp 0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded border border-light">
                                            <p class="mb-1 fw-bold text-dark small">Catatan:</p>
                                            <p class="mb-0 text-muted small fst-italic" id="inv-notes">Tidak ada catatan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="col-lg-4">
                        <!-- Documents Sidebar -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-folder-open text-warning me-2"></i> Dokumen Pendukung</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-danger">
                                                <i class="fa fa-file-pdf fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Faktur Penjualan</h6>
                                                <small class="text-muted" style="font-size: 11px;" id="sidebar-inv-no">Otomatis</small>
                                            </div>
                                        </div>
                                        <a id="sidebar-btn-print" href="#" target="_blank" class="btn btn-sm btn-light border"><i class="fa fa-eye"></i></a>
                                    </div>

                                    <!-- Placeholder for other docs -->
                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-secondary">
                                                <i class="fa fa-file-invoice fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Surat Jalan</h6>
                                                <small class="text-warning" style="font-size: 11px;">Belum Diunggah</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-white border text-primary"><i class="fa fa-upload"></i></button>
                                    </div>

                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-secondary">
                                                <i class="fa fa-receipt fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Bukti Pembayaran</h6>
                                                <small class="text-muted" style="font-size: 11px;" id="payment-count">
                                                    0 Transaksi
                                                </small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#payment-history"><i class="fa fa-chevron-down"></i></button>
                                    </div>
                                    
                                    <div class="collapse bg-light" id="payment-history">
                                        <div class="p-3" id="payment-list">
                                            <p class="text-center text-muted small mb-0">Belum ada pembayaran.</p>
                                        </div>
                                    </div>

                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-success">
                                                <i class="fa fa-calculator fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Jurnal Akuntansi</h6>
                                                <small class="text-muted" style="font-size: 11px;">Terbentuk Otomatis</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#journal-entries"><i class="fa fa-chevron-down"></i></button>
                                    </div>

                                    <div class="collapse bg-light" id="journal-entries">
                                        <div class="p-3">
                                            <div class="alert alert-info small mb-0 p-2">
                                                <i class="fa fa-info-circle me-1"></i> Jurnal tercatat di sistem Accounting.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity/Status Sidebar -->
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-history text-primary me-2"></i> Riwayat Status</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="timeline-simple">
                                    <div class="d-flex mb-3">
                                        <div class="me-3 d-flex flex-column align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;"><i class="fa fa-check"></i></div>
                                            <div class="h-100 border-start border-2 mt-1" style="border-color: #e2e8f0;"></div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 small">Invoice Dibuat</h6>
                                            <p class="text-muted small mb-0" id="created-at">-</p>
                                            <p class="text-muted small fst-italic">Dibuat oleh System</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="loading-state" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Memuat data invoice...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    const invoiceId = '{{ $id ?? $invoice->id }}'; // Handle both transition cases
    const API_URL = '{{ url('api/invoice-api') }}';

    async function loadInvoiceDetail() {
        try {
            const response = await fetch(`${API_URL}/${invoiceId}`);
            const result = await response.json();
            
            if(result.success) {
                renderInvoice(result.data);
                document.getElementById('loading-state').style.display = 'none';
                document.getElementById('main-content').style.display = 'flex';
            } else {
                alert('Invoice tidak ditemukan');
            }
        } catch(e) {
            console.error(e);
            alert('Gagal memuat data');
        }
    }

    function renderInvoice(data) {
        // Header
        document.getElementById('header-subtitle').textContent = `No. ${data.nomor_invoice}`;
        
        const badge = document.getElementById('header-status-badge');
        badge.textContent = data.status_pembayaran;
        badge.className = `badge fs-6 px-3 py-2 rounded-pill me-2 ${
            data.status_pembayaran === 'Paid' ? 'bg-success' : 
            (data.status_pembayaran === 'Unpaid' ? 'bg-danger' : 'bg-warning')
        }`;

        document.getElementById('btn-print').href = `{{ url('sales/print') }}/${data.id}`;
        document.getElementById('btn-print').classList.remove('d-none');

        // Main Visual
        document.getElementById('inv-no').textContent = `#${data.nomor_invoice}`;
        document.getElementById('inv-date').textContent = formatDate(data.tgl_invoice);
        
        // Mitra
        if(data.mitra) {
            document.getElementById('mitra-name').textContent = data.mitra.nama;
            document.getElementById('mitra-address').textContent = data.mitra.alamat || '-';
            document.getElementById('mitra-phone').textContent = data.mitra.no_telp || '-';
        } else {
            document.getElementById('mitra-name').textContent = 'Umum';
        }

        document.getElementById('inv-ref').textContent = data.ref_no || '-';
        document.getElementById('inv-due').textContent = formatDate(data.tgl_jatuh_tempo);

        // Items
        const tbody = document.getElementById('items-body');
        tbody.innerHTML = '';
        if(data.items) {
            data.items.forEach(item => {
                tbody.innerHTML += `
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">${item.product?.nama_produk || item.nama_produk_manual || '-'}</div>
                            <div class="small text-muted">${item.product?.kode_produk || '-'}</div>
                        </td>
                        <td class="text-center py-3">${parseFloat(item.qty)} ${item.product?.unit?.nama_unit || ''}</td>
                        <td class="text-end py-3">${formatIDR(item.harga_satuan)}</td>
                        <td class="text-end pe-4 py-3 fw-bold text-dark">${formatIDR(item.total_harga_item)}</td>
                    </tr>
                `;
            });
        }

        // Totals
        document.getElementById('val-subtotal').textContent = formatIDR(data.subtotal || data.total_akhir); // Fallback if subtotal not calc in BE
        document.getElementById('val-discount').textContent = `- ${formatIDR(data.diskon_global || 0)}`;
        document.getElementById('val-tax').textContent = formatIDR(data.pajak_ppn || 0);
        document.getElementById('val-total').textContent = formatIDR(data.total_akhir);

        document.getElementById('inv-notes').textContent = data.catatan || 'Tidak ada catatan.';

        // Sidebar
        document.getElementById('sidebar-inv-no').textContent = `Otomatis - ${data.nomor_invoice}`;
        document.getElementById('sidebar-btn-print').href = `{{ url('sales/print') }}/${data.id}`;

        // Payments
        const payCount = data.payment ? data.payment.length : 0;
        document.getElementById('payment-count').textContent = `${payCount} Transaksi`;
        
        const payList = document.getElementById('payment-list');
        if(payCount > 0) {
            payList.innerHTML = '';
            data.payment.forEach(pay => {
                payList.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <div>
                            <div class="fw-bold small text-dark">${pay.nomor_pembayaran || '-'}</div>
                            <div class="text-muted" style="font-size: 10px;">${formatDate(pay.tgl_bayar)}</div>
                        </div>
                        <div class="fw-bold text-success small">${formatIDR(pay.jumlah_bayar)}</div>
                    </div>
                `;
            });
        }

        document.getElementById('created-at').textContent = new Date(data.created_at).toLocaleString('id-ID');
    }

    function formatIDR(val) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
    }

    function formatDate(dateStr) {
        if(!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric'
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadInvoiceDetail();
    });
</script>
@endpush
