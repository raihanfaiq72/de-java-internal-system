<div class="modal fade" id="mitraModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 bg-white">
            <!-- Header -->
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top d-flex align-items-center justify-content-between" style="padding: 20px 24px !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 40px; height: 40px;">
                        <i class="iconoir-group" style="font-size: 20px;"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                        <h5 class="dr-title mb-0" id="mitraModalTitle" style="line-height: 1.2;">Formulir Mitra Bisnis</h5>
                        <p class="dr-subtitle mb-0" style="line-height: 1.2;">Kelola informasi supplier, vendor, atau klien perusahaan.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                        <i class="iconoir-xmark text-danger"></i> Batalkan
                    </button>
                    <button type="button" class="dr-btn-modal" onclick="saveMitra()">
                        <i class="iconoir-check text-success"></i> Simpan Mitra
                    </button>
                </div>
            </div>

            <div class="modal-body p-0 bg-white">
                <div class="container py-5">
                    <form id="mitraForm">
                        <input type="hidden" id="mitra_form_mode" value="create">
                        <input type="hidden" id="edit_mitra_id" value="">

                        <div class="row g-4">
                            <!-- Section 1: Informasi Dasar -->
                            <div class="col-lg-7">
                                <div class="bg-white p-4 border rounded-3">
                                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                        <i class="iconoir-box-iso text-dark me-2 fs-5"></i>
                                        <h6 class="fw-bold mb-0">Informasi Perusahaan</h6>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="dr-label mb-2">Tipe Mitra <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-4 mt-2">
                                            <div class="form-check custom-radio">
                                                <input class="form-check-input" type="radio" name="tipe_mitra" id="typeSupplier" value="Supplier" checked>
                                                <label class="form-check-label fw-medium" for="typeSupplier">Supplier / Vendor</label>
                                            </div>
                                            <div class="form-check custom-radio">
                                                <input class="form-check-input" type="radio" name="tipe_mitra" id="typeClient" value="Client">
                                                <label class="form-check-label fw-medium" for="typeClient">Client / Customer</label>
                                            </div>
                                            <div class="form-check custom-radio">
                                                <input class="form-check-input" type="radio" name="tipe_mitra" id="typeBoth" value="Both">
                                                <label class="form-check-label fw-medium" for="typeBoth">Keduanya</label>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="modal_is_cash_customer">
                                                <label class="form-check-label fw-medium text-dark" for="modal_is_cash_customer">
                                                    Pelanggan Cash (Tanpa Piutang)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="dr-label">Badan Usaha <span class="text-danger">*</span></label>
                                            <select id="modal_badan_usaha" class="dr-input" required>
                                                <option value="PT">PT</option>
                                                <option value="CV">CV</option>
                                                <option value="UD">UD</option>
                                                <option value="Perorangan">Perorangan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="dr-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                            <input type="text" id="modal_nama" class="dr-input fw-bold" placeholder="Nama Lengkap" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label">No. ID Mitra (Opsional)</label>
                                            <input type="text" id="modal_nomor_mitra" class="dr-input" placeholder="Auto-generated">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label">No. KTP / NPWP <span class="text-danger">*</span></label>
                                            <input type="text" id="modal_ktp_npwp" class="dr-input" placeholder="NIK / NPWP" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="dr-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                            <textarea id="modal_alamat" class="dr-input" rows="3" placeholder="Masukkan alamat lengkap..." required></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label">Email Kantor</label>
                                            <input type="email" id="modal_email" class="dr-input" placeholder="office@company.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label">Telepon Kantor</label>
                                            <input type="text" id="modal_no_hp" class="dr-input" placeholder="021-xxxxxx">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Kontak PIC & Peta -->
                            <div class="col-lg-5">
                                <div class="bg-white p-4 mb-4 border rounded-3">
                                    <div class="d-flex align-items-center mb-4">
                                        <i class="iconoir-user-badge text-dark me-2" style="font-size: 20px;"></i>
                                        <h6 class="fw-bold text-dark mb-0 small text-uppercase ls-1">Kontak Person (PIC)</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="dr-label">Nama PIC <span class="text-danger">*</span></label>
                                            <input type="text" id="modal_kontak_nama" class="dr-input" placeholder="Nama Narahubung" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label">No. HP PIC <span class="text-danger">*</span></label>
                                            <input type="text" id="modal_kontak_no_hp" class="dr-input font-monospace" placeholder="08xxxxxxxxxx" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="dr-label">Jabatan</label>
                                            <input type="text" id="modal_kontak_jabatan" class="dr-input" placeholder="Manager/Sales/Dll">
                                        </div>
                                        <div class="col-12">
                                            <label class="dr-label">Email PIC</label>
                                            <input type="email" id="modal_kontak_email" class="dr-input" placeholder="pic@email.com">
                                        </div>
                                        <div class="col-12">
                                            <label class="dr-label">Salesperson Penanggung Jawab</label>
                                            <select id="modal_salesperson_id" class="dr-input">
                                                <option value="0">Tanpa Sales Person</option>
                                                @foreach ($salesUsers ?? [] as $u)
                                                    <option value="{{ $u->id }}" @if ($u->id == auth()->id()) selected @endif>
                                                        {{ $u->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white p-4 border rounded-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="iconoir-map-pin text-dark me-2 fs-5"></i>
                                            <h6 class="fw-bold mb-0">Lokasi Peta</h6>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light border" onclick="geocodeAddress()">
                                            <i class="iconoir-search-window me-1"></i>Cari dari Alamat
                                        </button>
                                    </div>
                                    <div id="mitraMap" class="w-100 rounded-3 border" style="height: 250px; z-index: 0;"></div>
                                    <input type="hidden" id="modal_latitude">
                                    <input type="hidden" id="modal_longitude">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer removed because actions moved to header -->
        </div>
    </div>
</div>

<style>
    #mitraModal .modal-content { box-shadow: none !important; }
    #mitraModal .dr-card { border: 1px solid #f1f5f9; box-shadow: none !important; }
    .custom-radio .form-check-input:checked { background-color: var(--dr-primary); border-color: var(--dr-primary); }
</style>

<script>
    let mitraMap = null;
    let mitraMarker = null;
    let leafletIconFixed = false;

    function fixLeafletDefaultIcon() {
        if (leafletIconFixed) return;
        if (!window.L || !L.Icon) {
            setTimeout(fixLeafletDefaultIcon, 200);
            return;
        }
        leafletIconFixed = true;
        delete L.Icon.Default.prototype._getIconUrl;
        L.Icon.Default.mergeOptions({
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        });
    }

    function initMitraMap(lat = -6.200000, lng = 106.816666) {
        // Ensure container exists
        if (!document.getElementById('mitraMap')) return;
        fixLeafletDefaultIcon();

        if (!mitraMap) {
            mitraMap = L.map('mitraMap').setView([lat, lng], 13);
            addBestTileLayer(mitraMap);

            mitraMap.on('click', function (e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });
        } else {
            mitraMap.setView([lat, lng], 13);
        }

        updateMarker(lat, lng);

        // Fix Leaflet sizing issue in modal
        setTimeout(() => {
            mitraMap.invalidateSize();
        }, 500);
    }

    function updateMarker(lat, lng) {
        if (mitraMarker) {
            mitraMarker.setLatLng([lat, lng]);
        } else {
            mitraMarker = L.marker([lat, lng], { draggable: true }).addTo(mitraMap);
            mitraMarker.on('dragend', function (e) {
                const position = e.target.getLatLng();
                document.getElementById('modal_latitude').value = position.lat.toFixed(8);
                document.getElementById('modal_longitude').value = position.lng.toFixed(8);
            });
        }
        document.getElementById('modal_latitude').value = parseFloat(lat).toFixed(8);
        document.getElementById('modal_longitude').value = parseFloat(lng).toFixed(8);
    }

    async function geocodeAddress() {
        const address = document.getElementById('modal_alamat').value;
        if (!address) {
            alert('Mohon isi alamat terlebih dahulu');
            return;
        }

        const btn = document.querySelector('button[onclick="geocodeAddress()"]');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Mencari...';
        btn.disabled = true;

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
            const data = await response.json();

            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                mitraMap.setView([lat, lon], 16);
                updateMarker(lat, lon);
            } else {
                alert('Lokasi tidak ditemukan. Silakan geser marker secara manual.');
            }
        } catch (error) {
            console.error('Geocoding error:', error);
            alert('Gagal koneksi ke layanan peta.');
        } finally {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    }

    function addBestTileLayer(mapInstance) {
        const localUrl = '/assets/tiles/{z}/{x}/{y}.png';
        const remoteUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        let layer = L.tileLayer(localUrl, { attribution: '© OpenStreetMap contributors' });
        let switched = false;
        layer.on('tileerror', function () {
            if (switched) return;
            switched = true;
            mapInstance.removeLayer(layer);
            layer = L.tileLayer(remoteUrl, { attribution: '© OpenStreetMap contributors' });
            layer.addTo(mapInstance);
        });
        layer.addTo(mapInstance);
    }

    async function openMitraModal(id = null) {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mitraModal'));
        document.getElementById('mitraForm').reset();

        // Default Jakarta
        let lat = -6.200000;
        let lng = 106.816666;

        if (id) {
            document.getElementById('mitraModalTitle').innerText = 'Edit Data Mitra';
            document.getElementById('mitra_form_mode').value = 'edit';
            document.getElementById('edit_mitra_id').value = id;

            try {
                const res = await fetch(`/api/mitra-api/${id}`);
                const json = await res.json();
                if (json.success) {
                    const data = json.data;
                    document.querySelector(`input[name="tipe_mitra"][value="${data.tipe_mitra}"]`).checked = true;
                    document.getElementById('modal_is_cash_customer').checked = data.is_cash_customer ? true : false;
                    document.getElementById('modal_badan_usaha').value = data.badan_usaha || 'PT';
                    document.getElementById('modal_nama').value = data.nama;
                    document.getElementById('modal_nomor_mitra').value = data.nomor_mitra || '';
                    document.getElementById('modal_ktp_npwp').value = data.ktp_npwp || '';
                    document.getElementById('modal_alamat').value = data.alamat || '';
                    document.getElementById('modal_email').value = data.email || '';
                    document.getElementById('modal_no_hp').value = data.no_hp || '';

                    document.getElementById('modal_kontak_nama').value = data.kontak_nama || '';
                    document.getElementById('modal_kontak_jabatan').value = data.kontak_jabatan || '';
                    document.getElementById('modal_kontak_no_hp').value = data.kontak_no_hp || '';
                    document.getElementById('modal_kontak_email').value = data.kontak_email || '';
                    document.getElementById('modal_salesperson_id').value = data.salesperson_id || '';

                    if (data.latitude && data.longitude) {
                        lat = parseFloat(data.latitude);
                        lng = parseFloat(data.longitude);
                    }
                }
            } catch (e) { console.error(e); alert('Gagal load data mitra'); return; }

        } else {
            document.getElementById('mitraModalTitle').innerText = 'Tambah Mitra Baru';
            document.getElementById('mitra_form_mode').value = 'create';
            document.getElementById('modal_nomor_mitra').value = '';
            document.getElementById('modal_ktp_npwp').value = '';
            document.getElementById('modal_is_cash_customer').checked = false;
        }

        modal.show();

        // Initialize map after modal show
        document.getElementById('mitraModal').addEventListener('shown.bs.modal', function () {
            initMitraMap(lat, lng);
        }, { once: true });

        // Also call it immediately if modal is already shown (edge case) or use timeout
        setTimeout(() => initMitraMap(lat, lng), 200);
    }

    async function saveMitra() {
        const mode = document.getElementById('mitra_form_mode').value;
        const id = document.getElementById('edit_mitra_id').value;
        const url = mode === 'edit' ? `/api/mitra-api/${id}` : '/api/mitra-api';
        const method = mode === 'edit' ? 'PUT' : 'POST';

        const payload = {
            tipe_mitra: document.querySelector('input[name="tipe_mitra"]:checked').value,
            is_cash_customer: document.getElementById('modal_is_cash_customer').checked ? 1 : 0,
            badan_usaha: document.getElementById('modal_badan_usaha').value,
            nama: document.getElementById('modal_nama').value,
            nomor_mitra: document.getElementById('modal_nomor_mitra').value,
            ktp_npwp: document.getElementById('modal_ktp_npwp').value,
            alamat: document.getElementById('modal_alamat').value,
            email: document.getElementById('modal_email').value,
            no_hp: document.getElementById('modal_no_hp').value,
            kontak_nama: document.getElementById('modal_kontak_nama').value,
            kontak_jabatan: document.getElementById('modal_kontak_jabatan').value,
            kontak_no_hp: document.getElementById('modal_kontak_no_hp').value,
            kontak_email: document.getElementById('modal_kontak_email').value,
            salesperson_id: document.getElementById('modal_salesperson_id').value || null,
            latitude: document.getElementById('modal_latitude').value,
            longitude: document.getElementById('modal_longitude').value
        };

        const missing = [];
        if (!payload.tipe_mitra) missing.push('Tipe Mitra');
        if (!payload.badan_usaha) missing.push('Bentuk Usaha');
        if (!payload.nama) missing.push('Nama Perusahaan / Mitra');
        if (!payload.ktp_npwp) missing.push('No. KTP / NPWP');
        if (!payload.alamat) missing.push('Alamat Lengkap');
        if (!payload.kontak_nama) missing.push('Nama PIC');
        if (!payload.kontak_no_hp) missing.push('No. HP PIC');

        if (missing.length) {
            alert('Mohon lengkapi field wajib:\n- ' + missing.join('\n- '));
            return;
        }

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();

            if (result.success) {
                alert('Data Mitra berhasil disimpan.');
                bootstrap.Modal.getInstance(document.getElementById('mitraModal')).hide();
                if (typeof loadMitraData === 'function') loadMitraData();
                // Trigger event for external listeners (like Sales page)
                document.dispatchEvent(new CustomEvent('mitra-saved', { detail: result.data }));
            } else {
                if (result.errors && typeof result.errors === 'object') {
                    const messages = Object.values(result.errors).flat().join('\n');
                    alert('Validasi gagal:\n' + messages);
                } else {
                    alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
                }
            }
        } catch (e) {
            console.error(e); alert('Terjadi kesalahan sistem.');
        }
    }
</script>
