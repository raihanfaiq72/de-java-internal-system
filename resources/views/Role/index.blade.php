@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    
    #roleTable thead th { border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; background: #f8fafc; padding-top: 12px; padding-bottom: 12px; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
    #roleTable tbody td { font-size: 13px; color: #334155; padding-top: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .role-badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
    .permission-group h6 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; color: #334155; }
    .form-check-label { font-size: 13px; color: #475569; }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Manajemen Role & Hak Akses</h4>
                    <p class="text-muted small mb-0">Atur hierarki jabatan dan batasan akses modul sistem.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button onclick="openRoleModal()" class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="fa fa-plus-circle me-1"></i> Buat Role Baru
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                 <div class="card-header bg-white border-bottom py-3 px-4">
                   <h6 class="mb-0 fw-bold text-dark">Daftar Role Tersedia</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="roleTable">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-4 text-center">#</th>
                                    <th width="250">Nama Role</th>
                                    <th>Deskripsi</th>
                                    <th width="150" class="text-center">Jumlah Izin</th>
                                    <th width="150" class="text-center">Dibuat Pada</th>
                                    <th width="120" class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $index => $role)
                                <tr>
                                    <td class="text-center ps-4 text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded text-primary me-3 border d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                                                <i class="fa fa-shield-alt"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $role->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $role->description ?: '-' }}</td>
                                    <td class="text-center">
                                        <span class="role-badge bg-soft-info text-info border border-info border-opacity-25">{{ $role->permissions_count }} Modul</span>
                                    </td>
                                    <td class="text-center text-muted small f-mono">
                                        {{ $role->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button onclick="openRoleModal({{ $role->id }})" class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-primary me-1" title="Edit Role">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus role {{ $role->name }}? User yang menggunakan role ini akan kehilangan akses.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-danger" title="Hapus Role">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted small">Belum ada role yang dibuat.</p>
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
        <form id="roleForm" action="{{ route('roles.store') }}" method="POST" class="modal-content" style="background-color: #f8fafc;">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white p-2 rounded-2 me-3">
                        <i class="fa fa-user-shield fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="roleModalTitle">Buat Role Baru</h5>
                        <p class="text-muted small mb-0">Tentukan nama role dan centang izin akses yang diberikan.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa fa-save me-2"></i>Simpan Role
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4">
                        <h6 class="fw-bold text-dark mb-3 text-uppercase small ls-1"><i class="fa fa-info-circle me-2 text-primary"></i>Informasi Dasar</h6>
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="f-label">Nama Role *</label>
                                    <input type="text" name="name" id="roleName" class="form-control fw-bold" placeholder="Misal: Admin Gudang" required>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label">Deskripsi</label>
                                    <textarea name="description" id="roleDesc" class="form-control" rows="3" placeholder="Deskripsi tugas role ini..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 text-uppercase small ls-1"><i class="fa fa-lock me-2 text-primary"></i>Hak Akses (Permissions)</h6>
                            <button type="button" class="btn btn-sm btn-light border small" onclick="toggleAllPermissions()">Pilih Semua</button>
                        </div>
                        
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    @php 
                                        $prefixes = \App\Models\Prefix::with('permissions')->orderBy('name')->get(); 
                                    @endphp
                                    @foreach($prefixes as $prefix)
                                    <div class="col-md-6 permission-group">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <h6 class="mb-0 text-primary">{{ $prefix->name }}</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="check-all-{{ $prefix->id }}" onchange="toggleGroup('{{ $prefix->id }}', this)">
                                                <label class="form-check-label small text-muted" for="check-all-{{ $prefix->id }}">All</label>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($prefix->permissions as $perm)
                                            <div class="form-check me-2 mb-1">
                                                <input class="form-check-input permission-checkbox group-{{ $prefix->id }}" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm-{{ $perm->id }}">
                                                <label class="form-check-label small" for="perm-{{ $perm->id }}">
                                                    {{ $perm->description ?: $perm->name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

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
                if(json.success) {
                    document.getElementById('roleName').value = json.data.name;
                    document.getElementById('roleDesc').value = json.data.description || '';
                    
                    // Check permissions
                    json.permissions.forEach(pId => {
                        const chk = document.getElementById(`perm-${pId}`);
                        if(chk) chk.checked = true;
                    });
                }
            } catch(e) { console.error(e); alert('Error loading role data'); return; }

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
    }
</script>
@endpush
@endsection