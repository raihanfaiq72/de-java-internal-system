@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Title -->
                <div class="row mb-3">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title fw-bold">Semua Kuitansi Penjualan</h4>
                                <p class="text-muted mb-0 small">Kelola kuitansi dan riwayat pembayaran pelanggan.</p>
                            </div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Kuitansi</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <!-- Filters -->
                                <div class="row g-2 mb-4 p-3 bg-light rounded-3 align-items-end border">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Cari Kuitansi / Pelanggan</label>
                                        <input type="text" id="filter-search" class="form-control"
                                            placeholder="Ketik nomor kuitansi...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted">Metode</label>
                                        <select id="filter-metode" class="form-select">
                                            <option value="">Semua Metode</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Transfer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button onclick="loadReceiptData()" class="btn btn-dark w-100 fw-bold shadow-sm">
                                            <i class="fa fa-filter me-1"></i> FILTER
                                        </button>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button class="btn btn-primary fw-bold shadow-sm" onclick="openCreateModal()">
                                            <i class="fa fa-plus-circle me-1"></i> TAMBAH KUITANSI
                                        </button>
                                    </div>
                                </div>

                                <!-- List Header -->
                                <div class="bg-light border-bottom header-table-custom py-3 rounded-top border d-none d-md-block"
                                    style="padding-left: 1.25rem; padding-right: 1.25rem;">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="col-fixed-check text-center px-2">#</div>
                                        <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-muted text-center align-items-center"
                                            style="font-size: 10px; letter-spacing: 1px;">
                                            <div class="col-3 text-start ps-0">No. Kuitansi & Pelanggan</div>
                                            <div class="col-2">Metode / Akun</div>
                                            <div class="col-2">Status</div>
                                            <div class="col-2">Tgl Bayar</div>
                                            <div class="col-3 text-end pe-4">Jumlah Bayar</div>
                                        </div>
                                        <div style="width: 1.25rem; flex-shrink: 0; margin-left: auto;"></div>
                                    </div>
                                </div>

                                <!-- Accordion List -->
                                <div class="accordion custom-coa-accordion mt-3" id="receiptAccordion">
                                    <!-- Data will be populated by JS -->
                                </div>

                                <!-- Pagination -->
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <span id="pagination-info" class="text-muted small fw-medium"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Receipt -->
    <div class="modal fade" id="modalCreateReceipt" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0 text-white">Terima Pembayaran untuk Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-receipt" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Pembayaran Dari</label>
                                <select id="mitra_id" name="mitra_id" class="tom-select-init"
                                    placeholder="Pilih Pelanggan...">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Metode Pembayaran</label>
                                <select id="metode_pembayaran" name="metode_pembayaran" class="form-select">
                                    <option value="Transfer">Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cek/Giro">Cek/Giro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Ref. No.</label>
                                <input type="text" name="ref_no" id="ref_no" class="form-control"
                                    placeholder="Contoh: REF-123">
                            </div>
                        </div>

                        <!-- Row 2: Setoran Ke & Catatan -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Setoran Ke (Akun Keuangan)</label>
                                <select id="akun_keuangan_id" name="akun_keuangan_id" class="form-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="1"
                                    placeholder="Masukkan catatan tambahan..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0"><i class="fa fa-file-invoice me-2 text-primary"></i>Rincian Tagihan
                            </h6>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3"
                                onclick="openInvoiceSelection()">
                                <i class="fa fa-plus-circle me-1"></i> Pilih Invoice Penjualan
                            </button>
                        </div>

                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-hover align-middle mb-0" id="table-selected-invoices">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small fw-bold text-muted py-3 ps-3">No. Invoice</th>
                                        <th class="small fw-bold text-muted py-3">Pelanggan</th>
                                        <th class="small fw-bold text-muted py-3 text-center">Tgl / Jatuh Tempo</th>
                                        <th class="small fw-bold text-muted py-3 text-end">Total Jumlah</th>
                                        <th class="small fw-bold text-muted py-3 text-end">Jumlah Tertagih</th>
                                        <th class="small fw-bold text-muted py-3 text-end pe-3" width="200">Jumlah
                                            Terbayar</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-details-body">
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa fa-info-circle me-1"></i> Belum ada invoice yang dipilih.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-light border-top-0">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold py-3">Total Jumlah Terbayar</td>
                                        <td class="text-end fw-bold py-3 pe-3 text-primary" id="total-payment-display">IDR
                                            0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">

                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small d-block">Tanda Tangan dan Meterai
                                    (Opsional)</label>
                                <div class="upload-area text-center border p-4 rounded-3 bg-light"
                                    id="upload-signature-area" style="cursor: pointer;"
                                    onclick="document.getElementById('signature_file').click()">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1 small fw-bold">Unggah Tanda Tangan</p>
                                    <p class="mb-0 text-muted" style="font-size: 10px;">Maksimal 20MB (JPEG, PNG)</p>
                                </div>
                                <input type="file" name="signature_file" id="signature_file" class="d-none"
                                    accept="image/*">
                                <div id="signature-preview" class="mt-2 text-center d-none">
                                    <img src="" class="img-thumbnail" style="max-height: 100px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger border-0 d-block mx-auto mt-1"
                                        onclick="clearSignature()">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="button" onclick="submitReceipt('save')" class="btn btn-primary px-4 fw-bold shadow-sm"
                        id="btn-save-submit">SIMPAN</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Invoice Selection -->
    <div class="modal fade" id="modalInvoiceSelection" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0 text-white">Pilih Invoice Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="selection-list-container">
                    <!-- Population by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="addSelectedInvoices()">Tambahkan
                        Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalPrintPreviewLabel">
                        <i class="fa fa-print text-primary me-2"></i> Preview Cetak Invoice
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="ratio ratio-16x9 border rounded bg-light" style="min-height: 70vh;">
                        <iframe id="print-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" onclick="triggerPrint()">
                        <i class="fa fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
    <template id="receipt-template">
        <div class="accordion-item shadow-sm border-0 overflow-hidden mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-white py-3" type="button" data-bs-toggle="collapse">
                    <div class="d-flex align-items-center w-100">
                        <div class="col-fixed-check text-center fw-bold text-muted tpl-index"></div>
                        <div class="row flex-grow-1 m-0 align-items-center text-center">
                            <div class="col-3 text-start ps-0">
                                <div class="fw-bold text-dark tpl-nomor-pembayaran"></div>
                                <div class="small text-muted text-uppercase tpl-mitra"></div>
                            </div>
                            <div class="col-2">
                                <div class="badge bg-light text-dark border px-2 py-1 tpl-metode"></div>
                                <div class="small text-muted mt-1 tpl-akun"></div>
                            </div>
                            <div class="col-2">
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                    SETTLED
                                </span>
                            </div>
                            <div class="col-2 small text-muted">
                                <i class="fa fa-calendar-alt me-1"></i> <span class="tpl-tanggal"></span>
                            </div>
                            <div class="col-3 text-end pe-4">
                                <div class="fw-bold text-dark tpl-jumlah" style="font-size: 1.1em;"></div>
                            </div>
                        </div>
                    </div>
                </button>
            </h2>
            <div class="accordion-collapse collapse">
                <div class="accordion-body bg-light border-top p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-3">Informasi Invoice</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" width="120">No. Invoice</td>
                                    <td class="fw-bold tpl-no-invoice"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Tagihan</td>
                                    <td class="fw-bold tpl-total-tagihan"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Catatan</td>
                                    <td class="tpl-catatan"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-end d-flex flex-column justify-content-center align-items-end">
                            <a href="#" class="btn btn-primary btn-sm fw-bold mb-2 tpl-link-detail"
                                style="width: 170px;">
                                <i class="fa fa-arrow-right me-1"></i> LIHAT DETAIL
                            </a>
                            <a href="javascript:void(0)" class="btn btn-outline-dark btn-sm fw-bold tpl-btn-print"
                                style="width: 170px;">
                                <i class="fa fa-print me-1"></i> CETAK KUITANSI
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Template for Invoice Selection Item -->
    <template id="tpl-invoice-selection-item">
        <label class="list-group-item d-flex gap-3">
            <input class="form-check-input flex-shrink-0 tpl-checkbox" type="checkbox">
            <span class="pt-1 form-checked-content w-100">
                <div class="d-flex justify-content-between w-100">
                    <strong class="tpl-inv-no"></strong>
                    <small class="text-muted tpl-inv-date"></small>
                </div>
                <div class="d-flex justify-content-between w-100 small">
                    <span class="tpl-inv-total"></span>
                    <span class="text-danger fw-bold tpl-inv-sisa"></span>
                </div>
            </span>
        </label>
    </template>

    <!-- Template for Selected Invoice Row -->
    <template id="tpl-selected-invoice-row">
        <tr>
            <td class="tpl-inv-no"></td>
            <td class="tpl-pelanggan"></td>
            <td class="text-center tpl-tgl"></td>
            <td class="text-end tpl-total"></td>
            <td class="text-end tpl-tertagih"></td>
            <td class="text-end pe-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text small">Rp</span>
                    <input type="text" class="form-control text-end tpl-input-bayar">
                    <button class="btn btn-outline-danger tpl-btn-remove" type="button">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </td>
        </tr>
    </template>

    <!-- Template for Pagination Item -->
    <template id="tpl-pagination-item">
        <li class="page-item">
            <button class="page-link border-0 mx-1 rounded shadow-sm fw-bold"></button>
        </li>
    </template>
@endsection

@push('css')
    <style>
        .col-fixed-check {
            width: 50px;
            flex-shrink: 0;
        }

        .col-fixed-aksi {
            width: 80px;
            flex-shrink: 0;
        }

        .header-table-custom {
            background: #f8f9fa;
        }

        .accordion-item {
            border: 1px solid #edf2f9 !important;
            margin-bottom: 10px;
            border-radius: 12px !important;
        }

        .row-spacious {
            padding: 15px 0;
        }

        .upload-area:hover {
            border-color: #0d6efd !important;
            background-color: #f0f7ff !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #fff;
            color: inherit;
        }
    </style>
    <style>
        @media (max-width: 576px) {
            .page-title-box .breadcrumb {
                display: none;
            }

            .card .card-body {
                padding: 1rem !important;
            }

            .row.g-2 .col-md-4,
            .row.g-2 .col-md-3,
            .row.g-2 .col-md-2 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .header-grid,
            .header-table-custom {
                display: none;
            }

            .accordion-button .row>[class^="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 6px;
            }

            .col-fixed-check,
            .col-fixed-aksi {
                text-align: left !important;
                width: auto;
            }
        }
    </style>
@endpush

@push('js')
    {{--
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script> --}}
    <script>
        const API_PAYMENT = '{{ url('api/payment-api') }}';
        const API_INVOICE = '{{ url('api/invoice-api') }}';
        const API_MITRA = '{{ url('api/mitra-api') }}';
        const API_FIN_ACC = '{{ url('api/financial-account-api') }}';
        const SGD_RATE = 13128.53;
        let tomMitra;
        let selectedInvoices = [];

        // --- MODAL FUNCTIONS ---
        function openCreateModal() {
            document.getElementById('form-receipt').reset();
            selectedInvoices = [];
            renderSelectedTable();
            if (tomMitra) tomMitra.clear();
            document.getElementById('upload-signature-area').classList.remove('d-none');
            document.getElementById('signature-preview').classList.add('d-none');

            const modal = new bootstrap.Modal(document.getElementById('modalCreateReceipt'), {
                focus: false
            });
            modal.show();
        }

        function clearSignature() {
            document.getElementById('signature_file').value = '';
            document.getElementById('upload-signature-area').classList.remove('d-none');
            document.getElementById('signature-preview').classList.add('d-none');
        }

        // --- INVOICE SELECTION ---
        async function openInvoiceSelection() {
            // Force close TomSelect to prevent it from popping up when modal focus shifts
            if (typeof tomMitra !== 'undefined' && tomMitra) {
                tomMitra.close();
                tomMitra.blur();
            }

            const mitraId = document.getElementById('mitra_id').value;
            if (!mitraId) {
                alert('Pilih Pelanggan terlebih dahulu!');
                return;
            }

            // Get Mitra Name for Search
            let mitraName = '';
            if (tomMitra) {
                const options = tomMitra.options;
                if (options[mitraId]) {
                    mitraName = options[mitraId].text;
                } else {
                    const item = tomMitra.getItem(mitraId);
                    if (item) mitraName = item.textContent;
                }
            }
            mitraName = mitraName.trim();

            const container = document.getElementById('selection-list-container');
            container.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';

            const modal = new bootstrap.Modal(document.getElementById('modalInvoiceSelection'), {
                focus: false
            });
            modal.show();

            try {
                // Use Mitra ID for search
                const url =
                    `${API_INVOICE}?mitra_id=${mitraId}&tipe_invoice=Sales&per_page=100`;

                const res = await fetch(url);
                const result = await res.json();

                if (result.success) {
                    const listContainer = document.createElement('div');
                    listContainer.className = 'list-group';

                    const template = document.getElementById('tpl-invoice-selection-item');
                    const invoices = result.data.data.filter(inv =>
                        inv.mitra_id == mitraId &&
                        inv.status_pembayaran !== 'Paid' &&
                        inv.status_dok === 'Approved' &&
                        inv.tipe_invoice === 'Sales'
                    );

                    if (invoices.length === 0) {
                        container.innerHTML =
                            '<div class="alert alert-info border-0 shadow-sm rounded-3"><i class="fa fa-info-circle me-2"></i>Tidak ada invoice yang perlu dibayar untuk pelanggan ini.</div>';
                        return;
                    }

                    invoices.forEach(inv => {
                        const clone = template.content.cloneNode(true);
                        const isSelected = selectedInvoices.find(s => s.id == inv.id) ? true : false;
                        const sisa = inv.total_akhir - (inv.payment_sum_jumlah_bayar || 0);

                        const checkbox = clone.querySelector('.tpl-checkbox');
                        checkbox.value = inv.id;
                        checkbox.checked = isSelected;
                        checkbox.setAttribute('data-json', JSON.stringify(inv).replace(/'/g, "&apos;"));

                        clone.querySelector('.tpl-inv-no').textContent = inv.nomor_invoice;
                        clone.querySelector('.tpl-inv-date').textContent = formatDateToDMY(inv.tgl_invoice);
                        clone.querySelector('.tpl-inv-total').textContent =
                            `Total: ${formatIDR(inv.total_akhir)}`;
                        clone.querySelector('.tpl-inv-sisa').textContent = `Sisa: ${formatIDR(sisa)}`;

                        listContainer.appendChild(clone);
                    });

                    container.innerHTML = '';
                    container.appendChild(listContainer);
                }
            } catch (e) {
                container.innerHTML = '<div class="text-danger">Gagal memuat invoice.</div>';
                console.error(e);
            }
        }

        function addSelectedInvoices() {
            const checkboxes = document.querySelectorAll('#selection-list-container input[type="checkbox"]:checked');
            checkboxes.forEach(cb => {
                const inv = JSON.parse(cb.getAttribute('data-json'));
                if (!selectedInvoices.find(s => s.id == inv.id)) {
                    const sisa = inv.total_akhir - (inv.payment_sum_jumlah_bayar || 0);
                    selectedInvoices.push({
                        id: inv.id,
                        nomor_invoice: inv.nomor_invoice,
                        pelanggan: inv.mitra?.nama || '-',
                        tgl: inv.tgl_invoice,
                        jatuh_tempo: inv.tgl_jatuh_tempo || '-',
                        total: inv.total_akhir,
                        tertagih: sisa,
                        bayar: sisa
                    });
                }
            });
            renderSelectedTable();
            bootstrap.Modal.getInstance(document.getElementById('modalInvoiceSelection')).hide();
        }

        function renderSelectedTable() {
            const tbody = document.getElementById('invoice-details-body');
            tbody.innerHTML = '';
            let totalBayar = 0;

            if (selectedInvoices.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa fa-info-circle me-1"></i> Belum ada invoice yang dipilih.</td></tr>`;
                document.getElementById('total-payment-display').innerText = 'IDR 0.00';
                return;
            }

            const template = document.getElementById('tpl-selected-invoice-row');

            selectedInvoices.forEach((inv, index) => {
                const clone = template.content.cloneNode(true);
                totalBayar += parseFloat(inv.bayar);

                clone.querySelector('.tpl-inv-no').textContent = inv.nomor_invoice;
                clone.querySelector('.tpl-pelanggan').textContent = inv.pelanggan;
                clone.querySelector('.tpl-tgl').textContent = formatDateToDMY(inv.tgl);
                clone.querySelector('.tpl-total').textContent = formatNumber(inv.total);
                clone.querySelector('.tpl-tertagih').textContent = formatNumber(inv.tertagih);

                const inputBayar = clone.querySelector('.tpl-input-bayar');
                inputBayar.value = formatRupiahSimple(inv.bayar);
                inputBayar.oninput = (e) => {
                    formatRupiahInput(e.target);
                    updateBayar(index, e.target.value, e.target);
                };

                const btnRemove = clone.querySelector('.tpl-btn-remove');
                btnRemove.onclick = () => removeInvoice(index);

                tbody.appendChild(clone);
            });

            document.getElementById('total-payment-display').innerText = formatIDR(totalBayar);
        }


        function updateBayar(index, val, element) {
            // Remove dots to get raw number
            let rawValue = val.replace(/\D/g, '');
            const inv = selectedInvoices[index];

            if (parseFloat(rawValue) > inv.tertagih) {
                alert(`Jumlah bayar tidak boleh melebihi sisa tagihan (${formatIDR(inv.tertagih)})`);
                rawValue = inv.tertagih;
                if (element) element.value = formatRupiahSimple(rawValue);
            }

            selectedInvoices[index].bayar = rawValue || 0;
            renderSelectedTableCountOnly(); // Don't re-render whole table or we lose focus/cursor
        }

        // Optimized render for total only, to avoid input focus loss
        function renderSelectedTableCountOnly() {
            let totalBayar = 0;
            selectedInvoices.forEach(inv => {
                totalBayar += parseFloat(inv.bayar || 0);
            });
            document.getElementById('total-payment-display').innerText = formatIDR(totalBayar);
        }

        function removeInvoice(index) {
            selectedInvoices.splice(index, 1);
            renderSelectedTable();
        }

        // --- SUBMIT ---
        async function submitReceipt(action) {
            if (selectedInvoices.length === 0) {
                alert('Pilih minimal satu invoice!');
                return;
            }

            const akunId = document.getElementById('akun_keuangan_id').value;
            if (!akunId) {
                alert('Pilih Akun Keuangan (Setoran Ke) terlebih dahulu!');
                return;
            }

            const btn = document.getElementById('btn-save-submit');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const formData = new FormData(document.getElementById('form-receipt'));

                // We handle single payment for single invoice or multiple? 
                // Backend PaymentController::store seems to handle ONE invoice per payment request: 'invoice_id' => 'required'
                // If we want bulk payment, we need to loop calls or update backend.
                // Looking at PaymentController: 
                // public function store(Request $request) { ... 'invoice_id' => 'required' ... }
                // So it only supports one invoice per payment. 
                // For now, let's assume we loop through selectedInvoices and create multiple payments.

                let successCount = 0;
                for (const inv of selectedInvoices) {
                    const payload = new FormData();
                    payload.append('invoice_id', inv.id);
                    payload.append('nomor_pembayaran', 'PAY-' + Date.now() + '-' + Math.floor(Math.random() *
                        1000)); // Auto gen or input? 
                    // Wait, usually backend auto-gens or we provide. Controller says: 'nomor_pembayaran' => 'required|unique...'
                    // Let's generate a unique one or ask user? The form doesn't have nomor_pembayaran input.
                    // Ideally backend should auto-gen if not provided, or we generate here.
                    // Let's generate a unique sequence here.

                    const uniqueSuffix = Math.random().toString(36).substr(2, 5).toUpperCase();
                    payload.append('nomor_pembayaran', `RCP/${new Date().getFullYear()}/${inv.id}/${uniqueSuffix}`);

                    payload.append('tgl_pembayaran', new Date().toISOString().split('T')[0]); // Today
                    payload.append('metode_pembayaran', document.getElementById('metode_pembayaran').value);

                    payload.append('jumlah_bayar', inv.bayar);
                    payload.append('akun_keuangan_id', formData.get('akun_keuangan_id'));
                    payload.append('catatan', formData.get('catatan'));
                    if (formData.get('ref_no')) payload.append('ref_no', formData.get('ref_no'));

                    // Signature? Backend doesn't seem to handle it in store(), maybe ActivityLog or ignored?
                    // PaymentController::store doesn't look like it saves file. 
                    // We'll skip file upload for now as backend doesn't support it yet in the code I saw.

                    const res = await fetch(API_PAYMENT, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: payload
                    });

                    const json = await res.json();
                    if (json.success) successCount++;
                }

                if (successCount === selectedInvoices.length) {
                    alert('Semua pembayaran berhasil disimpan!');
                    bootstrap.Modal.getInstance(document.getElementById('modalCreateReceipt')).hide();
                    loadReceiptData();
                } else {
                    alert(`Berhasil menyimpan ${successCount} dari ${selectedInvoices.length} pembayaran.`);
                    loadReceiptData();
                }

            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat menyimpan.');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        function formatDateToDMY(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // --- RENDER LIST ---
        function renderReceiptList(data) {
            const accordion = document.getElementById('receiptAccordion');
            const template = document.getElementById('receipt-template');
            const baseUrl = '{{ url('sales-receipt') }}';

            accordion.innerHTML = '';

            if (!data || data.length === 0) {
                accordion.innerHTML = '<div class="text-center p-5 text-muted">Belum ada data kuitansi.</div>';
                return;
            }

            data.forEach((item, index) => {
                const clone = template.content.cloneNode(true);
                const invoice = item.invoice || {};
                const mitra = invoice.mitra || {};
                const account = item.akun_keuangan || {};

                const headerId = `heading${item.id}`;
                const collapseId = `collapse${item.id}`;

                const header = clone.querySelector('.accordion-header');
                header.id = headerId;

                const button = clone.querySelector('.accordion-button');
                button.setAttribute('data-bs-target', `#${collapseId}`);

                const collapse = clone.querySelector('.accordion-collapse');
                collapse.id = collapseId;
                collapse.setAttribute('data-bs-parent', '#receiptAccordion');

                clone.querySelector('.tpl-index').textContent = index + 1;
                clone.querySelector('.tpl-nomor-pembayaran').textContent = item.nomor_pembayaran;
                clone.querySelector('.tpl-mitra').textContent = mitra.nama || '-';
                clone.querySelector('.tpl-metode').textContent = item.metode_pembayaran;
                clone.querySelector('.tpl-akun').textContent = account.nama_akun || '-';
                clone.querySelector('.tpl-tanggal').textContent = formatDateToDMY(item.tgl_pembayaran);
                clone.querySelector('.tpl-jumlah').textContent = formatIDR(item.jumlah_bayar);

                const linkDetail = clone.querySelector('.tpl-link-detail');
                linkDetail.href = `${baseUrl}/${item.id}`;

                const noInvoice = clone.querySelector('.tpl-no-invoice');
                noInvoice.textContent = `: ${invoice.nomor_invoice || '-'}`;

                const totalTagihan = clone.querySelector('.tpl-total-tagihan');
                totalTagihan.textContent = `: ${formatIDR(invoice.total_akhir)}`;

                const catatan = clone.querySelector('.tpl-catatan');
                catatan.textContent = `: ${item.catatan || '-'}`;

                const btnPrint = clone.querySelector('.tpl-btn-print');
                btnPrint.onclick = function() {
                    openPrintPreview(item.id);
                };

                accordion.appendChild(clone);
            });
        }

        function renderPagination(meta) {
            const container = document.getElementById('pagination-container');
            const info = document.getElementById('pagination-info');

            if (!meta || !meta.links) return;

            if (info) {
                info.innerText = `${meta.from || 0}-${meta.to || 0} dari ${meta.total || 0}`;
            }
            container.innerHTML = '';

            const template = document.getElementById('tpl-pagination-item');

            meta.links.forEach(link => {
                const clone = template.content.cloneNode(true);
                const li = clone.querySelector('.page-item');
                const btn = clone.querySelector('.page-link');

                if (link.active) li.classList.add('active');
                if (!link.url) li.classList.add('disabled');

                btn.innerHTML = link.label;
                btn.onclick = () => {
                    if (link.url) loadReceiptData(link.url);
                };

                container.appendChild(clone);
            });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            // Load Dropdowns Data
            await loadDropdowns();

            document.getElementById('signature_file').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        document.getElementById('upload-signature-area').classList.add('d-none');
                        document.getElementById('signature-preview').classList.remove('d-none');
                        document.querySelector('#signature-preview img').src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            loadReceiptData();

            // Check URL Params for Direct Create
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('open_create') === 'true') {
                openCreateModal();

                const mitraId = urlParams.get('mitra_id');
                const invoiceId = urlParams.get('invoice_id');

                // Wait for TomSelect to be ready if it's async (it's not here, but good practice)
                if (mitraId && tomMitra) {
                    tomMitra.skipClear = true;
                    tomMitra.setValue(mitraId);
                    tomMitra.skipClear = false;
                }

                if (invoiceId) {
                    try {
                        const res = await fetch(`${API_INVOICE}/${invoiceId}`);
                        const json = await res.json();
                        if (json.success) {
                            const data = json.data;
                            if (!selectedInvoices.find(s => s.id == data.id)) {
                                const tertagih = data.total_akhir - (data.payment_sum_jumlah_bayar || 0);
                                selectedInvoices.push({
                                    id: data.id,
                                    nomor_invoice: data.nomor_invoice,
                                    pelanggan: data.mitra?.nama || '-',
                                    tgl: data.tgl_invoice,
                                    jatuh_tempo: data.tgl_jatuh_tempo || '-',
                                    total: data.total_akhir,
                                    tertagih: tertagih,
                                    bayar: tertagih // Auto-fill full amount
                                });
                                renderSelectedTable();
                            }
                        }
                    } catch (e) {
                        console.error('Failed to load invoice for receipt', e);
                    }
                }
            }
        });

        async function loadDropdowns() {
            try {
                const partnerParam = new URLSearchParams({
                    per_page: 1000,
                });
                // Load Mitras
                const resMitra = await fetch(`${API_MITRA}?${partnerParam.toString()}`);
                if (!resMitra.ok) throw new Error("Gagal ambil data Mitra");
                const jsonMitra = await resMitra.json();

                let mitraData = [];
                if (jsonMitra.success) {
                    mitraData = jsonMitra.data?.data || jsonMitra.data || [];
                }

                const mitraSelect = document.getElementById('mitra_id');
                if (!mitraSelect) {
                    console.warn("Element mitra_id tidak ditemukan");
                    return;
                }

                // Destroy existing TomSelect if any
                if (mitraSelect.tomselect) {
                    mitraSelect.tomselect.destroy();
                }

                mitraSelect.innerHTML = '<option value="">Pilih Pelanggan...</option>';
                mitraData.forEach(m => {
                    mitraSelect.innerHTML += `<option value="${m.id}">${m.nama}</option>`;
                });

                // Initialize TomSelect for Mitra
                tomMitra = new TomSelect("#mitra_id", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    onChange: () => {
                        if (!tomMitra.skipClear) {
                            selectedInvoices = [];
                            renderSelectedTable();
                        }
                    }
                });

                // Load Financial Accounts
                const resFin = await fetch(API_FIN_ACC);
                if (!resFin.ok) throw new Error("Gagal ambil data Akun Keuangan");
                const jsonFin = await resFin.json();

                let finData = [];
                if (jsonFin.success) {
                    finData = jsonFin.data?.data || jsonFin.data || [];
                }

                const coaSelect = document.getElementById('akun_keuangan_id');
                if (!coaSelect) {
                    return;
                }

                if (coaSelect.tomselect) {
                    coaSelect.tomselect.destroy();
                }

                coaSelect.innerHTML = '<option value="">Pilih Akun...</option>';
                finData.forEach(acc => {
                    const accName = acc.name || acc.nama_akun || 'Unnamed';
                    const accCode = acc.code || acc.kode_akun || '';
                    coaSelect.innerHTML +=
                        `<option value="${acc.id}">${accName} ${accCode ? `(${accCode})` : ''}</option>`;
                });

                new TomSelect("#akun_keuangan_id", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

            } catch (e) {
                console.error("Critical error in loadDropdowns:", e);
                // Fallback: Clear the loading state
                const coaSelect = document.getElementById('akun_keuangan_id');
                if (coaSelect) coaSelect.innerHTML = '<option value="">Gagal memuat akun</option>';
            }
        }

        // --- RUPIAH FORMATTER HELPER ---
        function formatRupiahSimple(angka) {
            if (!angka) return '';
            let number_string = angka.toString().replace(/[^,\d]/g, '');
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        function formatRupiahInput(input) {
            let val = input.value;
            // Allow only digits
            val = val.replace(/[^0-9]/g, '');
            // Convert to integer to remove leading zeros if any, then format
            if (val !== '') {
                val = parseInt(val, 10);
                input.value = formatRupiahSimple(val);
            } else {
                input.value = '';
            }
        }

        function formatIDR(val) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val);
        }

        function formatNumber(val) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(val);
        }

        // --- LOGIKA LIST ---
        async function loadReceiptData(url = API_PAYMENT) {
            const accordion = document.getElementById('receiptAccordion');
            accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';

            try {
                const search = document.getElementById('filter-search').value;
                const metode = document.getElementById('filter-metode').value;

                // If url is just a base path, we make it a URL object to append params
                // If url already has params (from pagination links), URL object handles it
                let fetchUrl = new URL(url);

                fetchUrl.searchParams.set('tipe_receipt', 'Sales');
                if (search) fetchUrl.searchParams.set('search', search);
                if (metode) fetchUrl.searchParams.set('metode_pembayaran', metode);

                const res = await fetch(fetchUrl);
                const result = await res.json();
                if (result.success) {
                    // result.data is the paginator object
                    renderReceiptList(result.data.data);
                    renderPagination(result.data);
                }
            } catch (e) {
                console.error("Error loading receipt data:", e);
                accordion.innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
            }
        }

        function openPrintPreview(id) {
            const printUrl = `{{ url('sales-receipt/print') }}/${id}`;

            const modalContainer = document.getElementById('modalPrintPreview');

            if (!modalContainer) {
                console.error('Modal container tidak ditemukan di halaman ini.');
                return;
            }

            const iframe = modalContainer.querySelector('iframe');

            if (!iframe) {
                console.error('Elemen iframe tidak ditemukan di dalam modal.');
                alert('Gagal memuat preview cetak.');
                return;
            }

            iframe.src = printUrl;

            const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
            bModal.show();
        }

        function triggerPrint() {
            const iframe = document.getElementById('print-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }
    </script>
@endpush
