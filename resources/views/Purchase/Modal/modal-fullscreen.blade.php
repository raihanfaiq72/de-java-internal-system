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
    #stockModal, #mitraModal {
        z-index: 1070 !important;
    }
</style>
<div class="modal fade" id="invoiceModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">

            {{-- Header --}}
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-3">
                        <i class="fa fa-shopping-cart fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Invoice Pembelian</h5>
                        <p class="text-muted small mb-0">Input data tagihan dari supplier/vendor.</p>
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

            {{-- Body --}}
            <div class="modal-body p-0">
                <form id="invoiceForm" class="h-100">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-0 h-100">
                        {{-- Main Form --}}
                        <div class="col-lg-9 h-100 overflow-auto custom-scrollbar p-4">

                            {{-- Customer & Doc Details --}}
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        {{-- Supplier --}}
                                        <div class="col-lg-6 border-end">
                                            <label class="f-label mb-2">Mitra <span class="text-danger">*</span></label>
                                            <select id="modal_mitra_id" class="form-select f-input-lg mb-3"
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
                                            <div id="mitra_detail_display" class="d-none bg-light p-3 rounded border">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="fw-bold text-dark" id="disp_mitra_nama">-</div>
                                                    <span class="badge bg-primary ms-auto" id="disp_mitra_tipe">-</span>
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
                                                <div class="small">Pilih mitra untuk melihat detail</div>
                                            </div>
                                        </div>

                                        {{-- Document --}}
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
                                                        placeholder="PO-SUP-001">
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
                                                    <label class="f-label">Purchasing Staff</label>
                                                    <select id="modal_sales_id" class="form-select f-input">
                                                        <option value="">Pilih Staff...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                        <i class="fa fa-box me-2 text-primary"></i> Rincian Barang & Jasa
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
                                                <th class="ps-4 py-3" width="28%">Produk / Deskripsi</th>
                                                <th class="text-center" width="8%">Qty</th>
                                                <th class="text-center" width="8%">Satuan</th>
                                                <th class="text-end" width="12%">Harga Beli</th>
                                                <th class="text-center" width="10%">Tipe Disc</th>
                                                <th class="text-end" width="12%">Disc</th>
                                                <th class="text-end pe-4" width="15%">Total</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemBodyList" class="border-top-0"></tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="8"
                                                    class="text-center py-2 text-muted small fst-italic">
                                                    Klik tombol tambah untuk memasukkan item
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            {{-- Bottom Notes --}}
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
                                            <textarea id="modal_syarat" class="form-control f-text bg-light border-0" rows="3">1. Pastikan barang yang di-input sudah benar</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Summary --}}

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
                                        <label class="text-muted small d-block mb-1">Potongan Lumpsum</label>
                                        <div class="input-group input-group-sm">
                                            <select id="modal_diskon_tambahan_tipe" class="form-select border-end-0"
                                                style="max-width: 85px;" onchange="calculateInvoiceTotal()">
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
                                    <div class="small opacity-75 fw-bold text-uppercase mb-1">Total Tagihan</div>
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

<!-- Modal Mitra -->
<div class="modal fade" id="mitraModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <!-- Header -->
            <div class="modal-header bg-white bg-opacity-95 backdrop-blur border-bottom px-4 py-3 sticky-top shadow-lg">
                <div class="d-flex align-items-center">
                    <div class="bg-gradient-primary text-white p-3 rounded-3 me-3 shadow-lg">
                        <i class="fa fa-user-tie me-2 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="mitraModalTitle">Formulir Mitra Bisnis</h5>
                        <p class="text-muted small mb-0">Kelola informasi supplier, vendor, atau klien perusahaan.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border text-secondary fw-semibold px-4 rounded-2 shadow-sm"
                        data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Batalkan
                    </button>
                    <button type="button" class="btn btn-gradient-primary px-4 fw-bold shadow-lg rounded-2" onclick="saveMitra()">
                        <i class="fa fa-save me-2"></i>Simpan Data
                    </button>
                </div>
            </div>

            <div class="modal-body p-4 bg-white bg-opacity-95 backdrop-blur">
                <form id="mitraForm">
                    <input type="hidden" id="mitra_form_mode" value="create">
                    <input type="hidden" id="edit_mitra_id" value="">

                    <div class="row g-4 justify-content-center">
                        <!-- Card 1: Informasi Perusahaan -->
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-gradient-primary text-white p-2 rounded-2 me-2 shadow">
                                        <i class="fa fa-user-tie me-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">Informasi Perusahaan</h6>
                                </div>
                                <div class="border-bottom border-2 border-primary opacity-25"></div>
                            </div>
                            <div class="card border-0 shadow-lg rounded-4 bg-white bg-opacity-90 backdrop-blur border border-primary border-opacity-25">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="f-label text-danger fw-bold">Tipe Mitra <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                        <div class="d-flex gap-3 mt-1 align-items-center flex-wrap">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra"
                                                    id="typeSupplier" value="Supplier" checked>
                                                <label class="form-check-label small fw-bold"
                                                    for="typeSupplier">Supplier / Vendor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra"
                                                    id="typeClient" value="Client">
                                                <label class="form-check-label small fw-bold" for="typeClient">Client /
                                                    Customer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra"
                                                    id="typeBoth" value="Both">
                                                <label class="form-check-label small fw-bold"
                                                    for="typeBoth">Keduanya</label>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2 pt-1 border-top">
                                            <input class="form-check-input" type="checkbox" id="modal_is_cash_customer">
                                            <label class="form-check-label small fw-bold text-primary"
                                                for="modal_is_cash_customer">
                                                Apakah Pelanggan Cash?
                                            </label>
                                            <div class="form-text x-small text-muted" style="font-size: 11px;">
                                                Pelanggan cash membeli langsung (tanpa piutang/tempo).
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="f-label text-danger fw-bold">Usaha <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                            <select id="modal_badan_usaha" class="form-select f-input" required>
                                                <option value="PT">PT</option>
                                                <option value="CV">CV</option>
                                                <option value="UD">UD</option>
                                                <option value="Perorangan">Perorangan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="f-label text-danger fw-bold">Nama Perusahaan / Mitra <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                            <input type="text" id="modal_nama" class="form-control f-input fw-bold"
                                                placeholder="Nama Lengkap" required>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">Nomor ID Mitra (Opsional)</label>
                                        <input type="text" id="modal_nomor_mitra" class="form-control f-input bg-light"
                                            placeholder="Auto-generated jika kosong">
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label text-danger fw-bold">No. KTP / NPWP <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                        <input type="text" id="modal_ktp_npwp" class="form-control f-input"
                                            placeholder="Nomor Identitas (NIK / NPWP)" required>
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label text-danger fw-bold">Alamat Lengkap <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                        <textarea id="modal_alamat" class="form-control f-input" rows="3"
                                            placeholder="Masukkan alamat lengkap..." required></textarea>

                                            <!-- Map Section -->
                                            <div class="mt-3">
                                                <label class="f-label d-flex justify-content-between align-items-center">
                                                    <span>Lokasi Peta</span>
                                                    <button type="button"
                                                        class="btn btn-sm btn-link text-decoration-none p-0 fw-bold"
                                                        onclick="geocodeAddress()" style="font-size: 11px;">
                                                        <i class="fa fa-search-location me-1"></i>Cari dari Alamat
                                                    </button>
                                                </label>
                                                <div id="mitraMap" class="w-100 rounded-3 border"
                                                    style="height: 250px; z-index: 0;"></div>
                                                
                                                <input type="hidden" id="modal_latitude">
                                                <input type="hidden" id="modal_longitude">
                                            </div>
                                    </div>

                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label class="f-label">Email Perusahaan</label>
                                            <input type="email" id="modal_email" class="form-control f-input"
                                                placeholder="email@company.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">No. Telepon Kantor</label>
                                            <input type="text" id="modal_no_hp" class="form-control f-input"
                                                placeholder="021-xxxxxx">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Kontak Person -->
                        <div class="col-lg-4">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i
                                    class="fa fa-user-tie me-2 text-primary"></i>Kontak Person (PIC)</h6>
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="f-label text-danger fw-bold">Nama PIC <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                            <input type="text" id="modal_kontak_nama" class="form-control f-input"
                                                placeholder="Nama Narahubung" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="f-label">Jabatan</label>
                                            <input type="text" id="modal_kontak_jabatan" class="form-control f-input"
                                                placeholder="Misal: Manager">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label text-danger fw-bold">No. HP PIC <span class="badge bg-danger text-white fs-6 ms-1">Wajib</span></label>
                                            <input type="text" id="modal_kontak_no_hp" class="form-control f-input"
                                                placeholder="08xxxxxxxxxx" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">Email PIC</label>
                                            <input type="email" id="modal_kontak_email" class="form-control f-input"
                                                placeholder="pic@email.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Sales Person -->
                        <div class="col-lg-4">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i
                                    class="fa fa-user me-2 text-primary"></i>Sales Person</h6>
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="alert alert-soft-info py-2 px-3 small border-0 mb-3">
                                        <i class="fa fa-info-circle me-1"></i> Pilih salesperson yang akan menangani mitra ini.
                                    </div>
                                    
                                    <div class="mt-3">
                                        <label class="f-label">Salesperson</label>
                                        <select id="modal_salesperson_id" class="form-select f-input">
                                            <option value="0" selected>Tanpa Sales Person</option>
                                            <option value="">Pilih Sales...</option>
                                            @foreach ($salesUsers ?? [] as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mt-4">
                                        <div class="text-center py-4 border border-dashed rounded bg-light">
                                            <i class="fa fa-user-plus mb-2 fs-4 text-secondary"></i>
                                            <div class="small text-muted">Salesperson akan otomatis terisi saat membuat invoice</div>
                                        </div>
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

<template id="productRowTemplate">
    <tr class="border-bottom border-light product-row">
        <td class="ps-4 py-3">
            <input type="hidden" class="prod-id">
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
            <span class="badge bg-light text-secondary border prod-unit-label">-</span>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm text-end prod-price" value="0"
                oninput="calculateInvoiceTotal()">
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
    .ls-1 {
        letter-spacing: 1px;
    }

    .f-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }

    .f-input {
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.8rem;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.2s;
    }

    .f-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

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
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
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


        let fpInvoice = null,
            fpDue = null;

    let masterProduk = [];

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
            const data = JSON.parse(chk.dataset.raw);
            toggleStockSelection(data, el.checked);
        });
    }

    function applyDueShortcut(days) {
        const base = document.getElementById('modal_tgl_invoice').value;
        if (!base) return;
        const date = new Date(base);
        date.setDate(date.getDate() + parseInt(days));
        if (fpDue) fpDue.setDate(date, true);
    }

    function initInvoiceDatePickers() {
        if (fpInvoice) fpInvoice.destroy();
        if (fpDue) fpDue.destroy();

        const common = {
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
            ...common,
            onChange: () => {
                if (fpDue && !document.getElementById('modal_tgl_jatuh_tempo').value) applyDueShortcut(30);
            }
        });

        fpDue = flatpickr('#modal_tgl_jatuh_tempo', {
            ...common,
            onReady: (sd, ds, inst) => injectDueSidebar(inst)
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



    let tomSelectMitraModal = null;
    let tomSelectStaffModal = null;

    function initTomSelectStaffModal(staffData = [], selectedId = null) {
        if (!window.TomSelect) return;

        if (tomSelectStaffModal) {
            try {
                tomSelectStaffModal.destroy();
            } catch (_) {}
            tomSelectStaffModal = null;
        }

        const sel = document.getElementById('modal_sales_id');
        if (!sel) return;

        // ONLY initialize TomSelect if the element is a select
        if (sel.tagName !== 'SELECT') {
            if (selectedId) sel.value = selectedId;
            return;
        }

        // Gather staff data
        const options = [];
        const finalStaffData = (staffData && staffData.length > 0) ? staffData : (window.masterStaff || []);

        finalStaffData.forEach(s => {
            options.push({
                id: String(s.id),
                name: s.name
            });
        });

        // Clear select to let TomSelect handle it via the options array
        if (sel.options) {
            sel.innerHTML = '<option value="">Pilih Staff...</option>';
        }

        tomSelectStaffModal = new TomSelect('#modal_sales_id', {
            options: options,
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            placeholder: 'Pilih Staff...',
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
            tomSelectStaffModal.setValue(String(selectedId), true);
        } else {
            tomSelectStaffModal.clear(true);
        }
    }

    function initTomSelectMitraModal(selectedId = null) {
        if (!window.TomSelect) {
            const sel = document.getElementById('modal_mitra_id');
            sel.innerHTML = '<option value="">Cari Supplier...</option>';
            masterMitra.forEach(m => sel.insertAdjacentHTML('beforeend',
                `<option value="${m.id}">${m.nama}</option>`));
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
            placeholder: 'Cari Supplier...',
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
        if (tomSelectStaffModal) {
            try {
                tomSelectStaffModal.destroy();
            } catch (_) {}
            tomSelectStaffModal = null;
        }
    });

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
        const unitLabel = tr.querySelector('.prod-unit-label');
        const descInput = tr.querySelector('.prod-desc');

        // Event Listeners for Calculation
        [priceInput, discInput].forEach(el => {
            el.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
                calculateInvoiceTotal();
            });
        });

        qtyInput.addEventListener('input', calculateInvoiceTotal);

        const ts = new TomSelect(selectEl, {
            options: masterProduk.map(p => ({
                id: String(p.id),
                nama: p.nama_produk,
                sku: p.sku_kode,
                harga: parseFloat(p.harga_beli || 0),
                unit: p.unit?.nama_unit || 'Pcs',
                qty: Number(p.qty || 0)
            })),
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'sku'],
            placeholder: 'Cari barang...',
            render: {
                option: (d, esc) => {
                    const badge = `<span class="badge bg-light text-dark border float-end">${d.qty} ${esc(d.unit)}</span>`;
                    return `<div>
                                <div class="fw-bold">${esc(d.nama)} ${badge}</div>
                                <small class="text-muted">${esc(d.sku)}</small>
                            </div>`;
                },
                item: (d, esc) => `<div>${esc(d.nama)}</div>`
            },
            onChange: function(val) {
                const selected = this.options[val];
                if (selected) {
                    idInput.value = selected.id;
                    priceInput.value = formatRupiah(selected.harga);
                    unitLabel.innerText = selected.unit;
                    calculateInvoiceTotal();
                }
            }
        });

        if (data) {
            setTimeout(() => {
                let rawId = data.produk_id || data.product_id;

                if (!rawId) {
                    if (data.nama_produk || data.sku_kode) {
                        // Try to find by name/sku if id missing? 
                        // For now assume rawId found or logic below handles text
                        rawId = data.id;
                    } else {
                        rawId = data.id;
                    }
                }

                const prodId = String(rawId);

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
                qtyInput.value = parseFloat(data.qty) || 1;

                const rawPrice = parseFloat(data.harga_satuan ?? data.harga_beli ?? data.harga_jual ?? 0);
                const rawDisc = parseFloat(data.diskon_item ?? data.diskon_nilai ?? 0);

                priceInput.value = formatRupiah(rawPrice);
                discInput.value = formatRupiah(rawDisc);
                if (data.diskon_tipe) {
                    tr.querySelector('.prod-disc-tipe').value = data.diskon_tipe;
                }
                descInput.value = data.deskripsi_item || data.deskripsi_produk || '';

                let unitName = 'Pcs';
                const masterItem = masterProduk.find(p => String(p.id) === prodId);

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

    function removeProductRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    function formatRupiah(angka) {
        if (angka === null || angka === undefined || angka === '') return '0';
        let value = String(angka).replace(/[^0-9]/g, '');
        let num = parseInt(value) || 0;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function cleanNumber(angka) {
        if (!angka) return 0;
        if (typeof angka === 'number') return angka;
        return parseInt(String(angka).replace(/[^0-9]/g, '')) || 0;
    }

    const _origOpenModal = openInvoiceModal;
    openInvoiceModal = async function(id = null, type = null, mode = 'create') {
        await _origOpenModal(id, type, mode);
        initInvoiceDatePickers();
    };

    // Helper IDR & Number
    window.financeHelpers = window.financeHelpers || {
        formatIDR: (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val),
        unformatIDR: (str) => {
            if (!str) return 0;
            return parseFloat(str.replace(/[^0-9,-]+/g, "").replace(",", "."));
        }
    };

    // Finance App object (required by other functions)
    window.financeApp = window.financeApp || {
        formatIDR: window.financeHelpers.formatIDR,
        API_URL: '/api/invoice-api',
        formatDate: (date) => {
            if (!date) return '-';
            const d = new Date(date);
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
    };

    window.productCollection = window.productCollection || [];
    window.mitraCollection = window.mitraCollection || [];
    window.masterMitra = window.masterMitra || [];

    // Initialize Master Data
    async function initializeInvoiceData() {
        try {
            const [mRes, pRes, sRes] = await Promise.all([
                fetch('/api/mitra-api?per_page=1000').then(r => r.json()),
                fetch('/api/product-api?per_page=1000').then(r => r.json()),
                fetch('/api/user-api/staff-by-permission?permission=purchase').then(r => r.json())
            ]);

            // Handle both paginated and non-paginated responses
            mitraCollection = mRes.data.data || mRes.data;
            productCollection = pRes.data.data || pRes.data;
            window.masterStaff = sRes.success ? sRes.data : [];

            // Assign all mitra to masterMitra (No filtering)
            masterMitra = mitraCollection;

            const select = document.getElementById('modal_mitra_id');
            select.innerHTML = '<option value="">Cari dan pilih mitra...</option>';
            masterMitra.forEach(m => select.insertAdjacentHTML('beforeend',
                `<option value="${m.id}">${m.nama}</option>`));

            initTomSelectStaffModal(window.masterStaff);
        } catch (e) {
            console.error("Master data fail", e);
        }
    }

    async function openInvoiceModal(id = null, type = null, mode = 'create') {
        const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').classList.add('d-none');
        document.getElementById('mitra_empty_state').classList.remove('d-none');

        if (tomSelectStaffModal) tomSelectStaffModal.clear();

        // Reset totals
        document.getElementById('summary_subtotal').innerText = 'Rp 0';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';

        if (masterProduk.length === 0) await fetchMasterProduk();
        if (masterMitra.length === 0) await initializeInvoiceData();

        initTomSelectMitraModal();
        initTomSelectStaffModal(window.masterStaff || []);

        if (mode === 'edit' && id) {
            document.getElementById('modalTitle').innerText = 'Edit Invoice Pembelian';
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
                    initTomSelectStaffModal(window.masterStaff || [], inv.sales_id || null);

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
            document.getElementById('modalTitle').innerText = 'Invoice Pembelian Baru';
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


            addNewProductRow(); // Add 1 empty row
        }

        modal.show();
    }

    function removeRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    function autoFillPrice(id) {
        // Warning: Product API returns 'harga_jual'. For Purchase, we might want 'harga_beli' ideally.
        // But assuming Product API only gives 'harga_jual' for now, or user overrides it manually.
        const row = document.getElementById(`row_${id}`);
        const sel = row.querySelector('.prod-select');
        const price = sel.options[sel.selectedIndex].dataset.price || 0;
        row.querySelector('.prod-price').value = price;
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

    function renderMitraDetail() {
        const id = document.getElementById('modal_mitra_id').value;
        const disp = document.getElementById('mitra_detail_display');
        const empty = document.getElementById('mitra_empty_state');

        if (!id) {
            disp.classList.add('d-none');
            empty.classList.remove('d-none');
            return;
        }

        const m = masterMitra.find(x => x.id == id);
        if (m) {
            document.getElementById('disp_mitra_nama').innerText = m.nama;
            document.getElementById('disp_mitra_tipe').innerText = m.tipe_mitra;
            document.getElementById('disp_mitra_alamat').innerText = m.alamat || '-';
            document.getElementById('disp_mitra_telp').innerText = m.no_hp || '-';

            disp.classList.remove('d-none');
            empty.classList.add('d-none');
        } else {
            disp.classList.add('d-none');
            empty.classList.remove('d-none');
        }

    }

    function resetModalForm() {
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').innerHTML =
            `<i class="fa fa-info-circle me-2 text-primary"></i>Detail muncul otomatis.`;
        document.getElementById('summary_subtotal').innerText = 'Rp 0';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';
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
                    harga_satuan: price,
                    diskon_nilai: disc,
                    diskon_tipe: row.querySelector('.prod-disc-tipe').value,
                    total_harga_item: cleanNumber(row.querySelector('.prod-subtotal').innerText)
                });
            }
        }

        const payload = {
            invoice: {
                tipe_invoice: 'Purchase',
                nomor_invoice: document.getElementById('modal_nomor_invoice').value,
                tgl_invoice: document.getElementById('modal_tgl_invoice').value,
                tgl_jatuh_tempo: document.getElementById('modal_tgl_jatuh_tempo').value,
                mitra_id: mitraIdVal,
                ref_no: document.getElementById('modal_ref_no').value,
                keterangan: document.getElementById('modal_keterangan').value,
                syarat_ketentuan: document.getElementById('modal_syarat').value,
                sales_id: document.getElementById('modal_sales_id').value || 0,
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
                window.location.href = `{{ url('purchase') }}/${invoiceId}?action=created`;
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

    let stockCache = [];
    let selectedStockItems = [];

    function toggleStockSelection(item, isChecked) {
        if (isChecked) {
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
        
        const checkboxes = document.querySelectorAll('.stock-check');
        checkboxes.forEach(chk => {
            if (String(chk.value) === String(id)) chk.checked = false;
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
                if (typeof renderPaginationControls === 'function') {
                    renderPaginationControls(result.data, 'stockPagination', fetchStockPage);
                }
            }
        } catch (e) {
            console.error('Fetch stock page failed:', e);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger p-4">Gagal memuat data</td></tr>';
        }
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

            if (selectedStockItems.find(x => String(x.id) === String(item.id)) || existingIds.includes(String(item.id))) {
                chk.checked = true;
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

    function setupStockSearch() {
        const searchInput = document.getElementById('stockSearchInput');
        const categoryFilter = document.getElementById('stockCategoryFilter');

        let stockSearchTimer = null;
        if (searchInput) {
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

    async function loadStockCategories() {
        try {
            const res = await fetch('/api/product-categories-api');
            const result = await res.json();

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

        // Reload semua data
        openStockModal();
    }

    // Panggil setup saat modal dibuka
    document.addEventListener('DOMContentLoaded', function() {
        setupStockSearch();
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

        selectedStockItems.forEach(data => {
            const productId = String(data.id);

            if (!existingIds.includes(productId)) {
                if (target === 'bulk') {
                    if (typeof bulkAddNewProductRow === 'function') {
                        bulkAddNewProductRow({
                            id: data.id,
                            nama_produk: data.nama_produk,
                            harga_beli: data.harga_beli,
                            qty: 1,
                            unit: data.unit?.nama_unit || 'Pcs',
                            product: data
                        });
                    }
                } else {
                    addNewProductRow({
                        product_id: data.id,
                        id: data.id,
                        nama_produk_manual: data.nama_produk,
                        harga_jual: data.harga_beli,
                        qty: 1,
                        unit: data.unit?.nama_unit || 'Pcs',
                        product: data
                    });
                }
            }
        });

        const modalEl = document.getElementById('stockModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    // Mitra Modal JavaScript Functions
let mitraMap = null;
let mitraMarker = null;
let leafletIconFixed = false;

function fixLeafletDefaultIcon() {
    if (leafletIconFixed) return;
    if (!window.L || !L.Icon) {
        setTimeout(fixLeafletDefaultIcon, 200);
        return;
    }
    leafletIconFixed = true;
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    });
}

function initMitraMap(lat = -6.200000, lng = 106.816666) {
    // Ensure container exists
    if (!document.getElementById('mitraMap')) return;
    fixLeafletDefaultIcon();

    if (!mitraMap) {
        mitraMap = L.map('mitraMap').setView([lat, lng], 13);
        addBestTileLayer(mitraMap);

        mitraMap.on('click', function (e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
        });
    } else {
        mitraMap.setView([lat, lng], 13);
    }

    updateMarker(lat, lng);

    // Fix Leaflet sizing issue in modal
    setTimeout(() => {
        mitraMap.invalidateSize();
    }, 500);
}

function updateMarker(lat, lng) {
    if (mitraMarker) {
        mitraMarker.setLatLng([lat, lng]);
    } else {
        mitraMarker = L.marker([lat, lng], { draggable: true }).addTo(mitraMap);
        mitraMarker.on('dragend', function (e) {
            const position = e.target.getLatLng();
            document.getElementById('modal_latitude').value = position.lat.toFixed(8);
            document.getElementById('modal_longitude').value = position.lng.toFixed(8);
        });
    }
    document.getElementById('modal_latitude').value = parseFloat(lat).toFixed(8);
    document.getElementById('modal_longitude').value = parseFloat(lng).toFixed(8);
}

async function geocodeAddress() {
    const address = document.getElementById('modal_alamat').value;
    if (!address) {
        alert('Mohon isi alamat terlebih dahulu');
        return;
    }

    const btn = document.querySelector('button[onclick="geocodeAddress()"]');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Mencari...';
    btn.disabled = true;

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
        const data = await response.json();

        if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lon = parseFloat(data[0].lon);
            mitraMap.setView([lat, lon], 16);
            updateMarker(lat, lon);
        } else {
            alert('Lokasi tidak ditemukan. Silakan geser marker secara manual.');
        }
    } catch (error) {
        console.error('Geocoding error:', error);
        alert('Gagal koneksi ke layanan peta.');
    } finally {
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }
}

function addBestTileLayer(mapInstance) {
    const localUrl = '/assets/tiles/{z}/{x}/{y}.png';
    const remoteUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    let layer = L.tileLayer(localUrl, { attribution: '© OpenStreetMap contributors' });
    let switched = false;
    layer.on('tileerror', function () {
        if (switched) return;
        switched = true;
        mapInstance.removeLayer(layer);
        layer = L.tileLayer(remoteUrl, { attribution: '© OpenStreetMap contributors' });
        layer.addTo(mapInstance);
    });
    layer.addTo(mapInstance);
}

async function openMitraModal(id = null) {
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mitraModal'));
    document.getElementById('mitraForm').reset();

    // Default Jakarta
    let lat = -6.200000;
    let lng = 106.816666;

    if (id) {
        document.getElementById('mitraModalTitle').innerText = 'Edit Data Mitra';
        document.getElementById('mitra_form_mode').value = 'edit';
        document.getElementById('edit_mitra_id').value = id;

        try {
            const res = await fetch(`/api/mitra-api/${id}`);
            const json = await res.json();
            if (json.success) {
                const data = json.data;
                document.querySelector(`input[name="tipe_mitra"][value="${data.tipe_mitra}"]`).checked = true;
                document.getElementById('modal_is_cash_customer').checked = data.is_cash_customer ? true : false;
                document.getElementById('modal_badan_usaha').value = data.badan_usaha || 'PT';
                document.getElementById('modal_nama').value = data.nama;
                document.getElementById('modal_nomor_mitra').value = data.nomor_mitra || '';
                document.getElementById('modal_ktp_npwp').value = data.ktp_npwp || '';
                document.getElementById('modal_alamat').value = data.alamat || '';
                document.getElementById('modal_email').value = data.email || '';
                document.getElementById('modal_no_hp').value = data.no_hp || '';

                document.getElementById('modal_kontak_nama').value = data.kontak_nama || '';
                document.getElementById('modal_kontak_jabatan').value = data.kontak_jabatan || '';
                document.getElementById('modal_kontak_no_hp').value = data.kontak_no_hp || '';
                document.getElementById('modal_kontak_email').value = data.kontak_email || '';
                document.getElementById('modal_salesperson_id').value = data.salesperson_id || '';

                if (data.latitude && data.longitude) {
                    lat = parseFloat(data.latitude);
                    lng = parseFloat(data.longitude);
                }
            }
        } catch (e) { console.error(e); alert('Gagal load data mitra'); return; }

    } else {
        document.getElementById('mitraModalTitle').innerText = 'Tambah Mitra Baru';
        document.getElementById('mitra_form_mode').value = 'create';
        document.getElementById('modal_nomor_mitra').value = '';
        document.getElementById('modal_ktp_npwp').value = '';
        document.getElementById('modal_is_cash_customer').checked = false;
        document.getElementById('modal_salesperson_id').value = '0';
        // Default to Supplier for Purchase modal
        document.getElementById('typeSupplier').checked = true;
    }

    modal.show();

    // Initialize map after modal show
    document.getElementById('mitraModal').addEventListener('shown.bs.modal', function () {
        initMitraMap(lat, lng);
    }, { once: true });

    // Also call it immediately if modal is already shown (edge case) or use timeout
    setTimeout(() => initMitraMap(lat, lng), 200);
}

async function saveMitra() {
    const mode = document.getElementById('mitra_form_mode').value;
    const id = document.getElementById('edit_mitra_id').value;
    const url = mode === 'edit' ? `/api/mitra-api/${id}` : '/api/mitra-api';
    const method = mode === 'edit' ? 'PUT' : 'POST';

    const payload = {
        tipe_mitra: document.querySelector('input[name="tipe_mitra"]:checked').value,
        is_cash_customer: document.getElementById('modal_is_cash_customer').checked ? 1 : 0,
        badan_usaha: document.getElementById('modal_badan_usaha').value,
        nama: document.getElementById('modal_nama').value,
        nomor_mitra: document.getElementById('modal_nomor_mitra').value,
        ktp_npwp: document.getElementById('modal_ktp_npwp').value,
        alamat: document.getElementById('modal_alamat').value,
        email: document.getElementById('modal_email').value,
        no_hp: document.getElementById('modal_no_hp').value,
        kontak_nama: document.getElementById('modal_kontak_nama').value,
        kontak_jabatan: document.getElementById('modal_kontak_jabatan').value,
        kontak_no_hp: document.getElementById('modal_kontak_no_hp').value,
        kontak_email: document.getElementById('modal_kontak_email').value,
        salesperson_id: document.getElementById('modal_salesperson_id').value === '0' ? null : (document.getElementById('modal_salesperson_id').value || null),
        latitude: document.getElementById('modal_latitude').value,
        longitude: document.getElementById('modal_longitude').value
    };

    const missing = [];
    if (!payload.tipe_mitra) missing.push('Tipe Mitra');
    if (!payload.badan_usaha) missing.push('Bentuk Usaha');
    if (!payload.nama) missing.push('Nama Perusahaan / Mitra');
    if (!payload.ktp_npwp) missing.push('No. KTP / NPWP');
    if (!payload.alamat) missing.push('Alamat Lengkap');
    if (!payload.kontak_nama) missing.push('Nama PIC');
    if (!payload.kontak_no_hp) missing.push('No. HP PIC');

    if (missing.length) {
        alert('Mohon lengkapi field wajib:\n- ' + missing.join('\n- '));
        return;
    }

    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success) {
            alert('Data Mitra berhasil disimpan.');
            bootstrap.Modal.getInstance(document.getElementById('mitraModal')).hide();
            if (typeof loadMitraData === 'function') loadMitraData();
            
            // Update TomSelect in Purchase modal
            if (tomSelectMitraModal) {
                const opt = {
                    id: result.data.id,
                    nama: result.data.nama,
                    info: [result.data.no_hp, result.data.alamat].filter(Boolean).join(' \u2022 ') || ''
                };
                tomSelectMitraModal.addOption(opt);
                tomSelectMitraModal.addItem(result.data.id);
            } else {
                initTomSelectMitraModal(result.data.id);
            }
            
            // Trigger event for external listeners
            document.dispatchEvent(new CustomEvent('mitra-saved', { detail: result.data }));
        } else {
            if (result.errors && typeof result.errors === 'object') {
                const messages = Object.values(result.errors).flat().join('\n');
                alert('Validasi gagal:\n' + messages);
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
            }
        }
    } catch (e) {
        console.error(e); alert('Terjadi kesalahan sistem.');
    }
}
</script>
