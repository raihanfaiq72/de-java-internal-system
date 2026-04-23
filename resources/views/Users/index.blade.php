@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Hero Header -->
                    <div class="dr-breadcrumb">
                        <span>Sistem</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-user"></i></span>
                        <strong>Manajemen Pengguna</strong>
                    </div>

                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Daftar Pengguna</h1>
                            <p class="dr-subtitle">Kelola hak akses dan profil pengguna sistem secara terpusat.</p>
                        </div>
                        <div class="dr-actions">
                            <div class="position-relative" style="width: 300px;">
                                <i class="iconoir-search text-muted position-absolute start-0 top-50 translate-middle-y ms-3" style="font-size: 16px;"></i>
                                <input type="text" id="filter-search" class="dr-input ps-5" placeholder="Cari nama, username..." onkeyup="if(event.key === 'Enter') loadUserData()">
                            </div>
                            <button class="dr-btn dr-btn-outline" onclick="loadUserData()">
                                <i class="iconoir-refresh"></i> Refresh
                            </button>
                            <button class="dr-btn dr-btn-primary" onclick="openUserModal()">
                                <i class="iconoir-plus-circle"></i> Tambah User
                            </button>
                        </div>
                    </div>

                    <!-- Main Table Card -->
                    <div class="dr-card p-0 overflow-hidden">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0" id="userTable">
                                <thead>
                                    <tr>
                                        <th width="60" class="text-center">#</th>
                                        <th>Pengguna</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Terdaftar</th>
                                        <th width="150" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    <!-- Rows injected by JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 bg-light border-top">
                            <span id="pagination-info" class="text-muted small fw-medium"></span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 gap-1" id="pagination-container"></ul>
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
        .user-avatar-text {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--dr-panel-soft);
            color: var(--dr-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            border: 1px solid var(--dr-line);
        }
    </style>
@endpush

@push('js')
    <script>
        const USER_API_URL = '/api/user-api';

        async function loadUserData(url = USER_API_URL, page = 1) {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-dark"></div><p class="mt-2 text-muted small mb-0">Memuat data user...</p></td></tr>';

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
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger p-4 small">Gagal memuat data. Periksa koneksi atau hubungi admin.</td></tr>';
            }
        }

        function renderUserList(data) {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center p-5 text-muted fst-italic small">Data user tidak ditemukan.</td></tr>';
                return;
            }

            data.forEach((item, index) => {
                const initials = item.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                const tr = `
                    <tr>
                        <td class="text-center text-muted small">${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-text">${initials}</div>
                                <div>
                                    <div class="fw-bold text-dark">${item.name}</div>
                                    <div class="text-muted small" style="font-size: 11px;">ID: #${item.id}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="font-monospace small bg-light px-2 py-1 rounded">${item.username}</span></td>
                        <td><div class="text-muted small"><i class="iconoir-mail me-1"></i> ${item.email}</div></td>
                        <td class="text-center">
                            ${item.email_verified_at 
                                ? '<span class="dr-badge bg-soft-success text-success"><i class="iconoir-check-circle me-1"></i>Verified</span>' 
                                : '<span class="dr-badge bg-soft-secondary text-secondary"><i class="iconoir-clock me-1"></i>Pending</span>'}
                        </td>
                        <td class="text-center text-muted small">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <button class="dr-btn-icon dr-btn-icon-edit" onclick="openUserModal(${item.id})"><i class="iconoir-edit-pencil"></i></button>
                                <button class="dr-btn-icon dr-btn-icon-delete" onclick="deleteUser(${item.id})"><i class="iconoir-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', tr);
            });
        }

        async function deleteUser(id) {
            if (!await macConfirm('Hapus User', 'Apakah Anda yakin ingin menghapus user ini secara permanen?')) return;
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
                info.innerText = `${meta.from || 0}-${meta.to || 0} dari ${meta.total} data`;
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
                const label = link.label.includes('Previous') ? '<i class="iconoir-nav-arrow-left"></i>' : (link.label.includes('Next') ? '<i class="iconoir-nav-arrow-right"></i>' : link.label);

                container.insertAdjacentHTML('beforeend', `
                    <li class="page-item ${activeClass} ${disabledClass}">
                        <button class="page-link border-0 mx-0 rounded shadow-none fw-bold" ${onclick}>${label}</button>
                    </li>`);
            });
        }

        function resetFilter() {
            document.getElementById('filter-search').value = '';
            loadUserData();
        }

        document.addEventListener('DOMContentLoaded', () => loadUserData());
    </script>
@endpush
