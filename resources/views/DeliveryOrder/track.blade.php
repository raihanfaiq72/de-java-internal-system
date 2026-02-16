@extends('Layout.main')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
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
                        {{ $do->delivery_order_number }} •
                        {{ $do->office->nama_office ?? 'Kantor' }}
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('delivery-order.index') }}" class="btn btn-light border me-2">
                        <i class="iconoir-arrow-left me-1"></i> Kembali
                    </a>
                    @php
                        $fleet = $do->fleets->first();
                    @endphp
                    @if($fleet)
                        <span class="badge bg-{{ $do->status === 'completed' ? 'success' : 'info' }} do-track-badge">
                            Status: {{ ucfirst($do->status ?? 'draft') }}
                        </span>
                    @endif
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
                                        {{ $fleet->driver->name ?? 'Belum ditentukan' }}
                                    </div>
                                    <div class="text-muted small">
                                        Armada: {{ $fleet->fleet->fleet_name ?? '-' }} ({{ $fleet->fleet->license_plate ?? '-' }})
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="border rounded-3 px-3 py-2">
                                        <div class="text-muted small">Estimasi Jarak</div>
                                        <div class="fw-bold">
                                            {{ number_format($fleet->estimated_distance_km ?? 0, 2, ',', '.') }} km
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-3 px-3 py-2">
                                        <div class="text-muted small">Estimasi BBM</div>
                                        <div class="fw-bold">
                                            Rp {{ number_format($fleet->estimated_fuel_cost ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded-3 px-3 py-2">
                                        <div class="text-muted small mb-1">Ringkasan Tujuan</div>
                                        <div class="fw-bold small">
                                            {{ $do->invoices->count() }} tujuan •
                                            {{ $do->invoices->where('delivery_status', 'delivered')->count() }} selesai
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
@php
    $stops = $do->invoices->map(function ($i) {
        return [
            'name' => $i->invoice->mitra->nama,
            'lat' => $i->invoice->mitra->latitude,
            'lng' => $i->invoice->mitra->longitude,
            'status' => $i->delivery_status,
        ];
    });
@endphp
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
    const deliveryId = {{ $do->id }};
    const fleet = @json($do->fleets->first());
    const stops = @json($stops);

    const OFFICE_LAT = -6.966667;
    const OFFICE_LNG = 110.416664;

    let map, driverMarker, routingControl;

    function initMap() {
        let center = [-6.200000, 106.816666];
        if (fleet && fleet.last_latitude && fleet.last_longitude) {
            center = [fleet.last_latitude, fleet.last_longitude];
        } else if (stops.length > 0 && stops[0].lat) {
            center = [stops[0].lat, stops[0].lng];
        }

        map = L.map('map').setView(center, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const routePoints = [];
        routePoints.push(L.latLng(OFFICE_LAT, OFFICE_LNG));

        stops.filter(s => s.lat && s.lng).forEach(s => {
            routePoints.push(L.latLng(s.lat, s.lng));
        });

        routePoints.push(L.latLng(OFFICE_LAT, OFFICE_LNG));

        if (routePoints.length > 1) {
            routingControl = L.Routing.control({
                waypoints: routePoints,
                routeWhileDragging: false,
                addWaypoints: false,
                show: false
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

    document.addEventListener('DOMContentLoaded', function() {
        initMap();

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
