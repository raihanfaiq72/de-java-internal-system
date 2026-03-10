@extends('Layout.main')

@section('styles')
<link rel="stylesheet" href="/assets/libs/leaflet/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #map {
        height: 520px;
        width: 100%;
        border-radius: 12px;
    }
    .do-track-badge {
        font-size: 11px;
    }
</style>
@endsection

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-lg-8">
                    <h4 class="fw-bold text-dark mb-1">Live Tracking Delivery Order</h4>
                    <p class="text-muted small mb-0">
                        <span id="trackDoNumber">-</span> •
                        <span id="trackOfficeName">Kantor</span>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('delivery-order.index') }}" class="btn btn-light border me-2">
                        <i class="iconoir-arrow-left me-1"></i> Kembali
                    </a>
                    <span class="badge do-track-badge d-none" id="trackStatusBadge">Status: -</span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Rute Pengiriman</h6>
                                <span class="text-muted small">Visualisasi peta dan jalur perjalanan</span>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-3 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <div class="text-muted small">Driver</div>
                                    <div class="fw-bold">
                                        <span id="trackDriverName">Belum ditentukan</span>
                                    </div>
                                    <div class="text-muted small">
                                        Armada: <span id="trackFleetName">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="border rounded-3 px-3 py-2">
                                        <div class="text-muted small">Estimasi Jarak</div>
                                        <div class="fw-bold">
                                            <span id="trackDistance">0</span> km
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-3 px-3 py-2">
                                        <div class="text-muted small">Estimasi BBM</div>
                                        <div class="fw-bold">
                                            Rp <span id="trackFuelCost">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded-3 px-3 py-2">
                                        <div class="text-muted small mb-1">Ringkasan Tujuan</div>
                                        <div class="fw-bold small">
                                            <span id="trackStopsSummary">0 tujuan • 0 selesai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger me-2">&nbsp;</span>
                                <span class="small text-muted">Belum dikirim</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success me-2">&nbsp;</span>
                                <span class="small text-muted">Sudah dikirim</span>
                            </div>
                            <div class="small text-muted">
                                Posisi driver akan bergerak mengikuti update lokasi terbaru.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/assets/libs/leaflet/leaflet.js" onerror="(function(){var s=document.createElement('script');s.src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';document.head.appendChild(s)})()"></script>
<script src="/assets/vendor/leaflet-routing/leaflet-routing-machine.js" onerror="(function(){var s=document.createElement('script');s.src='https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js';document.head.appendChild(s)})()"></script>
<script>
    const deliveryId = {{ $doId }};
    const DO_API_SHOW = "{{ route('delivery-order-api.show', ['id' => '__ID__']) }}";
    const DO_FLEET_API_BY_DO = "{{ route('delivery-order-fleet-api.by-do', ['doId' => '__ID__']) }}";
    let fleet = null;
    let stops = [];

    const OFFICE_LAT = -6.966667;
    const OFFICE_LNG = 110.416664;

    let map, driverMarker, routingControl;

    async function loadData() {
        const doRes = await fetch(DO_API_SHOW.replace('__ID__', deliveryId));
        const doJson = await doRes.json();
        if (!doJson.success) throw new Error(doJson.message || 'Gagal memuat data DO');
        const doData = doJson.data;

        const fRes = await fetch(DO_FLEET_API_BY_DO.replace('__ID__', deliveryId));
        const fJson = await fRes.json();
        if (fJson.success) fleet = fJson.data;

        document.getElementById('trackDoNumber').textContent = doData.delivery_order_number || '-';
        document.getElementById('trackOfficeName').textContent = doData.office?.name || 'Kantor';
        const badge = document.getElementById('trackStatusBadge');
        badge.classList.remove('d-none', 'bg-success', 'bg-info', 'bg-secondary');
        const st = (doData.status || 'draft');
        badge.classList.add(st === 'completed' ? 'bg-success' : 'bg-info');
        badge.textContent = 'Status: ' + st;

        document.getElementById('trackDriverName').textContent = fleet?.driver?.name || 'Belum ditentukan';
        const fn = fleet?.fleet ? `${fleet.fleet.fleet_name} (${fleet.fleet.license_plate || fleet.fleet.plate_number || '-'})` : '-';
        document.getElementById('trackFleetName').textContent = fn;
        document.getElementById('trackDistance').textContent = Number(fleet?.estimated_distance_km || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('trackFuelCost').textContent = Number(fleet?.estimated_fuel_cost || 0).toLocaleString('id-ID');

        const inv = Array.isArray(doData.invoices) ? doData.invoices : [];
        const deliveredCount = inv.filter(i => i.delivery_status === 'delivered').length;
        document.getElementById('trackStopsSummary').textContent = `${inv.length} tujuan • ${deliveredCount} selesai`;
        stops = inv.map(i => ({
            name: i.invoice?.mitra?.nama || 'Tujuan',
            lat: i.invoice?.mitra?.latitude,
            lng: i.invoice?.mitra?.longitude,
            status: i.delivery_status
        }));
    }

    async function initMap() {
        await loadData();
        if (typeof L === 'undefined') {
            await new Promise(r => setTimeout(r, 100));
        }
        let center = [-6.200000, 106.816666];
        if (fleet && fleet.last_latitude && fleet.last_longitude) {
            center = [fleet.last_latitude, fleet.last_longitude];
        } else if (stops.length > 0 && stops[0].lat) {
            center = [stops[0].lat, stops[0].lng];
        }

        map = L.map('map').setView(center, 13);

        addBestTileLayer(map);
        setDefaultMarkerAssets();

        const routePoints = [];
        routePoints.push(L.latLng(OFFICE_LAT, OFFICE_LNG));

        stops.filter(s => s.lat && s.lng).forEach(s => {
            routePoints.push(L.latLng(s.lat, s.lng));
        });

        routePoints.push(L.latLng(OFFICE_LAT, OFFICE_LNG));

        if (routePoints.length > 1) {
            await ensureRoutingReady();
            routingControl = L.Routing.control({
                waypoints: routePoints,
                routeWhileDragging: false,
                addWaypoints: false,
                show: false,
                createMarker: function() { return null; }
            }).addTo(map);
        }

        const pendingStops = stops.filter(s => s.lat && s.lng && s.status !== 'delivered');
        const deliveredStops = stops.filter(s => s.lat && s.lng && s.status === 'delivered');

        pendingStops.forEach((stop, index) => {
            L.circleMarker([stop.lat, stop.lng], {
                color: 'red',
                fillColor: 'red',
                fillOpacity: 0.5,
                radius: 8
            }).addTo(map).bindPopup(`<b>${index + 1}. ${stop.name}</b><br>${stop.status}`);
        });

        deliveredStops.forEach((stop, index) => {
            L.circleMarker([stop.lat, stop.lng], {
                color: 'green',
                fillColor: 'green',
                fillOpacity: 0.7,
                radius: 9
            }).addTo(map).bindPopup(`<b>${index + 1}. ${stop.name}</b><br>${stop.status}`);
        });

        // Add Driver Marker if location exists
        if (fleet && fleet.last_latitude && fleet.last_longitude) {
            updateDriverMarker(fleet.last_latitude, fleet.last_longitude);
        }
    }

    function updateDriverMarker(lat, lng) {
        if (!driverMarker) {
            driverMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'driver-marker',
                    html: '<div style="background-color: blue; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
                    iconSize: [24, 24]
                })
            }).addTo(map);
        } else {
            driverMarker.setLatLng([lat, lng]);
        }
        map.panTo([lat, lng]);
    }

    function addBestTileLayer(mapInstance) {
        const remoteUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        const localUrl = '/assets/tiles/{z}/{x}/{y}.png';
        const remoteLayer = L.tileLayer(remoteUrl, {
            attribution: '© OpenStreetMap contributors',
            minZoom: 5,
            maxZoom: 18
        }).addTo(mapInstance);
        setTimeout(() => mapInstance.invalidateSize(), 100);
        const z = mapInstance.getZoom ? mapInstance.getZoom() : 13;
        const c = mapInstance.getCenter ? mapInstance.getCenter() : L.latLng(-6.200000, 106.816666);
        const t = latLngToTile(c.lat, c.lng, z);
        const testImg = new Image();
        testImg.onload = function() {
            mapInstance.removeLayer(remoteLayer);
            L.tileLayer(localUrl, {
                attribution: '© OpenStreetMap contributors',
                minZoom: 5,
                maxZoom: 18
            }).addTo(mapInstance);
            setTimeout(() => mapInstance.invalidateSize(), 100);
        };
        testImg.onerror = function() { /* keep remote */ };
        testImg.src = `/assets/tiles/${z}/${t.x}/${t.y}.png?v=1`;
    }
    function latLngToTile(lat, lon, zoom) {
        const latRad = lat * Math.PI / 180;
        const n = Math.pow(2, zoom);
        const x = Math.floor((lon + 180) / 360 * n);
        const y = Math.floor((1 - Math.log(Math.tan(latRad) + 1 / Math.cos(latRad)) / Math.PI) / 2 * n);
        return { x, y };
    }

    function ensureRoutingReady() {
        if (typeof L !== 'undefined' && typeof L.Routing !== 'undefined') {
            return Promise.resolve(true);
        }
        return new Promise((resolve) => {
            const local = document.createElement('script');
            local.src = '/assets/vendor/leaflet-routing/leaflet-routing-machine.js';
            local.onload = () => resolve(true);
            local.onerror = () => {
                const cdn = document.createElement('script');
                cdn.src = 'https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js';
                cdn.onload = () => resolve(true);
                cdn.onerror = () => resolve(false);
                document.head.appendChild(cdn);
            };
            document.head.appendChild(local);
        });
    }

    function setDefaultMarkerAssets() {
        if (L && L.Icon && L.Icon.Default) {
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: '/assets/libs/leaflet/images/marker-icon.png',
                iconUrl: '/assets/libs/leaflet/images/marker-icon.png',
                shadowUrl: null
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        setTimeout(function(){ if (map) map.invalidateSize(); }, 300);

        // Listen to Reverb Channel
        if (window.Echo) {
            console.log('Listening to delivery-order.' + deliveryId);
            window.Echo.private('delivery-order.' + deliveryId)
                .listen('.driver.location.updated', (e) => {
                    console.log('Location Update:', e);
                    updateDriverMarker(e.latitude, e.longitude);
                });
        }
    });
</script>
@endsection
