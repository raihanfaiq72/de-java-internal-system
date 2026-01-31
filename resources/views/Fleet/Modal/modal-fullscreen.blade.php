<div class="modal fade" id="fleetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white p-2 rounded-2 me-3">
                        <i class="fa fa-user-lock fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="fleetModalTitle">Formulir Armada</h5>
                        <p class="text-muted small mb-0">Kelola armada perusahaan</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold"
                        data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveFleet()">
                        <i class="fa fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">
                <form id="fleetForm">
                    <input type="hidden" id="fleet_form_mode" value="create">
                    <input type="hidden" id="edit_fleet_id" value="">

                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="f-label">Nama Armada *</label>
                                    <input type="text" id="modal_fleet_fleet_name"
                                        class="form-control f-input fw-bold" placeholder="Co: Grand Max">
                                </div>
                                <div class="col-md-6">
                                    <label class="f-label">Tipe Bensin *</label>
                                    <input type="text" id="modal_fleet_fuel_type" class="form-control f-input"
                                        placeholder="Co: Bensin, Solar, Gas">
                                </div>
                                <div class="col-md-6">
                                    <label class="f-label">Konsumsi Bensin (dalam km/L) *</label>
                                    <input type="number" id="modal_fleet_km_per_liter" class="form-control f-input"
                                        placeholder="Co: 5">
                                </div>
                                <div class="col-md-12">
                                    <label class="f-label">Nomor Plat *</label>
                                    <input type="text" id="modal_fleet_license_plate" class="form-control f-input"
                                        placeholder="Co: H 1234 DJV">
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
    // Include Users/Modal/modal-fullscreen specific logic here or in index
    async function openFleetModal(id = null) {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('fleetModal'));
        document.getElementById('fleetForm').reset();

        if (id) {
            document.getElementById('fleetModalTitle').innerText = 'Edit Data User';
            document.getElementById('fleet_form_mode').value = 'edit';
            document.getElementById('edit_fleet_id').value = id;

            try {
                const res = await fetch(`${FLEET_URL}/${id}`);
                const json = await res.json();
                if (json.success) {
                    const data = json.data;
                    document.getElementById('modal_fleet_fleet_name').value = data.fleet_name;
                    document.getElementById('modal_fleet_fuel_type').value = data.fuel_type;
                    document.getElementById('modal_fleet_km_per_liter').value = data.km_per_liter;
                    document.getElementById('modal_fleet_license_plate').value = data.licence_plate;
                }
            } catch (e) {
                console.error(e);
                alert('Gagal load data armada');
                return;
            }

        } else {
            document.getElementById('fleetModalTitle').innerText = 'Tambah Armada Baru';
            document.getElementById('fleet_form_mode').value = 'create';
        }

        modal.show();
    }

    async function saveFleet() {
        const mode = document.getElementById('fleet_form_mode').value;
        const id = document.getElementById('edit_fleet_id').value;
        const url = mode === 'edit' ? `${FLEET_URL}/${id}` : FLEET_URL;
        const method = mode === 'edit' ? 'PUT' : 'POST';

        const payload = {
            fleet_name: document.getElementById('modal_fleet_fleet_name').value,
            fuel_type: document.getElementById('modal_fleet_fuel_type').value,
            license_plate: document.getElementById('modal_fleet_license_plate').value,
            km_per_liter: document.getElementById('modal_fleet_km_per_liter').value
        };

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();

            if (result.success) {
                alert('Data armada berhasil disimpan.');
                bootstrap.Modal.getInstance(document.getElementById('fleetModal')).hide();
                loadFleetsData();
            } else {
                alert('Gagal: ' + (JSON.stringify(result.errors || result.message)));
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan sistem.');
        }
    }
</script>
