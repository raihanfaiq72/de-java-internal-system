<div class="modal fade" id="modalKategoriProduk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title" id="modalKategoriTitle">Tambah Kategori</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="kategori-id">

                <div class="mb-2">
                    <label class="small fw-bold">Nama Kategori</label>
                    <input type="text" id="kategori-nama"
                           class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label class="small fw-bold">Parent Kategori</label>
                    <select id="kategori-parent"
                            class="form-select form-select-sm">
                        <option value="">— Tanpa Parent —</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="small fw-bold">Deskripsi</label>
                    <textarea id="kategori-deskripsi"
                              class="form-control form-control-sm"
                              rows="2"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-primary"
                        onclick="submitKategoriProduk()">Simpan</button>
            </div>

        </div>
    </div>
</div>
