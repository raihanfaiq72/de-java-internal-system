<div class="modal fade" id="modalBrand" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="brandModalTitle">
                    <i class="fa fa-tags me-2 text-primary"></i>Tambah Brand
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formBrand">
                    <input type="hidden" id="brandId">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" id="nama_brand" class="form-control"
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
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSaveBrand" onclick="saveBrand()">
                    <i class="fa fa-save me-2"></i>Simpan Brand
                </button>
            </div>

        </div>
    </div>
</div>
