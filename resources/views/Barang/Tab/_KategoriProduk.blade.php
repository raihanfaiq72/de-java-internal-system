<div class="tab-pane fade" id="tab-kategori-produk" role="tabpanel">
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Kategori</label>
            <input type="text" class="form-control form-control-sm" placeholder="Nama kategori...">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-sm btn-dark px-3"><i class="fa fa-filter me-1"></i> Filter</button>
        </div>
        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKategoriProduk"><i class="fa fa-plus me-1"></i> TAMBAH
                KATEGORI</button>
        </div>
    </div>
    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nama Kategori</th>
                <th>Parent</th>
                <th>Deskripsi</th>
                <th width="80" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Makanan Ringan</td>
                <td>Makanan</td>
                <td>Semua jenis snack</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle"
                            data-bs-toggle="dropdown">Aksi</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="#">Hapus</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@include('Barang.Modal._KategoriProduk')