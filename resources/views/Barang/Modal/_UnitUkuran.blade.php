<div class="modal fade" id="modalUnitUkuran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title" id="modalUnitTitle">Tambah Unit</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="unit-id">

                <div class="mb-2">
                    <label class="small fw-bold">Nama Unit</label>
                    <input type="text" id="unit-nama" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label class="small fw-bold">Simbol</label>
                    <input type="text" id="unit-simbol" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label class="small fw-bold">Kategori Unit</label>
                    <select id="unit-category" class="form-select form-select-sm"></select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-sm btn-primary" onclick="submitUnit()">
                    Simpan
                </button>
            </div>

        </div>
    </div>
</div>
