<div class="modal fade" id="modalBrand" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: var(--dr-radius);">

            <div class="modal-header border-bottom-0 p-4 pb-2">
                <h5 class="dr-card-title fs-4" id="brandModalTitle">
                    <i class="iconoir-tag me-2 text-primary"></i> Tambah Brand
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 pt-2">
                <form id="formBrand">
                    <input type="hidden" id="brandId">

                    <div class="mb-4">
                        <label class="dr-label">Nama Brand / Merk <span class="text-danger">*</span></label>
                        <input type="text" id="nama_brand" class="dr-input"
                            placeholder="Cth: Castrol atau AHM">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="dr-label">Tautkan ke Supplier</label>
                        <select multiple id="suppliers" class="dr-input" placeholder="Pilih supplier terkait..."></select>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="dr-btn dr-btn-outline px-4" data-bs-dismiss="modal">
                    <i class="iconoir-cancel me-2"></i> Batal
                </button>
                <button type="button" class="dr-btn dr-btn-primary px-4 shadow-sm" id="btnSaveBrand" onclick="saveBrand()">
                    <i class="iconoir-check me-2"></i> Simpan Brand
                </button>
            </div>

        </div>
    </div>
</div>
