@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Manajemen Armada</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Fleets</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">Daftar Armada</h6>
                            <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openFleetModal()">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Armada Baru
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
                                        class="form-control border-start-0 ps-0 shadow-none" placeholder="Nama Armada...">
                                </div>
                            </div>
                            <div class="col-lg-7 text-end">
                                <button onclick="loadFleetsData()"
                                    class="btn btn-dark fw-bold py-2 px-4 shadow-sm btn-sm">FILTER</button>
                                <button onclick="resetFilter()"
                                    class="btn btn-light border fw-bold text-dark py-2 btn-sm">RESET</button>
                            </div>
                        </div>

                        <!-- Table Structure -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="fleetTable">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th width="50" class="ps-4 text-center">#</th>
                                        <th width="200">Nama Armada</th>
                                        <th width="150">Plat Nomor</th>
                                        <th width="150">Tipe BBM</th>
                                        <th width="120" class="text-center">KM/Liter</th>
                                        <th width="150" class="text-center">Terdaftar</th>
                                        <th width="200" class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="fleetTableBody" class="border-top-0">
                                    <!-- Rows injected by JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
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

    @include('Fleet.Modal.modal-fullscreen')
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

        #fleetTable thead th {
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        #fleetTable tbody td {
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
        const FLEET_URL = "{{ route('fleet-api.index') }}";

        async function loadFleetsData(url = FLEET_URL) {
            const tbody = document.getElementById('fleetTableBody');
            tbody.innerHTML =
                '<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data fleet...</p></td></tr>';

            try {
                const searchVal = document.getElementById('filter-search').value;
                const finalUrl = new URL(url, window.location.origin);
                if (searchVal) finalUrl.searchParams.set('search', searchVal);

                const response = await fetch(finalUrl.toString());
                const result = await response.json();

                if (result.success) {
                    renderFleetsData(result.data.data ? result.data.data : result.data);
                    renderPagination(result.data);
                }
            } catch (error) {
                console.error(error);
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger p-4">Gagal memuat data.</td></tr>';
            }
        }

        function renderFleetsData(data) {
            const tbody = document.getElementById('fleetTableBody');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center p-5 text-muted fst-italic">Data armada tidak ditemukan.</td></tr>';
                return;
            }

            data.forEach((item, index) => {
                const tr = `
                    <tr class="clickable-row">
                        <td class="text-center ps-4 text-muted small">${index + 1}</td>
                        <td class="fw-bold text-dark">${item.fleet_name}</td>
                        <td class="text-muted f-mono">${item.license_plate}</td>
                        <td class="text-muted">${item.fuel_type}</td>
                        <td class="text-center"><span class="f-badge f-active">${item.km_per_liter || '-'}</span></td>
                        <td class="text-center text-muted small">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-primary me-1" onclick="openFleetModal(${item.id})"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-danger" onclick="deleteFleet(${item.id})"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', tr);
            });
        }

        async function deleteFleet(id) {
            if (!await macConfirm('Hapus armada', 'Yakin ingin menghapus armada ini?')) return;
            try {
                const res = await fetch(`${FLEET_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const dat = await res.json();
                if (dat.success) {
                    alert('Berhasil dihapus');
                    loadFleetsData();
                } else alert(dat.message);
            } catch (e) {
                alert('Gagal menghapus');
            }
        }

        function renderPagination(meta) {
            const c = document.getElementById('pagination-container');
            c.innerHTML = '';
            if (!meta || !meta.links) return;
            document.getElementById('pagination-info').innerText = `${meta.from || 0}-${meta.to || 0} dari ${meta.total} data`;
            meta.links.forEach(l => {
                const cls = l.active ? 'bg-primary text-white' : 'bg-white text-dark';
                c.insertAdjacentHTML('beforeend',
                    `<li class="page-item ${!l.url ? 'disabled' : ''}"><a class="page-link border-0 mx-1 rounded shadow-sm fw-bold ${cls}" href="#" onclick="loadFleetsData('${l.url}')">${l.label}</a></li>`
                );
            });
        }

        function resetFilter() {
            document.getElementById('filter-search').value = '';
            loadFleetsData();
        }

        document.addEventListener('DOMContentLoaded', () => loadFleetsData());
    </script>
@endpush
