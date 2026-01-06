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
                data-bs-toggle="modal" data-bs-target="#modalProduk">
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
                    <th>Unit</th>
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
    document.addEventListener('DOMContentLoaded', function () {
        loadProductData();
        loadKategoriProduk();
    });

    function loadKategoriProduk() {
        fetch('api/product-categories-api')
            .then(res => res.json())
            .then(res => {
                console.log(res);
                if (!res.success) return;

                const select = document.getElementById('filter-produk-kategori');

                res.data.data.forEach(item => {
                    select.insertAdjacentHTML('beforeend', `
                        <option value="${item.id}">${item.nama_kategori}</option>
                    `);
                });
            });
    }

    function loadProductData(url = null) {
        const search = document.getElementById('filter-produk-search').value;
        const kategori = document.getElementById('filter-produk-kategori').value;

        let apiUrl = url ?? `api/product-api?search=${search}&category=${kategori}`;

        fetch(apiUrl)
            .then(res => res.json())
            .then(res => {
                if (!res.success) return;

                renderProductTable(res.data.data);
                renderProductPagination(res.data);
            });
    }

    function renderProductTable(data) {
        const tbody = document.getElementById('produk-table-body');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted">Data tidak ditemukan</td>
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
                    <td>${item.harga_beli ?? '-'}</td>
                    <td>${item.harga_jual ?? '-'}</td>
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
@endpush