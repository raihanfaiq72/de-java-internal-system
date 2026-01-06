<div class="modal fade" id="modalConfirmDelete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title text-danger">
                    Konfirmasi Hapus
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fa fa-triangle-exclamation text-danger fs-2 mb-2"></i>
                <p class="mb-1 fw-semibold" id="confirmDeleteText">
                    Yakin ingin menghapus data ini?
                </p>
                <small class="text-muted">
                    Data yang dihapus tidak dapat dikembalikan
                </small>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button"
                    class="btn btn-sm btn-secondary"
                    data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button"
                    class="btn btn-sm btn-danger"
                    id="btnConfirmDelete">
                    Hapus
                </button>
            </div>

        </div>
    </div>
</div>
