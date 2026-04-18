<div class="modal fade" id="detailInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="detailInvoiceTitle">Detail Invoice</h5>
                    <div class="small text-muted mt-1" id="detailInvoiceNumber"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Status & Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Pelanggan</label>
                            <div class="fw-bold text-dark fs-6" id="detailMitraName">-</div>
                            <div class="small text-muted" id="detailMitraAddress">-</div>
                        </div>
                        <div>
                            <label class="small text-muted text-uppercase fw-bold mb-1">Status Pembayaran</label>
                            <div id="detailPaymentStatus"></div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Tanggal Invoice</label>
                            <div class="fw-bold text-dark" id="detailDate">-</div>
                        </div>
                        <div>
                            <label class="small text-muted text-uppercase fw-bold mb-1">Jatuh Tempo</label>
                            <div class="fw-bold text-danger" id="detailDueDate">-</div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4 rounded border">
                    <table class="table table-borderless mb-0 align-middle">
                        <thead class="bg-light text-secondary small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-3 py-3">Produk</th>
                                <th class="text-center py-3">Qty</th>
                                <th class="text-end py-3">Harga</th>
                                <th class="text-end pe-3 py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody">
                            <!-- Items will be populated here -->
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end pe-3 py-2 small text-muted">Subtotal</td>
                                <td class="text-end pe-3 py-2 fw-bold" id="detailSubtotal">0</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end pe-3 py-2 small text-muted">Diskon Tambahan</td>
                                <td class="text-end pe-3 py-2 fw-bold text-danger" id="detailDiskon">- 0</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end pe-3 py-2 small text-muted">Biaya Lain-lain</td>
                                <td class="text-end pe-3 py-2 fw-bold text-success" id="detailBiayaLain">+ 0</td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="3" class="text-end pe-3 py-3 fw-bold text-dark">Total Akhir</td>
                                <td class="text-end pe-3 py-3 fw-bold text-primary fs-6" id="detailTotal">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Payment History (Optional) -->
                <div id="detailPaymentHistory" class="d-none">
                    <h6 class="fw-bold text-dark mb-3">Riwayat Pembayaran</h6>
                    <div class="table-responsive rounded border">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-3 py-2">Tanggal</th>
                                    <th class="py-2">Metode</th>
                                    <th class="text-end pe-3 py-2">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="detailPaymentBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-3">
                <div class="d-flex gap-2 w-100 justify-content-end">
                    <button type="button" class="btn btn-outline-danger" id="btnDetailDelete">
                        <i class="fa fa-trash me-1"></i> Hapus
                    </button>
                    <a href="#" class="btn btn-outline-secondary" id="btnDetailPrint">
                        <i class="fa fa-print me-1"></i> Print
                    </a>
                    <button type="button" class="btn btn-primary" id="btnDetailEdit">
                        <i class="fa fa-edit me-1"></i> Edit Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>