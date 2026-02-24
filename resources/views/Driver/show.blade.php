@extends('Layout.main')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    #map { height: 400px; width: 100%; border-radius: 8px; }
    .stop-card { border-left: 4px solid #dee2e6; transition: all 0.2s; }
    .stop-card.active { border-left-color: #0d6efd; background-color: #f8f9fa; }
    .stop-card.completed { border-left-color: #198754; opacity: 0.8; }
</style>
@endsection

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="page-title fw-bold">Detail Pengiriman</h4>
                            <p class="text-muted mb-0 small">No. DO: {{ $do->delivery_order_number }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $fleet->status == 'assigned' ? 'warning' : ($fleet->status == 'in_transit' ? 'info' : 'success') }} fs-6">
                                {{ ucfirst(str_replace('_', ' ', $fleet->status)) }}
                            </span>
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
                        <div class="card-body p-2">
                            <div id="map"></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            @if($fleet->status == 'assigned')
                                <button id="btn-start-trip" class="btn btn-primary w-100 btn-lg">
                                    <i class="iconoir-play me-2"></i> Mulai Perjalanan
                                </button>
                            @elseif($fleet->status == 'in_transit')
                                <div class="alert alert-info d-flex align-items-center mb-0">
                                    <div class="spinner-grow spinner-grow-sm text-primary me-3" role="status"></div>
                                    <div>
                                        <strong>Sedang dalam perjalanan</strong><br>
                                        Lokasi Anda sedang dilacak secara realtime.
                                    </div>
                                </div>
                            @elseif($fleet->status == 'completed')
                                <div class="alert alert-success">
                                    <i class="iconoir-check-circle me-2"></i> Perjalanan Selesai
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h5 class="mb-3">Daftar Tujuan</h5>
                    <div class="list-group">
                @foreach($do->invoices as $index => $item)
                    @php
                        $isNext = false;
                        if ($fleet->status == 'in_transit' && $item->delivery_status == 'pending') {
                            // Check if previous ones are done
                            $prevDone = true;
                            foreach($do->invoices as $prevItem) {
                                if ($prevItem->delivery_sequence < $item->delivery_sequence && $prevItem->delivery_status == 'pending') {
                                    $prevDone = false;
                                    break;
                                }
                            }
                            $isNext = $prevDone;
                        }
                    @endphp

                    <div class="list-group-item stop-card p-3 mb-2 shadow-sm border-0 {{ $item->delivery_status == 'delivered' ? 'completed' : ($isNext ? 'active' : '') }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $index + 1 }}. {{ $item->invoice->mitra->nama }}</h6>
                                <p class="mb-1 small text-muted">{{ $item->invoice->mitra->alamat }}</p>
                                <span class="badge bg-{{ $item->delivery_status == 'delivered' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($item->delivery_status) }}
                                </span>
                            </div>
                            @if($isNext)
                                <button class="btn btn-sm btn-success btn-arrive" 
                                    data-id="{{ $item->invoice_id }}" 
                                    data-lat="{{ $item->invoice->mitra->latitude }}" 
                                    data-lng="{{ $item->invoice->mitra->longitude }}"
                                    data-name="{{ $item->invoice->mitra->nama }}">
                                    Sampai
                                </button>
                            @endif
                        </div>
                        @if($item->delivery_status == 'delivered')
                            <div class="mt-2 small text-success">
                                <i class="iconoir-check"></i> Diterima: {{ $item->arrived_at ? \Carbon\Carbon::parse($item->arrived_at)->format('H:i') : '-' }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($fleet->status == 'in_transit')
                @php
                    $allDelivered = $do->invoices->every(fn($i) => $i->delivery_status == 'delivered');
                @endphp
                @if($allDelivered)
                    <button id="btn-finish-trip" class="btn btn-success w-100 btn-lg mt-3">
                        <i class="iconoir-home me-2"></i> Kembali ke Kantor & Selesai
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal Bukti Pengiriman -->
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
                        <div class="border rounded p-2">
                            <video id="cameraStream" autoplay playsinline style="width:100%; max-height:240px; background:#000;"></video>
                            <canvas id="captureCanvas" class="d-none"></canvas>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-primary btn-sm" id="btn-capture" disabled>
                                    <i class="iconoir-camera me-1"></i> Jepret
                                </button>
                                <button type="button" class="btn btn-light border btn-sm" id="btn-preview" disabled data-bs-toggle="modal" data-bs-target="#previewModal">
                                    <i class="iconoir-image me-1"></i> Lihat Foto
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">Gunakan kamera belakang jika tersedia.</small>
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

<!-- Modal Preview Foto -->
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
</div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
    const deliveryId = {{ $do->id }};
    const fleetStatus = '{{ $fleet->status }}';
    @php
        $stopsData = $do->invoices->map(fn($i) => [
            'name' => $i->invoice->mitra->nama,
            'lat' => $i->invoice->mitra->latitude,
            'lng' => $i->invoice->mitra->longitude,
            'status' => $i->delivery_status
        ]);
    @endphp
    const stops = @json($stopsData);
    
    let map, userMarker;
    let watchId;

    function initMap() {
        // Default center (Indonesia) or first stop
        const center = stops.length > 0 && stops[0].lat ? [stops[0].lat, stops[0].lng] : [-6.200000, 106.816666];
        map = L.map('map').setView(center, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add markers for stops
        stops.forEach((stop, index) => {
            if (stop.lat && stop.lng) {
                const color = stop.status === 'delivered' ? 'green' : 'red';
                const marker = L.circleMarker([stop.lat, stop.lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.5,
                    radius: 8
                }).addTo(map);
                marker.bindPopup(`<b>${index + 1}. ${stop.name}</b><br>${stop.status}`);
            }
        });

        // Try to get user location immediately
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude, longitude } = pos.coords;
                updateUserMarker(latitude, longitude);
                map.setView([latitude, longitude], 13);
            });
        }
    }

    let routingControl;
    let routeCalculated = false;

    function updateUserMarker(lat, lng) {
        if (!userMarker) {
            userMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'user-marker',
                    html: '<div style="background-color: blue; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>',
                    iconSize: [16, 16]
                })
            }).addTo(map);
        } else {
            userMarker.setLatLng([lat, lng]);
        }

        // Calculate route if not already done and we have valid location
        if (!routeCalculated && fleetStatus === 'in_transit') {
            calculateRoute(lat, lng);
            routeCalculated = true;
        }
    }

    function calculateRoute(startLat, startLng) {
        if (routingControl) {
            map.removeControl(routingControl);
        }

        const waypoints = [
            L.latLng(startLat, startLng)
        ];

        // Add all pending stops to waypoints
        stops.forEach(stop => {
            if (stop.lat && stop.lng && stop.status === 'pending') {
                waypoints.push(L.latLng(stop.lat, stop.lng));
            }
        });

        if (waypoints.length > 1) {
            routingControl = L.Routing.control({
                waypoints: waypoints,
                routeWhileDragging: false,
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: true,
                showAlternatives: false,
                lineOptions: {
                    styles: [{color: 'blue', opacity: 0.6, weight: 4}]
                },
                createMarker: function() { return null; }, // Disable default markers
                show: false // Hide turn-by-turn instructions
            }).addTo(map);
        }
    }

    // Start Trip
    $('#btn-start-trip').click(async function() {
        if (!await macConfirm('Mulai Perjalanan', 'Mulai perjalanan pengiriman sekarang?', {
            confirmText: 'Mulai',
            confirmType: 'success',
            cancelText: 'Batal'
        })) return;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                
                $.post("{{ route('driver.delivery.start', $do->id) }}", {
                    _token: "{{ csrf_token() }}",
                    latitude: latitude,
                    longitude: longitude
                })
                .done(function() {
                    location.reload();
                })
                .fail(function(err) {
                    alert('Gagal memulai perjalanan: ' + (err.responseJSON?.message || 'Error'));
                });
            }, err => alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.'));
        } else {
            alert('Browser tidak mendukung Geolocation.');
        }
    });

    // Tracking Logic
    if (fleetStatus === 'in_transit') {
        if (navigator.geolocation) {
            watchId = navigator.geolocation.watchPosition(position => {
                const { latitude, longitude } = position.coords;
                updateUserMarker(latitude, longitude);

                // Send update to server (throttle this in production, e.g., every 30s)
                // For demo, we do it here but maybe debounced
                sendLocationUpdate(latitude, longitude);
            }, null, { enableHighAccuracy: true });
        }
    }

    let lastUpdate = 0;
    function sendLocationUpdate(lat, lng) {
        const now = Date.now();
        if (now - lastUpdate < 10000) return; // Limit to every 10s
        lastUpdate = now;

        $.post("{{ route('driver.delivery.location', $do->id) }}", {
            _token: "{{ csrf_token() }}",
            latitude: lat,
            longitude: lng
        }); // Fire and forget
    }

    // Arrive Button
    $('.btn-arrive').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const lat = $(this).data('lat');
        const lng = $(this).data('lng');
        
        // Get current location for validation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async position => {
                const currentLat = position.coords.latitude;
                const currentLng = position.coords.longitude;

                // Validation: Distance Check (Warning if > 500m)
                const dist = getDistanceFromLatLonInKm(currentLat, currentLng, lat, lng);
                if (dist > 0.5) {
                     if (!await macConfirm('Peringatan Jarak', `Peringatan: Anda terdeteksi berada ${dist.toFixed(2)} km dari lokasi tujuan. Apakah Anda yakin sudah sampai?`, {
                        confirmText: 'Ya, Saya Sudah Sampai',
                        confirmType: 'success',
                        cancelText: 'Batal'
                     })) {
                        return;
                     }
                }

                $('#proofInvoiceId').val(id);
                $('#proofMitraName').val(name);
                $('#proofLat').val(currentLat);
                $('#proofLng').val(currentLng);
                
                // Show Modal
                new bootstrap.Modal(document.getElementById('proofModal')).show();
                startCamera();
            });
        }
    });

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = deg2rad(lat2-lat1); 
        var dLon = deg2rad(lon2-lon1); 
        var a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
            Math.sin(dLon/2) * Math.sin(dLon/2)
            ; 
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
        var d = R * c; // Distance in km
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI/180)
    }

    let streamRef = null;
    let capturedBlob = null;

    async function startCamera() {
        try {
            if (streamRef) return;
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            streamRef = stream;
            const video = document.getElementById('cameraStream');
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                document.getElementById('btn-capture').disabled = false;
            };
        } catch (e) {
            console.warn('Camera unavailable, fallback to file input');
            document.getElementById('proofPhoto').classList.remove('d-none');
            document.getElementById('btn-capture').disabled = true;
            document.getElementById('btn-preview').disabled = true;
        }
    }

    document.getElementById('btn-capture').addEventListener('click', function() {
        const video = document.getElementById('cameraStream');
        const canvas = document.getElementById('captureCanvas');
        if (!video.videoWidth || !video.videoHeight) {
            alert('Kamera belum siap. Tunggu sebentar lalu coba lagi.');
            return;
        }
        const w = video.videoWidth;
        const h = video.videoHeight;
        canvas.width = w; canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, w, h);
        applyWatermark(ctx, w, h);
        getCanvasBlob(canvas, function(blob) {
            capturedBlob = blob;
            const url = URL.createObjectURL(blob);
            const img = document.getElementById('previewImage');
            img.src = url;
            document.getElementById('btn-preview').disabled = false;
        });
    });

    function applyWatermark(ctx, w, h) {
        const name = document.getElementById('proofMitraName').value || 'Lokasi';
        const lat = document.getElementById('proofLat').value || '-';
        const lng = document.getElementById('proofLng').value || '-';
        const time = new Date().toLocaleString('id-ID');
        const lines = [
            `Lokasi: ${name}`,
            `Koordinat: ${lat}, ${lng}`,
            `Waktu: ${time}`
        ];
        ctx.fillStyle = 'rgba(0,0,0,0.45)';
        const rectH = 70;
        ctx.fillRect(0, h - rectH - 10, w, rectH + 10);
        ctx.fillStyle = '#fff';
        ctx.font = '16px system-ui, -apple-system, Segoe UI, Roboto, sans-serif';
        let y = h - rectH;
        lines.forEach((line, idx) => {
            ctx.fillText(line, 12, y + (idx * 22));
        });
    }

    function getCanvasBlob(canvas, cb) {
        if (canvas.toBlob) {
            canvas.toBlob(function (blob) { cb(blob); }, 'image/jpeg', 0.9);
        } else {
            const dataURL = canvas.toDataURL('image/jpeg', 0.9);
            const byteString = atob(dataURL.split(',')[1]);
            const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            cb(new Blob([ab], { type: mimeString }));
        }
    }

    // Stop camera when modal closes
    document.getElementById('proofModal').addEventListener('hidden.bs.modal', function () {
        if (streamRef) {
            streamRef.getTracks().forEach(t => t.stop());
            streamRef = null;
            capturedBlob = null;
            document.getElementById('btn-preview').disabled = true;
        }
    });

    // Submit Proof
    $('#btn-submit-proof').click(function() {
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('latitude', $('#proofLat').val());
        formData.append('longitude', $('#proofLng').val());
        formData.append('notes', $('#proofNotes').val());
        
        if (capturedBlob) {
            formData.append('photo', capturedBlob, 'capture.jpg');
        } else {
            const fileInput = document.getElementById('proofPhoto');
            if (fileInput.files.length === 0) {
                alert('Harap jepret foto bukti!');
                return;
            }
            formData.append('photo', fileInput.files[0]);
        }

        const invoiceId = $('#proofInvoiceId').val();
        const btn = $(this);
        btn.prop('disabled', true).text('Mengirim...');

        $.ajax({
            url: "{{ url('driver/delivery') }}/" + deliveryId + "/invoice/" + invoiceId + "/arrive",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                location.reload();
            },
            error: function(err) {
                alert('Gagal mengirim bukti: ' + (err.responseJSON?.message || 'Error'));
                btn.prop('disabled', false).text('Kirim Bukti');
            }
        });
    });

    // Finish Trip
    $('#btn-finish-trip').click(async function() {
        if (!await macConfirm('Selesaikan Tugas', 'Apakah Anda sudah kembali ke kantor dan ingin menyelesaikan tugas ini?', {
            confirmText: 'Ya, Saya Sudah Kembali',
            confirmType: 'success',
            cancelText: 'Batal'
        })) return;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                $.post("{{ route('driver.delivery.finish', $do->id) }}", {
                    _token: "{{ csrf_token() }}",
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                })
                .done(function() {
                    location.reload();
                })
                .fail(function(err) {
                    alert('Gagal menyelesaikan tugas: ' + (err.responseJSON?.message || 'Error'));
                });
            });
        }
    });

    $(document).ready(function() {
        initMap();
    });
</script>
@endsection
