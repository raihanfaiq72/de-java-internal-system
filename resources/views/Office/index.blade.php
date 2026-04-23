@extends('Layout.main')

@push('css')
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <i class="iconoir-home dr-breadcrumb-icon"></i>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Daftar Kantor (Outlet)</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h4 class="dr-title">Manajemen Kantor</h4>
                            <p class="dr-subtitle">Kelola daftar cabang, gudang, dan outlet operasional sistem.</p>
                        </div>
                        <div class="dr-actions">
                            <form id="searchForm" action="{{ route('offices.index') }}" method="GET" class="d-flex gap-2">
                                <div class="position-relative" style="width: 250px;">
                                    <i class="iconoir-search text-muted position-absolute start-0 top-50 translate-middle-y ms-3" style="font-size: 16px;"></i>
                                    <input type="text" name="search" id="searchInput" class="dr-input ps-5" placeholder="Cari nama atau kode..." value="{{ request('search') }}" autocomplete="off">
                                </div>
                            </form>
                            <button class="dr-btn dr-btn-primary" onclick="openOfficeModal()">
                                <i class="iconoir-plus-circle"></i> Tambah Kantor Baru
                            </button>
                        </div>
                    </div>

                    <div class="dr-card p-0 overflow-hidden bg-white mt-4">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0" id="officeTable">
                                <thead>
                                    <tr>
                                        <th width="150" class="ps-4">Kode Kantor</th>
                                        <th>Nama Kantor</th>
                                        <th width="200" class="text-center">Tanggal Dibuat</th>
                                        <th width="150" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($offices as $o)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="dr-badge bg-soft-danger text-danger fw-bold font-monospace">{{ $o->code }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft-primary p-2 rounded-3 me-3">
                                                        <i class="iconoir-city text-primary" style="font-size: 18px;"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $o->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center text-muted small">
                                                {{ $o->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button onclick="openOfficeModal({{ $o->id }})"
                                                        class="dr-btn-icon dr-btn-icon-edit"
                                                        title="Edit Kantor">
                                                        <i class="iconoir-edit-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('offices.destroy', $o->id) }}" method="POST"
                                                        id="delete-form-{{ $o->id }}" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <button type="button"
                                                        onclick="deleteOffice({{ $o->id }}, '{{ $o->name }}')"
                                                        class="dr-btn-icon dr-btn-icon-delete"
                                                        title="Hapus Kantor">
                                                        <i class="iconoir-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="iconoir-city text-muted mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                                                    <p class="text-muted mb-0">Data tidak ditemukan.</p>
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

    <div class="modal fade" id="officeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white">
                <form id="officeForm" action="{{ route('offices.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="iconoir-city text-dark" style="font-size: 20px;"></i>
                            <div>
                                <h5 class="dr-title mb-1" id="officeModalTitle" style="font-size: 16px;">Tambah Kantor Baru</h5>
                                <p class="dr-subtitle mb-0" style="font-size: 12px;">Kelola informasi cabang dan kode identitas kantor.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 pb-4 pt-0 bg-white">
                        <div class="mb-3">
                            <label class="dr-label mb-2">Nama Kantor *</label>
                            <input type="text" name="name" id="officeName" class="dr-input fw-bold"
                                placeholder="Misal: Kantor Pusat Semarang" required>
                        </div>
                        <div class="mb-0">
                            <label class="dr-label mb-2">Kode Kantor (Singkatan) *</label>
                            <input type="text" name="code" id="officeCode" class="dr-input font-monospace text-uppercase"
                                placeholder="Misal: KCP-SMG" required>
                            <div class="d-flex align-items-center gap-2 mt-2 text-muted small" style="font-size: 11px;">
                                <i class="iconoir-info-circle"></i>
                                <span>Kode unik untuk identifikasi sistem di laporan.</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                        <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                            <i class="iconoir-xmark text-danger"></i> Batal
                        </button>
                        <button type="submit" class="dr-btn-modal">
                            <i class="iconoir-check text-success"></i> Simpan Kantor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            // Dynamic Search
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchForm.submit();
                    }, 500);
                });

                if (searchInput.value) {
                    searchInput.focus();
                    const val = searchInput.value;
                    searchInput.value = '';
                    searchInput.value = val;
                }
            }

            async function deleteOffice(id, name) {
                if (typeof macConfirm === 'function') {
                    const confirmed = await macConfirm(
                        'Hapus Kantor', 
                        `Apakah Anda yakin ingin menghapus kantor "${name}"? Data terkait mungkin akan terpengaruh.`, 
                        'danger'
                    );
                    if (confirmed) document.getElementById(`delete-form-${id}`).submit();
                } else {
                    if (confirm(`Hapus kantor ${name}?`)) {
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                }
            }

            async function openOfficeModal(id = null) {
                const form = document.getElementById('officeForm');
                const modalTitle = document.getElementById('officeModalTitle');
                const methodInput = document.getElementById('formMethod');
                const nameInput = document.getElementById('officeName');
                const codeInput = document.getElementById('officeCode');

                form.reset();

                if (id) {
                    modalTitle.innerText = 'Edit Data Kantor';
                    form.action = `/admin/offices/${id}`;
                    methodInput.value = 'PUT';

                    try {
                        const res = await fetch(`/admin/offices/${id}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();
                        if (json.success) {
                            nameInput.value = json.data.name;
                            codeInput.value = json.data.code;
                        }
                    } catch (e) { console.error(e); alert('Gagal load data kantor'); return; }

                } else {
                    modalTitle.innerText = 'Tambah Kantor Baru';
                    form.action = "{{ route('offices.store') }}";
                    methodInput.value = 'POST';
                }

                new bootstrap.Modal(document.getElementById('officeModal')).show();
            }
        </script>
    @endpush
@endsection
