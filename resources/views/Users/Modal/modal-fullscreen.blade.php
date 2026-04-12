<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <div class="modal-header bg-white border-bottom px-5 py-4 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white p-2 rounded-2 me-3">
                        <i class="fa fa-user-lock fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="userModalTitle">Formulir User</h5>
                        <p class="text-muted small mb-0">Kelola akun pengguna sistem.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveUser()">
                        <i class="fa fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">
                <form id="userForm">
                    <input type="hidden" id="user_form_mode" value="create">
                    <input type="hidden" id="edit_user_id" value="">

                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="f-label">Nama Lengkap *</label>
                                    <input type="text" id="modal_user_name" class="form-control f-input fw-bold" placeholder="Nama Lengkap">
                                </div>
                                <div class="col-md-6">
                                    <label class="f-label">Username *</label>
                                    <input type="text" id="modal_user_username" class="form-control f-input" placeholder="Username Login">
                                </div>
                                <div class="col-md-6">
                                    <label class="f-label">Email *</label>
                                    <input type="email" id="modal_user_email" class="form-control f-input" placeholder="email@example.com">
                                </div>
                                <div class="col-md-12">
                                    <div class="alert alert-soft-warning border-0 small py-2 mb-2">
                                        <i class="fa fa-info-circle me-1"></i> Kosongkan password jika tidak ingin mengubah (saat edit).
                                    </div>
                                    <label class="f-label">Password</label>
                                    <input type="password" id="modal_user_password" class="form-control f-input" placeholder="Password...">
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
    async function openUserModal(id = null) {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('userModal'));
        document.getElementById('userForm').reset();
        
        if (id) {
            document.getElementById('userModalTitle').innerText = 'Edit Data User';
            document.getElementById('user_form_mode').value = 'edit';
            document.getElementById('edit_user_id').value = id;

            try {
                const res = await fetch(`/api/user-api/${id}`);
                const json = await res.json();
                if(json.success) {
                    const data = json.data;
                    document.getElementById('modal_user_name').value = data.name;
                    document.getElementById('modal_user_username').value = data.username;
                    document.getElementById('modal_user_email').value = data.email;
                    document.getElementById('modal_user_password').value = '';
                }
            } catch(e) { console.error(e); alert('Gagal load data user'); return; }

        } else {
            document.getElementById('userModalTitle').innerText = 'Tambah User Baru';
            document.getElementById('user_form_mode').value = 'create';
        }

        modal.show();
    }

    async function saveUser() {
        const mode = document.getElementById('user_form_mode').value;
        const id = document.getElementById('edit_user_id').value;
        const url = mode === 'edit' ? `/api/user-api/${id}` : '/api/user-api';
        const method = mode === 'edit' ? 'PUT' : 'POST';

        const payload = {
            name: document.getElementById('modal_user_name').value,
            username: document.getElementById('modal_user_username').value,
            email: document.getElementById('modal_user_email').value
        };

        const pass = document.getElementById('modal_user_password').value;
        if(pass) payload.password = pass;

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
                alert('Data User berhasil disimpan.');
                bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                loadUserData();
            } else {
                alert('Gagal: ' + (JSON.stringify(result.errors || result.message)));
            }
        } catch(e) {
            console.error(e); alert('Terjadi kesalahan sistem.');
        }
    }
</script>
