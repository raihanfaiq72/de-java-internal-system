<div class="modal fade" id="modalKategoriUnit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title" id="kategoriUnitModalTitle">
                    Tambah Kategori Unit
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formKategoriUnit">
                    <input type="hidden" id="kategoriUnitId">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori</label>
                        <input type="text" id="nama_kategori"
                            class="form-control form-control-sm"
                            placeholder="Contoh: Berat">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konversi Dasar</label>
                        <input type="number" step="0.01" id="konversi_nilai"
                            class="form-control form-control-sm"
                            placeholder="1.00">
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn btn-sm btn-secondary"
                    data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button"
                    class="btn btn-sm btn-primary"
                    id="btnSaveKategoriUnit">
                    Simpan
                </button>
            </div>

        </div>
    </div>
</div>
