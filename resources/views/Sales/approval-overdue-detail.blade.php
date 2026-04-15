@extends('Layout.main')

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Header -->
                <div class="row align-items-center mt-3 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('sales.approval.overdue') }}" class="btn btn-light border me-3 shadow-sm rounded-circle"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Review Persetujuan Overdue</h4>
                                <p class="text-muted small mb-0" id="header-subtitle">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-md-end gap-2" id="action-header">
                        <!-- Buttons injected by JS -->
                    </div>
                </div>

                <div class="row justify-content-center" id="main-content" style="display:none;">
                    <div class="col-lg-10">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                            <div class="card-body p-5 bg-white">
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
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Ditagihkan
                                            Kepada:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="mitra-name">-</h6>
                                        <p class="text-muted small mb-0" id="mitra-address">-</p>
                                        <p class="text-muted small" id="mitra-phone">-</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Detail
                                            Transaksi:</label>
                                        <p class="mb-1"><span class="text-muted">Tipe:</span> <span
                                                class="badge bg-light text-dark border" id="inv-type">-</span></p>
                                        <p class="mb-1"><span class="text-muted">Salesman/Ref:</span> <span
                                                class="fw-bold" id="inv-ref">-</span></p>
                                        <p class="mb-1"><span class="text-muted">Jatuh Tempo:</span> <span
                                                class="fw-bold text-danger" id="inv-due">-</span></p>
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
                                                <td colspan="3" class="text-end py-2 text-muted">Diskon Tambahan</td>
                                                <td class="text-end py-2 fw-bold text-danger pe-4" id="val-discount">- Rp 0
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-2 text-muted">Pajak</td>
                                                <td class="text-end py-2 fw-bold pe-4" id="val-tax">Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-3 fs-5 fw-bold text-dark">Total
                                                    Tagihan</td>
                                                <td class="text-end py-3 fs-5 fw-bold text-primary pe-4" id="val-total">Rp 0
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded border border-light">
                                            <p class="mb-1 fw-bold text-dark small">Catatan Sistem / User:</p>
                                            <p class="mb-0 text-muted small fst-italic" id="inv-notes">Tidak ada catatan.
                                            </p>
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
                    <p class="text-muted mt-2">Memuat preview invoice...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const invoiceId = '{{ $id }}';
        const API_URL = '{{ url('api/invoice-api') }}';

        async function loadInvoiceDetail() {
            try {
                const directResponse = await fetch(`${API_URL}/${invoiceId}`);
                const directResult = await directResponse.json();

                if (directResult.success) {
                    renderInvoice(directResult.data);
                    document.getElementById('loading-state').style.display = 'none';
                    document.getElementById('main-content').style.display = 'flex';
                } else {
                    alert('Invoice tidak ditemukan');
                    window.location.href = '{{ route('sales.approval.overdue') }}';
                }
            } catch (e) {
                console.error(e);
                alert('Gagal memuat data');
            }
        }

        function renderInvoice(data) {
            document.getElementById('header-subtitle').textContent = `No. ${data.nomor_invoice}`;
            document.getElementById('inv-no').textContent = `#${data.nomor_invoice}`;
            document.getElementById('inv-date').textContent = formatDate(data.tgl_invoice);
            document.getElementById('inv-type').textContent = data.tipe_invoice;

            if (data.mitra) {
                document.getElementById('mitra-name').textContent = data.mitra.nama;
                document.getElementById('mitra-address').textContent = data.mitra.alamat || '-';
                document.getElementById('mitra-phone').textContent = data.mitra.no_telp || '-';
            }

            document.getElementById('inv-ref').textContent = data.ref_no || '-';
            document.getElementById('inv-due').textContent = formatDate(data.tgl_jatuh_tempo);

            // Action Header Logic
            const header = document.getElementById('action-header');
            
            if (data.status_pembayaran !== 'Overdue') {
                header.innerHTML = `
                    <button class="btn btn-outline-secondary fw-bold px-3 disabled" disabled>
                        Telah Diproses (${data.status_pembayaran} / ${data.status_dok})
                    </button>
                    <a href="{{ route('sales.approval.overdue') }}" class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="fa fa-arrow-left me-1"></i> Kembali
                    </a>
                `;
            } else {
                header.innerHTML = `
                    <button class="btn btn-danger fw-bold px-4 shadow-sm me-2" onclick="rejectInvoice()">
                        <i class="fa fa-times-circle me-1"></i> Tolak
                    </button>
                    <button class="btn btn-success fw-bold px-4 shadow-sm" onclick="approveInvoice()">
                        <i class="fa fa-check-circle me-1"></i> Setujui
                    </button>
                `;
            }

            const tbody = document.getElementById('items-body');
            tbody.innerHTML = '';
            let subTotal = 0;

            if (data.items) {
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
                    subTotal += parseFloat(item.total_harga_item);
                });
            }

            document.getElementById('val-subtotal').textContent = formatIDR(subTotal);
            document.getElementById('val-discount').textContent = `- ${formatIDR(data.diskon_tambahan_nilai || 0)}`;
            document.getElementById('val-tax').textContent = formatIDR(data.pajak_ppn || 0);
            document.getElementById('val-total').textContent = formatIDR(data.total_akhir);
            document.getElementById('inv-notes').textContent = data.keterangan || 'Tidak ada catatan.';
        }

        async function approveInvoice() {
            if (!await macConfirm('Konfirmasi Persetujuan', 'Apakah Anda yakin ingin MENYETUJUI perpanjangan invoice ini? Status akan menjadi Unpaid dan tempo bertambah 1 bulan.', {
                    confirmText: 'Setujui',
                    confirmType: 'success'
                })) return;

            try {
                const response = await fetch(`${API_URL}/${invoiceId}/approve-overdue`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    loadInvoiceDetail();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
            }
        }

        async function rejectInvoice() {
            if (!await macConfirm('Konfirmasi Penolakan', 'Apakah Anda yakin ingin MENOLAK persetujuan overdue invoice ini?', {
                    confirmText: 'Tolak',
                    confirmType: 'danger'
                })) return;

            try {
                const response = await fetch(`${API_URL}/${invoiceId}/reject-overdue`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    loadInvoiceDetail();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
            }
        }

        function formatIDR(val) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        document.addEventListener('DOMContentLoaded', loadInvoiceDetail);
    </script>
@endpush
