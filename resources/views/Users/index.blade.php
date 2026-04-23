@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <div class="page-wrapper">
<<<<<<< Updated upstream
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Manajemen Pengguna</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark">Daftar Pengguna</h6>
                        <div class="ms-auto text-end">
                            <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openUserModal()">
                                <i class="fa fa-plus-circle me-1"></i> Tambah User Baru
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4 bg-white">
                        <!-- Filter Section -->
                        <div class="row g-2 mb-4 align-items-end bg-light p-3 rounded border">
                            <div class="col-lg-5">
                                <label class="f-label">Pencarian</label>
                                <div class="input-group input-group-finance">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fa fa-search text-muted"></i></span>
                                    <input type="text" id="filter-search"
                                        class="form-control border-start-0 ps-0 shadow-none"
                                        placeholder="Nama, Username, atau Email...">
                                </div>
                            </div>
                            <div class="col-lg-7 text-end">
                                <button onclick="loadUserData()"
                                    class="btn btn-dark fw-bold py-2 px-4 shadow-sm btn-sm">FILTER</button>
                                <button onclick="resetFilter()"
                                    class="btn btn-light border fw-bold text-dark py-2 btn-sm">RESET</button>
                            </div>
=======
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <i class="iconoir-home dr-breadcrumb-icon"></i>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Manajemen Pengguna</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h4 class="dr-title">Daftar Pengguna</h4>
                            <p class="dr-subtitle">Kelola hak akses dan profil pengguna sistem secara terpusat.</p>
                        </div>
                        <div class="dr-actions">
                            <form id="searchForm" onsubmit="event.preventDefault(); loadUserData();" class="d-flex gap-2">
                                <div class="position-relative" style="width: 250px;">
                                    <i class="iconoir-search text-muted position-absolute start-0 top-50 translate-middle-y ms-3" style="font-size: 16px;"></i>
                                    <input type="text" id="filter-search" class="dr-input ps-5" placeholder="Cari nama, username..." autocomplete="off">
                                </div>
                            </form>
                            <button class="dr-btn dr-btn-primary" onclick="openUserModal()">
                                <i class="iconoir-plus-circle"></i> Tambah User
                            </button>
>>>>>>> Stashed changes
                        </div>

<<<<<<< Updated upstream
                        <!-- Table Structure -->
=======
                    <!-- Main Table Card -->
                    <div class="dr-card p-0 overflow-hidden bg-white mt-4">
>>>>>>> Stashed changes
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="userTable">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
<<<<<<< Updated upstream
                                        <th width="50" class="ps-4 text-center">#</th>
                                        <th width="200">Nama Lengkap</th>
                                        <th width="150">Username</th>
                                        <th width="200">Email</th>
                                        <th width="120" class="text-center">Status</th>
                                        <th width="150" class="text-center">Terdaftar</th>
                                        <th width="200" class="text-end pe-4">Aksi</th>
=======
                                        <th width="60" class="ps-4 text-center">#</th>
                                        <th>Pengguna</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Terdaftar</th>
                                        <th width="150" class="text-end pe-4">Aksi</th>
>>>>>>> Stashed changes
                                    </tr>
                                </thead>
                                <tbody id="userTableBody" class="border-top-0">
                                    <!-- Rows injected by JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
<<<<<<< Updated upstream
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
=======
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 bg-white border-top">
>>>>>>> Stashed changes
                            <span id="pagination-info" class="text-muted small fw-medium"></span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul>
                            </nav>
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
        .f-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        .f-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        #userTable thead th {
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        #userTable tbody td {
            font-size: 13px;
            color: #334155;
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.1s;
        }

        .clickable-row:hover {
            background-color: #f1f5f9 !important;
        }

        .f-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .f-active {
            background: #dcfce7;
            color: #166534;
        }
    </style>
@endpush

@push('js')
    <script>
        const USER_API_URL = '/api/user-api';

        // Dynamic Search with Debounce
        let searchTimeout;
        const searchInput = document.getElementById('filter-search');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadUserData();
                }, 500);
            });

            if (searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }

        async function loadUserData(url = USER_API_URL, page = 1) {
            const tbody = document.getElementById('userTableBody');
<<<<<<< Updated upstream
            tbody.innerHTML =
                '<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data user...</p></td></tr>';
=======
            tbody.innerHTML = '<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-dark opacity-25"></div><p class="mt-2 text-muted small mb-0">Memuat data user...</p></td></tr>';
>>>>>>> Stashed changes

            try {
                const searchVal = document.getElementById('filter-search').value;
                let finalUrlStr = (typeof url === 'string' && url.includes('/')) ? url : USER_API_URL;

                if (searchVal) {
                    finalUrlStr = `/api/user-api/search/${encodeURIComponent(searchVal)}`;
                }

                const finalUrl = new URL(finalUrlStr, window.location.origin);
                if (page) finalUrl.searchParams.set('page', page);

                const response = await fetch(finalUrl.toString());
                const result = await response.json();

                if (result.success) {
                    renderUserList(result.data.data ? result.data.data : result.data);
                    renderPagination(result.data);
                }
            } catch (error) {
                console.error(error);
<<<<<<< Updated upstream
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger p-4">Gagal memuat data.</td></tr>';
=======
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger p-5 small"><i class="iconoir-warning-triangle d-block mb-2 fs-3"></i>Gagal memuat data. Periksa koneksi atau hubungi admin.</td></tr>';
>>>>>>> Stashed changes
            }
        }

        function renderUserList(data) {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
<<<<<<< Updated upstream
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center p-5 text-muted fst-italic">Data user tidak ditemukan.</td></tr>';
=======
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="py-4"><i class="iconoir-user-xmark text-muted mb-3" style="font-size: 48px; opacity: 0.3;"></i><p class="text-muted mb-0">Data user tidak ditemukan.</p></div></td></tr>';
>>>>>>> Stashed changes
                return;
            }

            data.forEach((item, index) => {
                const tr = `
<<<<<<< Updated upstream
                                    <tr class="clickable-row">
                                        <td class="text-center ps-4 text-muted small">${index + 1}</td>
                                        <td class="fw-bold text-dark">${item.name}</td>
                                        <td class="text-muted">${item.username}</td>
                                        <td class="text-muted"><i class="fa fa-envelope me-1 small"></i> ${item.email}</td>
                                        <td class="text-center">
                                            ${item.email_verified_at 
                                                ? '<span class="f-badge f-active text-success"><i class="fa fa-check-circle me-1"></i>Verified</span>' 
                                                : '<span class="f-badge bg-soft-secondary text-secondary"><i class="fa fa-clock me-1"></i>Unverified</span>'}
                                        </td>
                                        <td class="text-center text-muted small">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-primary me-1" onclick="openUserModal(${item.id})"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-danger" onclick="deleteUser(${item.id})"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                `;
=======
                    <tr>
                        <td class="ps-4 text-center text-muted small">${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-text">${initials}</div>
                                <div>
                                    <div class="fw-bold text-dark">${item.name}</div>
                                    <div class="text-muted small" style="font-size: 11px;">ID: #${item.id}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="font-monospace small bg-light px-2 py-1 rounded text-dark">${item.username}</span></td>
                        <td><div class="text-muted small"><i class="iconoir-mail me-1"></i> ${item.email}</div></td>
                        <td class="text-center">
                            ${item.email_verified_at 
                                ? '<span class="dr-badge bg-soft-success text-success"><i class="iconoir-check-circle me-1"></i>Verified</span>' 
                                : '<span class="dr-badge bg-soft-secondary text-secondary"><i class="iconoir-clock me-1"></i>Pending</span>'}
                        </td>
                        <td class="text-center text-muted small">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="dr-btn-icon dr-btn-icon-edit" onclick="openUserModal(${item.id})"><i class="iconoir-edit-pencil"></i></button>
                                <button class="dr-btn-icon dr-btn-icon-delete" onclick="deleteUser(${item.id})"><i class="iconoir-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
>>>>>>> Stashed changes
                tbody.insertAdjacentHTML('beforeend', tr);
            });
        }

        async function openUserModal(id = null) {
            const form = document.getElementById('userForm');
            form.reset();
            
            if (id) {
                document.getElementById('userModalTitle').innerText = 'Edit Pengguna';
                document.getElementById('user_form_mode').value = 'edit';
                document.getElementById('edit_user_id').value = id;

                try {
                    const res = await fetch(`${USER_API_URL}/${id}`);
                    const result = await res.json();
                    if (result.success) {
                        const user = result.data;
                        document.getElementById('modal_user_name').value = user.name;
                        document.getElementById('modal_user_username').value = user.username;
                        document.getElementById('modal_user_email').value = user.email;
                        document.getElementById('modal_user_is_sales').checked = !!user.is_sales;
                    }
                } catch (e) {
                    console.error('Fetch error:', e);
                }
            } else {
                document.getElementById('userModalTitle').innerText = 'Tambah User Baru';
                document.getElementById('user_form_mode').value = 'create';
                document.getElementById('edit_user_id').value = '';
            }

            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }

        async function saveUser() {
            const mode = document.getElementById('user_form_mode').value;
            const id = document.getElementById('edit_user_id').value;
            const btn = event.target;
            
            const payload = {
                name: document.getElementById('modal_user_name').value,
                username: document.getElementById('modal_user_username').value,
                email: document.getElementById('modal_user_email').value,
                is_sales: document.getElementById('modal_user_is_sales').checked ? 1 : 0
            };

            const password = document.getElementById('modal_user_password').value;
            if (password || mode === 'create') {
                payload.password = password;
            }

            if (!payload.name || !payload.username || !payload.email) {
                alert('Nama, Username, dan Email wajib diisi!');
                return;
            }

            if (mode === 'create' && !payload.password) {
                alert('Password wajib diisi untuk user baru!');
                return;
            }

            const url = mode === 'create' ? USER_API_URL : `${USER_API_URL}/${id}`;
            const method = mode === 'create' ? 'POST' : 'PUT';

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                    loadUserData();
                } else {
                    alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan sistem.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="iconoir-check text-success"></i> Simpan User';
            }
        }

        async function deleteUser(id) {
<<<<<<< Updated upstream
            if (!await macConfirm('Hapus User', 'Hapus user ini?')) return;
=======
            if (typeof macConfirm === 'function') {
                const confirmed = await macConfirm('Hapus User', 'Apakah Anda yakin ingin menghapus user ini secara permanen?', 'danger');
                if (!confirmed) return;
            } else {
                if (!confirm('Hapus user?')) return;
            }

>>>>>>> Stashed changes
            try {
                const res = await fetch(`${USER_API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const dat = await res.json();
                if (dat.success) {
                    alert('Berhasil dihapus');
                    loadUserData();
                } else alert(dat.message);
            } catch (e) {
                alert('Gagal menghapus');
            }
        }

        function renderPagination(meta) {
            const container = document.getElementById('pagination-container');
            const info = document.getElementById('pagination-info');

            if (!meta || !meta.links) return;

            if (info) {
                info.innerText = `Menampilkan ${meta.from || 0} - ${meta.to || 0} dari ${meta.total} data`;
            }

            container.innerHTML = '';
            meta.links.forEach(link => {
                const activeClass = link.active ? 'active' : '';
                const disabledClass = !link.url ? 'disabled' : '';

                let pageNum = 1;
                if (link.url) {
                    const url = new URL(link.url, window.location.origin);
                    pageNum = url.searchParams.get('page') || 1;
                }

                const onclick = link.url ? `onclick="loadUserData(undefined, ${pageNum})"` : '';

                container.insertAdjacentHTML('beforeend', `
                    <li class="page-item ${activeClass} ${disabledClass}">
                        <button class="page-link border-0 mx-1 rounded shadow-sm fw-bold" ${onclick}>${link.label}</button>
                    </li>`);
            });
        }

        document.addEventListener('DOMContentLoaded', () => loadUserData());
    </script>
@endpush
