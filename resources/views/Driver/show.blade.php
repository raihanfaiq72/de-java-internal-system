@extends('Layout.main')

@section('styles')
<link rel="stylesheet" href="/assets/libs/leaflet/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #map { height: 400px; width: 100%; border-radius: 8px; }
    .stop-card { border-left: 4px solid #dee2e6; transition: all 0.2s; }
    .stop-card.active { border-left-color: #0d6efd; background-color: #f8f9fa; }
    .stop-card.completed { border-left-color: #198754; opacity: 0.9; cursor: pointer; }
</style>
@endsection

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="page-title fw-bold">Detail Pengiriman</h4>
                            <p class="text-muted mb-0 small">No. DO: <span id="doNumber">-</span></p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge fs-6" id="fleetStatusBadge">-</span>
                            <a href="{{ route('driver.delivery.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="iconoir-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <small class="text-muted d-block">Armada</small>
                                    <div class="fw-bold text-truncate" id="fleetName">-</div>
                                    <small class="text-secondary" id="fleetPlate">-</small>
                                </div>
                                <div class="col-3 text-center border-start">
                                    <small class="text-muted d-block">Odo Awal</small>
                                    <div class="fw-bold" id="fleetOdoStart">0</div>
                                    <small class="text-secondary">KM</small>
                                </div>
                                <div class="col-3 text-center border-start">
                                    <small class="text-muted d-block">Uang Jalan</small>
                                    <div class="fw-bold text-success" id="fleetCashAmount">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-2">
                            <div id="map"></div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div id="tripAssigned" class="d-none">
                                <button id="btn-start-trip" class="btn btn-primary w-100 btn-lg">
                                    <i class="iconoir-play me-2"></i> Mulai Perjalanan
                                </button>
                            </div>
                            <div id="tripInTransit" class="d-none">
                                <div class="alert alert-info d-flex align-items-center mb-0">
                                    <div class="spinner-grow spinner-grow-sm text-primary me-3" role="status"></div>
                                    <div>
                                        <strong>Sedang dalam perjalanan</strong><br>
                                        Lokasi Anda sedang dilacak secara realtime.
                                    </div>
                                </div>
                            </div>
                            <div id="tripCompleted" class="d-none">
                                <div class="alert alert-success mb-0">
                                    <i class="iconoir-check-circle me-2"></i> Perjalanan Selesai
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h5 class="mb-3">Daftar Tujuan</h5>
                    <div class="list-group" id="stopsList"></div>

                    <button id="btn-finish-trip" class="btn btn-success w-100 btn-lg mt-3 d-none">
                        <i class="iconoir-home me-2"></i> Kembali ke Kantor & Selesai
                    </button>
                </div>
            </div>
            <div class="mt-4">
                <span class="badge bg-light text-dark"><i class="iconoir-time"></i> Riwayat Selesai (10 Terakhir)</span>
                <div class="list-group mt-3" id="historyList"></div>
                <div class="text-muted small mt-2 d-none" id="historyEmpty">Belum ada riwayat selesai.</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="proofModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Kedatangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="proofForm">
                    <input type="hidden" id="proofInvoiceId">
                    <input type="hidden" id="proofLat">
                    <input type="hidden" id="proofLng">
                    <div class="mb-3">
                        <label class="form-label">Lokasi Tujuan</label>
                        <input type="text" class="form-control" id="proofMitraName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jepret Foto Bukti <span class="text-danger">*</span></label>
                        <div class="border rounded p-2 position-relative text-center">
                            <div class="position-relative">
                                <video id="cameraStream" autoplay playsinline style="width:100%; max-height:240px; background:#000; border-radius: 4px;"></video>
                                <img id="capturedResult" class="d-none" style="width:100%; max-height:240px; object-fit:contain; border-radius: 4px;">
                                <canvas id="captureCanvas" class="d-none"></canvas>
                                <div id="shutterFlash" class="d-none" style="position:absolute; inset:0; background:#fff; opacity:0.7;"></div>
                            </div>
                            
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-primary" id="btn-capture" disabled>
                                    <i class="iconoir-camera me-1"></i> Jepret Foto
                                </button>
                                <button type="button" class="btn btn-warning d-none" id="btn-retake">
                                    <i class="iconoir-refresh-double me-1"></i> Foto Ulang
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2" id="cam-hint">Gunakan kamera belakang jika tersedia.</small>
                        </div>
                        <input type="file" class="form-control d-none" id="proofPhoto" accept="image/*">
                        <div class="form-text">Ambil foto penerima atau lokasi pengiriman.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="proofNotes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-submit-proof">Kirim Bukti</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Foto Bukti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" style="max-width:100%; border-radius:8px;">
            </div>
        </div>
    </div>
</div>
<!-- End Preview Modal (Removed) -->

<!-- Modal Detail Bukti -->
<div class="modal fade" id="proofDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Bukti Kedatangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <img id="proofDetailImage" src="" alt="Bukti" style="max-width:100%; border-radius:8px;">
                        <div id="proofDetailImageEmpty" class="text-muted small mt-2 d-none">Foto bukti tidak tersedia.</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2"><strong>Tujuan:</strong> <span id="proofDetailName">-</span></div>
                        <div class="mb-2"><strong>Alamat:</strong> <span id="proofDetailAddress">-</span></div>
                        <div class="mb-2"><strong>Koordinat:</strong> <span id="proofDetailCoord">-</span></div>
                        <div class="mb-2"><strong>Diterima:</strong> <span id="proofDetailArrivedAt">-</span></div>
                        <div class="mb-2"><strong>Catatan:</strong> <span id="proofDetailNotes">-</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesaikan Trip -->
<div class="modal fade" id="finishTripModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan Perjalanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="finishTripForm">
                    <input type="hidden" id="finishLat">
                    <input type="hidden" id="finishLng">
                    <div class="mb-3">
                        <label class="form-label">Odometer Awal</label>
                        <input type="text" class="form-control" id="finishOdoStart" value="0" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Odometer Akhir (KM) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" class="form-control" id="finishOdoEnd" required placeholder="Contoh: 12500.5">
                        <div class="form-text">Masukkan angka odometer saat tiba di kantor.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sisa Bensin (Liter) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" class="form-control" id="finishGasLeftover" required placeholder="Contoh: 5.5">
                        <div class="form-text">Perkiraan sisa bensin di tangki untuk trip berikutnya.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-submit-finish">Selesaikan & Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const deliveryId = {{ $deliveryOrderId }};
const CSRF_TOKEN = "{{ csrf_token() }}";
const DRIVER_SHOW_API = "{{ route('driver-delivery-api.show', ['id' => '__ID__']) }}";
const DRIVER_START_API = "{{ route('driver-delivery-api.start', ['id' => '__ID__']) }}";
const DRIVER_LOCATION_API = "{{ route('driver-delivery-api.location', ['id' => '__ID__']) }}";
const DRIVER_ARRIVE_API = "{{ route('driver-delivery-api.arrive', ['id' => '__ID__', 'invoiceId' => '__INV__']) }}";
const DRIVER_PROOF_API = "{{ route('driver-delivery-api.proof', ['id' => '__ID__', 'invoiceId' => '__INV__']) }}";
const DRIVER_FINISH_API = "{{ route('driver-delivery-api.finish', ['id' => '__ID__']) }}";
let doData = null;
let fleetData = null;
let fleetStatus = '';
let stops = [];
let map, userMarker, watchId, routingControl, routeCalculated = false, routingLibLoaded = false;
function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function formatTime(v) {
    if (!v) return '-';
    const d = new Date(v);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
function formatDateTime(v) {
    if (!v) return '-';
    const d = new Date(v);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
function setFleetBadge(status) {
    const el = document.getElementById('fleetStatusBadge');
    el.classList.remove('bg-warning', 'bg-info', 'bg-success', 'bg-secondary');
    const s = (status || '').toLowerCase();
    const cls = s === 'assigned' ? 'bg-warning' : (s === 'in_transit' ? 'bg-info' : (s === 'completed' ? 'bg-success' : 'bg-secondary'));
    el.classList.add(cls);
    el.textContent = (status || '-').replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase());
}
function buildStops() {
    const inv = Array.isArray(doData?.invoices) ? doData.invoices : [];
    const ordered = inv.slice().sort((a, b) => (a.delivery_sequence || 0) - (b.delivery_sequence || 0));
    stops = ordered.map((i) => {
        const m = i.invoice?.mitra || {};
        return {
            invoiceId: i.invoice_id,
            name: m.nama || 'Tujuan',
            address: m.alamat || '-',
            lat: m.latitude,
            lng: m.longitude,
            status: i.delivery_status || 'pending',
            arrivedAt: i.arrived_at || null,
            seq: i.delivery_sequence || 0
        };
    });
}
function getNextInvoiceId() {
    if (fleetStatus !== 'in_transit') return null;
    const pending = stops.filter(s => s.status === 'pending').sort((a, b) => a.seq - b.seq);
    return pending.length ? pending[0].invoiceId : null;
}
function renderStops() {
    const container = document.getElementById('stopsList');
    container.innerHTML = '';
    const nextId = getNextInvoiceId();
    stops.forEach((s, idx) => {
        const delivered = s.status === 'delivered';
        const isNext = !delivered && nextId && String(nextId) === String(s.invoiceId);
        const classes = delivered ? 'completed' : (isNext ? 'active' : '');
        const badgeCls = delivered ? 'success' : 'secondary';
        const card = document.createElement('div');
        card.className = `list-group-item stop-card p-3 mb-2 shadow-sm border-0 ${classes}`;
        if (delivered) {
            card.dataset.invoiceId = s.invoiceId;
            card.dataset.name = s.name;
            card.dataset.address = s.address;
            card.dataset.lat = s.lat || '';
            card.dataset.lng = s.lng || '';
            card.dataset.arrivedAt = s.arrivedAt ? formatDateTime(s.arrivedAt) : '';
        }
        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${idx + 1}. ${escapeHtml(s.name)}</h6>
                    <p class="mb-1 small text-muted">${escapeHtml(s.address)}</p>
                    <span class="badge bg-${badgeCls}">${escapeHtml(s.status.charAt(0).toUpperCase() + s.status.slice(1))}</span>
                </div>
                <div class="text-end">
                    ${isNext ? `<button class="btn btn-sm btn-success btn-arrive" data-id="${s.invoiceId}" data-lat="${s.lat || ''}" data-lng="${s.lng || ''}" data-name="${escapeHtml(s.name)}">Sampai</button>` : ''}
                    ${delivered ? `<button class="btn btn-sm btn-outline-primary btn-detail-proof">Detail</button>` : ''}
                </div>
            </div>
            ${delivered ? `<div class="mt-2 small text-success"><i class="iconoir-check"></i> Diterima: ${formatTime(s.arrivedAt)}</div>` : ''}
        `;
        container.appendChild(card);
    });
    const allDelivered = stops.length > 0 && stops.every(s => s.status === 'delivered');
    document.getElementById('btn-finish-trip').classList.toggle('d-none', !(fleetStatus === 'in_transit' && allDelivered));
}
function renderHistory() {
    const list = document.getElementById('historyList');
    const empty = document.getElementById('historyEmpty');
    const delivered = stops.filter(s => s.status === 'delivered').sort((a, b) => new Date(b.arrivedAt || 0) - new Date(a.arrivedAt || 0)).slice(0, 10);
    list.innerHTML = '';
    empty.classList.toggle('d-none', delivered.length > 0);
    delivered.forEach((s) => {
        const card = document.createElement('div');
        card.className = 'list-group-item stop-card completed p-3 mb-2 shadow-sm border-0';
        card.dataset.invoiceId = s.invoiceId;
        card.dataset.name = s.name;
        card.dataset.address = s.address;
        card.dataset.lat = s.lat || '';
        card.dataset.lng = s.lng || '';
        card.dataset.arrivedAt = s.arrivedAt ? formatDateTime(s.arrivedAt) : '';
        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${escapeHtml(s.name)}</h6>
                    <p class="mb-1 small text-muted">${escapeHtml(s.address)}</p>
                    <span class="badge bg-success">Delivered</span>
                    <div class="mt-1 small text-success"><i class="iconoir-check"></i> Diterima: ${formatTime(s.arrivedAt)}</div>
                </div>
                <div class="text-end">
                    <button class="btn btn-sm btn-outline-primary btn-detail-proof">Detail</button>
                </div>
            </div>
        `;
        list.appendChild(card);
    });
}
async function loadDeliveryData() {
    const url = DRIVER_SHOW_API.replace('__ID__', deliveryId);
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'Gagal memuat data');
    doData = json.data?.do;
    fleetData = json.data?.fleet;
    fleetStatus = fleetData?.status || '';
    document.getElementById('doNumber').textContent = doData?.delivery_order_number || '-';
    setFleetBadge(fleetStatus);
    document.getElementById('fleetName').textContent = fleetData?.fleet?.fleet_name || '-';
    document.getElementById('fleetPlate').textContent = fleetData?.fleet?.license_plate || '-';
    document.getElementById('fleetOdoStart').textContent = Number(fleetData?.odo_start || 0).toLocaleString('id-ID');
    document.getElementById('fleetCashAmount').textContent = Number(fleetData?.cash_amount || 0).toLocaleString('id-ID');
    document.getElementById('finishOdoStart').value = Number(fleetData?.odo_start || 0).toLocaleString('id-ID');
    document.getElementById('tripAssigned').classList.toggle('d-none', fleetStatus !== 'assigned');
    document.getElementById('tripInTransit').classList.toggle('d-none', fleetStatus !== 'in_transit');
    document.getElementById('tripCompleted').classList.toggle('d-none', fleetStatus !== 'completed');
    buildStops();
    renderStops();
    renderHistory();
}
function fitStopsBounds() {
    const pts = stops.filter(s => s.lat && s.lng).map(s => [parseFloat(s.lat), parseFloat(s.lng)]);
    if (pts.length > 0) {
        const b = L.latLngBounds(pts);
        map.fitBounds(b, { padding: [40, 40] });
    }
}
async function initMap() {
    await ensureLeafletReady();
    setDefaultMarkerAssets();
    const el = document.getElementById('map');
    if (!el) return;
    const firstStop = stops.find(s => s.lat && s.lng);
    const center = firstStop ? [parseFloat(firstStop.lat), parseFloat(firstStop.lng)] : [-6.2, 106.816666];
    map = L.map(el).setView(center, 13);
    addBestTileLayer(map);
    stops.forEach((s, i) => {
        if (s.lat && s.lng) {
            const col = s.status === 'delivered' ? 'green' : 'red';
            L.circleMarker([parseFloat(s.lat), parseFloat(s.lng)], { color: col, fillColor: col, fillOpacity: 0.5, radius: 8 })
                .addTo(map)
                .bindPopup(`${i + 1}. ${s.name} (${s.status})`);
        }
    });
    fitStopsBounds();
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(p => {
            const { latitude, longitude } = p.coords;
            updateUserMarker(latitude, longitude);
        }, () => {
            calculateRoute(center[0], center[1]);
        });
    } else {
        calculateRoute(center[0], center[1]);
    }
}
function updateUserMarker(lat, lng) {
    if (!userMarker) {
        userMarker = L.marker([lat, lng], {
            icon: L.divIcon({ className: 'user-marker', html: '<div style="background-color: blue; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>', iconSize: [16, 16] })
        }).addTo(map);
    } else {
        userMarker.setLatLng([lat, lng]);
    }
    const hasPending = stops.some(s => s.status === 'pending' && s.lat && s.lng);
    if (!routeCalculated && hasPending) {
        calculateRoute(lat, lng);
        routeCalculated = true;
    }
}
async function calculateRoute(slat, slng) {
    const pending = stops.filter(s => s.status === 'pending' && s.lat && s.lng);
    if (pending.length === 0) return;
    await ensureRoutingReady();
    if (routingControl) map.removeControl(routingControl);
    const w = [L.latLng(slat, slng), ...pending.map(s => L.latLng(parseFloat(s.lat), parseFloat(s.lng)))];
    if (typeof L.Routing !== 'undefined') {
        routingControl = L.Routing.control({
            waypoints: w,
            routeWhileDragging: false,
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: true,
            showAlternatives: false,
            lineOptions: { styles: [{ color: 'blue', opacity: 0.6, weight: 4 }] },
            createMarker: function() { return null; },
            show: false
        }).addTo(map);
    }
}
let lastUpdate = 0;
function sendLocationUpdate(lat, lng) {
    const now = Date.now();
    if (now - lastUpdate < 10000) return;
    lastUpdate = now;
    fetch(DRIVER_LOCATION_API.replace('__ID__', deliveryId), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: new URLSearchParams({ latitude: lat, longitude: lng }).toString()
    }).catch(() => {});
}
document.getElementById('btn-start-trip')?.addEventListener('click', async function() {
    if (!await macConfirm('Mulai Perjalanan', 'Mulai perjalanan pengiriman sekarang?', { confirmText: 'Mulai', confirmType: 'success', cancelText: 'Batal' })) return;
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async pos => {
            const { latitude, longitude } = pos.coords;
            try {
                const res = await fetch(DRIVER_START_API.replace('__ID__', deliveryId), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: new URLSearchParams({ latitude, longitude }).toString()
                });
                if (res.ok) location.reload(); else { const d = await res.json().catch(() => ({})); alert('Gagal memulai perjalanan: ' + (d.message || res.statusText)); }
            } catch (e) { alert('Gagal memulai perjalanan: ' + (e.message || 'Error')); }
        }, () => alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.'));
    } else { alert('Browser tidak mendukung Geolocation.'); }
});
function startTrackingIfNeeded() {
    if (watchId) return;
    if (fleetStatus !== 'in_transit') return;
    if (!navigator.geolocation) return;
    watchId = navigator.geolocation.watchPosition(p => {
        const { latitude, longitude } = p.coords;
        updateUserMarker(latitude, longitude);
        sendLocationUpdate(latitude, longitude);
    }, null, { enableHighAccuracy: true });
}
document.addEventListener('click', async (e) => {
    const arriveBtn = e.target.closest('.btn-arrive');
    if (arriveBtn) {
        e.preventDefault();
        const id = arriveBtn.dataset.id;
        const name = arriveBtn.dataset.name;
        const lat = parseFloat(arriveBtn.dataset.lat);
        const lng = parseFloat(arriveBtn.dataset.lng);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async p => {
                const clat = p.coords.latitude;
                const clng = p.coords.longitude;
                const dist = getDistanceFromLatLonInKm(clat, clng, lat, lng);
                if (dist > 0.5) {
                    const ok = await macConfirm('Peringatan Jarak', `Peringatan: Anda terdeteksi berada ${dist.toFixed(2)} km dari lokasi tujuan. Apakah Anda yakin sudah sampai?`, { confirmText: 'Ya, Saya Sudah Sampai', confirmType: 'success', cancelText: 'Batal' });
                    if (!ok) return;
                }
                document.getElementById('proofInvoiceId').value = id;
                document.getElementById('proofMitraName').value = name;
                document.getElementById('proofLat').value = clat;
                document.getElementById('proofLng').value = clng;
                const Modal = window.bootstrap?.Modal;
                if (Modal) new Modal(document.getElementById('proofModal')).show(); else document.getElementById('proofModal').classList.add('show');
                startCamera();
            });
        }
        return;
    }
    const detailBtn = e.target.closest('.btn-detail-proof');
    if (detailBtn) {
        e.preventDefault();
        e.stopPropagation();
        const card = detailBtn.closest('.stop-card');
        if (!card || !card.dataset.invoiceId) return;
        openProofDetailModal({
            invoiceId: card.dataset.invoiceId,
            name: card.dataset.name,
            address: card.dataset.address,
            lat: card.dataset.lat,
            lng: card.dataset.lng,
            arrivedAt: card.dataset.arrivedAt
        });
        return;
    }
    const completedCard = e.target.closest('.stop-card.completed');
    if (completedCard && completedCard.dataset.invoiceId) {
        openProofDetailModal({
            invoiceId: completedCard.dataset.invoiceId,
            name: completedCard.dataset.name,
            address: completedCard.dataset.address,
            lat: completedCard.dataset.lat,
            lng: completedCard.dataset.lng,
            arrivedAt: completedCard.dataset.arrivedAt
        });
    }
});
function getDistanceFromLatLonInKm(a1, o1, a2, o2) {
    const R = 6371, dA = (a2 - a1) * Math.PI / 180, dO = (o2 - o1) * Math.PI / 180;
    const A1 = a1 * Math.PI / 180, A2 = a2 * Math.PI / 180;
    const a = Math.sin(dA/2)**2 + Math.cos(A1) * Math.cos(A2) * Math.sin(dO/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
let streamRef = null, capturedBlob = null;
async function startCamera() {
    try {
        if (streamRef) return;
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        streamRef = stream;
        const v = document.getElementById('cameraStream');
        v.srcObject = stream;
        v.onloadedmetadata = () => { 
            document.getElementById('btn-capture').disabled = false;
            v.play(); // ensure play
        };
    } catch {
        document.getElementById('proofPhoto').classList.remove('d-none');
        document.getElementById('btn-capture').classList.add('d-none');
        document.getElementById('cam-hint').innerText = 'Kamera tidak tersedia. Silakan upload foto manual.';
    }
}
document.getElementById('btn-capture').addEventListener('click', function() {
    const v = document.getElementById('cameraStream');
    const c = document.getElementById('captureCanvas');
    const img = document.getElementById('capturedResult');
    
    if (!v.videoWidth || !v.videoHeight) { alert('Kamera belum siap.'); return; }
    
    const w = v.videoWidth, h = v.videoHeight;
    c.width = w; c.height = h;
    const ctx = c.getContext('2d');
    
    // Flash effect
    const f = document.getElementById('shutterFlash'); 
    f.classList.remove('d-none'); 
    setTimeout(() => f.classList.add('d-none'), 120);
    
    setTimeout(() => {
        // Draw video to canvas
        ctx.drawImage(v, 0, 0, w, h);
        applyWatermark(ctx, w, h);
        
        getCanvasBlob(c, function(blob) {
            capturedBlob = blob;
            const url = URL.createObjectURL(blob);
            
            // Show result
            const img = document.getElementById('capturedResult');
            img.src = url;
            img.classList.remove('d-none');
            v.classList.add('d-none');
            
            // Toggle buttons
            document.getElementById('btn-capture').classList.add('d-none');
            document.getElementById('btn-retake').classList.remove('d-none');
        });
    }, 50);
});

document.getElementById('btn-retake').addEventListener('click', function() {
    // Reset state
    capturedBlob = null;
    document.getElementById('capturedResult').classList.add('d-none');
    document.getElementById('cameraStream').classList.remove('d-none');
    
    document.getElementById('btn-retake').classList.add('d-none');
    document.getElementById('btn-capture').classList.remove('d-none');
});

document.querySelectorAll('.stop-card.completed').forEach(el => {
    el.addEventListener('click', function() {
        const d = {
            invoiceId: this.dataset.invoiceId,
            name: this.dataset.name,
            address: this.dataset.address,
            lat: this.dataset.lat,
            lng: this.dataset.lng,
            arrivedAt: this.dataset.arrivedAt
        };
        openProofDetailModal(d);
    });
});
document.querySelectorAll('.btn-detail-proof').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const card = this.closest('.stop-card');
        if (!card) return;
        const d = {
            invoiceId: card.dataset.invoiceId,
            name: card.dataset.name,
            address: card.dataset.address,
            lat: card.dataset.lat,
            lng: card.dataset.lng,
            arrivedAt: card.dataset.arrivedAt
        };
        openProofDetailModal(d);
    });
});
async function openProofDetailModal(d) {
    document.getElementById('proofDetailName').textContent = d.name || '-';
    document.getElementById('proofDetailAddress').textContent = d.address || '-';
    document.getElementById('proofDetailCoord').textContent = (d.lat && d.lng) ? `${d.lat}, ${d.lng}` : '-';
    document.getElementById('proofDetailArrivedAt').textContent = d.arrivedAt || '-';
    document.getElementById('proofDetailNotes').textContent = '-';
    const img = document.getElementById('proofDetailImage');
    const imgEmpty = document.getElementById('proofDetailImageEmpty');
    img.src = ''; img.classList.remove('d-none'); imgEmpty.classList.add('d-none');
    let photoUrl = null;
    try {
        const res = await fetch(DRIVER_PROOF_API.replace('__ID__', deliveryId).replace('__INV__', d.invoiceId));
        if (res.ok) {
            const data = await res.json();
            const payload = data.data || data;
            if (payload?.photo_url) photoUrl = payload.photo_url;
            if (payload?.notes) document.getElementById('proofDetailNotes').textContent = payload.notes;
        }
    } catch {}
    if (!photoUrl) {
        const guesses = [
            "/storage/proofs/" + d.invoiceId + ".jpg",
            "/uploads/proofs/" + d.invoiceId + ".jpg",
            "/assets/proofs/" + d.invoiceId + ".jpg"
        ];
        photoUrl = await findExistingImage(guesses);
    }
    if (photoUrl) {
        img.src = photoUrl;
    } else {
        img.classList.add('d-none');
        imgEmpty.classList.remove('d-none');
    }
    const Modal = window.bootstrap?.Modal;
    if (Modal) {
        new Modal(document.getElementById('proofDetailModal')).show();
    } else {
        document.getElementById('proofDetailModal').classList.add('show');
    }
}
function findExistingImage(urls) {
    return new Promise(resolve => {
        let i = 0;
        function tryNext() {
            if (i >= urls.length) return resolve(null);
            const u = urls[i++];
            const im = new Image();
            im.onload = () => resolve(u);
            im.onerror = tryNext;
            im.src = u + '?v=' + Date.now();
        }
        tryNext();
    });
}
function applyWatermark(ctx, w, h) {
    const name = document.getElementById('proofMitraName').value || 'Lokasi';
    const lat = document.getElementById('proofLat').value || '-';
    const lng = document.getElementById('proofLng').value || '-';
    const time = new Date().toLocaleString('id-ID');
    ctx.fillStyle = 'rgba(0,0,0,0.45)';
    const rectH = 70;
    ctx.fillRect(0, h - rectH - 10, w, rectH + 10);
    ctx.fillStyle = '#fff';
    ctx.font = '16px system-ui, -apple-system, Segoe UI, Roboto, sans-serif';
    let y = h - rectH;
    ctx.fillText(`Lokasi: ${name}`, 12, y + 0);
    ctx.fillText(`Koordinat: ${lat}, ${lng}`, 12, y + 22);
    ctx.fillText(`Waktu: ${time}`, 12, y + 44);
}
function getCanvasBlob(canvas, cb) {
    if (canvas.toBlob) { canvas.toBlob(blob => cb(blob), 'image/jpeg', 0.9); return; }
    const dataURL = canvas.toDataURL('image/jpeg', 0.9), byteString = atob(dataURL.split(',')[1]), mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length), ia = new Uint8Array(ab);
    for (let i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
    cb(new Blob([ab], { type: mimeString }));
}
function addBestTileLayer(m) {
    const remoteUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    const localUrl = '/assets/tiles/{z}/{x}/{y}.png';
    const remoteLayer = L.tileLayer(remoteUrl, { attribution: '© OpenStreetMap contributors', minZoom: 5, maxZoom: 18 }).addTo(m);
    setTimeout(() => m.invalidateSize(), 100);
    const z = m.getZoom ? m.getZoom() : 13;
    const c = m.getCenter ? m.getCenter() : L.latLng(-6.2, 106.816666);
    const t = latLngToTile(c.lat, c.lng, z);
    const img = new Image();
    img.onload = function() {
        m.removeLayer(remoteLayer);
        L.tileLayer(localUrl, { attribution: '© OpenStreetMap contributors', minZoom: 5, maxZoom: 18 }).addTo(m);
        setTimeout(() => m.invalidateSize(), 100);
    };
    img.onerror = function() {};
    img.src = `/assets/tiles/${z}/${t.x}/${t.y}.png?v=1`;
}
function latLngToTile(lat, lon, zoom) {
    const lr = lat * Math.PI / 180, n = Math.pow(2, zoom);
    const x = Math.floor((lon + 180) / 360 * n);
    const y = Math.floor((1 - Math.log(Math.tan(lr) + 1 / Math.cos(lr)) / Math.PI) / 2 * n);
    return { x, y };
}
function ensureRoutingReady() {
    if (typeof L !== 'undefined' && typeof L.Routing !== 'undefined') return Promise.resolve(true);
    if (routingLibLoaded) return new Promise(r => r(true));
    routingLibLoaded = true;
    return new Promise((resolve) => {
        const s = document.createElement('script');
        s.src = '/assets/vendor/leaflet-routing/leaflet-routing-machine.js';
        s.onload = () => resolve(true);
        s.onerror = () => { routingLibLoaded = false; const cdn = document.createElement('script'); cdn.src = 'https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js'; cdn.onload = () => resolve(true); cdn.onerror = () => resolve(false); document.body.appendChild(cdn); };
        document.body.appendChild(s);
    });
}
function ensureLeafletReady() {
    if (typeof L !== 'undefined') return Promise.resolve(true);
    return new Promise((resolve) => {
        let done = false;
        function ok(){ if (!done && typeof L !== 'undefined') { done = true; resolve(true); } }
        const local = document.createElement('script'); local.src = '/assets/libs/leaflet/leaflet.js'; local.onload = ok; local.onerror = () => { const cdn = document.createElement('script'); cdn.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'; cdn.onload = ok; cdn.onerror = ok; document.head.appendChild(cdn); }; document.head.appendChild(local);
        const iv = setInterval(() => { if (typeof L !== 'undefined') { clearInterval(iv); ok(); } }, 50);
        setTimeout(() => { clearInterval(iv); ok(); }, 2000);
    });
}
function setDefaultMarkerAssets() {
    if (L && L.Icon && L.Icon.Default) {
        L.Icon.Default.mergeOptions({ iconRetinaUrl: '/assets/libs/leaflet/images/marker-icon.png', iconUrl: '/assets/libs/leaflet/images/marker-icon.png', shadowUrl: null });
    }
}
document.getElementById('proofModal').addEventListener('hidden.bs.modal', function () {
    if (streamRef) { streamRef.getTracks().forEach(t => t.stop()); streamRef = null; capturedBlob = null; document.getElementById('btn-preview').disabled = true; }
});
document.getElementById('btn-submit-proof')?.addEventListener('click', async function() {
    const fd = new FormData();
    fd.append('_token', CSRF_TOKEN);
    fd.append('latitude', document.getElementById('proofLat').value);
    fd.append('longitude', document.getElementById('proofLng').value);
    fd.append('notes', document.getElementById('proofNotes').value);
    if (capturedBlob) fd.append('photo', capturedBlob, 'capture.jpg'); else {
        const fi = document.getElementById('proofPhoto');
        if (fi.files.length === 0) { alert('Harap jepret foto bukti!'); return; }
        fd.append('photo', fi.files[0]);
    }
    const invoiceId = document.getElementById('proofInvoiceId').value;
    const btn = this; btn.disabled = true; btn.textContent = 'Mengirim...';
    try {
        const res = await fetch(DRIVER_ARRIVE_API.replace('__ID__', deliveryId).replace('__INV__', invoiceId), { method: 'POST', body: fd });
        if (res.ok) location.reload(); else { const d = await res.json().catch(() => ({})); alert('Gagal mengirim bukti: ' + (d.message || res.statusText)); btn.disabled = false; btn.textContent = 'Kirim Bukti'; }
    } catch (e) { alert('Gagal mengirim bukti: ' + (e.message || 'Error')); btn.disabled = false; btn.textContent = 'Kirim Bukti'; }
});
document.getElementById('btn-finish-trip')?.addEventListener('click', async function() {
    if (!await macConfirm('Selesaikan Tugas', 'Apakah Anda sudah kembali ke kantor dan ingin menyelesaikan tugas ini?', { confirmText: 'Ya, Saya Sudah Kembali', confirmType: 'success', cancelText: 'Batal' })) return;
    
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Lokasi...';

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(p => {
            btn.disabled = false;
            btn.innerHTML = '<i class="iconoir-home me-2"></i> Kembali ke Kantor & Selesai';
            
            document.getElementById('finishLat').value = p.coords.latitude;
            document.getElementById('finishLng').value = p.coords.longitude;
            
            const Modal = window.bootstrap?.Modal;
            if (Modal) {
                new Modal(document.getElementById('finishTripModal')).show();
            } else {
                document.getElementById('finishTripModal').classList.add('show');
            }
        }, () => {
            btn.disabled = false;
            btn.innerHTML = '<i class="iconoir-home me-2"></i> Kembali ke Kantor & Selesai';
            alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
        });
    } else {
        btn.disabled = false;
        btn.innerHTML = '<i class="iconoir-home me-2"></i> Kembali ke Kantor & Selesai';
        alert('Browser tidak mendukung Geolocation.');
    }
});

document.getElementById('btn-submit-finish')?.addEventListener('click', async function() {
    const odoEnd = document.getElementById('finishOdoEnd').value;
    const gasLeftover = document.getElementById('finishGasLeftover').value;
    const lat = document.getElementById('finishLat').value;
    const lng = document.getElementById('finishLng').value;

    if (!odoEnd || !gasLeftover) {
        alert('Mohon lengkapi data Odometer Akhir dan Sisa Bensin.');
        return;
    }
    
    const odoStart = Number(fleetData?.odo_start || 0);
    if (parseFloat(odoEnd) < parseFloat(odoStart)) {
        if (!confirm('Odometer Akhir (' + odoEnd + ') lebih kecil dari Odometer Awal (' + odoStart + '). Yakin ingin melanjutkan?')) return;
    }

    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
        const res = await fetch(DRIVER_FINISH_API.replace('__ID__', deliveryId), {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                odo_end: odoEnd,
                gas_leftover: gasLeftover
            })
        });

        if (res.ok) {
            location.reload();
        } else {
            const d = await res.json().catch(() => ({}));
            alert('Gagal menyelesaikan tugas: ' + (d.message || res.statusText));
            btn.disabled = false;
            btn.textContent = 'Selesaikan & Simpan';
        }
    } catch (e) {
        alert('Gagal menyelesaikan tugas: ' + (e.message || 'Error'));
        btn.disabled = false;
        btn.textContent = 'Selesaikan & Simpan';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    loadDeliveryData()
        .then(() => initMap())
        .then(() => startTrackingIfNeeded())
        .catch(e => console.error(e));
    const el = document.getElementById('map');
    if (el) {
        const obs = new IntersectionObserver(es => { es.forEach(x => { if (x.isIntersecting && map) map.invalidateSize(); }); }, { threshold: 0.2 });
        obs.observe(el);
    }
    setTimeout(function(){ if (map) { map.invalidateSize(); map.setView(map.getCenter(), map.getZoom()); } }, 600);
});
</script>
@endsection
