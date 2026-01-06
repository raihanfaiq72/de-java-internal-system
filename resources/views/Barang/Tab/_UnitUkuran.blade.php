<div class="tab-pane fade" id="tab-unit" role="tabpanel">
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Unit / Simbol</label>
            <input type="text" id="filter-unit-search" class="form-control form-control-sm" placeholder="Pcs, Kg, dll...">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-sm btn-dark px-3" onclick="loadUnit()">
                <i class="fa fa-filter me-1"></i> Filter
            </button>
        </div>
        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnitUkuran"><i class="fa fa-plus me-1"></i> TAMBAH UNIT</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Unit</th>
                    <th>Simbol</th>
                    <th>Kategori Unit</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="unit-table-body">
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="unit-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="unit-pagination-container"></ul>
        </nav>
    </div>

</div>
@include('Barang.Modal._UnitUkuran')

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadUnit();
    });

    async function loadUnit(url = 'http://localhost:8000/api/unit-api') {
        if (typeof url !== 'string') url = 'http://localhost:8000/api/unit-api';

        const tbody = document.getElementById('unit-table-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </td>
            </tr>`;

        try {
            const search = document.getElementById('filter-unit-search')?.value || '';
            const fetchUrl = new URL(url, window.location.origin);
            if (search) fetchUrl.searchParams.append('search', search);

            const res = await fetch(fetchUrl);
            const result = await res.json();

            console.log(result);

            if (result.success) {
                renderUnitTable(result.data.data);
                renderUnitPagination(result.data);
            }
        } catch {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderUnitTable(data) {
        const tbody = document.getElementById('unit-table-body');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Data tidak ditemukan</td></tr>`;
            return;
        }

        data.forEach(item => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold">${item.nama_unit}</td>
                    <td>${item.simbol}</td>
                    <td>${item.category?.nama_kategori ?? '-'}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">Aksi</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="editUnit(${item.id})">
                                        <i class="fa fa-edit me-2 text-warning"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="hapusUnit(${item.id})">
                                        <i class="fa fa-trash me-2"></i> Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    function renderUnitPagination(meta) {
        document.getElementById('unit-pagination-info').innerText =
            `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;

        const container = document.getElementById('unit-pagination-container');
        container.innerHTML = '';

        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

            container.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none" href="javascript:void(0)"
                    onclick="loadUnit('${link.url}')">${label}</a>
                </li>
            `);
        });
    }
</script>
@endpush