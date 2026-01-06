<div class="tab-pane fade" id="tab-unit" role="tabpanel">
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Unit / Simbol</label>
            <input type="text" class="form-control form-control-sm" placeholder="Pcs, Kg, dll...">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-sm btn-dark px-3"><i class="fa fa-filter me-1"></i> Filter</button>
        </div>
        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnitUkuran"><i class="fa fa-plus me-1"></i> TAMBAH UNIT</button>
        </div>
    </div>
    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Nama Unit</th>
                <th>Simbol</th>
                <th>Kategori Unit</th>
                <th width="80" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kilogram</td>
                <td>Kg</td>
                <td>Berat</td>
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
@include('Barang.Modal._UnitUkuran')