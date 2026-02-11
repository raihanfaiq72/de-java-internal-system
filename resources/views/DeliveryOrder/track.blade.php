@extends('Layout.main')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 600px; width: 100%; border-radius: 8px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Live Tracking: {{ $do->delivery_order_number }}</h4>
            <p class="text-muted mb-0">Driver: {{ $do->fleets->first()->driver->name ?? 'Unassigned' }} | Plat: {{ $do->fleets->first()->fleet->license_plate ?? '-' }}</p>
        </div>
        <a href="{{ route('delivery-order.index') }}" class="btn btn-secondary">
            <i class="iconoir-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-2">
            <div id="map"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const deliveryId = {{ $do->id }};
    const fleet = @json($do->fleets->first());
    
    // Invoices as stops
    const stops = @json($do->invoices->map(fn($i) => [
        'name' => $i->invoice->mitra->nama,
        'lat' => $i->invoice->mitra->latitude,
        'lng' => $i->invoice->mitra->longitude,
        'status' => $i->delivery_status
    ]));

    let map, driverMarker;

    function initMap() {
        // Center: Driver's last location OR First Stop OR Default
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

        // Add stops
        stops.forEach((stop, index) => {
            if (stop.lat && stop.lng) {
                const color = stop.status === 'delivered' ? 'green' : 'red';
                L.circleMarker([stop.lat, stop.lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.5,
                    radius: 8
                }).addTo(map).bindPopup(`<b>${index + 1}. ${stop.name}</b><br>${stop.status}`);
            }
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
