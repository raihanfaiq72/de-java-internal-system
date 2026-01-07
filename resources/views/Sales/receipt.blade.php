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
                            <p class="text-muted mb-0 small">Mencatat pelunasan invoice dari pelanggan.</p>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="openReceiptModal()">
                                <i class="fa fa-plus-circle me-1"></i> TERIMA PEMBAYARAN
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="bg-light border-bottom py-2 px-1 rounded-top border">
                        <div class="d-flex align-items-center fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                            <div style="width: 50px;" class="text-center">ID</div>
                            <div class="row flex-grow-1 m-0">
                                <div class="col-3">Nomor & Pelanggan</div>
                                <div class="col-2 text-center">Metode</div>
                                <div class="col-3 text-center">Nominal Bayar</div>
                                <div class="col-2 text-center">Tanggal</div>
                                <div class="col-2 text-center">No. Invoice</div>
                            </div>
                            <div style="width: 80px;" class="text-center">Aksi</div>
                        </div>
                    </div>
                    <div class="accordion custom-coa-accordion mt-3" id="receiptAccordion"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReceipt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="fw-bold mb-0">Terima Pembayaran untuk Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Pembayaran Dari *</label>
                                    <select id="mitra_id_modal" placeholder="Pilih Pelanggan...">
                                        <option value="">Pilih Pelanggan</option>
                                        @foreach($mitras as $m)
                                            <option value="{{$m->id}}">{{$m->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Nomor Pembayaran *</label>
                                    <input type="text" id="nomor_pembayaran" class="form-control" value="PYM/{{date('YmdHis')}}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tanggal *</label>
                                    <input type="date" id="tgl_pembayaran" class="form-control" value="{{date('Y-m-d')}}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Metode Pembayaran *</label>
                                    <select id="metode_pembayaran" class="form-select">
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Ke Akun Keuangan *</label>
                                    <select id="akun_keuangan_id" class="form-select">
                                        @foreach($accounts as $acc)
                                            <option value="{{$acc->id}}">{{$acc->nama_akun}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Ref. No.</label>
                                    <input type="text" id="ref_no" class="form-control" placeholder="Contoh: No. Transfer">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold">Catatan</label>
                                    <textarea id="catatan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="fw-bold mb-0">Kepada Invoice:</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small fw-bold text-muted">
                                                <th class="ps-3">No. Invoice</th>
                                                <th>Tanggal / Jatuh Tempo</th>
                                                <th class="text-end">Total</th>
                                                <th class="text-end text-danger">Tertagih</th>
                                                <th class="text-center" width="180">Jumlah Terbayar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoice-rows">
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    Silahkan pilih pelanggan terlebih dahulu.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white py-3 border-top">
                                <div class="d-flex justify-content-between align-items-center px-2">
                                    <h5 class="fw-bold mb-0">Total Jumlah Terbayar</h5>
                                    <h5 class="fw-bold text-primary mb-0" id="display-total-bayar">IDR 0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-3">
                <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" onclick="saveReceipt()" class="btn btn-primary px-4 fw-bold shadow-sm" id="btn-save">
                    Simpan Pembayaran Masuk
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPrint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-0" id="receipt-print-area">
                <div class="p-4 bg-white">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold mb-1 uppercase">KWITANSI PEMBAYARAN</h5>
                        <p class="small text-muted mb-0">Sistem Akuntansi Terpadu</p>
                        <hr class="border-dashed my-3">
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">No. Referensi:</span>
                        <span class="fw-bold" id="p-nomor"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Tanggal:</span>
                        <span id="p-tanggal"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Metode:</span>
                        <span id="p-metode"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 small">
                        <span class="text-muted">Pelanggan:</span>
                        <span class="fw-bold" id="p-pelanggan"></span>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="small text-muted mb-1 text-center text-uppercase" style="letter-spacing: 1px;">Total Dibayarkan</div>
                        <h3 class="fw-bold text-center mb-0 text-primary" id="p-nominal"></h3>
                    </div>

                    <div class="border-top pt-3 small">
                        <div class="mb-2">
                            <div class="text-muted">Untuk Invoice:</div>
                            <div class="fw-bold" id="p-invoice"></div>
                        </div>
                        <div>
                            <div class="text-muted">Catatan:</div>
                            <div id="p-catatan" class="font-italic text-muted">-</div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <p class="small text-muted mb-0">Terima kasih atas pembayaran Anda.</p>
                        <div class="mt-4 border-top pt-2">
                            <p class="text-center" style="font-size: 10px;">Dicetak pada {{date('d/m/Y H:i')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-dark w-100" onclick="printReceiptContent()">
                    <i class="fa fa-print me-2"></i> Cetak Kwitansi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const API_PAYMENT = '{{url("api/payment-api")}}';
    const API_INVOICE = '{{url("api/invoice-api")}}';
    let tsMitra;

    document.addEventListener('DOMContentLoaded', () => {
        tsMitra = new TomSelect("#mitra_id_modal", {
            onChange: (id) => fetchUnpaidInvoices(id)
        });
        loadReceiptData();
    });

    const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(val);

    async function fetchUnpaidInvoices(mitraId) {
        const container = document.getElementById('invoice-rows');
        if(!mitraId) {
            container.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">Silahkan pilih pelanggan.</td></tr>';
            return;
        }

        container.innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border spinner-border-sm"></div> Memuat invoice...</td></tr>';
        
        try {
            const res = await fetch(`${API_INVOICE}?mitra_id=${mitraId}&status_pembayaran=Unpaid`);
            const result = await res.json();
            
            container.innerHTML = '';
            if(result.data.data.length === 0) {
                container.innerHTML = '<tr><td colspan="5" class="text-center py-5">Pelanggan ini tidak memiliki tagihan aktif.</td></tr>';
                return;
            }

            result.data.data.forEach(inv => {
                const tertagih = inv.total_akhir; 
                const row = `
                    <tr>
                        <td class="ps-3 fw-bold text-primary">${inv.nomor_invoice}</td>
                        <td class="small">${inv.tgl_invoice} / <span class="text-danger">${inv.tgl_jatuh_tempo || '-'}</span></td>
                        <td class="text-end">${formatIDR(inv.total_akhir)}</td>
                        <td class="text-end text-danger fw-bold">${formatIDR(tertagih)}</td>
                        <td class="px-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">Rp</span>
                                <input type="number" class="form-control inv-pay-input" 
                                    data-id="${inv.id}" 
                                    oninput="calculateTotal()" 
                                    placeholder="0">
                            </div>
                        </td>
                    </tr>`;
                container.insertAdjacentHTML('beforeend', row);
            });
        } catch (e) {
            container.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-5">Gagal mengambil data.</td></tr>';
        }
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.inv-pay-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('display-total-bayar').innerText = formatIDR(total);
    }

    async function saveReceipt() {
        const btn = document.getElementById('btn-save');
        const inputs = document.querySelectorAll('.inv-pay-input');
        
        let processed = 0;
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';

        for (let input of inputs) {
            const amount = parseFloat(input.value);
            if(amount > 0) {
                const payload = {
                    invoice_id: input.dataset.id,
                    nomor_pembayaran: document.getElementById('nomor_pembayaran').value,
                    tgl_pembayaran: document.getElementById('tgl_pembayaran').value,
                    metode_pembayaran: document.getElementById('metode_pembayaran').value,
                    akun_keuangan_id: document.getElementById('akun_keuangan_id').value,
                    jumlah_bayar: amount,
                    ref_no: document.getElementById('ref_no').value,
                    catatan: document.getElementById('catatan').value
                };

                await fetch(API_PAYMENT, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                processed++;
            }
        }

        if(processed > 0) {
            alert('Pembayaran berhasil disimpan!');
            location.reload();
        } else {
            alert('Masukkan jumlah bayar minimal pada satu invoice.');
            btn.disabled = false;
            btn.innerHTML = 'Simpan Pembayaran Masuk';
        }
    }

    async function loadReceiptData() {
        const accordion = document.getElementById('receiptAccordion');
        const res = await fetch(API_PAYMENT);
        const result = await res.json();
        
        accordion.innerHTML = '';
        result.data.data.forEach(item => {
            const itemJson = JSON.stringify(item).replace(/"/g, '&quot;');
            const html = `
                <div class="accordion-item border-0 mb-2 shadow-none border-bottom rounded-0">
                    <div class="d-flex align-items-center py-3">
                        <div style="width: 50px;" class="text-center text-muted small">${item.id}</div>
                        <div class="row flex-grow-1 m-0 align-items-center text-center">
                            <div class="col-3 text-start ps-0">
                                <div class="fw-bold text-dark">${item.nomor_pembayaran}</div>
                                <div class="text-muted small">${item.invoice?.mitra?.nama || 'Umum'}</div>
                            </div>
                            <div class="col-2"><span class="badge bg-light text-dark border">${item.metode_pembayaran}</span></div>
                            <div class="col-3 fw-bold text-primary">${formatIDR(item.jumlah_bayar)}</div>
                            <div class="col-2 text-muted small">${item.tgl_pembayaran}</div>
                            <div class="col-2 fw-bold text-dark small">${item.invoice?.nomor_invoice || '-'}</div>
                        </div>
                        <div style="width: 80px;" class="text-center">
                            <button class="btn btn-sm btn-light border p-1 px-2" onclick="showPrintModal(${itemJson})">
                                <i class="fa fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    function showPrintModal(data) {
        document.getElementById('p-nomor').innerText = data.nomor_pembayaran;
        document.getElementById('p-tanggal').innerText = data.tgl_pembayaran;
        document.getElementById('p-metode').innerText = data.metode_pembayaran;
        document.getElementById('p-pelanggan').innerText = data.invoice?.mitra?.nama || 'Umum';
        document.getElementById('p-nominal').innerText = formatIDR(data.jumlah_bayar);
        document.getElementById('p-invoice').innerText = data.invoice?.nomor_invoice || '-';
        document.getElementById('p-catatan').innerText = data.catatan || '-';

        const modal = new bootstrap.Modal(document.getElementById('modalPrint'));
        modal.show();
    }

    function printReceiptContent() {
        const printContents = document.getElementById('receipt-print-area').innerHTML;
        const originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }

    function openReceiptModal() {
        const modal = new bootstrap.Modal(document.getElementById('modalReceipt'));
        modal.show();
    }
</script>
@endpush

<style>
    .badge-soft-primary { background: #e7f0ff; color: #3b7ddd; }
    .inv-pay-input:focus { border-color: #007AFF; box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1); }
    .table thead th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .border-dashed { border-top: 2px dashed #dee2e6; }
    @media print {
        body * { visibility: hidden; }
        #receipt-print-area, #receipt-print-area * { visibility: visible; }
        #receipt-print-area { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>