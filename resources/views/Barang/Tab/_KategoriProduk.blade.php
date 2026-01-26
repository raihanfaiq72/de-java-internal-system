<div class="tab-pane fade" id="tab-kategori-produk" role="tabpanel">

    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Kategori</label>
            <input type="text" id="filter-kategori-search"
                   class="form-control form-control-sm"
                   placeholder="Nama kategori...">
        </div>

        <div class="col-md-auto">
            <button onclick="loadKategoriProduk()"
                    class="btn btn-sm btn-dark px-3">
                <i class="fa fa-filter me-1"></i> Filter
            </button>
        </div>

        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm"
                    onclick="tambahKategori()">
                <i class="fa fa-plus me-1"></i> TAMBAH KATEGORI
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Kategori</th>
                    <th>Parent</th>
                    <th>Deskripsi</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kategori-table-body">
                <tr>
                    <td colspan="4" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="kategori-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="kategori-pagination-container"></ul>
        </nav>
    </div>

</div>

@include('Barang.Modal._KategoriProduk')

@push('js')
<script>

    let deleteKategoriId = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadKategoriProduk();
        loadKategoriParentOption();

        let timeout;
        document.getElementById('filter-kategori-search')
            .addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => loadKategoriProduk(), 400);
            });
    });

    async function loadKategoriParentOption() {
        const select = document.getElementById('kategori-parent');
        select.innerHTML = `<option value="">— Tanpa Parent —</option>`;

        const res = await fetch(CATEGORY_API_URL);
        const result = await res.json();

        if (!result.success) return;

        result.data.data.forEach(item => {
            select.insertAdjacentHTML('beforeend', `
                <option value="${item.id}">
                    ${item.nama_kategori}
                </option>
            `);
        });
    }

    async function loadKategoriProduk(url = CATEGORY_API_URL) {
        if (typeof url !== 'string') url = CATEGORY_API_URL;

        const tbody = document.getElementById('kategori-table-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </td>
            </tr>`;

        try {
            const search = document.getElementById('filter-kategori-search').value;

            const fetchUrl = new URL(url);
            if (search) fetchUrl.searchParams.append('search', search);

            const res = await fetch(fetchUrl);
            const result = await res.json();

            if (result.success) {
                renderKategoriTable(result.data.data);
                renderKategoriPagination(result.data);
            }
        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        Gagal memuat data
                    </td>
                </tr>`;
        }
    }

    function renderKategoriTable(data) {
        const tbody = document.getElementById('kategori-table-body');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Data tidak ditemukan
                    </td>
                </tr>`;
            return;
        }

        data.forEach(item => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold">${item.nama_kategori}</td>
                    <td>${item.parent?.nama_kategori ?? '-'}</td>
                    <td>${item.deskripsi ?? '-'}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item"
                                       href="javascript:void(0)"
                                       onclick="editKategori(${item.id})">
                                        <i class="fa fa-edit me-2 text-warning"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger"
                                       href="javascript:void(0)"
                                       onclick="hapusKategori(${item.id})">
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

    function renderKategoriPagination(meta) {
        document.getElementById('kategori-pagination-info').innerText =
            `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;

        const container = document.getElementById('kategori-pagination-container');
        container.innerHTML = '';

        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

            container.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none"
                       href="javascript:void(0)"
                       onclick="loadKategoriProduk('${link.url}')">
                        ${label}
                    </a>
                </li>
            `);
        });
    }

    function tambahKategori() {
        document.getElementById('modalKategoriTitle').innerText = 'Tambah Kategori';
        document.getElementById('kategori-id').value = '';
        document.getElementById('kategori-nama').value = '';
        document.getElementById('kategori-parent').value = '';
        document.getElementById('kategori-deskripsi').value = '';

        new bootstrap.Modal('#modalKategoriProduk').show();
    }

    async function editKategori(id) {
        const res = await fetch(`${CATEGORY_API_URL}/${id}`);
        const result = await res.json();

        if (!result.success) return;

        const data = result.data;

        document.getElementById('modalKategoriTitle').innerText = 'Edit Kategori';
        document.getElementById('kategori-id').value = data.id;
        document.getElementById('kategori-nama').value = data.nama_kategori;
        document.getElementById('kategori-parent').value = data.parent_id ?? '';
        document.getElementById('kategori-deskripsi').value = data.deskripsi ?? '';

        new bootstrap.Modal('#modalKategoriProduk').show();
    }

    async function submitKategoriProduk() {
        const id = document.getElementById('kategori-id').value;

        const payload = {
            nama_kategori: document.getElementById('kategori-nama').value,
            parent_id: document.getElementById('kategori-parent').value || null,
            deskripsi: document.getElementById('kategori-deskripsi').value
        };

        const url = id
            ? `${CATEGORY_API_URL}/${id}`
            : CATEGORY_API_URL;

        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (!result.success) {
            alert(result.message);
            return;
        }

        bootstrap.Modal.getInstance(
            document.getElementById('modalKategoriProduk')
        ).hide();

        loadKategoriProduk();
    }

    function hapusKategori(id) {
        deleteKategoriId = id;

        document.getElementById('confirmDeleteText').innerText =
            'Yakin ingin menghapus kategori produk ini?';

        new bootstrap.Modal('#modalConfirmDelete').show();
    }

    document.getElementById('btnConfirmDelete')
        .addEventListener('click', async () => {

        if (!deleteKategoriId) return;

        const res = await fetch(
            `${CATEGORY_API_URL}/${deleteKategoriId}`,
            { method: 'DELETE' }
        );

        const result = await res.json();

        if (!result.success) {
            alert(result.message);
            return;
        }

        bootstrap.Modal.getInstance(
            document.getElementById('modalConfirmDelete')
        ).hide();

        deleteKategoriId = null;
        loadKategoriProduk();
        alert('Data berhasil dihapus');
    });

</script>
@endpush
