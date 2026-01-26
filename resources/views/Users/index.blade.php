@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Manajemen Pengguna (User)</h4>
                    <p class="text-muted small mb-0">Kelola akun dan akses login sistem.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openUserModal()">
                        <i class="fa fa-plus-circle me-1"></i> Tambah User Baru
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                   <h6 class="mb-0 fw-bold text-dark">Daftar Pengguna</h6>
                </div>

                <div class="card-body p-4 bg-white">
                     <!-- Filter Section -->
                     <div class="row g-2 mb-4 align-items-end bg-light p-3 rounded border">
                        <div class="col-lg-5">
                            <label class="f-label">Pencarian</label>
                            <div class="input-group input-group-finance">
                                <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                <input type="text" id="filter-search" class="form-control border-start-0 ps-0 shadow-none" placeholder="Nama, Username, atau Email...">
                            </div>
                        </div>
                        <div class="col-lg-7 text-end">
                            <button onclick="loadUserData()" class="btn btn-dark fw-bold py-2 px-4 shadow-sm btn-sm">FILTER</button>
                            <button onclick="resetFilter()" class="btn btn-light border fw-bold text-dark py-2 btn-sm">RESET</button>
                        </div>
                    </div>

                    <!-- Table Structure -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="userTable">
                            <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th width="50" class="ps-4 text-center">#</th>
                                    <th width="200">Nama Lengkap</th>
                                    <th width="150">Username</th>
                                    <th width="200">Email</th>
                                    <th width="120" class="text-center">Status</th>
                                    <th width="150" class="text-center">Terdaftar</th>
                                    <th width="200" class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody" class="border-top-0">
                                <!-- Rows injected by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span id="pagination-info" class="text-muted small fw-medium"></span>
                        <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Users.Modal.modal-fullscreen') 
@endsection

@push('css')
<style>
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    
    #userTable thead th { border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; background: #f8fafc; padding-top: 12px; padding-bottom: 12px; }
    #userTable tbody td { font-size: 13px; color: #334155; padding-top: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
    .clickable-row { cursor: pointer; transition: background-color 0.1s; }
    .clickable-row:hover { background-color: #f1f5f9 !important; }
    
    .f-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
    .f-active { background: #dcfce7; color: #166534; }
</style>
@endpush

@push('js')
<script>
    const USER_API_URL = '/api/user-api';

    async function loadUserData(url = USER_API_URL) {
        const tbody = document.getElementById('userTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data user...</p></td></tr>';

        try {
            const searchVal = document.getElementById('filter-search').value;
            let finalUrl = url;

            if (searchVal) {
                finalUrl = `/api/user-api/search/${encodeURIComponent(searchVal)}`;
            }

            const response = await fetch(finalUrl);
            const result = await response.json();

            if (result.success) {
                renderUserList(result.data.data ? result.data.data : result.data);
                renderPagination(result.data);
            }
        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger p-4">Gagal memuat data.</td></tr>';
        }
    }

    function renderUserList(data) {
        const tbody = document.getElementById('userTableBody');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center p-5 text-muted fst-italic">Data user tidak ditemukan.</td></tr>';
            return;
        }

        data.forEach((item, index) => {
            const tr = `
                <tr class="clickable-row">
                    <td class="text-center ps-4 text-muted small">${index + 1}</td>
                    <td class="fw-bold text-dark">${item.name}</td>
                    <td class="text-muted">${item.username}</td>
                    <td class="text-muted"><i class="fa fa-envelope me-1 small"></i> ${item.email}</td>
                    <td class="text-center"><span class="f-badge f-active">Active</span></td>
                    <td class="text-center text-muted small">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-primary me-1" onclick="openUserModal(${item.id})"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-danger" onclick="deleteUser(${item.id})"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', tr);
        });
    }

    async function deleteUser(id) {
        if (!confirm('Hapus user ini?')) return;
        try {
            const res = await fetch(`${USER_API_URL}/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const dat = await res.json();
            if (dat.success) { alert('Berhasil dihapus'); loadUserData(); }
            else alert(dat.message);
        } catch (e) { alert('Gagal menghapus'); }
    }

    function renderPagination(meta) {
        const c = document.getElementById('pagination-container'); c.innerHTML = '';
        if(!meta || !meta.links) return; 
        document.getElementById('pagination-info').innerText = `${meta.from||0}-${meta.to||0} dari ${meta.total} data`;
        meta.links.forEach(l => {
             const cls = l.active ? 'bg-primary text-white' : 'bg-white text-dark';
             c.insertAdjacentHTML('beforeend', `<li class="page-item ${!l.url?'disabled':''}"><a class="page-link border-0 mx-1 rounded shadow-sm fw-bold ${cls}" href="#" onclick="loadUserData('${l.url}')">${l.label}</a></li>`);
        });
    }

    function resetFilter() {
        document.getElementById('filter-search').value = '';
        loadUserData();
    }

    document.addEventListener('DOMContentLoaded', () => loadUserData());
</script>
@endpush
