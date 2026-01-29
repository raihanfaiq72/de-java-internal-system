<div class="tab-pane fade show" id="tab-brand" role="tabpanel">

    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari</label>
            <input type="text" id="filter-produk-search" class="form-control form-control-sm"
                placeholder="Ketik pencarian...">
        </div>

        <div class="col-md-auto">
            <button onclick="loadProductData()" class="btn btn-sm btn-dark px-3">
                <i class="fa fa-filter me-1"></i> Filter
            </button>
        </div>

        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="openBrandModal()">
                <i class="fa fa-plus me-1"></i> TAMBAH BRAND
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Brand</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="produk-table-body">
                <tr>
                    <td colspan="2" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="produk-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="produk-pagination-container"></ul>
        </nav>
    </div>

</div>

@include('Barang.Modal._Brand')

@push('js')
    <script>
        const API_URL = "{{ route('brand-api.index') }}";
        const SUPPLIER_URL = "{{ route('mitra-api.index') }}";

        document.addEventListener('DOMContentLoaded', () => {

        });

        async function loadSupplier() {
            if (!tsSupplier) return;

            try {
                const res = await fetch(SUPPLIER_URL);
                const result = await res.json();

                if (!result.success) return;

                const data = result.data.data || result.data;

                tsSupplier.clearOptions();
                tsSupplier.addOptions(data);

                tsSupplier.refreshOptions(false);
            } catch (error) {
                console.error('Gagal load supplier: ', error);
            }
        }

        async function openBrandModal() {
            document.getElementById('formBrand').reset();
            document.getElementById('brandId').value = '';
            document.getElementById('brandModalTitle').innerText = 'Tambah Brand Baru';

            if (tsSupplier) {
                tsSupplier.clear();
            }

            await loadSupplier();

            new bootstrap.Modal(document.getElementById('modalBrand')).show();
        }

        async function saveBrand() {
            const btn = document.getElementById('btnSaveBrand');
            const brandName = document.getElementById('nama_brand').value;
            const selectedSuppliers = tsSupplier.getValue(); // Mengambil array ID supplier

            if (!brandName) {
                alert('Nama Brand wajib diisi!');
                return;
            }

            const payload = {
                nama_brand: brandName,
                supplier_ids: selectedSuppliers // Mengirim array ID
            };

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if (result.success) {
                    alert('Brand berhasil disimpan!');
                    bootstrap.Modal.getInstance(document.getElementById('modalBrand')).hide();
                    if (typeof loadBrandData === 'function') loadBrandData();
                } else {
                    alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Save error:', error);
                alert('Terjadi kesalahan sistem saat menyimpan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan';
            }
        }
    </script>
@endpush
