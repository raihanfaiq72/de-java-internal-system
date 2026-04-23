@extends('Layout.main')

@push('css')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <style>
        .page-wrapper {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
        }

        .permission-group h6 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: var(--dr-text-muted);
            border-bottom: 1px solid var(--dr-line);
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .form-check-label {
            font-size: 13px;
            color: var(--dr-text);
        }
        
        .role-icon-box {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid var(--dr-line);
            border-radius: 8px;
            color: var(--dr-primary);
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <span>Main Menu</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-user-shield"></i></span>
                        <strong>Roles & Permissions</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Role Management</h1>
                            <p class="dr-subtitle">Kelola tingkatan akses dan hak izin pengguna sistem secara terpusat.</p>
                        </div>
                        <div class="dr-actions">
                            <button onclick="location.reload()" class="dr-btn dr-btn-outline">
                                <i class="iconoir-refresh"></i> Refresh
                            </button>
                            <button onclick="openRoleModal()" class="dr-btn dr-btn-primary">
                                <i class="iconoir-plus-circle"></i> Buat Role Baru
                            </button>
                        </div>
                    </div>

                    <div class="dr-card p-0 overflow-hidden bg-white">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0" id="roleTable">
                                <thead>
                                    <tr>
                                        <th width="60" class="text-center">#</th>
                                        <th width="250">Nama Role</th>
                                        <th>Deskripsi</th>
                                        <th width="150" class="text-center">Jumlah Izin</th>
                                        <th width="150" class="text-center">Dibuat Pada</th>
                                        <th width="150" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $index => $role)
                                        <tr>
                                            <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="role-icon-box me-3">
                                                        <i class="iconoir-shield"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $role->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-muted small">{{ $role->description ?: '-' }}</td>
                                            <td class="text-center">
                                                <span class="dr-badge bg-soft-primary text-primary">
                                                    {{ $role->permissions_count }} Modul
                                                </span>
                                            </td>
                                            <td class="text-center text-muted small">
                                                {{ $role->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button onclick="openRoleModal({{ $role->id }})"
                                                        class="dr-btn-icon dr-btn-icon-edit" title="Edit Role">
                                                        <i class="iconoir-edit-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="event.preventDefault(); const form = this; (async () => { if (await macConfirm('Hapus role {{ $role->name }}?', 'User yang menggunakan role ini akan kehilangan akses.')) form.submit() })();">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dr-btn-icon dr-btn-icon-delete"
                                                            title="Hapus Role">
                                                            <i class="iconoir-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted small py-4">
                                                    <i class="iconoir-shield-off fa-2x mb-2 opacity-25"></i>
                                                    <p>Belum ada role yang dibuat.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Fullscreen -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <form id="roleForm" action="{{ route('roles.store') }}" method="POST" class="modal-content"
                style="background-color: #ffffff;">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header bg-white border-bottom px-4 sticky-top d-flex align-items-center" style="padding-top: 20px !important; padding-bottom: 20px !important; box-shadow: var(--dr-shadow);">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="iconoir-user-shield text-dark" style="font-size: 28px;"></i>
                        </div>
                        <div>
                            <h5 class="dr-title mb-1" id="roleModalTitle" style="font-size: 18px;">Buat Role Baru</h5>
                            <p class="dr-subtitle" style="font-size: 13px;">Tentukan nama role dan centang izin akses yang diberikan.</p>
                        </div>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                            <i class="iconoir-xmark text-danger"></i> Batal
                        </button>
                        <button type="submit" class="dr-btn-modal">
                            <i class="iconoir-check text-success"></i> Simpan Role
                        </button>
                    </div>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-center mb-3" style="height: 32px;">
                                <h6 class="fw-bold text-dark mb-0 text-uppercase small ls-1">
                                    <i class="iconoir-info-circle me-2 text-primary"></i>Informasi Dasar
                                </h6>
                            </div>
                            <div class="dr-card">
                                <div class="mb-3">
                                    <label class="dr-label mb-2">Nama Role *</label>
                                    <input type="text" name="name" id="roleName" class="dr-input fw-bold"
                                        placeholder="Misal: Admin Gudang" required>
                                </div>
                                <div class="mb-0">
                                    <label class="dr-label mb-2">Deskripsi</label>
                                    <textarea name="description" id="roleDesc" class="dr-input" rows="4"
                                        placeholder="Deskripsi tugas role ini..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center mb-3" style="height: 32px;">
                                <h6 class="fw-bold text-dark mb-0 text-uppercase small ls-1">
                                    <i class="iconoir-lock me-2 text-primary"></i>Hak Akses (Permissions)
                                </h6>
                                <button type="button" class="dr-btn dr-btn-outline py-1 px-3" style="font-size: 11px;"
                                    onclick="toggleAllPermissions()">Pilih Semua</button>
                            </div>

                            <div class="row g-3">
                                @php
                                    $prefixes = \App\Models\Prefix::with('permissions')->orderBy('name')->get();
                                @endphp
                                @foreach ($prefixes as $prefix)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="dr-card p-0 overflow-hidden h-100">
                                            <div class="bg-white d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                                                <span class="fw-bold text-primary small" style="letter-spacing: 0.5px;">{{ strtoupper($prefix->name) }}</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="check-all-{{ $prefix->id }}"
                                                        onchange="toggleGroup('{{ $prefix->id }}', this)">
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                <div class="d-flex flex-column gap-2">
                                                    @foreach ($prefix->permissions as $perm)
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input permission-checkbox group-{{ $prefix->id }}"
                                                                type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                                id="perm-{{ $perm->id }}">
                                                            <label class="form-check-label small" for="perm-{{ $perm->id }}" style="font-size: 12px; cursor: pointer;">
                                                                {{ $perm->description ?: $perm->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        async function openRoleModal(id = null) {
            const form = document.getElementById('roleForm');
            const modalTitle = document.getElementById('roleModalTitle');
            const methodInput = document.getElementById('formMethod');

            form.reset();
            document.querySelectorAll('.permission-checkbox').forEach(c => c.checked = false);

            if (id) {
                modalTitle.innerText = 'Edit Data Role';
                form.action = `/admin/roles/${id}`;
                methodInput.value = 'PUT';

                // Fetch data
                try {
                    const res = await fetch(`/admin/roles/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        document.getElementById('roleName').value = json.data.name;
                        document.getElementById('roleDesc').value = json.data.description || '';

                        // Check permissions
                        json.permissions.forEach(pId => {
                            const chk = document.getElementById(`perm-${pId}`);
                            if (chk) chk.checked = true;
                        });

                        // Auto toggle group switches when all in group are checked
                        updateAllGroupToggles();
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error loading role data');
                    return;
                }

            } else {
                modalTitle.innerText = 'Buat Role Baru';
                form.action = "{{ route('roles.store') }}";
                methodInput.value = 'POST';
            }

            const modal = new bootstrap.Modal(document.getElementById('roleModal'));
            modal.show();
        }

        function toggleAllPermissions() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => c.checked = !allChecked);

            // Update group toggles
            document.querySelectorAll('input[id^="check-all-"]').forEach(chk => {
                chk.checked = !allChecked;
            });
        }

        function toggleGroup(prefixId, toggle) {
            const checkboxes = document.querySelectorAll(`.group-${prefixId}`);
            checkboxes.forEach(c => c.checked = toggle.checked);

            // Reflect state immediately
            document.getElementById(`check-all-${prefixId}`).checked = toggle.checked;
        }

        // Update a single group's toggle based on its child checkboxes
        function updateGroupToggle(prefixId) {
            const checkboxes = document.querySelectorAll(`.group-${prefixId}`);
            const allChecked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(c => c.checked);
            const toggle = document.getElementById(`check-all-${prefixId}`);
            if (toggle) toggle.checked = allChecked;
        }

        // Update all group toggles
        function updateAllGroupToggles() {
            document.querySelectorAll('input[id^="check-all-"]').forEach(t => {
                const pid = t.id.replace('check-all-', '');
                updateGroupToggle(pid);
            });
        }

        // Listen to child checkbox changes to auto-update group toggles
        document.addEventListener('change', function(e) {
            if (e.target.classList && e.target.classList.contains('permission-checkbox')) {
                const cls = Array.from(e.target.classList).find(c => c.startsWith('group-'));
                if (cls) {
                    const pid = cls.replace('group-', '');
                    updateGroupToggle(pid);
                }
            }
        });
    </script>
@endpush
