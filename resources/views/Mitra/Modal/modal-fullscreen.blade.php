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
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold" data-bs-dismiss="modal">Batalkan</button>
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
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i class="fa fa-building me-2 text-primary"></i>Informasi Perusahaan</h6>
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="f-label">Tipe Mitra *</label>
                                        <div class="d-flex gap-3 mt-1 align-items-center flex-wrap">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra" id="typeSupplier" value="Supplier" checked>
                                                <label class="form-check-label small fw-bold" for="typeSupplier">Supplier / Vendor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra" id="typeClient" value="Client">
                                                <label class="form-check-label small fw-bold" for="typeClient">Client / Customer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_mitra" id="typeBoth" value="Both">
                                                <label class="form-check-label small fw-bold" for="typeBoth">Keduanya</label>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2 pt-1 border-top">
                                            <input class="form-check-input" type="checkbox" id="modal_is_cash_customer">
                                            <label class="form-check-label small fw-bold text-primary" for="modal_is_cash_customer">
                                                Apakah Pelanggan Cash?
                                            </label>
                                            <div class="form-text x-small text-muted" style="font-size: 11px;">
                                                Pelanggan cash membeli langsung (tanpa piutang/tempo).
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="f-label">Bentuk Usaha</label>
                                            <select id="modal_badan_usaha" class="form-select f-input">
                                                <option value="PT">PT</option>
                                                <option value="CV">CV</option>
                                                <option value="UD">UD</option>
                                                <option value="Perorangan">Perorangan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="f-label">Nama Perusahaan / Mitra *</label>
                                            <input type="text" id="modal_nama" class="form-control f-input fw-bold" placeholder="Nama Lengkap">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">Nomor ID Mitra (Opsional)</label>
                                        <input type="text" id="modal_nomor_mitra" class="form-control f-input bg-light" placeholder="Auto-generated jika kosong">
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">No. KTP / NPWP</label>
                                        <input type="text" id="modal_ktp_npwp" class="form-control f-input" placeholder="Nomor Identitas (NIK / NPWP)">
                                    </div>

                                    <div class="mt-3">
                                        <label class="f-label">Alamat Lengkap</label>
                                        <textarea id="modal_alamat" class="form-control f-input" rows="3" placeholder="Alamat kantor / gudang..."></textarea>
                                    </div>

                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label class="f-label">Email Perusahaan</label>
                                            <input type="email" id="modal_email" class="form-control f-input" placeholder="email@company.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">No. Telepon Kantor</label>
                                            <input type="text" id="modal_no_hp" class="form-control f-input" placeholder="021-xxxxxx">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Kontak Person & Akun -->
                        <div class="col-lg-5">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i class="fa fa-user-tie me-2 text-primary"></i>Kontak Person (PIC)</h6>
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="f-label">Nama PIC</label>
                                            <input type="text" id="modal_kontak_nama" class="form-control f-input" placeholder="Nama Narahubung">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="f-label">Jabatan</label>
                                            <input type="text" id="modal_kontak_jabatan" class="form-control f-input" placeholder="Misal: Manager">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">No. HP PIC</label>
                                            <input type="text" id="modal_kontak_no_hp" class="form-control f-input" placeholder="08xxxxxxxxxx">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label">Email PIC</label>
                                            <input type="email" id="modal_kontak_email" class="form-control f-input" placeholder="pic@email.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i class="fa fa-wallet me-2 text-primary"></i>Pengaturan Akun (Finance)</h6>
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="alert alert-soft-info py-2 px-3 small border-0 mb-3">
                                        <i class="fa fa-info-circle me-1"></i> Akun Hutang/Piutang diset otomatis ke default sistem. Hubungi admin finance untuk mengubah.
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="f-label text-muted">Akun Hutang (AP)</label>
                                            <input type="text" class="form-control f-input bg-light" value="Default AP" disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label text-muted">Akun Piutang (AR)</label>
                                            <input type="text" class="form-control f-input bg-light" value="Default AR" disabled>
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
    .ls-1 { letter-spacing: 1px; }
    .f-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block; }
    .f-input { border: 1px solid #e2e8f0; padding: 0.6rem 0.8rem; border-radius: 6px; font-size: 13px; transition: all 0.2s; }
    .f-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
</style>

<script>
    async function openMitraModal(id = null) {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mitraModal'));
        document.getElementById('mitraForm').reset();
        
        if (id) {
            document.getElementById('mitraModalTitle').innerText = 'Edit Data Mitra';
            document.getElementById('mitra_form_mode').value = 'edit';
            document.getElementById('edit_mitra_id').value = id;

            try {
                const res = await fetch(`/api/mitra-api/${id}`);
                const json = await res.json();
                if(json.success) {
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
                }
            } catch(e) { console.error(e); alert('Gagal load data mitra'); return; }

        } else {
            document.getElementById('mitraModalTitle').innerText = 'Tambah Mitra Baru';
            document.getElementById('mitra_form_mode').value = 'create';
            document.getElementById('modal_nomor_mitra').value = '';
            document.getElementById('modal_ktp_npwp').value = '';
            document.getElementById('modal_is_cash_customer').checked = false;
            // Auto generate ID logic could be here on backend usually
        }

        modal.show();
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
            kontak_email: document.getElementById('modal_kontak_email').value
        };

        if(!payload.nama) { alert('Nama Mitra wajib diisi!'); return; }

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
            
            if(result.success) {
                alert('Data Mitra berhasil disimpan.');
                bootstrap.Modal.getInstance(document.getElementById('mitraModal')).hide();
                if(typeof loadMitraData === 'function') loadMitraData();
                // Trigger event for external listeners (like Sales page)
                document.dispatchEvent(new CustomEvent('mitra-saved', { detail: result.data }));
            } else {
                alert('Gagal: ' + (JSON.stringify(result.errors || result.message)));
            }
        } catch(e) {
            console.error(e); alert('Terjadi kesalahan sistem.');
        }
    }
</script>
