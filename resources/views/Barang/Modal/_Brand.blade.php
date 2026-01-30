<div class="modal fade" id="modalBrand" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title" id="brandModalTitle">
                    Tambah Brand
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formBrand">
                    <input type="hidden" id="brandId">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Brand</label>
                        <input type="text" id="nama_brand" class="form-control form-control-sm"
                            placeholder="Contoh: Castrol">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Supplier</label>
                        <select multiple id="suppliers" class="form-select" placeholder="Pilih supplier ..."></select>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="btnSaveBrand">
                    Simpan
                </button>
            </div>

        </div>
    </div>
</div>
