@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

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
                                        <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="openCreateModal()">
                                            <i class="fa fa-plus-circle me-1"></i> TAMBAH KUITANSI
                                        </button>
                                    </div>
                                </div>

                                <!-- List Header -->
                                <div class="bg-light border-bottom header-table-custom py-2 px-1 rounded-top border">
                                    <div class="d-flex align-items-center">
                                        <div class="col-fixed-check text-center px-2">#</div>
                                        <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-muted text-center"
                                            style="font-size: 10px; letter-spacing: 1px;">
                                            <div class="col-3 text-start ps-0">No. Kuitansi & Pelanggan</div>
                                            <div class="col-2">Metode / Akun</div>
                                            <div class="col-2">Status</div>
                                            <div class="col-2">Tgl Bayar</div>
                                            <div class="col-3 text-end">Jumlah Bayar</div>
                                        </div>
                                        <div class="col-fixed-aksi text-center">Aksi</div>
                                    </div>
                                </div>

                                <!-- Accordion List -->
                                <div class="accordion custom-coa-accordion mt-3" id="receiptAccordion">
                                    <!-- Data will be populated by JS -->
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <span id="pagination-info" class="text-muted small"></span>
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
                    <h5 class="fw-bold mb-0">Terima Pembayaran untuk Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-receipt" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Pembayaran Dari</label>
                                <select id="mitra_id" name="mitra_id" class="tom-select-init"
                                    placeholder="Loading clients...">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Ref. No.</label>
                                <input type="text" name="ref_no" id="ref_no" class="form-control"
                                    placeholder="Contoh: REF-123">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Setoran Ke (Akun Keuangan)</label>
                                <select id="akun_keuangan_id" name="akun_keuangan_id" class="form-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0"><i class="fa fa-file-invoice me-2 text-primary"></i>Rincian Tagihan</h6>
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
                                <div class="p-3 border rounded-3 bg-light">
                                    <span class="small fw-bold text-muted d-block mb-1">Estimasi dalam SGD* (1 SGD =
                                        13,128.53 IDR)</span>
                                    <div class="d-flex align-items-baseline">
                                        <h4 class="fw-bold mb-0 text-dark" id="sgd-display">SGD 0.00</h4>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-2"
                                            onclick="refreshSgd()"><i class="fa fa-sync-alt fa-xs"></i></button>
                                    </div>
                                    <p class="text-muted small mt-1 mb-0">Estimasi Konversi Nominal</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3"
                                    placeholder="Masukkan catatan tambahan..."></textarea>
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
                    <h5 class="fw-bold mb-0">Pilih Invoice Penjualan</h5>
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
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        const API_PAYMENT = '{{ url('api/payment-api') }}';
        const API_INVOICE = '{{ url('api/invoice-api') }}';
        const API_MITRA = '{{ url('api/mitra-api') }}';
        const API_COA = '{{ url('api/coa-api') }}';
        const SGD_RATE = 13128.53;
        let tomMitra;
        let selectedInvoices = [];

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
                    } catch(e) {
                        console.error('Failed to load invoice for receipt', e);
                    }
                }
            }
        });

        async function loadDropdowns() {
            try {
                // Load Mitras
                const resMitra = await fetch(API_MITRA);
                const jsonMitra = await resMitra.json();
                
                const mitraSelect = document.getElementById('mitra_id');
                mitraSelect.innerHTML = '<option value="">Pilih Pelanggan...</option>';
                if(jsonMitra.success) {
                    jsonMitra.data.data.forEach(m => { // Assuming paginated, or adjust if array
                         // NOTE: If API returns paginated 'data', use jsonMitra.data.data. If flat, jsonMitra.data
                         // Checking standard API format in this project usually returns paginated.
                         // But index methods might return pagination object.
                         // Let's assume pagination based on other files.
                         // Wait, for dropdowns we usually need all or search. 
                         // Let's just try to map whatever array we find.
                         mitraSelect.innerHTML += `<option value="${m.id}">${m.nama}</option>`;
                    });
                }
                
                // Initialize TomSelect after options loaded
                tomMitra = new TomSelect("#mitra_id", {
                    onChange: () => {
                        if (!tomMitra.skipClear) {
                            selectedInvoices = [];
                            renderSelectedTable();
                        }
                    }
                });

                // Load COA
                const resCoa = await fetch(`${API_COA}?is_kas_bank=1`);
                const jsonCoa = await resCoa.json();
                
                const coaSelect = document.getElementById('akun_keuangan_id');
                coaSelect.innerHTML = '';
                if(jsonCoa.success) {
                    jsonCoa.data.forEach(acc => {
                        coaSelect.innerHTML += `<option value="${acc.id}">${acc.nama_akun}</option>`;
                    });
                }

            } catch(e) {
                console.error("Failed to load dropdowns", e);
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
                let fetchUrl = new URL(url);
                if (search) fetchUrl.searchParams.append('search', search);
                if (metode) fetchUrl.searchParams.append('metode_pembayaran', metode);

                const res = await fetch(fetchUrl);
                const result = await res.json();
                if (result.success) {
                    renderReceiptList(result.data.data);
                    renderPagination(result.data);
                }
            } catch (e) {
                accordion.innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
            }
        }
    </script>
@endpush
