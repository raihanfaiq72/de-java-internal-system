@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <style>
        .page-wrapper {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
        }

        /* TomSelect Premium Overrides */
        .ts-control {
            border: 1.5px solid var(--dr-line) !important;
            border-radius: var(--dr-radius-sm) !important;
            padding: 10px 12px !important;
            font-size: 14px !important;
            background-image: none !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: var(--dr-line-strong) !important;
            box-shadow: none !important;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Hero Header -->
                    <div class="dr-breadcrumb">
                        <span>Sistem</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-link"></i></span>
                        <strong>Ploting User</strong>
                    </div>

                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Akses & Ploting User</h1>
                            <p class="dr-subtitle">Hubungkan pengguna dengan kantor cabang dan tentukan hak akses peran mereka.</p>
                        </div>
                        <div class="dr-actions">
                            <button class="dr-btn dr-btn-primary" onclick="openPlotModal()">
                                <i class="iconoir-plus-circle"></i> Tambah Plotting
                            </button>
                        </div>
                    </div>

                    <!-- Main Table Card -->
                    <div class="dr-card p-0 overflow-hidden">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0" id="plotTable">
                                <thead>
                                    <tr>
                                        <th width="60" class="text-center">#</th>
                                        <th>Pengguna</th>
                                        <th>Kantor / Cabang</th>
                                        <th>Role / Jabatan</th>
                                        <th class="text-center">Tipe</th>
                                        <th width="150" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plots as $index => $p)
                                        <tr>
                                            <td class="text-center text-muted small">{{ $plots->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="user-avatar-text" style="width: 32px; height: 32px; font-size: 11px;">
                                                        {{ substr($p->user_name, 0, 2) }}
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $p->user_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="iconoir-nav-arrow-right text-muted small"></i>
                                                    <span class="text-dark fw-medium">{{ $p->office_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="dr-badge bg-soft-primary text-primary">{{ $p->role_name }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($p->is_sales)
                                                    <span class="dr-badge bg-soft-success text-success">
                                                        <i class="iconoir-user-badge-check me-1"></i>Sales
                                                    </span>
                                                @else
                                                    <span class="dr-badge bg-soft-secondary text-secondary">
                                                        <i class="iconoir-user me-1"></i>Staff
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button onclick="openPlotModal({{ $p->id }})" class="dr-btn-icon dr-btn-icon-edit" title="Edit Plotting">
                                                        <i class="iconoir-edit-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('user_plots.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); const form = this; (async () => { if (await macConfirm('Hapus akses plotting ini?', 'User tidak akan bisa mengakses data kantor ini lagi.')) form.submit() })();">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dr-btn-icon dr-btn-icon-delete" title="Hapus Akses">
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
                                                    <i class="iconoir-link-break fa-2x mb-2 opacity-25"></i>
                                                    <p>Belum ada data plotting user.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 bg-white border-top">
                            <span class="text-muted small fw-medium">
                                Showing {{ $plots->firstItem() ?? 0 }} to {{ $plots->lastItem() ?? 0 }} of {{ $plots->total() }} entries
                            </span>
                            <nav>
                                {{ $plots->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Plotting -->
    <div class="modal fade" id="plotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="plotForm" action="{{ route('user_plots.store') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="dr-card-title fs-5" id="plotModalTitle">Tambah Plotting User</h5>
                </div>
                <div class="modal-body">
                    <div class="p-3 border mb-4 d-flex align-items-start gap-3" style="background-color: var(--dr-panel-soft); border-radius: var(--dr-radius-sm);">
                        <i class="iconoir-info-empty text-primary mt-1"></i>
                        <span class="text-muted small" style="line-height: 1.4;">User yang diplot akan memiliki akses ke data kantor terkait sesuai role yang dipilih.</span>
                    </div>

                    <div class="mb-4">
                        <label class="dr-label mb-2">Pilih User</label>
                        <select name="user_id" id="select-user" class="dr-input" required placeholder="Cari User...">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" data-is-sales="{{ $u->is_sales ? 'true' : 'false' }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 p-3 border rounded" style="background: #fff;">
                        <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                            <input class="form-check-input ms-0" type="checkbox" id="plot_is_sales" name="is_sales" style="width: 40px; height: 20px;">
                            <label class="form-check-label" for="plot_is_sales">
                                <strong class="text-dark d-block" style="font-size: 14px;">Tandai sebagai Salesperson</strong>
                                <span class="text-muted small">Aktifkan jika user ini bertugas di lapangan.</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="dr-label mb-2">Pilih Kantor</label>
                            <select name="office_id" id="select-office" class="dr-input" required placeholder="Cari Kantor...">
                                @foreach ($offices as $o)
                                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="dr-label mb-2">Pilih Role</label>
                            <select name="role_id" id="select-role" class="dr-input" required placeholder="Cari Role...">
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex gap-2">
                    <button type="button" class="dr-btn-modal dr-btn-modal-cancel grow justify-content-center" data-bs-dismiss="modal">
                        <i class="iconoir-xmark"></i> Batal
                    </button>
                    <button type="submit" class="dr-btn-modal dr-btn-modal-save grow justify-content-center">
                        <i class="iconoir-check"></i> Simpan Plotting
                    </button>
                </div>
            </form>
        </div>
    </div>

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
            /* TomSelect Premium Overrides */
            .ts-control {
                border: 1.5px solid var(--dr-line) !important;
                border-radius: var(--dr-radius-sm) !important;
                padding: 10px 12px !important;
                font-size: 14px !important;
                background-image: none !important;
            }
            .ts-wrapper.focus .ts-control {
                border-color: var(--dr-line-strong) !important;
                box-shadow: none !important;
            }
        </style>
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            let tomUser, tomOffice, tomRole;

            document.addEventListener("DOMContentLoaded", function () {
                tomUser = new TomSelect("#select-user", {
                    create: false,
                    sortField: { field: "text", direction: "asc" }
                });
                tomOffice = new TomSelect("#select-office", {
                    create: false,
                    sortField: { field: "text", direction: "asc" }
                });
                tomRole = new TomSelect("#select-role", {
                    create: false,
                    sortField: { field: "text", direction: "asc" }
                });

                tomUser.on('change', function(value) {
                    const option = tomUser.getOption(value);
                    const isSales = option ? option.dataset.isSales === 'true' : false;
                    document.getElementById('plot_is_sales').checked = isSales;
                });
            });

            async function openPlotModal(id = null) {
                const form = document.getElementById('plotForm');
                const modalTitle = document.getElementById('plotModalTitle');
                const methodInput = document.getElementById('formMethod');

                tomUser.clear();
                tomOffice.clear();
                tomRole.clear();
                document.getElementById('plot_is_sales').checked = false;

                if (id) {
                    modalTitle.innerText = 'Edit Plotting User';
                    form.action = `/admin/user-plots/${id}`;
                    methodInput.value = 'PUT';

                    try {
                        const res = await fetch(`/admin/user-plots/${id}`);
                        const json = await res.json();
                        if (json.success) {
                            tomUser.setValue(json.data.user_id);
                            tomOffice.setValue(json.data.office_id);
                            tomRole.setValue(json.data.role_id);
                            document.getElementById('plot_is_sales').checked = json.data.is_sales ? true : false;
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Gagal load data plot');
                        return;
                    }

                } else {
                    modalTitle.innerText = 'Tambah Plotting User';
                    form.action = "{{ route('user_plots.store') }}";
                    methodInput.value = 'POST';
                }

                new bootstrap.Modal(document.getElementById('plotModal')).show();
            }
        </script>
    @endpush
@endsection
