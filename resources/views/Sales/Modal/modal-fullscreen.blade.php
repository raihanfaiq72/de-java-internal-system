<style>
    /* Overlay Hitam untuk Modal Berlapis */
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 1065;
        display: none;
    }
    #modalMitraHistory, #stockModal, #mitraModal {
        z-index: 1070 !important;
    }
</style>
<div class="modal fade" id="invoiceModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <!-- Header -->
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-3">
                        <i class="fa fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Invoice Penjualan</h5>
                        <p class="text-muted small mb-0">Kelola transaksi penjualan dengan pelanggan.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border text-secondary fw-semibold px-4"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveFullInvoice()">
                        <i class="fa fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>

            <div class="modal-body p-0">
                <form id="invoiceForm" class="h-100">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-0 h-100">
                        <!-- Left Panel: Main Form -->
                        <div class="col-lg-9 h-100 overflow-auto custom-scrollbar p-4">

                            <!-- Top Section: Customer & Doc Details -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <!-- Customer Selection -->
                                        <div class="col-lg-6 border-end">
                                            <label class="f-label mb-2">Mitra <span class="text-danger">*</span></label>
                                            <select id="modal_mitra_id" class="form-select f-input-lg mb-2"
                                                onchange="renderMitraDetail()">
                                                <option value="">Cari Mitra...</option>
                                            </select>
                                            <div class="text-end mb-3">
                                                <button type="button"
                                                    class="btn btn-sm btn-link text-primary text-decoration-none fw-bold p-0"
                                                    onclick="openMitraModal()">
                                                    <i class="fa fa-plus-circle me-1"></i> Tambah Mitra Baru
                                                </button>
                                            </div>

                                            <!-- Mitra Detail Card -->
                                            <div id="mitra_detail_display" class="d-none bg-light p-3 rounded border">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="fw-bold text-dark" id="disp_mitra_nama">-</div>
                                                    <div class="ms-auto d-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-xs btn-outline-primary fw-bold py-1 px-2" 
                                                            style="font-size: 10px;" onclick="showMitraHistoryFromModal()">
                                                            <i class="fa fa-history me-1"></i> Riwayat
                                                        </button>
                                                        <span class="badge bg-primary" id="disp_mitra_tipe">-</span>
                                                    </div>
                                                </div>
                                                <div class="small text-muted mb-1"><i
                                                        class="fa fa-map-marker-alt me-2 text-secondary"></i><span
                                                        id="disp_mitra_alamat">-</span></div>
                                                <div class="small text-muted"><i
                                                        class="fa fa-phone me-2 text-secondary"></i><span
                                                        id="disp_mitra_telp">-</span></div>
                                            </div>
                                            <div id="mitra_empty_state"
                                                class="text-center py-4 text-muted border border-dashed rounded bg-light">
                                                <i class="fa fa-user-plus mb-2 fs-4 text-secondary"></i>
                                                <div class="small">Pilih pelanggan untuk melihat detail</div>
                                            </div>
                                        </div>

                                        <!-- Document Details -->
                                        <div class="col-lg-6">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="f-label">Nomor Invoice</label>
                                                    <input type="text" id="modal_nomor_invoice"
                                                        class="form-control f-input fw-bold bg-light text-primary"
                                                        placeholder="Auto Generated">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Referensi / PO</label>
                                                    <input type="text" id="modal_ref_no" class="form-control f-input"
                                                        placeholder="Contoh: PO-001">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Tanggal Invoice</label>
                                                    <input type="text" id="modal_tgl_invoice"
                                                        class="form-control f-input">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Tanggal Jatuh Tempo</label>
                                                    <input type="text" id="modal_tgl_jatuh_tempo"
                                                        class="form-control f-input">
                                                </div>

                                                <div class="col-12">
                                                    <label class="f-label">Salesperson</label>
                                                    <select id="modal_sales_id" class="form-select f-input">
                                                        <option value="0">Tanpa Sales Person</option>
                                                        <option value="">Pilih Sales...</option>
                                                        @foreach ($users ?? [] as $u)
                                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Section -->
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                        <i class="fa fa-box me-2 text-primary"></i>Rincian Barang & Jasa
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill"
                                            onclick="openStockModal()">
                                            <i class="fa fa-search me-1"></i> Cari Stok
                                        </button>
                                        <button type="button"
                                            class="btn btn-primary btn-sm fw-bold px-3 rounded-pill"
                                            onclick="addNewProductRow()">
                                            <i class="fa fa-plus me-1"></i> Tambah Baris
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover" id="mainItemTable">
                                        <thead class="bg-light text-secondary small fw-bold text-uppercase">
                                            <tr>
                                                <th class="ps-4 py-3" width="22%">Produk / Deskripsi</th>
                                                <th class="text-center" width="8%">Qty</th>
                                                <th class="text-center" width="6%">Tempo</th>
                                                <th class="text-center" width="8%">X Tempo</th>
                                                <th class="text-center" width="6%">Satuan</th>
                                                <th class="text-end" width="12%">Harga</th>
                                                <th class="text-center" width="10%">Tipe Disc</th>
                                                <th class="text-end" width="11%">Disc</th>
                                                <th class="text-end pe-4" width="13%">Total</th>
                                                <th width="4%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemBodyList" class="border-top-0"></tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="10"
                                                    class="text-center py-2 text-muted small fst-italic">
                                                    Klik tombol tambah untuk memasukkan item
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Bottom Notes -->
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="f-label mb-2">Catatan Internal</label>
                                            <textarea id="modal_keterangan" class="form-control f-text bg-light border-0" rows="3"
                                                placeholder="Catatan untuk tim internal..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label mb-2">Syarat & Ketentuan</label>
                                            <textarea id="modal_syarat" class="form-control f-text bg-light border-0" rows="3">1. Pembayaran ditujukan ke rekening perusahaan.&#10;2. Barang yang dibeli tidak dapat dikembalikan.</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Right Panel: Summary -->
                        <div class="col-lg-3 bg-white border-start h-100 d-flex flex-column shadow-sm z-1">
                            <div class="p-4 border-bottom bg-light">
                                <h6 class="fw-bold text-dark mb-0">Ringkasan Pembayaran</h6>
                            </div>
                            <div class="p-4 flex-grow-1 overflow-auto">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span class="fw-bold text-dark" id="summary_subtotal">Rp 0</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Diskon Tambahan</label>
                                        <div class="input-group input-group-sm">
                                            <select id="modal_diskon_tambahan_tipe"
                                                class="form-select border-end-0 text-dark" style="max-width: 85px;"
                                                onchange="calculateInvoiceTotal()">
                                                <option value="Fixed">Rp</option>
                                                <option value="Percentage">%</option>
                                            </select>
                                            <input type="text" id="modal_diskon_tambahan"
                                                class="form-control text-end" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Biaya Lain-lain</label>
                                        <input type="text" id="modal_biaya_lain"
                                            class="form-control form-control-sm text-end" value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Cashback</label>
                                        <input type="text" id="modal_cashback"
                                            class="form-control form-control-sm text-end" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25">
                                <div class="text-center">
                                    <div class="small text-primary fw-bold text-uppercase mb-1">Total Tagihan</div>
                                    <h3 class="fw-bold text-primary mb-0" id="summary_grand_total">Rp 0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<!-- Stock Modal remains largely the same, just refined styling -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4" style="height: 90vh;">
            <div class="modal-header bg-dark border-bottom py-3">
                <h5 class="modal-title fw-bold">Pilih Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="stockSearchInput"
                                placeholder="Cari produk (nama, kode, kategori)...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="stockCategoryFilter">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetStockFilters()">
                                <i class="fa fa-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Selected Items Chips -->
                <div id="selectedStockContainer" class="p-3 border-bottom bg-white d-none" style="max-height: 120px; overflow-y: auto;">
                    <div class="small text-muted mb-2 fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                        Item Terpilih:
                    </div>
                    <div class="d-flex flex-wrap gap-2" id="selectedStockList"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="stockTable">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th width="5%" class="text-center py-3"><input type="checkbox"
                                        class="form-check-input" id="checkAllStock" onclick="toggleAllStock(this)">
                                </th>
                                <th class="py-3">Produk</th>
                                <th class="py-3">Supplier</th>
                                <th class="py-3">Kategori</th>
                                <th class="text-center py-3">Stok</th>
                                <th class="text-end py-3">Harga Jual</th>
                                <th class="text-center py-3">Track</th>
                            </tr>
                        </thead>
                        <tbody id="stockBodyList"></tbody>
                    </table>
                </div>
                <div id="stockPagination" class="p-3 bg-light border-top d-flex justify-content-center"></div>

            </div>
            <div class="modal-footer bg-light border-top py-3">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="small text-muted">
                        <i class="fa fa-info-circle me-1"></i> Pilih produk yang akan ditambahkan ke invoice.
                    </div>
                    <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill"
                        onclick="addSelectedStocks()">
                        <i class="fa fa-check me-2"></i> Tambahkan Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Invoice Mitra -->
<div class="modal fade" id="modalMitraHistory" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">
                    <i class="fa fa-history text-primary me-2"></i> Riwayat Nota: <span id="history-mitra-name">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="min-height: 500px;">
                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group input-group-sm shadow-sm border rounded">
                        <span class="input-group-text bg-white border-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="mitra-history-search" class="form-control border-0 ps-0 shadow-none" 
                            placeholder="Cari No. Nota / Tanggal (YYYY-MM-DD)..." onkeyup="debounceMitraHistorySearch()">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light small fw-bold text-muted text-uppercase">
                            <tr>
                                <th class="py-3 ps-3 sortable-history" onclick="sortMitraHistory('nomor_invoice')" style="cursor:pointer;">
                                    No. Nota <i class="fa fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="py-3 sortable-history" onclick="sortMitraHistory('tgl_invoice')" style="cursor:pointer;">
                                    Tgl Nota <i class="fa fa-sort ms-1 opacity-50"></i>
                                </th>
                                <th class="py-3">Umur Nota</th>
                                <th class="py-3 text-end pe-3 sortable-history" onclick="sortMitraHistory('total_akhir')" style="cursor:pointer;">
                                    Total Tagihan <i class="fa fa-sort ms-1 opacity-50"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="mitra-history-body">
                            <!-- populated by JS -->
                        </tbody>
                    </table>
                </div>
                <div id="mitra-history-pagination" class="mt-3 d-flex justify-content-center">
                    <!-- pagination controls -->
                </div>
            </div>
        </div>
    </div>
</div>

<template id="productRowTemplate">
    <tr class="border-bottom border-light product-row">
        <td class="ps-4 py-3">
            <input type="hidden" class="prod-id">
            <input type="hidden" class="prod-initial-id" value="">
            <input type="hidden" class="prod-initial-qty" value="0">
            <input type="hidden" class="prod-max-stok" value="0">
            <input type="hidden" class="prod-original-qty" value="0">
            <div class="mb-1">
                <select class="prod-select-item"></select>
            </div>
            <input type="text" class="form-control form-control-sm text-muted small prod-desc"
                placeholder="Deskripsi (Opsional)">
        </td>
        <td class="py-3">
            <input type="number" class="form-control form-control-sm text-center prod-qty" value="1"
                min="1" oninput="calculateInvoiceTotal()">
        </td>
        <td class="py-3 text-center">
            <div class="form-check form-switch p-0 m-0 d-inline-block">
                <input class="form-check-input prod-is-tempo shadow-none ms-0" type="checkbox"
                    onchange="toggleRowTempo(this)">
            </div>
        </td>
        <td class="py-3 text-center">
            <span class="text-muted tempo-placeholder">-</span>
            <div class="tempo-container d-none">
                <input type="number" class="form-control form-control-sm text-center prod-tempo-val" value="0"
                    min="0" oninput="calculateInvoiceTotal()">
            </div>
        </td>
        <td class="py-3 text-center">
            <span class="badge bg-light text-secondary border prod-unit-label">-</span>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm text-end prod-price rupiah-input"
                value="0">
        </td>
        <td class="py-3">
            <select class="form-select form-select-sm prod-disc-tipe" onchange="calculateInvoiceTotal()">
                <option value="Fixed">Rp</option>
                <option value="Percentage">%</option>
            </select>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm text-end prod-disc rupiah-input"
                value="0">
        </td>
        <td class="py-3 text-end pe-4 fw-bold text-dark prod-subtotal">Rp 0</td>
        <td class="py-3 text-center">
            <button type="button" class="btn btn-link text-danger p-0 btn-remove-row">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<template id="stockRowTemplate">
    <tr class="stock-item-row">
        <td class="text-center">
            <input type="checkbox" class="form-check-input stock-check">
        </td>
        <td>
            <div class="fw-bold text-dark col-stock-nama"></div>
            <div class="small text-muted col-stock-sku"></div>
        </td>
        <td class="text-start col-stock-supplier"></td>
        <td><span class="badge bg-light text-dark border col-stock-kategori"></span></td>
        <td class="text-center fw-bold col-stock-qty"></td>
        <td class="text-end font-monospace col-stock-harga"></td>
        <td class="text-center"><span class="badge col-stock-track"></span></td>
    </tr>
</template>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .f-input {
        padding: 0.75rem 1rem !important;
        font-size: 14px !important;
    }

    .flatpickr-wrapper {
        width: 100% !important;
        display: block !important;
    }

    .flatpickr-input.form-control[readonly] {
        background-color: #fff !important;
    }

    .flatpickr-input,
    .flatpickr-input+input {
        width: 100% !important;
        display: block !important;
    }

    .flatpickr-calendar {
        z-index: 10000 !important;
    }

    .flatpickr-calendar.fp-has-sidebar {
        width: 440px !important;
    }

    .flatpickr-calendar .fp-sidebar-abs {
        position: absolute;
        left: 0;
        width: 130px;
        top: 0;
        bottom: 0;
        border-right: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 12px 8px;
        z-index: 5;
    }

    .flatpickr-calendar.fp-has-sidebar .flatpickr-innerContainer {
        margin-left: 130px;
        width: 310px;
    }

    .fp-title {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .fp-sidebar-abs .btn {
        width: 100%;
        font-size: 11px;
        padding: 6px;
        margin-bottom: 5px;
        text-align: left;
        font-weight: 600;
    }

    /* Pastikan bulan/tahun tetap di atas sidebar */
    .flatpickr-months {
        z-index: 10 !important;
        pointer-events: auto !important;
    }

    .flatpickr-current-month select {
        appearance: auto !important;
    }

    /* Tempo UI Styling */
    .tempo-input-group {
        max-width: 100px;
        margin: 0 auto;
    }

    .tempo-input-group .input-group-text {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        background-color: #f1f5f9;
        color: #64748b;
        border-color: #e2e8f0;
    }

    .prod-tempo-val:disabled {
        background-color: #f8fafc !important;
        opacity: 0.6;
    }

    .row-tempo-active {
        background-color: rgba(37, 99, 235, 0.02);
    }

    .prod-is-tempo:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    let historySortBy = 'tgl_invoice';
    let historySortDir = 'desc';
    let historySearch = '';
    let historyDebounceTimer = null;

    function debounceMitraHistorySearch() {
        clearTimeout(historyDebounceTimer);
        historyDebounceTimer = setTimeout(() => {
            historySearch = document.getElementById('mitra-history-search').value;
            const mitraId = document.getElementById('modal_mitra_id').value;
            fetchMitraHistory(mitraId, 1);
        }, 500);
    }

    function sortMitraHistory(column) {
        if (historySortBy === column) {
            historySortDir = historySortDir === 'asc' ? 'desc' : 'asc';
        } else {
            historySortBy = column;
            historySortDir = 'asc';
        }
        
        // Update icons
        updateHistorySortIcons();
        
        const mitraId = document.getElementById('modal_mitra_id').value;
        fetchMitraHistory(mitraId, 1);
    }

    function updateHistorySortIcons() {
        document.querySelectorAll('.sortable-history i').forEach(i => {
            i.className = 'fa fa-sort ms-1 opacity-50';
        });
        
        const headers = document.querySelectorAll('.sortable-history');
        headers.forEach(th => {
            if (th.getAttribute('onclick').includes(`'${historySortBy}'`)) {
                const icon = th.querySelector('i');
                icon.className = `fa fa-sort-${historySortDir === 'asc' ? 'up' : 'down'} ms-1 text-primary`;
            }
        });
    }

    function showMitraHistoryFromModal(page = 1) {
        const mitraId = document.getElementById('modal_mitra_id').value;
        if (!mitraId) return;

        // Reset state on open
        historySortBy = 'tgl_invoice';
        historySortDir = 'desc';
        historySearch = '';
        const searchInput = document.getElementById('mitra-history-search');
        if (searchInput) searchInput.value = '';
        updateHistorySortIcons();

        const mitraName = document.getElementById('disp_mitra_nama').textContent;
        document.getElementById('history-mitra-name').textContent = mitraName;

        const modalEl = document.getElementById('modalMitraHistory');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        // Show custom backdrop
        showCustomBackdrop();
        
        modal.show();

        fetchMitraHistory(mitraId, page);
        
        // Hide custom backdrop when modal hidden
        modalEl.addEventListener('hidden.bs.modal', function() {
            hideCustomBackdrop();
        }, { once: true });
    }

    function showCustomBackdrop() {
        let backdrop = document.querySelector('.modal-backdrop-custom');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop-custom';
            document.body.appendChild(backdrop);
        }
        backdrop.style.display = 'block';
    }

    function hideCustomBackdrop() {
        const backdrop = document.querySelector('.modal-backdrop-custom');
        if (backdrop) {
            backdrop.style.display = 'none';
        }
    }

    async function fetchMitraHistory(mitraId, page = 1) {
        const body = document.getElementById('mitra-history-body');
        body.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>';

        try {
            const api_url = window.financeApp ? window.financeApp.API_URL : '/api/invoice-api';
            const params = new URLSearchParams({
                mitra_id: mitraId,
                tipe_invoice: 'Sales',
                per_page: 5,
                page: page,
                sort_by: historySortBy,
                sort_dir: historySortDir,
                search: historySearch
            });
            const response = await fetch(`${api_url}?${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                renderMitraHistory(result.data, mitraId);
            }
        } catch (e) {
            console.error(e);
            body.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
        }
    }

    function renderMitraHistory(data, mitraId) {
        const body = document.getElementById('mitra-history-body');
        const pagination = document.getElementById('mitra-history-pagination');

        body.innerHTML = '';

        const items = data.data || [];
        if (items.length === 0) {
            body.innerHTML = '<tr><td colspan="4" class="text-center py-4">Belum ada riwayat nota</td></tr>';
            pagination.innerHTML = '';
            return;
        }

        items.forEach(inv => {
            let ageDisplay = '';
            if (inv.status_pembayaran === 'Paid') {
                ageDisplay = '<span class="badge bg-soft-success text-success" style="background-color: rgba(25, 135, 84, 0.1); color: #198754 !important;">Lunas</span>';
            } else {
                const age = calculateAge(inv.tgl_invoice);
                ageDisplay = `<span class="badge bg-soft-secondary text-secondary" style="background-color: rgba(108, 117, 125, 0.1); color: #6c757d !important;">${age} Hari</span>`;
            }
            
            const total = window.financeApp ? window.financeApp.formatIDR(inv.total_akhir) : inv.total_akhir;
            const date = window.financeApp ? window.financeApp.formatDate(inv.tgl_invoice) : inv.tgl_invoice;

            body.innerHTML += `
                <tr>
                    <td class="ps-3 fw-bold">
                        <a href="/sales/${inv.id}" target="_blank" class="text-primary text-decoration-none">${inv.nomor_invoice}</a>
                    </td>
                    <td>${date}</td>
                    <td>${ageDisplay}</td>
                    <td class="text-end pe-3 fw-bold">${total}</td>
                </tr>
            `;
        });

        renderPaginationControls(data, 'mitra-history-pagination', fetchMitraHistory, mitraId);
    }

    function calculateAge(dateStr) {
        if (!dateStr) return 0;
        const start = new Date(dateStr);
        start.setHours(0, 0, 0, 0);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const diffTime = Math.abs(today - start);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }

    function renderPaginationControls(data, containerId, fetchFn, arg) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';

        if (data.last_page <= 1) {
            container.classList.add('d-none');
            return;
        }
        container.classList.remove('d-none');

        const argStr = (arg !== undefined && arg !== null) ? `${arg}, ` : '';

        let html = '<ul class="pagination pagination-sm mb-0">';
        html += `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="${fetchFn.name}(${argStr}${data.current_page - 1})">Prev</a>
        </li>`;

        for (let i = 1; i <= data.last_page; i++) {
            if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                html += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="${fetchFn.name}(${argStr}${i})">${i}</a>
                </li>`;
            } else if (i === data.current_page - 3 || i === data.current_page + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        html += `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="${fetchFn.name}(${argStr}${data.current_page + 1})">Next</a>
        </li>`;

        html += '</ul>';
        container.innerHTML = html;
    }
</script>

<script>
    let fpInvoice = null,
        fpDue = null;

    let masterProduk = [];

    function formatRupiah(angka) {
        if (angka === null || angka === undefined || angka === '') return '0';
        let value = String(angka).replace(/[^0-9]/g, '');
        let num = parseInt(value) || 0;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function cleanNumber(angka) {
        if (!angka) return 0;
        if (typeof angka === 'number') return angka;
        // Buang semua kecuali angka
        return parseInt(String(angka).replace(/[^0-9]/g, '')) || 0;
    }

    document.addEventListener('DOMContentLoaded', function() {
        ['modal_diskon_tambahan', 'modal_biaya_lain', 'modal_cashback'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                    calculateInvoiceTotal();
                });
            }
        });
    });

    async function fetchMasterProduk() {
        try {
            const res = await fetch('{{ route('product-api.index') }}?per_page=1000');
            const result = await res.json();
            if (result.success) {
                masterProduk = result.data.data || result.data;
            }
        } catch (error) {
            console.error('Gagal load master:', e);
        }
    }

    function toggleAllStock(el) {
        const checkboxes = document.querySelectorAll('.stock-check:not(:disabled)');
        checkboxes.forEach(chk => {
            chk.checked = el.checked;
            // Trigger manual change to update selectedStockItems
            const data = JSON.parse(chk.dataset.raw);
            toggleStockSelection(data, el.checked);
        });
    }

    function applyDueShortcut(days) {
        const baseDateStr = document.getElementById('modal_tgl_invoice').value;
        if (!baseDateStr) return;

        const date = new Date(baseDateStr);
        date.setDate(date.getDate() + parseInt(days));
        if (fpDue) fpDue.setDate(date, true);
    }

    function initInvoiceDatePickers() {
        if (fpInvoice) fpInvoice.destroy();
        if (fpDue) fpDue.destroy();

        const commonCfg = {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            disableMobile: true,
            monthSelectorType: 'dropdown',
            static: true,
            altInputClass: 'form-control f-input bg-white'
        };

        fpInvoice = flatpickr('#modal_tgl_invoice', {
            ...commonCfg,
            onChange: (selectedDates, dateStr) => {
                if (fpDue && !document.getElementById('modal_tgl_jatuh_tempo').value) {
                    applyDueShortcut(30);
                }
            }
        });

        fpDue = flatpickr('#modal_tgl_jatuh_tempo', {
            ...commonCfg,
            onReady: (selectedDates, dateStr, instance) => injectDueSidebar(instance)
        });
    }

    function injectDueSidebar(instance) {
        const cal = instance.calendarContainer;
        if (!cal || cal.querySelector('.fp-sidebar-abs')) return;

        const sidebar = document.createElement('div');
        sidebar.className = 'fp-sidebar-abs';
        sidebar.innerHTML = '<div class="fp-title">Tempo</div>';

        const options = [{
                l: 'Hari Ini',
                d: 0
            },
            {
                l: '+7 Hari',
                d: 7
            },
            {
                l: '+14 Hari',
                d: 14
            },
            {
                l: '+30 Hari',
                d: 30
            },
            {
                l: '+45 Hari',
                d: 45
            },
            {
                l: '+60 Hari',
                d: 60
            }
        ];

        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary shadow-none';
            btn.textContent = opt.l;
            btn.onclick = (e) => {
                e.preventDefault();
                applyDueShortcut(opt.d);
                instance.close();
            };
            sidebar.appendChild(btn);
        });

        cal.appendChild(sidebar);
        cal.classList.add('fp-has-sidebar');
    }

    // Override openInvoiceModal untuk memanggil inisialisasi picker
    const originalOpenInvoiceModal = openInvoiceModal;
    openInvoiceModal = async function(id = null, type = null, mode = 'create') {
        await originalOpenInvoiceModal(id, type, mode);
        initInvoiceDatePickers();
    };

    let tomSelectMitraModal = null;
    let tomSelectSalesModal = null;

    function initTomSelectSalesModal(selectedId = null) {
        if (!window.TomSelect) return;

        if (tomSelectSalesModal) {
            try {
                tomSelectSalesModal.destroy();
            } catch (_) {}
            tomSelectSalesModal = null;
        }

        const sel = document.getElementById('modal_sales_id');
        if (!sel) return;

        // ONLY initialize TomSelect if the element is a select
        // If it's an input (hidden/text), we just set the value and return
        if (sel.tagName !== 'SELECT') {
            if (selectedId) sel.value = selectedId;
            return;
        }

        // Gather all available options into a standard format
        const options = [];

        // 1. Get existing options from Blade
        if (sel.options) {
            Array.from(sel.options).forEach(opt => {
                if (opt.value !== '') {
                    options.push({
                        id: opt.value,
                        name: opt.textContent.trim()
                    });
                }
            });
        }

        // 2. Add from masterUsers if missing (e.g. on detail page)
        const staffData = Array.isArray(window.masterUsers) ? window.masterUsers : [];
        if (staffData.length > 0) {
            staffData.forEach(u => {
                if (!options.find(o => o.id == u.id)) {
                    options.push({
                        id: String(u.id),
                        name: u.name
                    });
                }
            });
        }

        // 3. Clear select to let TomSelect handle it via the options array
        sel.innerHTML = '<option value="">Pilih Sales...</option>';

        tomSelectSalesModal = new TomSelect('#modal_sales_id', {
            options: options,
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            placeholder: 'Pilih Sales...',
            allowEmptyOption: true,
            create: false,
            dropdownParent: 'body',
            maxOptions: 1000,
            render: {
                option: (data, escape) => `<div>${escape(data.name)}</div>`,
                item: (data, escape) => `<div>${escape(data.name)}</div>`
            }
        });

        if (selectedId && selectedId != "0" && selectedId != "null") {
            tomSelectSalesModal.setValue(String(selectedId), true);
        } else {
            tomSelectSalesModal.clear(true);
        }
    }

    function initTomSelectMitraModal(selectedId = null) {
        if (!window.TomSelect) {
            const sel = document.getElementById('modal_mitra_id');
            sel.innerHTML = '<option value="">Cari Pelanggan...</option>';
            masterMitra.forEach(m => sel.insertAdjacentHTML('beforeend', `<option value="${m.id}">${m.nama}</option>`));
            if (selectedId) sel.value = selectedId;
            return;
        }

        if (tomSelectMitraModal) {
            try {
                tomSelectMitraModal.destroy();
            } catch (_) {}
            tomSelectMitraModal = null;
        }

        const sel = document.getElementById('modal_mitra_id');
        sel.innerHTML = '';

        const options = (masterMitra || []).map(m => ({
            id: m.id,
            nama: m.nama,
            info: [m.no_hp, m.alamat].filter(Boolean).join(' • ') || ''
        }));

        tomSelectMitraModal = new TomSelect('#modal_mitra_id', {
            options,
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'info'],
            allowEmptyOption: true,
            placeholder: 'Cari Pelanggan...',
            maxOptions: 1000,
            create: false,
            dropdownParent: 'body',
            render: {
                option: (data, escape) => `
                <div>
                    <div class="fw-bold">${escape(data.nama || '')}</div>
                    ${data.info ? `<div class="small text-muted">${escape(data.info)}</div>` : ''}
                </div>
            `,
                item: (data, escape) => `<div>${escape(data.nama || '')}</div>`
            },
            onChange: function(val) {
                renderMitraDetail();
            }
        });

        if (selectedId) {
            tomSelectMitraModal.setValue(String(selectedId), true);
        }
    }

    document.getElementById('invoiceModal').addEventListener('hidden.bs.modal', () => {
        if (tomSelectMitraModal) {
            try {
                tomSelectMitraModal.destroy();
            } catch (_) {}
            tomSelectMitraModal = null;
        }
        if (tomSelectSalesModal) {
            try {
                tomSelectSalesModal.destroy();
            } catch (_) {}
            tomSelectSalesModal = null;
        }
    });

    // --- Modal Logic ---
    async function openInvoiceModal(id = null, type = null, mode = 'create') {
        const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').classList.add('d-none');
        document.getElementById('mitra_empty_state').classList.remove('d-none');

        // Reset totals
        document.getElementById('summary_subtotal').innerText = 'Rp 0';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';

        // Load Master Data if empty
        if (masterProduk.length === 0) await fetchMasterProduk();
        if (masterMitra.length === 0) await initializeMasterData();

        // Ensure masterUsers is loaded (part of initializeMasterData or separate fetch)
        if (!window.masterUsers || window.masterUsers.length === 0) {
            try {
                const uRes = await fetch('/api/user-api/staff-by-permission?permission=sales');
                const uData = await uRes.json();
                window.masterUsers = (uData.success && uData.data) ? uData.data : (uData.data || uData || []);
            } catch (e) {
                console.error("Failed to fetch staff", e);
                window.masterUsers = [];
            }
        }

        initTomSelectMitraModal();
        initTomSelectSalesModal();

        if (mode === 'edit' && id) {
            document.getElementById('modalTitle').innerText = 'Edit Invoice Penjualan';
            document.getElementById('form_mode').value = 'edit';
            document.getElementById('edit_invoice_id').value = id;

            try {
                const res = await fetch(`${window.financeApp.API_URL}/${id}`);
                const result = await res.json();
                if (result.success) {
                    const inv = result.data;
                    document.getElementById('modal_nomor_invoice').value = inv.nomor_invoice;
                    document.getElementById('modal_ref_no').value = inv.ref_no || '';
                    document.getElementById('modal_tgl_invoice').value = inv.tgl_invoice;
                    document.getElementById('modal_tgl_jatuh_tempo').value = inv.tgl_jatuh_tempo;
                    document.getElementById('modal_mitra_id').value = inv.mitra_id;
                    document.getElementById('modal_keterangan').value = inv.keterangan || '';
                    document.getElementById('modal_syarat').value = inv.syarat_ketentuan || '';
                    document.getElementById('modal_diskon_tambahan').value = formatRupiah(Math.round(inv
                        .diskon_tambahan_nilai || 0));
                    document.getElementById('modal_diskon_tambahan_tipe').value = inv.diskon_tambahan_tipe ||
                        'fixed';
                    document.getElementById('modal_biaya_lain').value = formatRupiah(Math.round(inv.other_fee || inv
                        .biaya_kirim || 0));
                    document.getElementById('modal_cashback').value = formatRupiah(Math.round(inv.cashback || 0));

                    if (inv.mitra_id && tomSelectMitraModal) {
                        tomSelectMitraModal.setValue(String(inv.mitra_id), true);
                    } else {
                        initTomSelectMitraModal(inv.mitra_id || null);
                    }

                    // Re-init staff with selectedId
                    initTomSelectSalesModal(inv.sales_id || null);

                    renderMitraDetail();

                    // Items
                    if (inv.items) {
                        inv.items.forEach(item => addNewProductRow(item));
                    }
                }
            } catch (e) {
                console.error(e);
                alert('Gagal load data invoice');
            }

        } else {
            document.getElementById('modalTitle').innerText = 'Invoice Penjualan Baru';
            document.getElementById('form_mode').value = 'create';
            document.getElementById('modal_tgl_invoice').value = new Date().toISOString().split('T')[0];
            // Set default due date (e.g. +30 days)
            const d = new Date();
            d.setDate(d.getDate() + 30);
            document.getElementById('modal_tgl_jatuh_tempo').value = d.toISOString().split('T')[0];
            document.getElementById('modal_nomor_invoice').value =
                `INV/${new Date().getFullYear()}/${Date.now().toString().slice(-4)}`;


            if (tomSelectMitraModal) {
                tomSelectMitraModal.clear(true);
            }
            if (tomSelectSalesModal) {
                tomSelectSalesModal.clear(true);
            }


            addNewProductRow(); // Add 1 empty row
        }

        modal.show();
    }

    function renderMitraDetail() {
        const id = document.getElementById('modal_mitra_id').value;
        const disp = document.getElementById('mitra_detail_display');
        const empty = document.getElementById('mitra_empty_state');

        if (!id) {
            disp.classList.add('d-none');
            empty.classList.remove('d-none');
            // Clear salesperson when no partner selected
            if (tomSelectSalesModal) {
                tomSelectSalesModal.clear(true);
            }
            return;
        }

        const m = masterMitra.find(x => x.id == id);
        if (m) {
            document.getElementById('disp_mitra_nama').innerText = m.nama;
            document.getElementById('disp_mitra_tipe').innerText = m.tipe_mitra;
            document.getElementById('disp_mitra_alamat').innerText = m.alamat || '-';
            document.getElementById('disp_mitra_telp').innerText = m.no_hp || '-';

            // Auto-fill salesperson if partner has assigned salesperson
            if (m.salesperson_id && tomSelectSalesModal) {
                tomSelectSalesModal.setValue(String(m.salesperson_id), true);
            } else {
                // Clear salesperson if partner has no salesperson
                if (tomSelectSalesModal) {
                    tomSelectSalesModal.clear(true);
                }
            }

            disp.classList.remove('d-none');
            empty.classList.add('d-none');
        } else {
            disp.classList.add('d-none');
            empty.classList.remove('d-none');
        }

    }

    function toggleRowTempo(el) {
        const row = el.closest('tr');
        const tempoInput = row.querySelector('.prod-tempo-val');
        const tempoContainer = row.querySelector('.tempo-container');
        const tempoPlaceholder = row.querySelector('.tempo-placeholder');
        const priceInput = row.querySelector('.prod-price');
        const idInput = row.querySelector('.prod-id');
        const prodId = idInput ? idInput.value : null;

        const isTempo = el.checked;
        tempoInput.disabled = !isTempo;

        if (isTempo) {
            row.classList.add('row-tempo-active');
            tempoContainer.classList.remove('d-none');
            tempoPlaceholder.classList.add('d-none');
            setTimeout(() => tempoInput.focus(), 0);
        } else {
            row.classList.remove('row-tempo-active');
            tempoContainer.classList.add('d-none');
            tempoPlaceholder.classList.remove('d-none');
            tempoInput.value = 0;
        }

        // Switch price if product selected
        if (prodId) {
            const product = masterProduk.find(p => String(p.id) === String(prodId));
            if (product) {
                const newPrice = isTempo ? (product.harga_tempo || 0) : (product.harga_jual || 0);
                priceInput.value = formatRupiah(Math.round(newPrice));
            }
        }

        calculateInvoiceTotal();
    }

    // --- Item Logic ---
    function addNewProductRow(data = null) {
        const tbody = document.getElementById('itemBodyList');
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);
        const tr = clone.querySelector('tr');

        const rowId = 'row_' + Date.now() + Math.random().toString(36).substr(2, 5);
        tr.id = rowId;

        const selectEl = tr.querySelector('.prod-select-item');
        const priceInput = tr.querySelector('.prod-price');
        const qtyInput = tr.querySelector('.prod-qty');
        const discInput = tr.querySelector('.prod-disc');
        const idInput = tr.querySelector('.prod-id');
        const initialIdInput = tr.querySelector('.prod-initial-id');
        const initialQtyInput = tr.querySelector('.prod-initial-qty');
        const maxStokInput = tr.querySelector('.prod-max-stok');
        const originalQtyInput = tr.querySelector('.prod-original-qty');
        const unitLabel = tr.querySelector('.prod-unit-label');
        const descInput = tr.querySelector('.prod-desc');

        [priceInput, discInput].forEach(el => {
            el.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
                calculateInvoiceTotal();
            });
        });

        qtyInput.addEventListener('input', function() {
            const maxVal = parseFloat(maxStokInput.value) || 0;
            const originalVal = parseFloat(originalQtyInput.value) || 0;
            let currentVal = parseFloat(this.value) || 0;

            // Jika qty baru <= qty lama, tidak perlu cek stok (selalu aman)
            if (currentVal <= originalVal) {
                calculateInvoiceTotal();
                return;
            }

            // Jika qty baru > qty lama, cek apakah selisihnya tersedia di stok
            if (currentVal > (maxVal + originalVal)) {
                const selectedId = selectEl.value;
                const product = masterProduk.find(p => p.id == selectedId);
                const isTracked = product ? (product.track_stock == 1) : true;

                if (isTracked) {
                    alert(`Stok tidak mencukupi! Maksimum stok tersedia adalah ${maxVal + originalVal}.`);
                    this.value = maxVal + originalVal;
                }
                calculateInvoiceTotal();
            } else {
                calculateInvoiceTotal();
            }
        });

        const ts = new TomSelect(selectEl, {
            options: masterProduk.map(p => ({
                id: String(p.id),
                nama: p.nama_produk,
                sku: p.sku_kode,
                harga: parseFloat(p.harga_jual),
                unit: p.unit?.nama_unit || 'Pcs',
                qty: Number(p.qty || 0)
            })),
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'sku'],
            placeholder: 'Cari barang...',
            render: {
                option: (d, esc) => {
                    const style = d.qty <= 0 ? 'opacity: 0.5; background: #f8fafc;' : '';
                    const badge = d.qty <= 0 ?
                        '<span class="badge bg-danger float-end">Stok Habis</span>' :
                        `<span class="badge bg-light text-dark border float-end">${d.qty} ${esc(d.unit)}</span>`;
                    return `<div style="${style}">
                                <div class="fw-bold">${esc(d.nama)} ${badge}</div>
                                <small class="text-muted">${esc(d.sku)}</small>
                            </div>`;
                },
                item: (d, esc) => `<div>${esc(d.nama)}</div>`
            },
            onChange: function(val) {
                const selected = this.options[val];
                if (selected && selected.qty <= 0) {
                    alert('Stok Habis! Produk ini tidak dapat dipilih.');
                    this.clear();
                    return;
                }
                if (selected) {
                    const isTempo = tr.querySelector('.prod-is-tempo').checked;
                    const product = masterProduk.find(p => String(p.id) === String(val));

                    // Jika produk berubah dari awal, reset original qty allowance
                    if (val !== initialIdInput.value) {
                        originalQtyInput.value = 0;
                    } else {
                        originalQtyInput.value = initialQtyInput.value;
                    }

                    idInput.value = selected.id;
                    maxStokInput.value = selected.qty;

                    const price = isTempo ? (product?.harga_tempo || 0) : (product?.harga_jual || selected
                        .harga);
                    priceInput.value = formatRupiah(Math.round(price));

                    unitLabel.innerText = selected.unit;
                    
                    // Check if current qty (default 1) > stock
                    const originalVal = parseFloat(originalQtyInput.value) || 0;
                    if (parseFloat(qtyInput.value) > (selected.qty + originalVal)) {
                        qtyInput.value = selected.qty + originalVal;
                        if ((selected.qty + originalVal) > 0) {
                            alert(`Stok terbatas! Qty disesuaikan ke ${selected.qty + originalVal}.`);
                        }
                    }
                    
                    calculateInvoiceTotal();
                }
            }
        });


        if (data) {
            setTimeout(() => {
                let rawId = data.produk_id || data.product_id;

                if (!rawId) {
                    if (data.nama_produk || data.sku_kode) {
                        rawId = data.id;
                    } else {
                        rawId = data.id;
                    }
                }

                const prodId = String(rawId);
                const masterItem = masterProduk.find(p => String(p.id) === prodId);

                if (ts.options[prodId]) {
                    ts.setValue(prodId, true);
                } else {
                    ts.addOption({
                        id: prodId,
                        nama: data.nama_produk_manual || data.nama_produk || 'Produk',
                        harga: 0
                    });
                    ts.setValue(prodId, true);
                }

                idInput.value = prodId;
                initialIdInput.value = prodId;
                initialQtyInput.value = parseFloat(data.qty) || 0;
                originalQtyInput.value = parseFloat(data.qty) || 0;
                maxStokInput.value = masterItem ? (masterItem.qty || 0) : 999999;

                qtyInput.value = parseFloat(data.qty) || 1;
                originalQtyInput.value = parseFloat(data.qty) || 0;

                const rawPrice = parseFloat(data.harga_satuan ?? data.harga_jual ?? 0);
                const rawDisc = parseFloat(data.diskon_item ?? data.diskon_nilai ?? 0);

                priceInput.value = formatRupiah(rawPrice);
                discInput.value = formatRupiah(rawDisc);
                if (data.diskon_tipe) {
                    tr.querySelector('.prod-disc-tipe').value = data.diskon_tipe;
                }
                descInput.value = data.deskripsi_item || data.deskripsi_produk || '';

                if (data.tempo && data.tempo > 0) {
                    const toggle = tr.querySelector('.prod-is-tempo');
                    const tempoVal = tr.querySelector('.prod-tempo-val');
                    const tempoContainer = tr.querySelector('.tempo-container');
                    const tempoPlaceholder = tr.querySelector('.tempo-placeholder');
                    toggle.checked = true;
                    tempoVal.disabled = false;
                    tempoVal.value = data.tempo;
                    tempoContainer.classList.remove('d-none');
                    tempoPlaceholder.classList.add('d-none');
                    tr.classList.add('row-tempo-active');
                }

                let unitName = 'Pcs';

                if (data.unit) {
                    unitName = (typeof data.unit === 'object') ? data.unit.nama_unit : data.unit;
                } else if (data.product && data.product.unit) {
                    unitName = data.product.unit.nama_unit;
                } else if (masterItem && masterItem.unit) {
                    unitName = masterItem.unit.nama_unit;
                }
                unitLabel.innerText = unitName;

                calculateInvoiceTotal();

            }, 50);
        } else {
            calculateInvoiceTotal();
        }

        tr.querySelector('.btn-remove-row').onclick = () => {
            ts.destroy();
            tr.remove();
            calculateInvoiceTotal();
        };

        tbody.appendChild(tr);
        calculateInvoiceTotal();
    }

    // Listen for new mitra creation from Mitra Modal
    document.addEventListener('mitra-saved', (e) => {
        const newMitra = e.detail;
        if (newMitra) {
            // Update masterMitra array if it exists
            if (typeof masterMitra !== 'undefined') {
                const exists = masterMitra.find(m => m.id == newMitra.id);
                if (!exists) {
                    masterMitra.push(newMitra);
                } else {
                    Object.assign(exists, newMitra);
                }
            }

            // Update TomSelect
            if (tomSelectMitraModal) {
                const opt = {
                    id: newMitra.id,
                    nama: newMitra.nama,
                    info: [newMitra.no_hp, newMitra.alamat].filter(Boolean).join(' • ') || ''
                };
                tomSelectMitraModal.addOption(opt);
                tomSelectMitraModal.addItem(newMitra.id);
            } else {
                initTomSelectMitraModal(newMitra.id);
            }
        }
    });

    function removeProductRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    function calculateInvoiceTotal() {
        let subtotal = 0;

        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const qtyEl = row.querySelector('.prod-qty');
            const priceEl = row.querySelector('.prod-price');
            const discEl = row.querySelector('.prod-disc');
            const discTipeEl = row.querySelector('.prod-disc-tipe');
            const subtotalDisplay = row.querySelector('.prod-subtotal');

            if (!qtyEl || !priceEl || !discEl) return;

            const qty = parseFloat(qtyEl.value) || 0;
            const price = cleanNumber(priceEl.value);
            const discValue = cleanNumber(discEl.value);
            const discTipe = discTipeEl ? discTipeEl.value : 'Fixed';

            let rowDisc = 0;
            if (discTipe === 'Percentage') {
                rowDisc = (price * discValue / 100);
            } else {
                rowDisc = discValue;
            }

            const rowTotal = Math.max(0, (price - rowDisc) * qty);
            subtotal += rowTotal;

            if (subtotalDisplay) {
                subtotalDisplay.innerText = window.financeApp.formatIDR(rowTotal);
            }
        });

        const extraDiscValue = cleanNumber(document.getElementById('modal_diskon_tambahan').value);
        const extraDiscTipe = document.getElementById('modal_diskon_tambahan_tipe').value;
        const otherFee = cleanNumber(document.getElementById('modal_biaya_lain').value);
        const cashback = cleanNumber(document.getElementById('modal_cashback').value);

        let extraDisc = 0;
        if (extraDiscTipe === 'Percentage') {
            extraDisc = (subtotal * extraDiscValue / 100);
        } else {
            extraDisc = extraDiscValue;
        }

        const grandTotal = Math.max(0, subtotal - extraDisc + otherFee - cashback);

        document.getElementById('summary_subtotal').innerText = window.financeApp.formatIDR(subtotal);
        document.getElementById('summary_grand_total').innerText = window.financeApp.formatIDR(grandTotal);
    }

    async function saveFullInvoice() {
        const mode = document.getElementById('form_mode').value;
        const id = document.getElementById('edit_invoice_id').value;
        const url = mode === 'edit' ? `/api/invoice-api/${id}` : '/api/invoice-create-api';
        const method = mode === 'edit' ? 'PUT' : 'POST';

        const mitraIdVal = tomSelectMitraModal ? tomSelectMitraModal.getValue() : document.getElementById(
            'modal_mitra_id').value;

        // Build Payload
        const items = [];
        const rows = document.querySelectorAll('#itemBodyList tr');

        for (const row of rows) {
            if (!row.querySelector('.prod-qty')) continue;

            const qtyInput = row.querySelector('.prod-qty');
            const priceInput = row.querySelector('.prod-price');
            const discInput = row.querySelector('.prod-disc');
            const idInput = row.querySelector('.prod-id');
            const descInput = row.querySelector('.prod-desc');

            const productId = idInput.value;
            const qty = parseFloat(qtyInput.value) || 0;

            // --- CLEAN DATA SEBELUM SAVE ---
            const price = cleanNumber(priceInput.value);
            const disc = cleanNumber(discInput.value);

            let namaProduk = '';

            const productData = masterProduk.find(p => String(p.id) === String(productId));

            if (productData) {
                namaProduk = productData.nama_produk;
            } else {
                const tsElement = row.querySelector('.prod-select-item');
                if (tsElement && tsElement.tomselect) {
                    const ts = tsElement.tomselect;
                    const val = ts.getValue();
                    if (val && ts.options[val]) {
                        namaProduk = ts.options[val].nama;
                    }
                }
            }
            if (namaProduk) {
                items.push({
                    produk_id: productId || null,
                    nama_produk_manual: namaProduk,
                    deskripsi_produk: row.querySelector('.prod-desc').value,
                    qty: qty,
                    tempo: row.querySelector('.prod-is-tempo').checked ? (parseFloat(row.querySelector(
                        '.prod-tempo-val').value) || 0) : 0,
                    harga_satuan: price,
                    diskon_nilai: disc,
                    diskon_tipe: row.querySelector('.prod-disc-tipe').value,
                    total_harga_item: cleanNumber(row.querySelector('.prod-subtotal').innerText)
                });
            }
        }

        const payload = {
            invoice: {
                tipe_invoice: 'Sales',
                nomor_invoice: document.getElementById('modal_nomor_invoice').value,
                tgl_invoice: document.getElementById('modal_tgl_invoice').value,
                tgl_jatuh_tempo: document.getElementById('modal_tgl_jatuh_tempo').value,
                mitra_id: mitraIdVal,
                sales_id: document.getElementById('modal_sales_id').value || 0,
                ref_no: document.getElementById('modal_ref_no').value,
                keterangan: document.getElementById('modal_keterangan').value,
                syarat_ketentuan: document.getElementById('modal_syarat').value,
                diskon_tambahan_nilai: cleanNumber(document.getElementById('modal_diskon_tambahan').value),
                diskon_tambahan_tipe: document.getElementById('modal_diskon_tambahan_tipe').value,
                total_akhir: cleanNumber(document.getElementById('summary_grand_total').innerText),
                status_dok: 'Approved',
                status_pembayaran: 'Unpaid',
                biaya_kirim: cleanNumber(document.getElementById('modal_biaya_lain').value),
                other_fee: cleanNumber(document.getElementById('modal_biaya_lain').value),
                cashback: cleanNumber(document.getElementById('modal_cashback').value),
                uang_muka: 0
            },
            items: items
        };

        // Basic Validation
        if (!payload.invoice.mitra_id) {
            alert('Harap pilih Mitra!');
            return;
        }
        if (!payload.invoice.tgl_invoice) {
            alert('Harap isi Tanggal Invoice!');
            return;
        }
        if (!payload.invoice.nomor_invoice) {
            alert('Nomor invoice belum diisi.');
            return;
        }
        if (items.length === 0) {
            alert('Harap tambahkan minimal satu item.');
            return;
        }

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide();
                let invoiceId = result.data?.invoice?.id || result.data?.id;
                // Redirect to detail page with flag
                window.location.href = `{{ url('sales') }}/${invoiceId}?action=created`;
            } else {
                if (result.errors) {
                    let errorMsg = "Terjadi kesalahan:\n";
                    Object.values(result.errors).forEach(err => errorMsg += `- ${err}\n`);
                    alert(errorMsg);
                } else {
                    alert('Gagal: ' + result.message);
                }
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menyimpan.');
        }
    }

    // --- Stock Modal Logic ---
    let stockCache = [];
    let selectedStockItems = []; // Array of product objects: {id, nama_produk, ...}

    function toggleStockSelection(item, isChecked) {
        if (isChecked) {
            // Check if already in array to avoid duplicates
            if (!selectedStockItems.find(x => String(x.id) === String(item.id))) {
                selectedStockItems.push(item);
            }
        } else {
            selectedStockItems = selectedStockItems.filter(x => String(x.id) !== String(item.id));
        }
        renderSelectedStockBadges();
    }

    function renderSelectedStockBadges() {
        const container = document.getElementById('selectedStockContainer');
        const list = document.getElementById('selectedStockList');
        
        if (selectedStockItems.length === 0) {
            container.classList.add('d-none');
            return;
        }

        container.classList.remove('d-none');
        list.innerHTML = '';

        selectedStockItems.forEach(item => {
            const badge = document.createElement('div');
            badge.className = 'badge bg-primary d-flex align-items-center gap-2 py-2 px-3 rounded-pill shadow-sm animate__animated animate__fadeIn';
            badge.style.fontSize = '12px';
            badge.innerHTML = `
                <span>${item.nama_produk}</span>
                <i class="fa fa-times-circle cursor-pointer" onclick="deselectStockItem('${item.id}')" style="font-size: 14px; opacity: 0.8;"></i>
            `;
            list.appendChild(badge);
        });
    }

    window.deselectStockItem = function(id) {
        selectedStockItems = selectedStockItems.filter(x => String(x.id) !== String(id));
        renderSelectedStockBadges();
        
        // Uncheck the checkbox if it's currently visible in the table
        const checkboxes = document.querySelectorAll('.stock-check');
        checkboxes.forEach(chk => {
            if (String(chk.value) === String(id)) {
                chk.checked = false;
            }
        });
    }

    async function fetchStockPage(page = 1) {
        const searchInput = document.getElementById('stockSearchInput');
        const categoryFilter = document.getElementById('stockCategoryFilter');
        const searchTerm = searchInput ? searchInput.value.trim() : '';
        const categoryId = categoryFilter ? categoryFilter.value : '';

        const tbody = document.getElementById('stockBodyList');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center p-4"><div class="spinner-border text-primary"></div></td></tr>';

        try {
            let url = `/api/product-api`;
            let params = new URLSearchParams({
                page: page,
                per_page: 10
            });
            if (searchTerm) params.append('search', searchTerm);
            if (categoryId) params.append('category', categoryId);
            
            const res = await fetch(url + '?' + params.toString());
            const result = await res.json();
            
            if (result.success) {
                const data = result.data.data || result.data;
                renderStockList(data);
                renderPaginationControls(result.data, 'stockPagination', fetchStockPage);
            }
        } catch (e) {
            console.error('Fetch stock page failed:', e);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger p-4">Gagal memuat data</td></tr>';
        }
    }

    async function openStockModal() {
        const modalEl = document.getElementById('stockModal');
        const modal = new bootstrap.Modal(modalEl);
        
        // Reset selection for this session
        selectedStockItems = [];
        renderSelectedStockBadges();
        
        document.getElementById('stockBodyList').innerHTML =
            '<tr><td colspan="7" class="text-center p-4"><div class="spinner-border text-primary"></div></td></tr>';
        
        showCustomBackdrop();
        modal.show();

        modalEl.addEventListener('hidden.bs.modal', function() {
            hideCustomBackdrop();
        }, { once: true });

        // Load Categories once
        loadStockCategories();
        
        // Setup Search Listeners
        setupStockSearch();

        // Fetch first page
        fetchStockPage(1);
    }

    function setupStockSearch() {
        const searchInput = document.getElementById('stockSearchInput');
        const categoryFilter = document.getElementById('stockCategoryFilter');

        let stockSearchTimer = null;
        if (searchInput) {
            // Remove old listeners to avoid duplicates if re-called
            const newSearchInput = searchInput.cloneNode(true);
            searchInput.parentNode.replaceChild(newSearchInput, searchInput);
            
            newSearchInput.addEventListener('input', function() {
                clearTimeout(stockSearchTimer);
                stockSearchTimer = setTimeout(() => {
                    fetchStockPage(1);
                }, 500);
            });
        }

        if (categoryFilter) {
            const newCategoryFilter = categoryFilter.cloneNode(true);
            categoryFilter.parentNode.replaceChild(newCategoryFilter, categoryFilter);
            
            newCategoryFilter.addEventListener('change', function() {
                fetchStockPage(1);
            });
        }
    }

    async function loadStockCategories() {
        try {
            const res = await fetch('/api/product-categories-api');
            const result = await res.json();
            console.log('Categories API Response:', result); // Debug log

            if (result.success) {
                const categories = result.data.data || result.data;
                const categoryFilter = document.getElementById('stockCategoryFilter');

                if (categoryFilter) {
                    categoryFilter.innerHTML = '<option value="">Semua Kategori</option>';
                    categories.forEach(cat => {
                        categoryFilter.innerHTML +=
                            `<option value="${cat.id}">${cat.nama_kategori}</option>`;
                    });
                }
            } else {
                console.error('Categories API Error:', result.message);
            }
        } catch (e) {
            console.error('Failed to load categories:', e);
        }
    }

    function resetStockFilters() {
        const searchInput = document.getElementById('stockSearchInput');
        const categoryFilter = document.getElementById('stockCategoryFilter');

        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';

        // Kembali ke cache saat reset
        renderStockList(stockCache);
        console.log('Reset to cache - showing all products');
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function renderStockList(data) {
        const tbody = document.getElementById('stockBodyList');
        const template = document.getElementById('stockRowTemplate');
        tbody.innerHTML = '';

        const existingIds = Array.from(document.querySelectorAll('#itemBodyList .prod-id'))
            .map(input => input.value);

        data.forEach(item => {
            const clone = template.content.cloneNode(true);
            const tr = clone.querySelector('tr');
            const chk = clone.querySelector('.stock-check');

            chk.value = item.id;
            chk.dataset.raw = JSON.stringify(item);

            const qty = Number(item.qty || 0);
            if (qty <= 0) {
                tr.style.opacity = '0.5';
                tr.style.backgroundColor = '#f8fafc';
                tr.style.cursor = 'not-allowed';
                chk.disabled = true;
            }

            // Check if it's already in the selection array OR already in the main table
            if (selectedStockItems.find(x => String(x.id) === String(item.id)) || existingIds.includes(String(item.id))) {
                chk.checked = true;
                // If it's already in the table but NOT in selectedStockItems yet, we might want to add it?
                // But usually we only care about new selections.
            }

            chk.addEventListener('change', function() {
                toggleStockSelection(item, this.checked);
            });

            clone.querySelector('.col-stock-nama').textContent = item.nama_produk;
            clone.querySelector('.col-stock-sku').textContent = item.sku_kode || item.kode_produk || '-';
            clone.querySelector('.col-stock-supplier').textContent = item.supplier?.nama || '-';
            clone.querySelector('.col-stock-kategori').textContent = item.category?.nama_kategori || '-';
            clone.querySelector('.col-stock-qty').textContent = `${qty} ${item.unit?.nama_unit || ''}`;
            clone.querySelector('.col-stock-harga').textContent = window.financeApp.formatIDR(item.harga_jual);

            const trackBadge = clone.querySelector('.col-stock-track');
            trackBadge.textContent = item.track_stock ? 'YES' : 'NO';
            trackBadge.className = `badge ${item.track_stock ? 'bg-success' : 'bg-secondary'}`;

            tbody.appendChild(clone);
        });

        const masterChk = document.getElementById('checkAllStock');
        if (masterChk) masterChk.checked = false;
    }

    // Filter Stock
    document.querySelectorAll('.col-filter').forEach(inp => {
        inp.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            const col = this.dataset.col; // 1=code, 2=name, 3=cat

            const filtered = stockCache.filter(item => {
                if (col == 1) return (item.kode_produk || '').toLowerCase().includes(val);
                if (col == 2) return (item.nama_produk || '').toLowerCase().includes(val);
                if (col == 3) return (item.kategori?.nama_kategori || '').toLowerCase()
                    .includes(val);
                return true;
            });
            renderStockList(filtered);
        });
    });

    function addSelectedStocks() {
        const target = window.activeStockTarget || 'main'; // 'main' or 'bulk'

        let existingIds = [];
        let tbodyId = '';

        if (target === 'bulk') {
            tbodyId = 'bulk-items-body';
            existingIds = Array.from(document.querySelectorAll('#bulk-items-body .prod-id'))
                .map(input => input.value);
        } else {
            tbodyId = 'itemBodyList';
            existingIds = Array.from(document.querySelectorAll('#itemBodyList .prod-id'))
                .map(input => input.value);
        }

        // Smart Placeholder Removal logic
        const tbody = document.getElementById(tbodyId);
        if (tbody) {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length === 1) {
                const idVal = rows[0].querySelector('.prod-id').value;
                if (!idVal) {
                    if (target === 'bulk') {
                        if (typeof bulkRemoveProductRow === 'function') {
                            const btn = rows[0].querySelector('.btn-remove-row');
                            if (btn) bulkRemoveProductRow(btn);
                            else rows[0].remove();
                        }
                    } else {
                        const btn = rows[0].querySelector('.btn-remove-row');
                        if (btn) {
                            btn.click();
                        } else {
                            rows[0].remove();
                        }
                    }
                    existingIds = [];
                }
            }
        }

        // Use selectedStockItems as the source of truth
        selectedStockItems.forEach(data => {
            const productId = String(data.id);

            if (!existingIds.includes(productId)) {
                const itemData = {
                    product_id: data.id,
                    id: data.id,
                    nama_produk_manual: data.nama_produk,
                    harga_jual: data.harga_jual,
                    qty: 1,
                    unit: data.unit?.nama_unit || 'Pcs',
                    product: data
                };

                if (target === 'bulk') {
                    if (typeof bulkAddNewProductRow === 'function') {
                        bulkAddNewProductRow(itemData);
                    }
                } else {
                    addNewProductRow(itemData);
                }
            }
        });

        bootstrap.Modal.getInstance(document.getElementById('stockModal')).hide();
    }
</script>
