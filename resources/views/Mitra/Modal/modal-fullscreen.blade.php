<div class="modal fade" id="mitraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white p-2 rounded-2 me-3">
                        <i class="fa fa-handshake fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="mitraModalTitle">Formulir Mitra Bisnis</h5>
                        <p class="text-muted small mb-0">Kelola informasi supplier, vendor, atau klien perusahaan.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold"
                        data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveMitra()">
                        <i class="fa fa-save me-2"></i>Simpan Data
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">
                <form id="mitraForm">
                    <input type="hidden" id="mitra_form_mode" value="create">
                    <input type="hidden" id="edit_mitra_id" value="">

                    <div class="row g-4 justify-content-center">
                        <!-- Kolom Kiri: Info Perusahaan -->
                        <div class="col-lg-5">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i
                                    class="fa fa-building me-2 text-primary"></i>Informasi Perusahaan</h6>
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="f-label">Tipe Mitra *</label>
                                        <div class="d-flex gap-3 mt-1 align-items-center flex-wrap">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra"
                                                    id="typeSupplier" value="Supplier" checked>
                                                <label class="form-check-label small fw-bold"
                                                    for="typeSupplier">Supplier / Vendor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra"
                                                    id="typeClient" value="Client">
                                                <label class="form-check-label small fw-bold" for="typeClient">Client /
                                                    Customer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra"
                                                    id="typeBoth" value="Both">
                                                <label class="form-check-label small fw-bold"
                                                    for="typeBoth">Keduanya</label>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2 pt-1 border-top">
                                            <input class="form-check-input" type="checkbox" id="modal_is_cash_customer">
                                            <label class="form-check-label small fw-bold text-primary"
                                                for="modal_is_cash_customer">
                                                Apakah Pelanggan Cash?
                                            </label>
                                            <div class="form-text x-small text-muted" style="font-size: 11px;">
                                                Pelanggan cash membeli langsung (tanpa piutang/tempo).
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="f-label">Bentuk Usaha *</label>
                                            <select id="modal_badan_usaha" class="form-select f-input" required>
                                                <option value="PT">PT</option>
                                                <option value="CV">CV</option>
                                                <option value="UD">UD</option>
                                                <option value="Perorangan">Perorangan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="f-label">Nama Perusahaan / Mitra *</label>
                                            <input type="text" id="modal_nama" class="form-control f-input fw-bold"
                                                placeholder="Nama Lengkap" required>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">Nomor ID Mitra (Opsional)</label>
                                        <input type="text" id="modal_nomor_mitra" class="form-control f-input bg-light"
                                            placeholder="Auto-generated jika kosong">
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">No. KTP / NPWP *</label>
                                        <input type="text" id="modal_ktp_npwp" class="form-control f-input"
                                            placeholder="Nomor Identitas (NIK / NPWP)" required>
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">Alamat Lengkap *</label>
                                        <div class="alert alert-soft-primary border-0 p-3 mb-2 rounded-3">
                                            <div class="d-flex gap-2">
                                                <i class="fa fa-info-circle mt-1 text-primary"></i>
                                                <div>
                                                    <h6 class="fw-bold small text-dark mb-1">Panduan Pengisian Alamat
                                                        OpenStreetMap</h6>
                                                    <p class="small text-muted mb-0"
                                                        style="font-size: 11px; line-height: 1.4;">
                                                        Agar lokasi peta akurat, ikuti panduan berikut:
                                                    </p>
                                                    <ul class="mb-0 ps-3 mt-1 small text-muted"
                                                        style="font-size: 11px;">
                                                        <li>Gunakan format lengkap: <b>Nama Jalan, Kecamatan, Kota,
                                                                Provinsi</b>.</li>
                                                        <li>Contoh: <i>Jl. Jend. Sudirman No. 1, Tanah Abang, Jakarta
                                                                Pusat, DKI Jakarta</i>.</li>
                                                        <li>Setelah mengetik alamat, klik tombol <b>"Cari dari
                                                                Alamat"</b> untuk menitikkan lokasi otomatis.</li>
                                                        <li>Jika titik kurang pas, Anda dapat menggeser peta atau
                                                            mencari lokasi manual di peta.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <textarea id="modal_alamat" class="form-control f-input" rows="3"
                                            placeholder="Masukkan alamat lengkap..." required></textarea>

                                            <!-- Map Section -->
                                            <div class="mt-3">
                                                <label class="f-label d-flex justify-content-between align-items-center">
                                                    <span>Lokasi Peta</span>
                                                    <button type="button"
                                                        class="btn btn-sm btn-link text-decoration-none p-0 fw-bold"
                                                        onclick="geocodeAddress()" style="font-size: 11px;">
                                                        <i class="fa fa-search-location me-1"></i>Cari dari Alamat
                                                    </button>
                                                </label>
                                                <div id="mitraMap" class="w-100 rounded-3 border"
                                                    style="height: 250px; z-index: 0;"></div>
                                                
                                                <input type="hidden" id="modal_latitude">
                                                <input type="hidden" id="modal_longitude">
                                            </div>
                                    </div>

                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label class="f-label">Email Perusahaan</label>
                                            <input type="email" id="modal_email" class="form-control f-input"
                                                placeholder="email@company.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">No. Telepon Kantor</label>
                                            <input type="text" id="modal_no_hp" class="form-control f-input"
                                                placeholder="021-xxxxxx">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Kontak Person & Akun -->
                        <div class="col-lg-5">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i
                                    class="fa fa-user-tie me-2 text-primary"></i>Kontak Person (PIC)</h6>
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="f-label">Nama PIC *</label>
                                            <input type="text" id="modal_kontak_nama" class="form-control f-input"
                                                placeholder="Nama Narahubung" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="f-label">Jabatan</label>
                                            <input type="text" id="modal_kontak_jabatan" class="form-control f-input"
                                                placeholder="Misal: Manager">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">No. HP PIC *</label>
                                            <input type="text" id="modal_kontak_no_hp" class="form-control f-input"
                                                placeholder="08xxxxxxxxxx" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">Email PIC</label>
                                            <input type="email" id="modal_kontak_email" class="form-control f-input"
                                                placeholder="pic@email.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1 d-none"><i
                                    class="fa fa-wallet me-2 text-primary"></i>Pengaturan Akun (Finance)</h6>
                            <div class="card border-0 shadow-sm rounded-3 d-none">
                                <div class="card-body p-4">
                                    <div class="alert alert-soft-info py-2 px-3 small border-0 mb-3">
                                        <i class="fa fa-info-circle me-1"></i> Akun Hutang/Piutang diset otomatis ke
                                        default sistem. Hubungi admin finance untuk mengubah.
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="f-label text-muted">Akun Hutang (AP)</label>
                                            <input type="text" class="form-control f-input bg-light" value="Default AP"
                                                disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label text-muted">Akun Piutang (AR)</label>
                                            <input type="text" class="form-control f-input bg-light" value="Default AR"
                                                disabled>
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

<style>
    .ls-1 {
        letter-spacing: 1px;
    }

    .f-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }

    .f-input {
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.8rem;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.2s;
    }

    .f-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
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
