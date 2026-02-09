<div class="modal fade" id="detailDOModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-white" id="detail_do_number">Loading...</h5>
                    <span class="badge bg-light text-dark border mb-2" id="detail_do_status">-</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Section -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Informasi Pengiriman</h6>
                            <div class="mb-2">
                                <label class="small text-muted d-block">Tanggal Pengiriman</label>
                                <span class="fw-medium text-dark" id="detail_do_date">-</span>
                            </div>
                            <div class="mb-0">
                                <label class="small text-muted d-block">Catatan</label>
                                <span class="fw-medium text-dark fst-italic" id="detail_do_notes">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Armada & Pengemudi</h6>
                            <div class="mb-2">
                                <label class="small text-muted d-block">Armada</label>
                                <span class="fw-medium text-dark" id="detail_fleet_name">-</span>
                            </div>
                            <div class="mb-0">
                                <label class="small text-muted d-block">Pengemudi</label>
                                <span class="fw-medium text-dark" id="detail_driver_name">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoices Table -->
                <h6 class="text-uppercase text-muted small fw-bold mb-3">Daftar Invoice / Tujuan</h6>
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover mb-0" id="detailInvoiceTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3 small text-secondary">No.</th>
                                <th class="py-2 px-3 small text-secondary">No. Invoice</th>
                                <th class="py-2 px-3 small text-secondary">Mitra / Tujuan</th>
                                <th class="py-2 px-3 small text-secondary">Alamat</th>
                                <th class="py-2 px-3 small text-secondary text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <!-- JS populated -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light border fw-medium" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="btn_print_do" class="btn btn-dark fw-bold">
                    <i class="fa fa-print me-1"></i> Cetak DO
                </a>
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

<script>
    async function openDetailDOModal(id) {
        const modalEl = document.getElementById('detailDOModal');
        const modal = new bootstrap.Modal(modalEl);

        // Reset content
        document.getElementById('detail_do_number').innerText = 'Loading...';
        document.getElementById('detailInvoiceTable').querySelector('tbody').innerHTML =
            '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></td></tr>';

        modal.show();

        try {
            const res = await fetch(`{{ url('api/delivery-order-api') }}/${id}`);
            const json = await res.json();

            if (!json.success) throw new Error(json.message);

            const data = json.data;

            // Header
            // Status Badge Logic
            const statusMap = {
                'draft': {
                    label: 'Draft',
                    class: 'bg-secondary'
                },
                'scheduled': {
                    label: 'Scheduled',
                    class: 'bg-info text-dark'
                },
                'in_transit': {
                    label: 'In Transit',
                    class: 'bg-primary'
                },
                'delivering': {
                    label: 'Delivering',
                    class: 'bg-warning text-dark'
                },
                'partially_delivered': {
                    label: 'Partially Delivered',
                    class: 'text-white',
                    style: 'background-color: #6f42c1'
                }, // Purple
                'completed': {
                    label: 'Completed',
                    class: 'bg-success'
                },
                'returned': {
                    label: 'Returned',
                    class: 'bg-danger'
                },
                'cancelled': {
                    label: 'Cancelled',
                    class: 'bg-dark'
                }
            };

            const st = data.status || 'draft';
            const map = statusMap[st] || {
                label: st,
                class: 'bg-secondary'
            };

            const badge = document.getElementById('detail_do_status');
            badge.className = `badge ${map.class} border mb-2`;
            if (map.style) {
                badge.setAttribute('style', map.style);
            } else {
                badge.removeAttribute('style');
            }
            badge.innerText = map.label;

            // Header
            document.getElementById('detail_do_number').innerText = data.delivery_order_number;

            const btnPrint = document.getElementById('btn_print_do');
            btnPrint.href = 'javascript:void(0)';
            btnPrint.onclick = () => openPrintPreview(data.id);

            // Info
            const date = new Date(data.delivery_date);
            document.getElementById('detail_do_date').innerText = date.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('detail_do_notes').innerText = data.notes || '-';

            // Fleet
            let fleetName = '-';
            let driverName = '-'; // Assuming driver name is stored or related
            if (data.fleets && data.fleets.length > 0) {
                const f = data.fleets[0];
                if (f.fleet) {
                    fleetName = `${f.fleet.fleet_name} (${f.fleet.license_plate})`;
                }
                // Check for driver relation or field
                if (f.driver) {
                    driverName = f.driver.name;
                } else if (f.driver_name) {
                    // Fallback if pivot stores driver_name string
                    driverName = f.driver_name;
                }
            }
            document.getElementById('detail_fleet_name').innerText = fleetName;
            document.getElementById('detail_driver_name').innerText = driverName;

            // Invoices
            const tbody = document.getElementById('detailInvoiceTable').querySelector('tbody');
            tbody.innerHTML = '';

            if (data.invoices && data.invoices.length > 0) {
                data.invoices.forEach((item, idx) => {
                    const inv = item.invoice;
                    const mitraName = inv && inv.mitra ? inv.mitra.nama : 'Umum';
                    const address = inv && inv.mitra ? inv.mitra.alamat : '-';
                    const invNo = inv ? inv.nomor_invoice : '-';
                    const status = item.delivery_status || 'Pending';

                    let badgeClass = 'bg-secondary';
                    if (status === 'Delivered') badgeClass = 'bg-success';
                    if (status === 'Failed') badgeClass = 'bg-danger';

                    const tr = `
                        <tr>
                            <td class="px-3 py-2 text-muted small">${idx + 1}</td>
                            <td class="px-3 py-2 fw-medium">${invNo}</td>
                            <td class="px-3 py-2">${mitraName}</td>
                            <td class="px-3 py-2 text-muted small text-truncate" style="max-width: 200px;">${address}</td>
                            <td class="px-3 py-2 text-end">
                                <span class="badge ${badgeClass} bg-opacity-10 text-${badgeClass.replace('bg-', '')} border border-${badgeClass.replace('bg-', '')}">${status}</span>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', tr);
                });
            } else {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="text-center py-3 text-muted fst-italic">Tidak ada invoice terlampir.</td></tr>';
            }

        } catch (e) {
            console.error(e);
            alert('Gagal memuat detail: ' + e.message);
            modal.hide();
        }
    }

    function openPrintPreview(id) {
        const printUrl = `{{ url('delivery-order/print') }}/${id}`;

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