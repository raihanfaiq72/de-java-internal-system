@extends('Layout.main')

@push('css')
<style>
    .driver-completed-card { border-radius: 16px; background: #fff; transition: transform .12s ease, box-shadow .12s ease; }
    .driver-completed-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(16,24,40,.08); }
    .driver-completed-card .badge { border-radius: 999px; font-size: 11px; padding: .35rem .6rem; }
    .driver-completed-card .card-body { padding: 1rem 1.25rem; }
    .driver-completed-card h6 { margin-bottom: .25rem; }
    .driver-section-title { display:inline-flex; align-items:center; gap:.5rem; padding:.35rem .6rem; border-radius:999px; background:#f8f9fa; color:#1f2937; font-weight:600; }
    .do-detail-label { color:#6c757d; font-size:12px; text-transform:uppercase; letter-spacing:.02em; }
    .do-detail-value { font-weight:600; font-size:14px; }
    #doDetailStopsList .list-group-item { display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; }
    #doDetailStopsList .list-group-item .left { max-width:72%; }
    #doDetailStopsList .list-group-item .right { text-align:right; min-width:28%; }
    #doDetailStopsList .badge { border-radius:999px; font-size:11px; padding:.25rem .5rem; }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Tugas Pengiriman Saya</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Driver App</li>
                        </ol>
                    </div>
                    <p class="text-muted mb-3 small">Kelola pengiriman dan rute perjalanan Anda</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div id="driverNoJobs" class="alert alert-info d-none">Belum ada tugas pengiriman yang diberikan.</div>

                    <h5 class="mb-3">Tugas Aktif</h5>
                    <div class="row" id="driverActiveJobsRow"></div>

                    <div class="mt-4">
                        <span class="driver-section-title"><i class="iconoir-clock-solid"></i> Riwayat Selesai (10 Terakhir)</span>
                        <div class="row mt-3">
                            <div class="text-muted small d-none" id="driverNoCompleted">Belum ada riwayat selesai.</div>
                            <div class="row" id="driverCompletedJobsRow"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Detail DO -->
<div class="modal fade" id="driverDoDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold mb-0">Detail Delivery Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="do-detail-label">Nomor DO</div>
                            <div class="do-detail-value" id="doDetailNumber">-</div>
                        </div>
                        <div class="mb-2">
                            <div class="do-detail-label">Tanggal</div>
                            <div class="do-detail-value" id="doDetailDate">-</div>
                        </div>
                        <div class="mb-2">
                            <div class="do-detail-label">Kendaraan</div>
                            <div class="do-detail-value" id="doDetailFleet">-</div>
                        </div>
                        <div class="mb-2">
                            <div class="do-detail-label">Driver</div>
                            <div class="do-detail-value" id="doDetailDriver">-</div>
                        </div>
                        <div class="mb-2">
                            <div class="do-detail-label">Status</div>
                            <div class="do-detail-value" id="doDetailStatus">-</div>
                        </div>
                        <div class="mb-2">
                            <div class="do-detail-label">Ringkasan Tujuan</div>
                            <div class="do-detail-value" id="doDetailStops">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-2">
                            <div class="small text-muted mb-2">Tujuan</div>
                            <div id="doDetailStopsList" class="list-group list-group-flush"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="doDetailOpenPage" class="btn btn-primary" href="#" target="">Buka Halaman Detail</a>
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const DO_API_BASE = "{{ url('api/delivery-order-api') }}";
    const FLEET_API_INDEX = "{{ route('delivery-order-fleet-api.index') }}";
    const openDetail = async (doId) => {
        try {
            const res = await fetch(`${DO_API_BASE}/${doId}`);
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Gagal memuat detail');
            const d = json.data;
            document.getElementById('doDetailNumber').textContent = d.delivery_order_number || '-';
            document.getElementById('doDetailDate').textContent = d.delivery_date ? new Date(d.delivery_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
            const f = (d.fleets && d.fleets.length) ? d.fleets[0] : null;
            document.getElementById('doDetailFleet').textContent = f && f.fleet ? `${f.fleet.fleet_name} (${f.fleet.license_plate})` : '-';
            document.getElementById('doDetailDriver').textContent = f && f.driver ? f.driver.name : '-';
            document.getElementById('doDetailStatus').textContent = d.status || '-';
            const totalStops = d.invoices ? d.invoices.length : 0;
            const delivered = d.invoices ? d.invoices.filter(i => i.delivery_status === 'delivered').length : 0;
            document.getElementById('doDetailStops').textContent = `${totalStops} tujuan • ${delivered} selesai`;
            const listEl = document.getElementById('doDetailStopsList');
            listEl.innerHTML = '';
            if (d.invoices && d.invoices.length) {
                d.invoices.forEach((item, idx) => {
                    const inv = item.invoice || {};
                    const mitra = inv.mitra || {};
                    const status = item.delivery_status || 'pending';
                    const badge = status === 'delivered' ? 'bg-success' : 'bg-danger';
                    const timeStr = item.arrived_at ? new Date(item.arrived_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                    const row = document.createElement('div');
                    row.className = 'list-group-item';
                    row.innerHTML = `
                        <div class="left">
                            <div class="fw-bold">${idx + 1}. ${mitra.nama || 'Tujuan'}</div>
                            <div class="text-muted small">${mitra.alamat || '-'}</div>
                        </div>
                        <div class="right">
                            <span class="badge ${badge} text-capitalize">${status}</span>
                            <div class="small text-muted">${timeStr}</div>
                        </div>
                    `;
                    listEl.appendChild(row);
                });
            }
            document.getElementById('doDetailOpenPage').href = "{{ route('driver.delivery.show', '__ID__') }}".replace('__ID__', doId);
            const Modal = window.bootstrap?.Modal;
            if (Modal) {
                new Modal(document.getElementById('driverDoDetailModal')).show();
            } else {
                document.getElementById('driverDoDetailModal').classList.add('show');
            }
        } catch (e) {
            alert(e.message || 'Gagal membuka detail DO');
        }
    };

    function formatDate(v) {
        if (!v) return '-';
        const d = new Date(v);
        if (Number.isNaN(d.getTime())) return '-';
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function renderActiveCard(item) {
        const doNum = item.delivery_order?.delivery_order_number || '-';
        const dateStr = item.delivery_order?.delivery_date ? formatDate(item.delivery_order.delivery_date) : '-';
        const fleetName = item.fleet?.fleet_name || '-';
        const plate = item.fleet?.license_plate || item.fleet?.plate_number || '-';
        const status = (item.status || '').replace(/_/g, ' ');
        const badge = item.status === 'assigned' ? 'warning' : 'info';
        const href = "{{ route('driver.delivery.show', '__ID__') }}".replace('__ID__', item.delivery_order_id);
        return `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title text-primary">${escapeHtml(doNum)}</h5>
                            <span class="badge bg-${badge}">${escapeHtml(status.charAt(0).toUpperCase() + status.slice(1))}</span>
                        </div>
                        <p class="card-text text-muted mb-1">
                            <i class="iconoir-calendar me-1"></i> ${escapeHtml(dateStr)}
                        </p>
                        <p class="card-text text-muted mb-3">
                            <i class="iconoir-truck me-1"></i> ${escapeHtml(fleetName)} (${escapeHtml(plate)})
                        </p>
                        <a href="${href}" class="btn btn-primary w-100">
                            <i class="iconoir-map me-1"></i> Lihat Rute & Tugas
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    function renderCompletedCard(item) {
        const doNum = item.delivery_order?.delivery_order_number || '-';
        const dateStr = item.delivery_order?.delivery_date ? formatDate(item.delivery_order.delivery_date) : '-';
        const fleetName = item.fleet?.fleet_name || '-';
        const plate = item.fleet?.license_plate || item.fleet?.plate_number || '-';
        const doId = item.delivery_order_id;
        return `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 shadow-sm border-0 driver-completed-card" data-do-id="${doId}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-0 fw-bold">${escapeHtml(doNum)}</h6>
                                <div class="text-muted small">
                                    <i class="iconoir-calendar me-1"></i> ${escapeHtml(dateStr)}
                                </div>
                                <div class="text-muted small">
                                    <i class="iconoir-truck me-1"></i> ${escapeHtml(fleetName)} (${escapeHtml(plate)})
                                </div>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary driver-completed-detail" data-do-id="${doId}">Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async function fetchFleet(status, perPage) {
        const url = new URL(FLEET_API_INDEX, window.location.origin);
        url.searchParams.set('mine', '1');
        url.searchParams.set('per_page', String(perPage));
        if (status) url.searchParams.set('status', status);
        const res = await fetch(url);
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Gagal memuat data');
        return json.data?.data || [];
    }

    async function loadDriverJobs() {
        const [assigned, inTransit, completed] = await Promise.all([
            fetchFleet('assigned', 50),
            fetchFleet('in_transit', 50),
            fetchFleet('completed', 10),
        ]);

        const active = [...assigned, ...inTransit];
        const activeRow = document.getElementById('driverActiveJobsRow');
        const completedRow = document.getElementById('driverCompletedJobsRow');
        activeRow.innerHTML = active.length ? active.map(renderActiveCard).join('') : '<div class="text-muted small">Belum ada tugas aktif.</div>';
        completedRow.innerHTML = completed.length ? completed.map(renderCompletedCard).join('') : '';
        document.getElementById('driverNoCompleted').classList.toggle('d-none', completed.length > 0);
        document.getElementById('driverNoJobs').classList.toggle('d-none', (active.length + completed.length) > 0);
    }

    document.addEventListener('click', (e) => {
        const detailBtn = e.target.closest('.driver-completed-detail');
        if (detailBtn) {
            e.preventDefault();
            e.stopPropagation();
            openDetail(detailBtn.dataset.doId);
            return;
        }
        const card = e.target.closest('.driver-completed-card');
        if (card) {
            openDetail(card.dataset.doId);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        loadDriverJobs().catch(e => console.error(e));
    });
</script>
@endpush
@endsection
