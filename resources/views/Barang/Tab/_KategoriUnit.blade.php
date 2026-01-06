<div class="tab-pane fade" id="tab-kategori-unit" role="tabpanel">
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Kategori Unit</label>
            <input type="text" id="filter-kategori-unit-search"
                class="form-control form-control-sm"
                placeholder="Berat, Satuan...">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-sm btn-dark px-3"><i class="fa fa-filter me-1"></i> Filter</button>
        </div>
        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm"
                onclick="tambahKategoriUnit()">
                <i class="fa fa-plus me-1"></i> TAMBAH KATEGORI UNIT
            </button>
        </div>
    </div>
    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nama Kategori Unit</th>
                <th>Konversi Dasar</th>
                <th width="80" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody id="kategori-unit-table-body"></tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="kategori-unit-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0"
                id="kategori-unit-pagination-container"></ul>
        </nav>
    </div>

</div>
@include('Barang.Modal._KategoriUnit')
@include('Barang.Modal._ConfirmDelete')

@push('js')
<script>
let deleteKategoriUnitId = null;

document.addEventListener('DOMContentLoaded', () => {
    loadKategoriUnit();

    let timeout;
    document.getElementById('filter-kategori-unit-search')
        .addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => loadKategoriUnit(), 400);
        });
});

async function loadKategoriUnit(url = '/api/unit-categories-api') {
    const tbody = document.getElementById('kategori-unit-table-body');
    tbody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4">
                <div class="spinner-border text-primary"></div>
            </td>
        </tr>`;

    try {
        const search = document.getElementById('filter-kategori-unit-search').value;
        const fetchUrl = new URL(url, window.location.origin);
        if (search) fetchUrl.searchParams.append('search', search);

        const res = await fetch(fetchUrl);
        const result = await res.json();

        if (result.success) {
            renderKategoriUnitTable(result.data.data);
            renderKategoriUnitPagination(result.data);
        }
    } catch {
        tbody.innerHTML = `
            <tr><td colspan="3" class="text-center text-danger">
                Gagal memuat data
            </td></tr>`;
    }
}

function renderKategoriUnitTable(data) {
    const tbody = document.getElementById('kategori-unit-table-body');
    tbody.innerHTML = '';

    if (!data.length) {
        tbody.innerHTML = `
            <tr><td colspan="3" class="text-center text-muted">
                Data tidak ditemukan
            </td></tr>`;
        return;
    }

    data.forEach(item => {
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td class="fw-bold">${item.nama_kategori}</td>
                <td>${item.konversi_nilai}</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle"
                            data-bs-toggle="dropdown">Aksi</button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item"
                                   href="javascript:void(0)"
                                   onclick="editKategoriUnit(${item.id})">
                                   Edit
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger"
                                   href="javascript:void(0)"
                                   onclick="hapusKategoriUnit(${item.id})">
                                   Hapus
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        `);
    });
}

function renderKategoriUnitPagination(meta) {
    document.getElementById('kategori-unit-pagination-info').innerText =
        `Menampilkan ${meta.from ?? 0}–${meta.to ?? 0} dari ${meta.total} data`;

    const container = document.getElementById('kategori-unit-pagination-container');
    container.innerHTML = '';

    meta.links.forEach(link => {
        container.insertAdjacentHTML('beforeend', `
            <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                <a class="page-link"
                   href="javascript:void(0)"
                   onclick="loadKategoriUnit('${link.url}')">
                    ${link.label.replace('&laquo;', '').replace('&raquo;', '')}
                </a>
            </li>
        `);
    });
}

function tambahKategoriUnit() {
    document.getElementById('kategoriUnitModalTitle').innerText = 'Tambah Kategori Unit';
    document.getElementById('formKategoriUnit').reset();
    document.getElementById('kategoriUnitId').value = '';

    clearValidation();

    new bootstrap.Modal('#modalKategoriUnit').show();
}

async function editKategoriUnit(id) {
    const res = await fetch(`/api/unit-categories-api/${id}`);
    const result = await res.json();

    if (!result.success) return alert('Data tidak ditemukan');

    const data = result.data;

    document.getElementById('kategoriUnitModalTitle').innerText = 'Edit Kategori Unit';
    document.getElementById('kategoriUnitId').value = data.id;
    document.getElementById('nama_kategori').value = data.nama_kategori;
    document.getElementById('konversi_nilai').value = data.konversi_nilai;

    clearValidation();

    new bootstrap.Modal('#modalKategoriUnit').show();
}

document.getElementById('btnSaveKategoriUnit')
    .addEventListener('click', async () => {

    const id = document.getElementById('kategoriUnitId').value;
    const payload = {
        nama_kategori: document.getElementById('nama_kategori').value,
        konversi_nilai: document.getElementById('konversi_nilai').value
    };

    const url = id
        ? `/api/unit-categories-api/${id}`
        : `/api/unit-categories-api`;

    const method = id ? 'PUT' : 'POST';

    clearValidation();

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (!result.success) {
            showValidation(result.errors);
            return;
        }

        bootstrap.Modal.getInstance(
            document.getElementById('modalKategoriUnit')
        ).hide();

        loadKategoriUnit();

    } catch {
        alert('Gagal menyimpan data');
    }
});

function showValidation(errors = {}) {
    Object.keys(errors).forEach(key => {
        const input = document.getElementById(key);
        if (!input) return;

        input.classList.add('is-invalid');
        input.nextElementSibling.innerText = errors[key][0];
    });
}

function clearValidation() {
    document.querySelectorAll('#formKategoriUnit .is-invalid')
        .forEach(el => el.classList.remove('is-invalid'));
}

function hapusKategoriUnit(id) {
    deleteKategoriUnitId = id;

    document.getElementById('confirmDeleteText').innerText =
        'Yakin ingin menghapus kategori unit ini?';

    new bootstrap.Modal('#modalConfirmDelete').show();
}

document.getElementById('btnConfirmDelete')
    .addEventListener('click', async () => {

    if (!deleteKategoriUnitId) return;

    try {
        const res = await fetch(
            `/api/unit-categories-api/${deleteKategoriUnitId}`,
            { method: 'DELETE' }
        );

        const result = await res.json();

        if (!result.success) {
            alert(result.message || 'Gagal menghapus data');
            return;
        }

        bootstrap.Modal.getInstance(
            document.getElementById('modalConfirmDelete')
        ).hide();

        deleteKategoriUnitId = null;

        loadKategoriUnit();

    } catch {
        alert('Terjadi kesalahan saat menghapus data');
    }
});

</script>
@endpush
