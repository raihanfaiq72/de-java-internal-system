@extends('Layout.main')

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
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="plotTable">
                                <thead>
                                    <tr>
                                        <th width="50" class="ps-4 text-center">#</th>
                                        <th width="20%">Nama User</th>
                                        <th width="25%">Kantor / Outlet</th>
                                        <th width="20%">Role / Jabatan</th>
                                        <th width="15%" class="text-center">Salesperson</th>
                                        <th width="20%" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plots as $index => $p)
                                        <tr>
                                            <td class="text-center ps-4 text-muted small">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle border d-flex align-items-center justify-content-center text-primary fw-bold me-2"
                                                        style="width:30px; height:30px; font-size: 11px;">
                                                        {{ substr($p->user_name, 0, 2) }}
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $p->user_name }}</span>
                                                </div>
                                            </td>
                                            <td>
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
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fa fa-link fa-3x mb-3 opacity-25"></i>
                                                <p class="text-muted small">Belum ada plotting user.</p>
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

    <div class="modal fade" id="plotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                        </div>
                    </div>
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

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            let tomUser, tomOffice, tomRole;

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
                            
                            // Set is_sales checkbox
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
