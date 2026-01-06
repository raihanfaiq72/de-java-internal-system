<div class="tab-pane fade show active" id="tab-produk" role="tabpanel">
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari SKU / Nama</label>
            <input type="text" class="form-control form-control-sm" placeholder="Ketik pencarian...">
        </div>
        <div class="col-md-2">
            <label class="small fw-bold text-muted">Kategori</label>
            <select class="form-select form-select-sm">
                <option value="">Semua Kategori</option>
                <option value="1">Makanan</option>
            </select>
        </div>
        <div class="col-md-auto">
            <button class="btn btn-sm btn-dark px-3"><i class="fa fa-filter me-1"></i> Filter</button>
        </div>
        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalProduk"><i class="fa fa-plus me-1"></i> TAMBAH PRODUK</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="120">SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold">PROD-001</td>
                    <td>Kertas HVS A4 80gr</td>
                    <td>ATK</td>
                    <td>Rp 55.000</td>
                    <td><span class="badge bg-info">Tracked</span></td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle"
                                data-bs-toggle="dropdown">Aksi</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>
                                        Detail</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fa fa-edit me-2 text-warning"></i>
                                        Edit</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="fa fa-trash me-2"></i>
                                        Hapus</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('Barang.Modal._Produk')