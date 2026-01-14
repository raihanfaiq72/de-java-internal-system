@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Manajemen Invoice Penjualan</h4>
                    <p class="text-muted small mb-0">Otorisasi transaksi dan pantau piutang secara real-time.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-white border fw-bold px-3 me-2 shadow-sm text-dark">
                        <i class="fa fa-file-excel me-1 text-success"></i> Export .xls
                    </button>
                    <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openInvoiceModal()">
                        <i class="fa fa-plus-circle me-1"></i> Buat Invoice Baru
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4 g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-primary">
                            <label class="f-label">Total Outstanding</label>
                            <h5 class="fw-bold mb-0 text-dark">Rp 450.000.000</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-danger">
                            <label class="f-label">Melewati Tempo</label>
                            <h5 class="fw-bold mb-0 text-danger">12 Dokumen</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-success">
                            <label class="f-label">Lunas Bulan Ini</label>
                            <h5 class="fw-bold mb-0 text-success">Rp 125.000.000</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-secondary">
                            <label class="f-label">Total Volume</label>
                            <h5 class="fw-bold mb-0 text-dark">1.240 Unit</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                        <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab" href="#invoice-active">Invoice Aktif</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#invoice-archive">Arsip</a></li>
                    </ul>
                </div>

                <div class="card-body p-4 bg-white">
                    <div class="tab-content">
                        <div class="tab-pane active" id="invoice-active">
                            
                            <!-- Filter Section -->
                            <div class="row g-2 mb-4 p-3 rounded-3 align-items-end bg-light border">
                                <div class="col-lg-3">
                                    <label class="f-label">Pencarian</label>
                                    <div class="input-group input-group-finance">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0 shadow-none" placeholder="No. Invoice / Mitra...">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label class="f-label">Status Dok.</label>
                                    <select id="filter-status-dok" class="tom-select-init">
                                        <option value="">Semua</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approved">Approved</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label class="f-label">Bayar</label>
                                    <select id="filter-status-bayar" class="tom-select-init">
                                        <option value="">Semua</option>
                                        <option value="Paid">Lunas</option>
                                        <option value="Unpaid">Belum</option>
                                        <option value="Partially Paid">Sebagian</option>
                                    </select>
                                </div>
                                <div class="col-lg-5 text-end">
                                    <div id="bulk-action-area" class="d-none d-inline-block me-2">
                                        <span class="small text-muted me-2 selected-count">0 terpilih</span>
                                        <button class="btn btn-outline-danger fw-bold py-2 px-3 shadow-sm btn-sm" onclick="bulkDelete()">
                                            <i class="fa fa-trash-can me-1"></i> HAPUS
                                        </button>
                                    </div>
                                    <button onclick="loadInvoiceData()" class="btn btn-dark fw-bold py-2 px-4 shadow-sm btn-sm">FILTER</button>
                                    <button onclick="resetFilter()" class="btn btn-light border fw-bold text-dark py-2 btn-sm">RESET</button>
                                </div>
                            </div>

                            <!-- Table Structure for Perfect Alignment -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="invoiceTable">
                                    <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-3" width="40">
                                                <input type="checkbox" class="form-check-input" id="master-checkbox" onclick="toggleSelectAll(this)">
                                            </th>
                                            <th width="40" class="text-center">#</th>
                                            <th width="150">Invoice No</th>
                                            <th width="200">Pemasok</th>
                                            <th width="120" class="text-center">Status</th>
                                            <th width="150" class="text-end">Jumlah</th>
                                            <th width="150" class="text-end">Terhutang</th>
                                            <th width="120" class="text-center">Tgl. Invoice</th>
                                            <th width="80" class="text-center">Att</th>
                                            <th width="80" class="text-end pe-3">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoiceTableBody" class="border-top-0">
                                        <!-- Rows injected by JS -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                                <span id="pagination-info" class="text-muted small fw-medium"></span>
                                <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Sales.Modal.modal-fullscreen')
@endsection

@push('css')
<style>
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    
    /* Table Styling */
    #invoiceTable thead th { border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; background: #f8fafc; padding-top: 12px; padding-bottom: 12px; }
    #invoiceTable tbody td { font-size: 13px; color: #334155; padding-top: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
    .clickable-row { cursor: pointer; transition: background-color 0.1s; }
    .clickable-row:hover { background-color: #f1f5f9 !important; }
    
    /* Nested Detail Row */
    .detail-row td { padding: 0 !important; border-bottom: 0 !important; }
    .detail-wrapper { background: #f8fafc; border-bottom: 2px solid #e2e8f0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }

    /* Badges */
    .f-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
    .f-paid { background: #dcfce7; color: #166534; }
    .f-unpaid { background: #fee2e2; color: #991b1b; }
    .f-partial { background: #fef3c7; color: #92400e; }

    /* Chevron */
    .chevron-icon { transition: transform 0.2s; color: #cbd5e1; font-size: 10px; margin-right: 2px; }
    .clickable-row[aria-expanded="true"] .chevron-icon { transform: rotate(90deg); color: #3b82f6; }

    /* Dropdown */
    .dropdown-menu-finance { border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-radius: 8px; min-width: 140px; border-top: 3px solid #3b82f6; }
    .dropdown-menu-finance .dropdown-item { font-size: 13px; font-weight: 600; color: #475569; padding: 6px 12px; }
    .dropdown-menu-finance .dropdown-item:hover { background-color: #f1f5f9; color: #3b82f6; }
</style>
@endpush

@push('js')
<script>
    if (!window.financeApp) {
        window.financeApp = {
            API_URL: '/api/invoice-api',
            selectedIds: [],
            formatIDR: (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val),
            formatDate: (dateStr) => {
                if(!dateStr) return '-';
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }
        };
    }

    async function loadInvoiceData(url = window.financeApp.API_URL) {
        const tbody = document.getElementById('invoiceTableBody');
        tbody.innerHTML = '<tr><td colspan="10" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data...</p></td></tr>';
        resetSelection();

        try {
            let finalUrl = (typeof url === 'string' && url.includes('/')) ? url : window.financeApp.API_URL;
            const params = new URLSearchParams({
                tipe_invoice: 'Sales', 
                search: document.getElementById('filter-search').value,
                status_dok: document.getElementById('filter-status-dok').value,
                status_pembayaran: document.getElementById('filter-status-bayar').value
            });

            const response = await fetch(`${finalUrl}${finalUrl.includes('?') ? '&' : '?'}${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                renderInvoiceList(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-danger p-4">Gagal memuat data.</td></tr>';
        }
    }

    function renderInvoiceList(data) {
        const tbody = document.getElementById('invoiceTableBody');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center p-5 text-muted fst-italic">Data tidak ditemukan.</td></tr>';
            return;
        }

        data.forEach((item, index) => {
            const payClass = item.status_pembayaran === 'Paid' ? 'f-paid' : (item.status_pembayaran === 'Unpaid' ? 'f-unpaid' : 'f-partial');
            const amountDue = item.total_akhir - (item.payment_sum_jumlah_bayar || 0);
            const amountDueClass = amountDue > 0 ? 'text-danger' : 'text-success';
            
            // Generate Detail Items HTML
            let itemRows = '';
            item.items?.forEach((it, idx) => {
                itemRows += `
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td class="text-muted text-center" style="width: 40px;">${idx + 1}</td>
                    <td class="fw-medium">${it.nama_produk_manual || it.product?.nama_produk || '-'}</td>
                    <td class="text-center">${parseFloat(it.qty)}</td>
                    <td class="text-end f-mono text-muted">${window.financeApp.formatIDR(it.harga_satuan)}</td>
                    <td class="text-end fw-bold f-mono">${window.financeApp.formatIDR(it.total_harga_item)}</td>
                </tr>`;
            });

            // Status Badge
            let statusDokBadge = '';
            switch(item.status_dok) {
                case 'Approved': statusDokBadge = '<span class="badge bg-soft-primary text-primary" style="font-size:10px;">APPROVED</span>'; break;
                case 'Draft': statusDokBadge = '<span class="badge bg-soft-secondary text-secondary" style="font-size:10px;">DRAFT</span>'; break;
                default: statusDokBadge = `<span class="badge bg-soft-dark text-dark" style="font-size:10px;">${item.status_dok}</span>`;
            }

            // Main Row
            const trMain = `
                <tr class="clickable-row" data-bs-toggle="collapse" data-bs-target="#detail-${item.id}" aria-expanded="false">
                    <td class="ps-3" onclick="event.stopPropagation()">
                        <input type="checkbox" class="form-check-input invoice-checkbox" data-id="${item.id}" onclick="updateSelection()">
                    </td>
                    <td class="text-center small text-muted"><span class="chevron-icon"><i class="fa fa-chevron-right"></i></span>${index + 1}</td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 13px;">${item.nomor_invoice}</div>
                        <div class="small text-muted" style="font-size: 11px;">${item.ref_no || '-'}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-truncate" style="max-width: 180px;">${item.mitra ? item.mitra.nama : 'Umum'}</div>
                        <div class="small text-muted" style="font-size: 11px;">${item.mitra?.nomor_mitra || '-'}</div>
                    </td>
                    <td class="text-center">
                        <div>${statusDokBadge}</div>
                        <div class="mt-1"><span class="f-badge ${payClass}">${item.status_pembayaran}</span></div>
                    </td>
                    <td class="text-end fw-bold f-mono text-dark">${window.financeApp.formatIDR(item.total_akhir)}</td>
                    <td class="text-end fw-bold f-mono ${amountDueClass}">${window.financeApp.formatIDR(amountDue)}</td>
                    <td class="text-center">
                        <div class="text-dark fw-medium small">${window.financeApp.formatDate(item.tgl_invoice)}</div>
                        <div class="text-danger" style="font-size: 10px;">Due: ${window.financeApp.formatDate(item.tgl_jatuh_tempo)}</div>
                    </td>
                     <td class="text-center text-muted"><i class="fa fa-paperclip"></i></td>
                     <td class="text-end pe-3" onclick="event.stopPropagation()">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-muted" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-finance">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="openInvoiceModal(${item.id})"><i class="fa fa-pencil text-primary me-2"></i> Edit Data</a></li>
                                <li><a class="dropdown-item" href="{{ url('sales/print') }}/${item.id}" target="_blank"><i class="fa fa-print text-dark me-2"></i> Cetak Nota</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteInvoice(${item.id})"><i class="fa fa-trash me-2"></i> Hapus</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `;

            // Detail Row
            const trDetail = `
                <tr class="detail-row">
                    <td colspan="10" class="p-0">
                        <div class="collapse detail-wrapper" id="detail-${item.id}">
                            <div class="p-4">
                                <h6 class="fw-bold text-uppercase text-secondary small mb-3"><i class="fa fa-list me-2"></i>Rincian Produk</h6>
                                <table class="table table-sm bg-white border rounded overflow-hidden mb-0">
                                    <thead class="bg-light text-secondary small">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Item Produk</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>${itemRows}</tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            tbody.insertAdjacentHTML('beforeend', trMain + trDetail);
        });
    }

    // Checkbox & Utils
    function toggleSelectAll(master) {
        document.querySelectorAll('.invoice-checkbox').forEach(cb => cb.checked = master.checked);
        updateSelection();
    }
    function updateSelection() {
        const chk = document.querySelectorAll('.invoice-checkbox:checked');
        window.financeApp.selectedIds = Array.from(chk).map(c => c.dataset.id);
        const area = document.getElementById('bulk-action-area');
        if(window.financeApp.selectedIds.length > 0) {
            area.classList.remove('d-none');
            area.querySelector('.selected-count').innerText = `${window.financeApp.selectedIds.length} terpilih`;
        } else {
            area.classList.add('d-none');
        }
    }
    function resetSelection() {
        window.financeApp.selectedIds = [];
        if(document.getElementById('master-checkbox')) document.getElementById('master-checkbox').checked = false;
        updateSelection();
    }
    async function deleteInvoice(id) {
        if (!confirm('Hapus invoice ini?')) return;
        try {
            const res = await fetch(`${window.financeApp.API_URL}/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const dat = await res.json();
            if (dat.success) { alert('Terhapus'); loadInvoiceData(); }
            else alert(dat.message);
        } catch (e) { alert('Gagal menghapus'); }
    }
    function bulkDelete() { alert('Fitur hapus massal segera hadir.'); }
    function renderPagination(meta) { /* reusing prev logic or simplified */
        const c = document.getElementById('pagination-container'); c.innerHTML = '';
        document.getElementById('pagination-info').innerText = `${meta.from||0}-${meta.to||0} dari ${meta.total}`;
        meta.links.forEach(l => {
             const cls = l.active ? 'bg-primary text-white' : 'bg-white text-dark';
             c.insertAdjacentHTML('beforeend', `<li class="page-item ${!l.url?'disabled':''}"><a class="page-link border-0 mx-1 rounded shadow-sm fw-bold ${cls}" href="#" onclick="loadInvoiceData('${l.url}')">${l.label}</a></li>`);
        });
    }
    function resetFilter() {
        document.getElementById('filter-search').value = '';
        document.querySelectorAll('.tom-select-init').forEach(s => s.tomselect?.clear());
        loadInvoiceData();
    }

    document.addEventListener('DOMContentLoaded', () => loadInvoiceData());
</script>
@endpush