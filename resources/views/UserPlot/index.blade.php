@extends('Layout.main')

<<<<<<< Updated upstream
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <style>
        .page-wrapper {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .f-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        #plotTable thead th {
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding-top: 12px;
            padding-bottom: 12px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        #plotTable tbody td {
            font-size: 13px;
            color: #334155;
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .badge-role {
            background: #fee2e2;
            color: #b91c1c;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #fecaca;
            font-weight: 600;
        }

        .badge-office {
            background: #e0f2fe;
            color: #0284c7;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #bae6fd;
            font-weight: 600;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Admin: User Plots</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">User Plots</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">Daftar User Plot</h6>
                            <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openPlotModal()">
                                <i class="fa fa-link me-1"></i> Plotting Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
=======
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
                        <strong>Plotting Management</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Manajemen Plotting User</h1>
                            <p class="dr-subtitle">Atur penempatan user di setiap kantor cabang dan tentukan rolenya.</p>
                        </div>
                        <div class="dr-actions">
                            <button class="dr-btn dr-btn-primary" onclick="openPlotModal()">
                                <i class="iconoir-plus"></i> Tambah Plotting
                            </button>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="dr-card p-4 d-flex align-items-center gap-4 bg-white border-0 shadow-sm">
                                <div class="bg-soft-primary d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px;">
                                    <i class="iconoir-group text-primary" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 11px;">Total Plotting</p>
                                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 24px;">{{ $plots->total() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dr-card p-4 d-flex align-items-center gap-4 bg-white border-0 shadow-sm">
                                <div class="bg-soft-success d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px;">
                                    <i class="iconoir-city text-success" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 11px;">Kantor Aktif</p>
                                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 24px;">{{ $offices->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dr-card p-4 d-flex align-items-center gap-4 bg-white border-0 shadow-sm">
                                <div class="bg-soft-warning d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px;">
                                    <i class="iconoir-badge-check text-warning" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 11px;">Total Salesperson</p>
                                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 24px;">{{ $plots->getCollection()->where('is_sales', true)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Table Card -->
                    <div class="dr-card p-0 overflow-hidden bg-white mt-4">
>>>>>>> Stashed changes
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="plotTable">
                                <thead>
                                    <tr>
<<<<<<< Updated upstream
                                        <th width="50" class="ps-4 text-center">#</th>
                                        <th width="20%">Nama User</th>
                                        <th width="25%">Kantor / Outlet</th>
                                        <th width="20%">Role / Jabatan</th>
                                        <th width="15%" class="text-center">Salesperson</th>
                                        <th width="20%" class="text-end pe-4">Aksi</th>
=======
                                        <th>User</th>
                                        <th>Kantor</th>
                                        <th>Role / Hak Akses</th>
                                        <th>Status Sales</th>
                                        <th class="text-end">Aksi</th>
>>>>>>> Stashed changes
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plots as $p)
                                        <tr>
<<<<<<< Updated upstream
                                            <td class="text-center ps-4 text-muted small">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle border d-flex align-items-center justify-content-center text-primary fw-bold me-2"
                                                        style="width:30px; height:30px; font-size: 11px;">
                                                        {{ substr($p->user_name, 0, 2) }}
=======
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold"
                                                        style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ strtoupper(substr($p->user_name ?? '?', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $p->user_name }}</div>
                                                        <div class="text-muted small">{{ $p->user_email }}</div>
>>>>>>> Stashed changes
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
<<<<<<< Updated upstream
                                                <span class="badge-office"><i class="fa fa-building me-1"></i>
                                                    {{ $p->office_name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge-role">{{ $p->role_name }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($p->is_sales)
                                                    <span class="badge bg-success text-white">
                                                        <i class="fa fa-user-tie me-1"></i>Sales
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary text-white">
                                                        <i class="fa fa-user me-1"></i>Staff
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <button onclick="openPlotModal({{ $p->id }})"
                                                    class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-primary me-1"
                                                    title="Edit Plotting">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <form action="{{ route('user_plots.destroy', $p->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="event.preventDefault(); const form = this; (async () => { if (await macConfirm('Hapus akses plotting ini?')) form.submit() })();">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-danger"
                                                        title="Hapus Akses">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
=======
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="iconoir-city text-muted" style="font-size: 14px;"></i>
                                                    <span class="fw-medium">{{ $p->office_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-primary text-primary px-2 py-1 rounded-pill" style="font-size: 11px;">
                                                    {{ $p->role_name }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($p->is_sales)
                                                    <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill" style="font-size: 11px;">
                                                        <i class="iconoir-check-circle me-1"></i> Salesperson
                                                    </span>
                                                @else
                                                    <span class="text-muted small italic">Non-Sales</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button class="btn btn-sm btn-icon btn-light"
                                                        onclick="editPlot({{ $p->id }}, {{ $p->user_id }}, {{ $p->office_id }}, {{ $p->role_id }}, {{ $p->is_sales ? 1 : 0 }})">
                                                        <i class="iconoir-edit-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('user_plots.destroy', $p->id) }}" method="POST"
                                                        onsubmit="return confirm('Hapus plotting ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light text-danger">
                                                            <i class="iconoir-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
>>>>>>> Stashed changes
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
<<<<<<< Updated upstream
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fa fa-link fa-3x mb-3 opacity-25"></i>
                                                <p class="text-muted small">Belum ada plotting user.</p>
                                            </td>
=======
                                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data plotting user.</td>
>>>>>>> Stashed changes
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
<<<<<<< Updated upstream
=======
                        
                        @if($plots->hasPages())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 bg-white border-top">
                            <span id="pagination-info" class="text-muted small fw-medium">
                                Menampilkan {{ $plots->firstItem() }} sampai {{ $plots->lastItem() }} dari {{ $plots->total() }} data
                            </span>
                            <nav>
                                {{ $plots->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                        @endif
>>>>>>> Stashed changes
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="plotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
<<<<<<< Updated upstream
            <form id="plotForm" action="{{ route('user_plots.store') }}" method="POST"
                class="modal-content border-0 shadow-lg">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header bg-white border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="plotModalTitle">Tambah Plotting User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-soft-info border-0 small mb-3">
                        <i class="fa fa-info-circle me-1"></i> User yang diplot akan memiliki akses ke data kantor terkait
                        sesuai role yang dipilih.
                    </div>
                    <div class="mb-3">
                        <label class="f-label">Pilih User</label>
                        <select name="user_id" id="select-user" class="form-control" required placeholder="Cari User...">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" data-is-sales="{{ $u->is_sales ? 'true' : 'false' }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="plot_is_sales" name="is_sales">
                            <label class="form-check-label" for="plot_is_sales">
                                <strong>Salesperson</strong>
                                <div class="form-text small text-muted">Aktifkan jika user ini adalah salesperson</div>
                            </label>
=======
            <div class="modal-content border-0 bg-white">
                <form id="plotForm" action="{{ route('user_plots.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="iconoir-link text-dark" style="font-size: 20px;"></i>
                            <div>
                                <h5 class="dr-title mb-1" id="plotModalTitle" style="font-size: 16px;">Tambah Plotting User</h5>
                                <p class="dr-subtitle mb-0" style="font-size: 12px;">Hubungkan user ke kantor cabang.</p>
                            </div>
>>>>>>> Stashed changes
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
<<<<<<< Updated upstream
                    <div class="mb-3">
                        <label class="f-label">Pilih Kantor</label>
                        <select name="office_id" id="select-office" class="form-control" required
                            placeholder="Cari Kantor...">
                            @foreach ($offices as $o)
                                <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="f-label">Pilih Role</label>
                        <select name="role_id" id="select-role" class="form-control" required placeholder="Cari Role...">
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-light">
                    <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">Simpan Plotting</button>
                </div>
            </form>
        </div>
    </div>

=======

                    <div class="modal-body px-4 pb-4 pt-0 bg-white">
                        <div class="p-3 border rounded-3 mb-4 d-flex align-items-start gap-3 bg-white">
                            <i class="iconoir-info-circle text-dark mt-1"></i>
                            <span class="text-dark small" style="line-height: 1.4; font-size: 12px;">User yang diplot akan memiliki akses ke data kantor terkait sesuai role yang dipilih.</span>
                        </div>

                        <div class="mb-4">
                            <label class="dr-label mb-2">Pilih User *</label>
                            <select name="user_id" id="select-user" class="dr-input" required placeholder="Cari User...">
                                <option value="">- Cari Nama User -</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" data-is-sales="{{ $u->is_sales ? 'true' : 'false' }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4 p-3 border rounded-3 bg-white" style="border-style: dashed !important;">
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <input class="form-check-input ms-0" type="checkbox" id="plot_is_sales" name="is_sales" style="width: 40px; height: 20px;">
                                <label class="form-check-label" for="plot_is_sales">
                                    <strong class="text-dark d-block" style="font-size: 13px;">Tandai sebagai Salesperson</strong>
                                    <span class="text-muted" style="font-size: 11px;">Aktifkan jika user ini bertugas di lapangan.</span>
                                </label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="dr-label mb-2">Pilih Kantor *</label>
                                <select name="office_id" id="select-office" class="dr-input" required placeholder="Cari Kantor...">
                                    <option value="">- Kantor -</option>
                                    @foreach ($offices as $o)
                                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="dr-label mb-2">Pilih Role *</label>
                                <select name="role_id" id="select-role" class="dr-input" required placeholder="Cari Role...">
                                    <option value="">- Role -</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                        <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                            <i class="iconoir-xmark text-danger"></i> Batal
                        </button>
                        <button type="submit" class="dr-btn-modal">
                            <i class="iconoir-check text-success"></i> Simpan Plotting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('css')
        <style>
            .user-avatar-text {
                background: linear-gradient(135deg, var(--dr-primary), #4e8cff);
                color: white;
            }
            .dr-table th {
                text-transform: uppercase;
                font-size: 11px;
                letter-spacing: 0.5px;
                color: #7c8fac;
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    @endpush

>>>>>>> Stashed changes
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            let tsUser, tsOffice, tsRole;

<<<<<<< Updated upstream
            document.addEventListener("DOMContentLoaded", function () {
                tomUser = new TomSelect("#select-user", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
                tomOffice = new TomSelect("#select-office", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
                tomRole = new TomSelect("#select-role", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                // Auto-set is_sales when user is selected
                tomUser.on('change', function(value) {
                    const option = tomUser.getOption(value);
                    const isSales = option ? option.dataset.isSales === 'true' : false;
                    document.getElementById('plot_is_sales').checked = isSales;
                });
=======
            document.addEventListener('DOMContentLoaded', function() {
                tsUser = new TomSelect('#select-user', { create: false });
                tsOffice = new TomSelect('#select-office', { create: false });
                tsRole = new TomSelect('#select-role', { create: false });
>>>>>>> Stashed changes
            });

            function openPlotModal() {
                document.getElementById('plotModalTitle').innerText = 'Tambah Plotting User';
                document.getElementById('plotForm').action = "{{ route('user_plots.store') }}";
                document.getElementById('formMethod').value = "POST";
                
                tsUser.clear();
                tsOffice.clear();
                tsRole.clear();
                document.getElementById('plot_is_sales').checked = false;

                new bootstrap.Modal(document.getElementById('plotModal')).show();
            }

<<<<<<< Updated upstream
                    try {
                        const res = await fetch(`/admin/user-plots/${id}`);
                        const json = await res.json();
                        if (json.success) {
                            tomUser.setValue(json.data.user_id);
                            tomOffice.setValue(json.data.office_id);
                            tomRole.setValue(json.data.role_id);
                            
                            // Set is_sales checkbox
                            document.getElementById('plot_is_sales').checked = json.data.is_sales ? true : false;
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Gagal load data plot');
                        return;
                    }
=======
            function editPlot(id, userId, officeId, roleId, isSales) {
                document.getElementById('plotModalTitle').innerText = 'Edit Plotting User';
                document.getElementById('plotForm').action = "/user_plots/" + id;
                document.getElementById('formMethod').value = "PUT";
>>>>>>> Stashed changes

                tsUser.setValue(userId);
                tsOffice.setValue(officeId);
                tsRole.setValue(roleId);
                document.getElementById('plot_is_sales').checked = (isSales == 1);

                new bootstrap.Modal(document.getElementById('plotModal')).show();
            }
        </script>
    @endpush
@endsection
