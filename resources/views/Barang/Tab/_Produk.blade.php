<div class="tab-pane fade show active" id="tab-produk" role="tabpanel">

    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari SKU / Nama</label>
            <input type="text" id="filter-produk-search" class="form-control form-control-sm"
                   placeholder="Ketik pencarian...">
        </div>

        <div class="col-md-2">
            <label class="small fw-bold text-muted">Kategori</label>
            <select id="filter-produk-kategori" class="form-select form-select-sm">
                <option value="">Semua Kategori</option>
            </select>
        </div>

        <div class="col-md-auto">
            <button onclick="loadProductData()" class="btn btn-sm btn-dark px-3">
                <i class="fa fa-filter me-1"></i> Filter
            </button>
        </div>

        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm"
                onclick="tambahProduk()">
                <i class="fa fa-plus me-1"></i> TAMBAH PRODUK
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="120">SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Unit</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Kuntitas</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="produk-table-body">
                <tr>
                    <td colspan="5" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="produk-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="produk-pagination-container"></ul>
        </nav>
    </div>

</div>

@include('Barang.Modal._Produk')

@push('js')
<script>
    const API_URL = 'http://localhost:8000/api/product-api';
    const CATEGORY_API_URL = 'http://localhost:8000/api/product-categories-api';
    const UNIT_CATEGORY_API = 'http://localhost:8000/api/unit-categories-api';
    const UNIT_API = 'http://localhost:8000/api/unit-api';

    document.addEventListener('DOMContentLoaded', () => {
        loadKategoriProduk();
        loadProductData();
    });

    const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(val);

    async function loadKategoriProduk() {
        const select = document.getElementById('filter-produk-kategori');
        if (!select) return;

        select.innerHTML = `<option value="">Semua Kategori</option>`;

        try {
            const res = await fetch(CATEGORY_API_URL);
            const result = await res.json();

            if (!result.success) return;

            result.data.data.forEach(item => {
                select.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.id}">${item.nama_kategori}</option>`
                );
            });
        } catch (error) {
            console.error('Gagal load kategori:', error);
        }
    }

    async function loadKategoriProdukModal() {
        const select = document.getElementById('produk-kategori');
        if (!select) return;

        select.innerHTML = `<option value="">-- Pilih --</option>`;

        const res = await fetch(CATEGORY_API_URL);
        const result = await res.json();

        if (!result.success) return;

        result.data.data.forEach(item => {
            select.insertAdjacentHTML(
                'beforeend',
                `<option value="${item.id}">${item.nama_kategori}</option>`
            );
        });
    }

    async function loadUnitCategoryModal() {
        const select = document.getElementById('produk-unit-category');
        if (!select) return;

        select.innerHTML = `<option value="">-- Pilih --</option>`;

        const res = await fetch(UNIT_CATEGORY_API);
        const result = await res.json();

        if (!result.success) return;

        // HATI-HATI: API kamu paginate
        const data = result.data.data ?? result.data;

        data.forEach(item => {
            select.insertAdjacentHTML(
                'beforeend',
                `<option value="${item.id}">${item.nama_kategori}</option>`
            );
        });
    }

    document.getElementById('produk-unit-category')
        ?.addEventListener('change', loadUnitByCategory);

    async function loadUnitByCategory() {
        const categoryId = this.value;
        const unitSelect = document.getElementById('produk-unit');

        unitSelect.innerHTML = `<option value="">Loading...</option>`;
        unitSelect.disabled = true;

        if (!categoryId) {
            unitSelect.innerHTML = `<option value="">-- Pilih --</option>`;
            unitSelect.disabled = false;
            return;
        }

        const res = await fetch(`${UNIT_API}?unit_category_id=${categoryId}`);
        const result = await res.json();

        if (!result.success) return;

        unitSelect.innerHTML = `<option value="">-- Pilih --</option>`;

        const data = result.data.data ?? result.data;

        data.forEach(item => {
            unitSelect.insertAdjacentHTML(
                'beforeend',
                `<option value="${item.id}">${item.nama_unit}</option>`
            );
        });

        unitSelect.disabled = false;
    }

    async function loadProductData(url = API_URL) {
        if (typeof url !== 'string') url = API_URL;

        const tbody = document.getElementById('produk-table-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </td>
            </tr>`;

        try {
            const search = document.getElementById('filter-produk-search').value;
            const kategori = document.getElementById('filter-produk-kategori').value;

            const fetchUrl = new URL(url);
            if (search) fetchUrl.searchParams.append('search', search);
            if (kategori) fetchUrl.searchParams.append('category', kategori);

            const res = await fetch(fetchUrl);
            const result = await res.json();

            if (result.success) {
                renderProductTable(result.data.data);
                renderProductPagination(result.data);
            }
        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        Gagal memuat data
                    </td>
                </tr>`;
        }
    }

    function renderProductTable(data) {
        const tbody = document.getElementById('produk-table-body');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        Data tidak ditemukan
                    </td>
                </tr>`;
            return;
        }

        data.forEach(item => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold">${item.sku_kode}</td>
                    <td>${item.nama_produk}</td>
                    <td>${item.category?.nama_kategori ?? '-'}</td>
                    <td>${item.unit?.nama_unit ?? '-'}</td>
                    <td>${formatIDR(item.harga_beli) ?? '-'}</td>
                    <td>${formatIDR(item.harga_jual) ?? '-'}</td>
                    <td>${item.qty ?? '-'}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle"
                                data-bs-toggle="dropdown">Aksi</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       onclick="detailProduk(${item.id})">
                                        <i class="fa fa-eye me-2 text-primary"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       onclick="editProduk(${item.id})">
                                        <i class="fa fa-edit me-2 text-warning"></i> Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                       onclick="hapusProduk(${item.id})">
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

    function renderProductPagination(meta) {
        document.getElementById('produk-pagination-info').innerText =
            `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;

        const container = document.getElementById('produk-pagination-container');
        container.innerHTML = '';

        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

            container.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none"
                       href="javascript:void(0)"
                       onclick="loadProductData('${link.url}')">
                        ${label}
                    </a>
                </li>
            `);
        });
    }
</script>

<script>
let deleteProdukId = null;

async function tambahProduk() {
    document.getElementById('modalProdukTitle').innerText = 'Tambah Produk';

    document.getElementById('produk-id').value = '';
    document.getElementById('produk-sku').value = '';
    document.getElementById('produk-nama').value = '';
    document.getElementById('produk-harga-beli').value = '';
    document.getElementById('produk-harga-jual').value = '';
    document.getElementById('produk-qty').value = '';
    document.getElementById('produk-deskripsi').value = '';

    await loadKategoriProdukModal();
    await loadUnitCategoryModal();

    document.getElementById('produk-unit').innerHTML =
        `<option value="">-- Pilih --</option>`;
    document.getElementById('produk-unit').disabled = true;

    new bootstrap.Modal('#modalProduk').show();
}

async function editProduk(id) {
    const res = await fetch(`${API_URL}/${id}`);
    const result = await res.json();

    if (!result.success) return alert(result.message);

    const p = result.data;

    document.getElementById('modalProdukTitle').innerText = 'Edit Produk';

    document.getElementById('produk-id').value = p.id;
    document.getElementById('produk-sku').value = p.sku_kode;
    document.getElementById('produk-nama').value = p.nama_produk;
    document.getElementById('produk-kategori').value = p.product_category_id;
    document.getElementById('produk-unit').value = p.unit_id;
    document.getElementById('produk-harga-beli').value = p.harga_beli;
    document.getElementById('produk-harga-jual').value = p.harga_jual;
    document.getElementById('produk-qty').value = p.qty;
    document.getElementById('produk-deskripsi').value = p.deskripsi_produk;

    new bootstrap.Modal('#modalProduk').show();
}

async function submitProduk() {
    const id = document.getElementById('produk-id').value;

    const payload = {
        sku_kode: document.getElementById('produk-sku').value,
        nama_produk: document.getElementById('produk-nama').value,
        product_category_id: document.getElementById('produk-kategori').value,
        unit_category_id: document.getElementById('produk-unit-category').value,
        unit_id: document.getElementById('produk-unit').value,
        harga_beli: document.getElementById('produk-harga-beli').value,
        harga_jual: document.getElementById('produk-harga-jual').value,
        qty: document.getElementById('produk-qty').value,
        deskripsi_produk: document.getElementById('produk-deskripsi').value,
        akun_penjualan_id: 1,
        akun_pembelian_id: 1,
        akun_diskon_penjualan_id: 1,
        akun_diskon_pembelian_id: 1,
    };

    const url = id ? `${API_URL}/${id}` : API_URL;
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
        document.getElementById('modalProduk')
    ).hide();

    loadProductData();
}

function hapusProduk(id) {
    if (!confirm('Yakin ingin menghapus produk ini?')) return;

    fetch(`${API_URL}/${id}`, {
        method: 'DELETE'
    })
    .then(res => res.json())
    .then(result => {
        if (!result.success) {
            alert(result.message);
            return;
        }
        loadProductData();
    });
}
</script>

@endpush