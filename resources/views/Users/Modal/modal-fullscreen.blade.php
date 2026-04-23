<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5" id="userModalTitle">Tambah User Baru</h5>
            </div>

            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="user_form_mode" value="create">
                    <input type="hidden" id="edit_user_id" value="">

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="dr-label mb-2">Nama Lengkap *</label>
                            <input type="text" id="modal_user_name" class="dr-input fw-bold" placeholder="Nama Lengkap">
                        </div>
                        <div class="col-md-6">
                            <label class="dr-label mb-2">Username *</label>
                            <input type="text" id="modal_user_username" class="dr-input" placeholder="Username Login">
                        </div>
                        <div class="col-md-6">
                            <label class="dr-label mb-2">Email *</label>
                            <input type="email" id="modal_user_email" class="dr-input" placeholder="email@example.com">
                        </div>
                        <div class="col-md-12">
                            <label class="dr-label mb-2">Password</label>
                            <div class="p-3 border mb-3" style="background-color: var(--dr-panel-soft); border-radius: var(--dr-radius-sm);">
                                <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                                    <i class="iconoir-info-empty text-primary"></i>
                                    <span>Kosongkan password jika tidak ingin mengubah (saat edit).</span>
                                </div>
                                <input type="password" id="modal_user_password" class="dr-input bg-white" placeholder="Password baru...">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button type="button" class="dr-btn-modal dr-btn-modal-cancel grow justify-content-center" data-bs-dismiss="modal">
                    <i class="iconoir-xmark"></i> Batal
                </button>
                <button type="button" class="dr-btn-modal dr-btn-modal-save grow justify-content-center" onclick="saveUser()">
                    <i class="iconoir-check"></i> Simpan User
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            
            btn.disabled = false;
            btn.innerHTML = originalHtml;

            if(result.success) {
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
