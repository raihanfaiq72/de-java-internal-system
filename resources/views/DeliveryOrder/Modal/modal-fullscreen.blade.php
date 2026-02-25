<div class="modal fade" id="deliveryOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <!-- Header -->
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-3">
                        <i class="fa fa-truck-moving fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Delivery Order</h5>
                        <p class="text-muted small mb-0">Kelola pengiriman dan armada.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border text-secondary fw-semibold px-4"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveDeliveryOrder()">
                        <i class="fa fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <form id="deliveryOrderForm">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_do_id" value="">

                    <!-- Leaflet Assets (CDN for reliability) -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
                    <script src="/assets/vendor/leaflet-routing/leaflet-routing-machine.js"></script>

                    <div class="row g-4">
                        <!-- Left: DO Info & Invoices -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white py-3 px-4 border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark">Informasi Pengiriman</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="f-label">Nomor DO</label>
                                        <input type="text" id="modal_do_number" class="form-control f-input fw-bold"
                                            placeholder="(Otomatis)" readonly>
                                        <div class="form-text text-muted small">Nomor akan digenerate otomatis jika
                                            kosong.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="f-label">Tanggal Pengiriman <span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="modal_delivery_date" class="form-control f-input">
                                    </div>

                                    <!-- Replaced Rute/Tujuan with Invoice Selection -->
                                    <div class="mb-3">
                                        <label class="f-label mb-2">Pilih Invoice (Tujuan) <span
                                                class="text-danger">*</span></label>
                                        <select id="modal_invoice_ids" multiple placeholder="Cari invoice..."></select>
                                        <div class="form-text text-muted small">Alamat akan diambil dari mitra invoice
                                            terpilih.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="f-label">Catatan</label>
                                        <textarea id="modal_notes" class="form-control f-input" rows="4"
                                            placeholder="Catatan tambahan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Fleet, Map & Costs -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-header bg-white py-3 px-4 border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark">Armada & Estimasi Biaya</h6>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Map Container -->
                                    <div id="map"
                                        style="height: 350px; width: 100%; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="f-label">Pilih Armada <span
                                                    class="text-danger">*</span></label>
                                            <select id="modal_fleet_id" class="form-select f-input"
                                                onchange="onFleetChange()">
                                                <option value="">-- Pilih Armada --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">Supir (Opsional)</label>
                                            <select id="modal_driver_id" class="form-select f-input">
                                                <option value="">-- Pilih Supir --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">Odometer Awal (KM)</label>
                                            <input type="number" id="modal_odo_start" class="form-control form-control-sm" placeholder="0">
                                            <small class="text-muted" id="last_odo_hint"></small>
                                        </div>

                                        <div class="col-12">
                                            <hr class="border-light my-2">
                                        </div>

                                        <!-- Auto-filled Fleet Specs -->
                                        <div class="col-md-3">
                                            <label class="f-label text-muted">Konsumsi (KM/L)</label>
                                            <input type="number" id="modal_km_per_liter"
                                                class="form-control bg-light border-0" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="f-label text-muted">Harga BBM (/L)</label>
                                            <input type="number" id="modal_liter_price"
                                                class="form-control bg-light border-0" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="f-label text-muted">Sisa Bensin (L)</label>
                                            <input type="number" id="modal_fuel_leftover"
                                                class="form-control bg-light border-0" readonly placeholder="0">
                                        </div>

                                        <!-- Estimation Inputs -->
                                        <div class="col-md-12">
                                            <label class="f-label text-primary">Estimasi Jarak</label>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <small class="text-muted d-block">Berangkat</small>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" id="modal_est_distance_outbound"
                                                            class="form-control fw-bold" placeholder="0" readonly>
                                                        <span class="input-group-text bg-white text-muted">km</span>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted d-block">Pulang</small>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" id="modal_est_distance_return"
                                                            class="form-control fw-bold" placeholder="0" readonly>
                                                        <span class="input-group-text bg-white text-muted">km</span>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted d-block">Total</small>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" id="modal_est_distance"
                                                            class="form-control fw-bold" placeholder="0" readonly>
                                                        <span class="input-group-text bg-white text-muted">km</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cost Summary -->
                                        <div class="col-12 mt-3">
                                            <h6 class="fw-bold mb-3">Rincian Biaya</h6>
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered table-sm" id="costTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Jenis Biaya</th>
                                                            <th width="150">Jumlah (Rp)</th>
                                                            <th width="50"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="costTableBody">
                                                        <!-- Fuel Cost Row (Readonly) -->
                                                        <tr class="bg-light">
                                                            <td class="align-middle">Biaya BBM (Estimasi)</td>
                                                            <td>
                                                                <input type="number" id="modal_est_fuel_cost"
                                                                    class="form-control form-control-sm border-0 bg-transparent text-end fw-bold"
                                                                    readonly value="0">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr class="bg-light text-success">
                                                            <td class="align-middle"><small>Potongan Sisa Bensin</small></td>
                                                            <td>
                                                                <input type="number" id="modal_fuel_deduction"
                                                                    class="form-control form-control-sm border-0 bg-transparent text-end fw-bold text-success"
                                                                    readonly value="0">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <!-- Additional Costs will be added here -->
                                                    </tbody>
                                                </table>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="addCostRow()">
                                                    <i class="fa fa-plus me-1"></i> Tambah Biaya Lain
                                                </button>
                                            </div>

                                            <div
                                                class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 d-flex justify-content-between align-items-center mb-0">
                                                <div>
                                                    <i class="fa fa-coins me-2"></i>
                                                    <span class="fw-bold">Total Estimasi Biaya</span>
                                                </div>
                                                <span class="fs-5 fw-bold text-primary" id="disp_total_cost">Rp
                                                    0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let fleetSelect, invoiceSelect, driverSelect, estCost;
    let selectedFleetData = null;
    let currentDOInvoices = [];
    let map, routingControl;
    let mapMarkers = [];

    // Default Office Location (Semarang)
    const OFFICE_LAT = -6.966667;
    const OFFICE_LNG = 110.416664;
    
    // Custom Red Icon for Invoices
    const redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    
    // Custom Blue Icon for Office (Start/End)
    const blueIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Store addresses to route
    let routeWaypoints = []; // Array of { lat, lng, title }

    async function initModalScripts() {
        // Initialize TomSelect for Invoices
        if (document.getElementById('modal_invoice_ids') && !invoiceSelect) {
            invoiceSelect = new TomSelect('#modal_invoice_ids', {
                plugins: ['remove_button'],
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                onChange: function (value) {
                    updateMapRoute(value);
                }
            });
        }

        // Populate Fleets
        const fleetEl = document.getElementById('modal_fleet_id');
        fleetEl.innerHTML = '<option value="">-- Pilih Armada --</option>';
        if (typeof masterFleets !== 'undefined') {
            masterFleets.forEach(f => {
                const opt = document.createElement('option');
                opt.value = f.id;
                opt.text = `${f.fleet_name} (${f.license_plate})`;
                opt.dataset.km = f.km_per_liter || 0;
                opt.dataset.price = f.liter_price || 0;
                opt.dataset.last_odo = f.last_odometer || 0;
                opt.dataset.leftover = f.last_fuel_leftover || 0;
                fleetEl.appendChild(opt);
            });
        }

        // Populate Drivers
        const driverEl = document.getElementById('modal_driver_id');
        driverEl.innerHTML = '<option value="">-- Pilih Supir --</option>';
        try {
            const res = await fetch("{{ route('user-api.index') }}?all=1");
            const json = await res.json();
            if (json.success) {
                json.data.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.text = `${u.name}`;
                    driverEl.appendChild(opt);
                });
            }
        } catch (e) {
            console.error(e);
        }

        // Populate Invoices
        try {
            if (invoiceSelect) {
                invoiceSelect.clearOptions();
                // Fetch more invoices to ensure better coverage
                // exclude_delivered=1 to hide invoices already in active DOs
                const resInv = await fetch("{{ route('invoice-api.index') }}?status=Unpaid&per_page=100&exclude_delivered=1");
                const jsonInv = await resInv.json();

                if (jsonInv.success) {
                    jsonInv.data.data.forEach(inv => {
                        invoiceSelect.addOption({
                            value: inv.id,
                            text: `${inv.nomor_invoice} - ${inv.mitra ? inv.mitra.nama : 'Umum'}`,
                            address: inv.mitra ? inv.mitra.alamat : '',
                            lat: inv.mitra ? inv.mitra.latitude : null,
                            lng: inv.mitra ? inv.mitra.longitude : null,
                            mitra_name: inv.mitra ? inv.mitra.nama : 'Umum'
                        });
                    });
                }
            }
        } catch (e) {
            console.error(e);
        }

        setTimeout(() => initMap(), 500);
    }

    async function initMap() {
        if (map) {
            map.invalidateSize();
            return;
        }

        if (typeof L === 'undefined') {
            await new Promise(r => setTimeout(r, 100));
        }

        map = L.map('map').setView([OFFICE_LAT, OFFICE_LNG], 12);
        addBestTileLayer(map);

        // Add Office Marker
        L.marker([OFFICE_LAT, OFFICE_LNG], {icon: blueIcon}).addTo(map)
            .bindPopup("Lokasi Kantor (Start)")
            .openPopup();
    }

    async function updateMapRoute(invoiceIds) {
        if (!map) return;
        await ensureRoutingReady();

        if (routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }

        mapMarkers.forEach(m => map.removeLayer(m));
        mapMarkers = [];

        if (!invoiceIds || invoiceIds.length === 0) {
            document.getElementById('modal_est_distance').value = 0;
            if(document.getElementById('modal_est_distance_outbound')) document.getElementById('modal_est_distance_outbound').value = 0;
            if(document.getElementById('modal_est_distance_return')) document.getElementById('modal_est_distance_return').value = 0;
            calculateTotalCost();
            return;
        }

        const waypoints = [L.latLng(OFFICE_LAT, OFFICE_LNG)];

        for (const id of invoiceIds) {
            try {
                // Try to get data from TomSelect options
                const opt = invoiceSelect.options[id];
                let lat, lng, address, title;

                if (opt) {
                    lat = parseFloat(opt.lat);
                    lng = parseFloat(opt.lng);
                    address = opt.address;
                    title = opt.mitra_name || opt.text;
                }

                if (lat && lng) {
                    const latLng = L.latLng(lat, lng);
                    waypoints.push(latLng);
                    const m = L.marker(latLng, {icon: redIcon}).addTo(map).bindPopup(`<b>${title}</b><br>${address}`);
                    mapMarkers.push(m);
                } else if (address) {
                    // Fallback to Geocoding if coordinates missing but address exists
                    const coords = await geocodeAddress(address);
                    if (coords) {
                        const latLng = L.latLng(coords.lat, coords.lon);
                        waypoints.push(latLng);

                        const m = L.marker(latLng, {icon: redIcon}).addTo(map)
                            .bindPopup(`<b>${title}</b><br>${address} (Geocoded)`);
                        mapMarkers.push(m);
                    }
                } else {
                    // Fallback: Fetch via API if not in options (e.g. from search)
                    const res = await fetch(`{{ url('api/invoice-api') }}/${id}`);
                    const json = await res.json();
                    if (json.success && json.data.mitra && json.data.mitra.alamat) {
                        const address = json.data.mitra.alamat;
                        const coords = await geocodeAddress(address);
                        if (coords) {
                            const latLng = L.latLng(coords.lat, coords.lon);
                            waypoints.push(latLng);
                            const m = L.marker(latLng, {icon: redIcon}).addTo(map)
                                .bindPopup(`<b>${json.data.mitra.nama}</b><br>${address}`);
                            mapMarkers.push(m);
                        }
                    }
                }
            } catch (e) {
                console.error(e);
            }
        }

        if (waypoints.length > 1) {
            // Add Return Trip (Back to Office)
            waypoints.push(L.latLng(OFFICE_LAT, OFFICE_LNG));

            routingControl = L.Routing.control({
                waypoints: waypoints,
                routeWhileDragging: false,
                show: false, // Hide instruction list
                addWaypoints: false,
                createMarker: function() { return null; }
            }).on('routesfound', function (e) {
                const routes = e.routes;
                const route = routes[0];
                const summary = route.summary;

                // Total Distance (Route)
                const totalKm = (summary.totalDistance / 1000).toFixed(2);
                document.getElementById('modal_est_distance').value = totalKm;
                
                // Calculate Breakdown (Outbound vs Return)
                // Use Geometric Estimation for Return Trip to ensure reliability
                // Logic: Return = Distance from Last Invoice to Office * 1.4 (Road Factor)
                let returnKm = 0;
                let outboundKm = totalKm;

                try {
                    if (waypoints.length >= 2) {
                         const lastInvPt = waypoints[waypoints.length - 2];
                         const officePt = waypoints[waypoints.length - 1];
                         
                         // distanceTo returns meters
                         const distMeters = lastInvPt.distanceTo(officePt); 
                         
                         // Apply 1.4 factor for road distance approximation
                         // This is more reliable than trying to split the OSRM route instructions
                         const returnKmVal = (distMeters * 1.4 / 1000); 
                         
                         returnKm = returnKmVal.toFixed(2);
                         
                         // Outbound is Total - Return
                         // If Total is very large (e.g. 4000km), Return might be small (e.g. 10km)
                         // This ensures Outbound captures the bulk of the trip
                         const outboundVal = parseFloat(totalKm) - returnKmVal;
                         outboundKm = (outboundVal > 0 ? outboundVal : 0).toFixed(2);
                    }
                } catch (err) {
                    console.error("Distance breakdown error:", err);
                    // Fallback: 50-50 split if calculation fails completely
                    returnKm = (totalKm / 2).toFixed(2);
                    outboundKm = (totalKm / 2).toFixed(2);
                }

                document.getElementById('modal_est_distance_outbound').value = outboundKm;
                document.getElementById('modal_est_distance_return').value = returnKm;

                calculateFuelCost();
            }).addTo(map);
        }
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
        const z = mapInstance.getZoom ? mapInstance.getZoom() : 12;
        const c = mapInstance.getCenter ? mapInstance.getCenter() : L.latLng(-6.966667, 110.416664);
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

    // Simple Nominatim Geocoding (Rate Limited!)
    const geocodeCache = {};
    async function geocodeAddress(address) {
        if (geocodeCache[address]) return geocodeCache[address];

        try {
            // Delay to respect rate limit
            await new Promise(r => setTimeout(r, 1000));

            const res = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
            const data = await res.json();
            if (data && data.length > 0) {
                const result = {
                    lat: parseFloat(data[0].lat),
                    lon: parseFloat(data[0].lon)
                };
                geocodeCache[address] = result;
                return result;
            }
        } catch (e) {
            console.error("Geocode error", e);
        }
        return null;
    }

    function onFleetChange() {
        const fleetEl = document.getElementById('modal_fleet_id');
        const selectedOpt = fleetEl.options[fleetEl.selectedIndex];

        if (selectedOpt && selectedOpt.value) {
            const km = parseFloat(selectedOpt.dataset.km) || 0;
            const price = parseFloat(selectedOpt.dataset.price) || 0;
            const lastOdo = parseFloat(selectedOpt.dataset.last_odo) || 0;
            const leftover = parseFloat(selectedOpt.dataset.leftover) || 0;

            document.getElementById('modal_km_per_liter').value = km;
            document.getElementById('modal_liter_price').value = price;
            document.getElementById('modal_odo_start').value = lastOdo;
            document.getElementById('modal_fuel_leftover').value = leftover;
            document.getElementById('last_odo_hint').innerText = `Odometer terakhir: ${lastOdo} KM`;
        } else {
            document.getElementById('modal_km_per_liter').value = '';
            document.getElementById('modal_liter_price').value = '';
            document.getElementById('modal_odo_start').value = '';
            document.getElementById('modal_fuel_leftover').value = '';
            document.getElementById('last_odo_hint').innerText = '';
        }
        calculateFuelCost();
    }

    function calculateFuelCost() {
        const distance = parseFloat(document.getElementById('modal_est_distance').value) || 0;
        const kmPerLiter = parseFloat(document.getElementById('modal_km_per_liter').value) || 0;
        const pricePerLiter = parseFloat(document.getElementById('modal_liter_price').value) || 0;
        const leftover = parseFloat(document.getElementById('modal_fuel_leftover').value) || 0;

        let cost = 0;
        if (distance > 0 && kmPerLiter > 0 && pricePerLiter > 0) {
            const litersNeeded = distance / kmPerLiter;
            cost = litersNeeded * pricePerLiter;
        }

        document.getElementById('modal_est_fuel_cost').value = cost.toFixed(2);
        
        // Calculate Deduction
        const deduction = leftover * pricePerLiter;
        if(document.getElementById('modal_fuel_deduction')) {
            document.getElementById('modal_fuel_deduction').value = deduction.toFixed(2);
        }

        calculateTotalCost();
    }

    function addCostRow(name = '', amount = 0) {
        const tbody = document.getElementById('costTableBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm border-0 cost-name" placeholder="Nama Biaya" value="${name}"></td>
            <td><input type="number" class="form-control form-control-sm border-0 text-end cost-amount" placeholder="0" value="${amount}" oninput="calculateTotalCost()"></td>
            <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove(); calculateTotalCost()"><i class="fa fa-times"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function calculateTotalCost() {
        let total = parseFloat(document.getElementById('modal_est_fuel_cost').value) || 0;
        const deduction = parseFloat(document.getElementById('modal_fuel_deduction') ? document.getElementById('modal_fuel_deduction').value : 0) || 0;

        document.querySelectorAll('.cost-amount').forEach(inp => {
            total += parseFloat(inp.value) || 0;
        });

        // Subtract deduction
        total -= deduction;
        if (total < 0) total = 0; // Cannot give negative cash

        estCost = total;

        document.getElementById('disp_total_cost').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    async function saveDeliveryOrder() {
        const mode = document.getElementById('form_mode').value;
        const id = document.getElementById('edit_do_id').value;
        const date = document.getElementById('modal_delivery_date').value;
        const notes = document.getElementById('modal_notes').value;
        const fleetId = document.getElementById('modal_fleet_id').value;
        const invoiceIds = invoiceSelect ? invoiceSelect.getValue() : [];

        if (!date) return alert("Tanggal Pengiriman wajib diisi!");
        if (invoiceIds.length === 0) return alert("Pilih minimal satu invoice!");
        if (!fleetId) return alert("Pilih Armada!");

        // 1. Create/Update DO
        const doPayload = {
            delivery_date: date,
            notes: notes
        };

        try {
            const baseUrl = "{{ url('api/delivery-order-api') }}";
            const url = mode === 'create' ? baseUrl : `${baseUrl}/${id}`;
            const method = mode === 'create' ? 'POST' : 'PUT';

            // Step 1: Save DO
            const resDO = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(doPayload)
            });
            const jsonDO = await resDO.json();

            if (!jsonDO.success) throw new Error(jsonDO.message);
            const doId = jsonDO.data.id;

            // Step 2: Assign Invoices
            let currentIds = [];
            if (mode === 'edit') {
                currentIds = currentDOInvoices.map(i => i.invoice_id.toString());
            }

            // Ensure invoiceIds is an array
            const selectedIds = Array.isArray(invoiceIds) ? invoiceIds : [invoiceIds];

            const toAdd = selectedIds.filter(id => !currentIds.includes(id));
            const toRemove = currentDOInvoices.filter(i => !selectedIds.includes(i.invoice_id.toString()));

            console.log("Saving DO:", doId);
            console.log("Invoices to add:", toAdd);

            for (const invId of toAdd) {
                const resInv = await fetch("{{ url('api/delivery-order-invoice-api') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        delivery_order_id: doId,
                        invoice_id: invId,
                        delivery_sequence: 1,
                        total_cost: estCost || 0
                    })
                });
                const jsonInv = await resInv.json();
                if (!jsonInv.success) {
                    console.error("Failed to save invoice:", jsonInv);
                    throw new Error("Gagal menyimpan invoice ID " + invId + ": " + jsonInv.message);
                }
            }

            for (const item of toRemove) {
                await fetch(`{{ url('api/delivery-order-invoice-api') }}/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });
            }

            // Step 3: Assign Fleet with Costs
            const additionalCosts = [];
            document.querySelectorAll('#costTableBody tr').forEach(tr => {
                const nameInp = tr.querySelector('.cost-name');
                const amountInp = tr.querySelector('.cost-amount');
                if (nameInp && amountInp && nameInp.value) {
                    additionalCosts.push({
                        name: nameInp.value,
                        amount: parseFloat(amountInp.value) || 0
                    });
                }
            });

            const fleetData = {
                delivery_order_id: doId,
                fleet_id: fleetId,
                driver_id: document.getElementById('modal_driver_id').value,
                estimated_distance_km: document.getElementById('modal_est_distance').value,
                estimated_fuel_cost: document.getElementById('modal_est_fuel_cost').value,
                additional_costs: additionalCosts,
                odo_start: document.getElementById('modal_odo_start').value,
                cash_amount: estCost || 0
            };

            const resFleet = await fetch("{{ url('api/delivery-order-fleet-api') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(fleetData)
            });
            const jsonFleet = await resFleet.json();

            if (!jsonFleet.success) throw new Error("Gagal menyimpan data armada: " + jsonFleet.message);

            // Success!
            alert('Delivery Order berhasil disimpan.');

            // Close Modal
            const modalEl = document.getElementById('deliveryOrderModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // Refresh List
            if (typeof loadInvoiceData === 'function') {
                loadInvoiceData();
            }

            // Open Print Preview
            if (typeof openPrintPreview === 'function') {
                openPrintPreview(doId);
            } else {
                console.warn('openPrintPreview function not found');
            }

        } catch (e) {
            alert("Terjadi kesalahan: " + e.message);
            console.error(e);
        }
    }

    async function openDeliveryOrderModal(id = null) {
        await ensureBootstrapReady();
        const modal = new bootstrap.Modal(document.getElementById('deliveryOrderModal'));
        document.getElementById('deliveryOrderForm').reset();
        document.getElementById('disp_total_cost').innerText = 'Rp 0';
        document.getElementById('costTableBody').innerHTML = `
            <tr class="bg-light">
                <td class="align-middle">Biaya BBM (Estimasi)</td>
                <td><input type="number" id="modal_est_fuel_cost" class="form-control form-control-sm border-0 bg-transparent text-end fw-bold" readonly value="0"></td>
                <td></td>
            </tr>`;

        currentDOInvoices = [];
        await initModalScripts();

        if (id) {
            document.getElementById('form_mode').value = 'edit';
            document.getElementById('edit_do_id').value = id;
            document.getElementById('modalTitle').innerText = 'Edit Delivery Order';

            try {
                const fleetRes = await fetch("{{ route('delivery-order-fleet-api.by-do', ':id') }}".replace(':id', id))
                    .then(r => r.json());
                if (fleetRes.success && fleetRes.data) {
                    const f = fleetRes.data;
                    document.getElementById('modal_fleet_id').value = f.fleet_id;
                    onFleetChange();
                    document.getElementById('modal_driver_id').value = f.driver_id;
                    if(f.odo_start) document.getElementById('modal_odo_start').value = f.odo_start;
                    document.getElementById('modal_est_distance').value = f.estimated_distance_km || 0;
                    document.getElementById('modal_est_fuel_cost').value = f.estimated_fuel_cost || 0;
                    if (f.additional_costs && Array.isArray(f.additional_costs)) {
                        f.additional_costs.forEach(c => addCostRow(c.name, c.amount));
                    }
                    calculateTotalCost();
                }

                // Fetch DO & Invoices
                const doResponse = await fetch(`{{ url('api/delivery-order-api') }}/${id}`);
                const doJson = await doResponse.json();
                if (doJson.success) {
                    const d = doJson.data;
                    document.getElementById('modal_do_number').value = d.delivery_order_number;
                    document.getElementById('modal_delivery_date').value = d.delivery_date;
                    document.getElementById('modal_notes').value = d.notes;

                    // Load Invoices into TomSelect
                    if (d.invoices && d.invoices.length > 0) {
                        const invIds = d.invoices.map(i => i.invoice_id.toString());
                        invoiceSelect.setValue(invIds);

                        // Store current state for diffing in Edit mode
                        currentDOInvoices = d.invoices.map(i => ({
                            id: i.id,
                            invoice_id: i.invoice_id
                        }));
                    }
                }
            } catch (e) {
                console.error(e);
            }
        } else {
            document.getElementById('form_mode').value = 'create';
            document.getElementById('modalTitle').innerText = 'Delivery Order Baru';
            // Clear TomSelect
            if (invoiceSelect) invoiceSelect.clear();
        }

        modal.show();
        // Resize map after modal show
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 500);
    }

    function ensureBootstrapReady() {
        if (typeof bootstrap !== 'undefined') return Promise.resolve(true);
        return new Promise((resolve) => {
            const local = document.createElement('script');
            local.src = '/assets/libs/bootstrap/js/bootstrap.bundle.min.js';
            local.onload = () => resolve(true);
            local.onerror = () => {
                const cdn = document.createElement('script');
                cdn.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js';
                cdn.onload = () => resolve(true);
                cdn.onerror = () => resolve(false);
                document.head.appendChild(cdn);
            };
            document.head.appendChild(local);
        });
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
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            });
        }
    }
</script>
