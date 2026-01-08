@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title fw-bold">Kwitansi Pembayaran</h4>
                            <p class="text-muted mb-0 small">Kelola bukti bayar dan pelunasan invoice pelanggan.</p>
                        </div>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kwitansi</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-body p-4">
                            <div class="row g-2 mb-4 p-3 bg-light rounded-3 align-items-end border">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Cari Kwitansi / Pelanggan</label>
                                    <input type="text" id="filter-search" class="form-control" placeholder="Ketik nomor kwitansi...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Metode</label>
                                    <select id="filter-metode" class="tom-select-init">
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
                                    <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="openReceiptModal()">
                                        <i class="fa fa-plus-circle me-1"></i> TAMBAH KWITANSI
                                    </button>
                                </div>
                            </div>

                            <div class="bg-light border-bottom header-table-custom py-2 px-1 rounded-top border">
                                <div class="d-flex align-items-center">
                                    <div class="col-fixed-check text-center px-2">#</div>
                                    <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">
                                        <div class="col-3 text-start">No. Kwitansi & Pelanggan</div>
                                        <div class="col-2 text-center">Metode</div>
                                        <div class="col-3 text-center">Jumlah Bayar</div>
                                        <div class="col-2 text-center">Tgl Bayar</div>
                                        <div class="col-2 text-center">Ref. Invoice</div>
                                    </div>
                                    <div class="col-fixed-aksi text-center">Aksi</div>
                                </div>
                            </div>

                            <div class="accordion custom-coa-accordion mt-3" id="receiptAccordion"></div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <span id="pagination-info" class="text-muted small"></span>
                                <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReceipt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Buat Kwitansi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pilih Pelanggan</label>
                        <select id="mitra_id_modal" placeholder="Cari nama pelanggan...">
                            <option value="">Pilih Pelanggan</option>
                            @foreach($mitras as $m)
                                <option value="{{$m->id}}">{{$m->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Pembayaran</label>
                        <input type="date" id="tgl_pembayaran" class="form-control" value="{{date('Y-m-d')}}">
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="fw-bold mb-3"><i class="fa fa-list me-2 text-primary"></i>Daftar Invoice Belum Lunas</h6>
                        <div id="invoice-list-container" class="border rounded-3 overflow-hidden bg-light p-3 text-center text-muted small">
                            Pilih pelanggan terlebih dahulu untuk melihat invoice.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveReceipt()" class="btn btn-primary px-4 fw-bold" id="btn-save-receipt" disabled>Simpan Kwitansi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .col-fixed-check { width: 50px; flex-shrink: 0; }
    .col-fixed-aksi { width: 80px; flex-shrink: 0; }
    .header-table-custom { background: #f8f9fa; }
    .accordion-item { border: 1px solid #edf2f9 !important; margin-bottom: 10px; border-radius: 12px !important; }
    .row-spacious { padding: 15px 0; }
    /* Tom Select Mac Style */
    .ts-wrapper .ts-control { border-radius: 8px; padding: 10px; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const API_PAYMENT = '{{url("api/payment-api")}}';
    const API_INVOICE = '{{url("api/invoice-api")}}';
    let tomMitra;

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tom-select-init').forEach(el => new TomSelect(el));
        
        tomMitra = new TomSelect("#mitra_id_modal", {
            onChange: (value) => fetchInvoices(value)
        });

        loadReceiptData();
    });

    const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(val);

    // --- LOGIKA INDEX ---
    async function loadReceiptData(url = API_PAYMENT) {
        const accordion = document.getElementById('receiptAccordion');
        accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
        
        try {
            const search = document.getElementById('filter-search').value;
            const metode = document.getElementById('filter-metode').value;
            let fetchUrl = new URL(url);
            if (search) fetchUrl.searchParams.append('search', search);
            if (metode) fetchUrl.searchParams.append('metode_pembayaran', metode);

            const response = await fetch(fetchUrl);
            const result = await response.json();

            if (result.success) {
                renderReceiptList(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            accordion.innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
        }
    }

    function renderReceiptList(data) {
        const accordion = document.getElementById('receiptAccordion');
        accordion.innerHTML = '';

        data.forEach(item => {
            const html = `
            <div class="accordion-item shadow-none">
                <div class="accordion-header bg-white">
                    <div class="accordion-content-wrapper">
                        <div class="col-fixed-check text-center text-muted small">${item.id}</div>
                        <button class="accordion-button collapsed shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#pay-${item.id}">
                            <div class="row flex-grow-1 m-0 align-items-center text-center row-spacious">
                                <div class="col-3 text-start ps-0">
                                    <div class="fw-bold text-dark">${item.nomor_pembayaran}</div>
                                    <div class="text-muted small">${item.invoice?.mitra?.nama || 'N/A'}</div>
                                </div>
                                <div class="col-2"><span class="badge badge-soft-primary px-2">${item.metode_pembayaran}</span></div>
                                <div class="col-3 fw-bold text-primary">${formatIDR(item.jumlah_bayar)}</div>
                                <div class="col-2 text-muted small">${item.tgl_pembayaran}</div>
                                <div class="col-2 fw-bold text-dark small">${item.invoice?.nomor_invoice || '-'}</div>
                            </div>
                        </button>
                    </div>
                </div>
                <div id="pay-${item.id}" class="accordion-collapse collapse" data-bs-parent="#receiptAccordion">
                    <div class="accordion-body bg-light border-top small">
                        Catatan: ${item.catatan || '-'}
                    </div>
                </div>
            </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    // --- LOGIKA MODAL TAMBAH ---
    function openReceiptModal() {
        new bootstrap.Modal(document.getElementById('modalReceipt')).show();
    }

    async function fetchInvoices(mitraId) {
        const container = document.getElementById('invoice-list-container');
        if(!mitraId) return;

        container.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Mencari invoice...';
        
        try {
            // Ambil invoice dengan status NOT PAID atau PARTIALLY PAID
            const response = await fetch(`${API_INVOICE}?mitra_id=${mitraId}&status_pembayaran=Unpaid`);
            const result = await response.json();
            
            container.innerHTML = '';
            if(result.data.data.length === 0) {
                container.innerHTML = 'Tidak ada invoice yang perlu dibayar untuk pelanggan ini.';
                document.getElementById('btn-save-receipt').disabled = true;
                return;
            }

            let tableHtml = `<table class="table table-sm bg-white mb-0 border rounded">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-2">No. Invoice</th>
                        <th class="text-end">Total Akhir</th>
                        <th class="text-end text-warning">Sisa Tagihan</th>
                        <th class="text-center" width="150">Bayar</th>
                    </tr>
                </thead>
                <tbody>`;

            result.data.data.forEach(inv => {
                tableHtml += `
                <tr class="align-middle">
                    <td class="ps-2 fw-bold">${inv.nomor_invoice}</td>
                    <td class="text-end">${formatIDR(inv.total_akhir)}</td>
                    <td class="text-end text-danger fw-bold">${formatIDR(inv.total_akhir)}</td>
                    <td class="px-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control invoice-pay-input" 
                                data-id="${inv.id}" 
                                data-max="${inv.total_akhir}" 
                                placeholder="0">
                        </div>
                    </td>
                </tr>`;
            });
            tableHtml += `</tbody></table>`;
            container.innerHTML = tableHtml;
            document.getElementById('btn-save-receipt').disabled = false;

        } catch (e) {
            container.innerHTML = 'Gagal memuat invoice.';
        }
    }

    async function saveReceipt() {
        const inputs = document.querySelectorAll('.invoice-pay-input');
        const btn = document.getElementById('btn-save-receipt');
        const tgl = document.getElementById('tgl_pembayaran').value;

        let totalRequest = 0;
        let successCount = 0;

        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        for (const input of inputs) {
            const amount = parseFloat(input.value);
            if (amount > 0) {
                totalRequest++;
                const payload = {
                    invoice_id: input.dataset.id,
                    nomor_pembayaran: 'PYI/' + Date.now(), // Logic nomor bisa disesuaikan di Backend
                    tgl_pembayaran: tgl,
                    metode_pembayaran: 'Cash', // Default atau ambil dari select
                    jumlah_bayar: amount,
                    akun_keuangan_id: 1 // Default COA (Kas/Bank) sesuai DB mu
                };

                try {
                    const res = await fetch(API_PAYMENT, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    if (res.ok) successCount++;
                } catch (e) {}
            }
        }

        if(successCount > 0) {
            alert(`${successCount} pembayaran berhasil dicatat!`);
            location.reload();
        } else {
            alert('Silahkan masukkan jumlah bayar minimal pada satu invoice.');
            btn.disabled = false;
            btn.innerHTML = 'Simpan Kwitansi';
        }
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText = `Total ${meta.total} Kwitansi`;
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        meta.links.forEach(link => {
            const li = `<li class="page-item ${link.active?'active':''} ${!link.url?'disabled':''}"><a class="page-link" href="javascript:void(0)" onclick="loadReceiptData('${link.url}')">${link.label.replace('&laquo;', '').replace('&raquo;', '')}</a></li>`;
            container.insertAdjacentHTML('beforeend', li);
        });
    }
</script>
@endpush