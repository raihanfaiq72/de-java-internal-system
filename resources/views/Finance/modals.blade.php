<!-- Create Account Modal -->
<div class="modal fade" id="modalCreateAccount" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAccountTitle">Buat Akun Keuangan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCreateAccount" onsubmit="submitAccount(event)">
                <div class="modal-body p-0">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                                data-bs-target="#info-panel" type="button" role="tab">Informasi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settings-tab" data-bs-toggle="tab"
                                data-bs-target="#settings-panel" type="button" role="tab">Pengaturan Akun</button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Informasi Tab -->
                        <div class="tab-pane fade show active" id="info-panel" role="tabpanel">

                            <div class="mb-3">
                                <label class="form-label">Tipe Akun Keuangan *</label>
                                <select class="form-select" name="tipe_akun" id="tipe_akun" required
                                    onchange="handleAccountTypeChange()">
                                    <option value="" selected disabled>Pilih Tipe Akun</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Corporate Card">Corporate Card</option>
                                </select>
                            </div>

                            <!-- Dynamic Fields Container -->
                            <div id="dynamic-fields">
                                <!-- Default/Cash Fields -->
                                <div id="fields-cash" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kas *</label>
                                        <input type="text" class="form-control" name="nama_akun_cash"
                                            id="nama_akun_cash" placeholder="Contoh: Kas Kecil">
                                    </div>
                                </div>

                                <!-- Bank/Card Fields -->
                                <div id="fields-bank" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Bank *</label>
                                        <input type="text" class="form-control" name="bank_name" id="bank_name"
                                            placeholder="cth. BRI, BCA, Mandiri, dll.">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">No. Rekening Bank *</label>
                                            <input type="text" class="form-control" name="bank_account_number"
                                                id="bank_account_number">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama pada Rekening *</label>
                                            <input type="text" class="form-control" name="bank_account_name"
                                                id="bank_account_name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Cabang Bank</label>
                                            <input type="text" class="form-control" name="bank_branch"
                                                id="bank_branch">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kota Cabang</label>
                                            <input type="text" class="form-control" name="bank_city" id="bank_city">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mata Uang * <i class="iconoir-info-circle"
                                        title="Mata uang default"></i></label>
                                <select class="form-select" name="currency" id="currency" readonly>
                                    <option value="IDR" selected>Rupiah</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pengaturan Akun Tab -->
                        <div class="tab-pane fade" id="settings-panel" role="tabpanel">

                            <!-- Accordion / Radio for Mode -->
                            <div class="accordion" id="accountModeAccordion">
                                <!-- Create New -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#mode-new" aria-expanded="true" onclick="setMode('new')">
                                            Detail Akun
                                        </button>
                                    </h2>
                                    <div id="mode-new" class="accordion-collapse collapse show"
                                        data-bs-parent="#accountModeAccordion">
                                        <div class="accordion-body">
                                            <div class="alert alert-light border">
                                                <h6 class="alert-heading">Info Akun</h6>
                                                <p class="mb-1">Kode Akun: <span id="preview-code"
                                                        class="fw-bold text-primary">...</span></p>
                                                <p class="mb-0">Akun Keuangan: <span id="preview-type"
                                                        class="fw-bold">...</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="mode" id="account_mode" value="new">
                                <input type="hidden" name="existing_coa_id" id="existing_coa_id">
                                <input type="hidden" name="kode_akun" id="generated_code">
                                <input type="hidden" name="nama_akun" id="final_nama_akun">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                    <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="modalTransfer" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Transfer Internal Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTransfer" onsubmit="submitTransaction(event, 'transfer')">
                <div class="modal-body">
                    <input type="hidden" name="type" value="transfer">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Transfer Dari *</label>
                            <select class="form-select" name="from_account_id" id="from_account_id" required>
                                <option value="">Pilih Akun</option>
                                @foreach ($all_accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Setor Ke *</label>
                            <select class="form-select" name="to_account_id" id="to_account_id" required>
                                <option value="">Pilih Akun</option>
                                @foreach ($all_accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount"
                            placeholder="Isi nominal transaksi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Referensi</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" name="lampiran">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" onclick="this.form.status.value='draft'" class="btn btn-warning">Simpan
                        Draft</button>
                    <button type="submit" onclick="this.form.status.value='posted'" class="btn btn-success">Simpan
                        Posted</button>
                    <input type="hidden" name="status" value="posted">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Income Modal -->
<div class="modal fade" id="modalIncome" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Pemasukan Lain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formIncome" onsubmit="submitTransaction(event, 'income')">
                <div class="modal-body">
                    <input type="hidden" name="type" value="income">

                    <div class="mb-3">
                        <label class="form-label">Masuk Ke Akun *</label>
                        <select class="form-select" name="to_account_id" id="income_to_account_id" required>
                            <option value="">Pilih Akun</option>
                            @foreach ($all_accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount" placeholder="Isi nominal"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" name="lampiran">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" onclick="this.form.status.value='draft'" class="btn btn-warning">Simpan
                        Draft</button>
                    <button type="submit" onclick="this.form.status.value='posted'" class="btn btn-success">Simpan
                        Posted</button>
                    <input type="hidden" name="status" value="posted">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Expense Modal -->
<div class="modal fade" id="modalExpense" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Pengeluaran Lain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formExpense" onsubmit="submitTransaction(event, 'expense')">
                <div class="modal-body">
                    <input type="hidden" name="type" value="expense">

                    <div class="mb-3">
                        <label class="form-label">Keluar Dari Akun *</label>
                        <select class="form-select" name="from_account_id" id="expense_from_account_id" required>
                            <option value="">Pilih Akun</option>
                            @foreach ($all_accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount" placeholder="Isi nominal"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" name="lampiran">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" onclick="this.form.status.value='draft'" class="btn btn-warning">Simpan
                        Draft</button>
                    <button type="submit" onclick="this.form.status.value='posted'" class="btn btn-success">Simpan
                        Posted</button>
                    <input type="hidden" name="status" value="posted">
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        // Handle Transaction Submission
        function submitTransaction(e, type) {
            e.preventDefault();
            let formData = new FormData(e.target);

            $.ajax({
                url: "{{ route('finance.transaction.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function(err) {
                    alert('Terjadi kesalahan: ' + (err.responseJSON ? err.responseJSON.message :
                        'Unknown error'));
                    console.error(err);
                }
            });
        }

        // Handle Account Creation
        function handleAccountTypeChange() {
            let type = document.getElementById('tipe_akun').value;
            let cashFields = document.getElementById('fields-cash');
            let bankFields = document.getElementById('fields-bank');
            // let parentSelect = document.getElementById('parent_id'); // No longer needed

            // Reset
            cashFields.classList.add('d-none');
            bankFields.classList.add('d-none');
            document.getElementById('preview-type').innerText = type || '...';

            if (type === 'Cash') {
                cashFields.classList.remove('d-none');
            } else if (type === 'Bank' || type === 'Corporate Card') {
                bankFields.classList.remove('d-none');
            }

            // Generate Code directly based on type
            generateCode();
        }

        function setMode(mode) {
            document.getElementById('account_mode').value = mode;
            // Simplified mode logic since we only support 'new' effectively for now
        }

        function generateCode() {
            let type = document.getElementById('tipe_akun').value;
            if (!type) return;

            $.ajax({
                url: "{{ route('finance.account.next-code') }}",
                type: "GET",
                data: {
                    type: type
                },
                success: function(res) {
                    if (res.code) {
                        document.getElementById('preview-code').innerText = res.code;
                        document.getElementById('generated_code').value = res.code;
                    }
                }
            });
        }

        function submitAccount(e) {
            e.preventDefault();

            // Handle disabled fields for FormData
            let typeSelect = document.getElementById('tipe_akun');
            let wasDisabled = typeSelect.disabled;
            if (wasDisabled) {
                typeSelect.disabled = false;
            }

            let formData = new FormData(e.target);

            if (wasDisabled) {
                typeSelect.disabled = true;
            }

            // Populate Hidden Name field based on Type
            let type = formData.get('tipe_akun');
            if (type === 'Cash') {
                formData.set('nama_akun', formData.get('nama_akun_cash'));
            } else {
                // For Bank, combine Name + Account Name? Or just use Bank Name?
                // Let's use Bank Name + Account Name for clarity
                let bankName = formData.get('bank_name');
                formData.set('nama_akun', bankName);
            }

            $.ajax({
                url: "{{ route('finance.account.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function(err) {
                    alert('Terjadi kesalahan: ' + (err.responseJSON ? err.responseJSON.message :
                        'Unknown error'));
                    console.error(err);
                }
            });
        }
        // Initialize Mode
        document.addEventListener('DOMContentLoaded', function() {
            setMode('new');
        });
    </script>
@endpush

<!-- Edit Transaction Modal -->
<div class="modal fade" id="modalEditTransaction" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-black fw-bold">
                    <i class="iconoir-edit-pencil me-2"></i>Edit Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditTransaction" onsubmit="submitEditTransaction(event)">
                <div class="modal-body">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="edit_transaction_id" id="edit_transaction_id">

                    <!-- Type Badge Info -->
                    <div class="mb-3">
                        <label class="form-label">Tipe Transaksi</label>
                        <div id="edit_type_badge"></div>
                        <input type="hidden" name="type" id="edit_type">
                    </div>

                    <!-- Transfer: From & To -->
                    <div id="edit_transfer_fields" class="d-none">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transfer Dari *</label>
                                <select class="form-select" name="from_account_id" id="edit_from_account_id">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($all_accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} -
                                            {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Setor Ke *</label>
                                <select class="form-select" name="to_account_id" id="edit_to_account_id">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($all_accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} -
                                            {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Income: To Account -->
                    <div id="edit_income_fields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Masuk Ke Akun *</label>
                            <select class="form-select" name="to_account_id" id="edit_income_to_account_id">
                                <option value="">Pilih Akun</option>
                                @foreach ($all_accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Expense: From Account -->
                    <div id="edit_expense_fields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Keluar Dari Akun *</label>
                            <select class="form-select" name="from_account_id" id="edit_expense_from_account_id">
                                <option value="">Pilih Akun</option>
                                @foreach ($all_accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi *</label>
                        <input type="date" class="form-control" name="transaction_date"
                            id="edit_transaction_date" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount" id="edit_amount"
                            placeholder="Isi nominal transaksi" required min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="2"
                            placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="draft">Draft</option>
                            <option value="posted">Posted</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lampiran Baru <small class="text-muted">(kosongkan jika tidak
                                diubah)</small></label>
                        <input type="file" class="form-control" name="lampiran">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="iconoir-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Report Modal -->
<div class="modal fade" id="modalPreviewReport" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="iconoir-page-search me-2"></i>Preview Laporan: <span id="preview-account-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Dari Tanggal</label>
                        <input type="date" class="form-control" id="preview-start-date"
                            value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="preview-end-date"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" onclick="fetchPreviewData()">
                            <i class="iconoir-refresh me-1"></i> Perbarui Preview
                        </button>
                    </div>
                </div>

                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover mb-0" id="preview-table">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th style="width: 140px;">Tanggal</th>
                                <th style="min-width: 300px;">Keterangan</th>
                                <th class="text-end" style="width: 160px;">Debit</th>
                                <th class="text-end" style="width: 160px;">Kredit</th>
                                <th class="text-end" style="width: 170px;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="preview-body">
                            <!-- Data populated via AJAX -->
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-center">TOTAL</td>
                                <td class="text-end text-success" id="total-debit">-</td>
                                <td class="text-end text-danger" id="total-credit">-</td>
                                <td class="text-end text-dark" id="final-balance">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div id="preview-loading" class="text-center d-none py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data preview...</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success text-white px-4" onclick="downloadReport()">
                    <i class="iconoir-download me-1"></i> Download Excel
                </button>
            </div>
        </div>
    </div>
</div>
