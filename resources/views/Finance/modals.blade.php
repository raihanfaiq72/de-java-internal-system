<!-- Create Account Modal -->
<div class="modal fade" id="modalCreateAccount" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Akun Keuangan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCreateAccount" onsubmit="submitAccount(event)">
                <div class="modal-body p-0">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-panel" type="button" role="tab">Informasi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-panel" type="button" role="tab">Pengaturan Akun</button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Informasi Tab -->
                        <div class="tab-pane fade show active" id="info-panel" role="tabpanel">
                            
                            <div class="mb-3">
                                <label class="form-label">Tipe Akun Keuangan *</label>
                                <select class="form-select" name="tipe_akun" id="tipe_akun" required onchange="handleAccountTypeChange()">
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
                                        <input type="text" class="form-control" name="nama_akun_cash" placeholder="Contoh: Kas Kecil">
                                    </div>
                                </div>

                                <!-- Bank/Card Fields -->
                                <div id="fields-bank" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Bank *</label>
                                        <input type="text" class="form-control" name="bank_name" placeholder="cth. BRI, BCA, Mandiri, dll.">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">No. Rekening Bank *</label>
                                            <input type="text" class="form-control" name="bank_account_number">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama pada Rekening *</label>
                                            <input type="text" class="form-control" name="bank_account_name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Cabang Bank</label>
                                            <input type="text" class="form-control" name="bank_branch">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kota Cabang</label>
                                            <input type="text" class="form-control" name="bank_city">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mata Uang * <i class="iconoir-info-circle" title="Mata uang default"></i></label>
                                <select class="form-select" name="currency" readonly>
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
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#mode-new" aria-expanded="true" onclick="setMode('new')">
                                            Buat Akun Baru
                                        </button>
                                    </h2>
                                    <div id="mode-new" class="accordion-collapse collapse show" data-bs-parent="#accountModeAccordion">
                                        <div class="accordion-body">
                                            <div class="alert alert-light border">
                                                <h6 class="alert-heading">Akan Terbuat Akun</h6>
                                                <p class="mb-1">Kode Akun: <span id="preview-code" class="fw-bold text-primary">...</span></p>
                                                <p class="mb-0">Akun Keuangan: <span id="preview-type" class="fw-bold">...</span></p>
                                            </div>

                                            <div class="mb-3 d-none">
                                                <label class="form-label">Pilih Akun yang ada</label>
                                                <p class="text-muted small mb-1">Akun Penyimpanan Saldo</p>
                                                <select class="form-select" name="parent_id" id="parent_id" onchange="generateCode()">
                                                    <option value="" selected disabled>Pilih Akun Induk</option>
                                                    @foreach($parent_accounts as $parent)
                                                        <option value="{{ $parent->id }}" data-code="{{ $parent->kode_akun }}">{{ $parent->kode_akun }} - {{ $parent->nama_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Use Existing - REMOVED since we are using standalone table now, can't easily link to existing COA unless migrated -->
                                <!-- Or maybe user wants to link to existing Financial Account? -->
                                <!-- For now, let's hide "Existing" or adapt it to FinancialAccount -->
                                
                                <input type="hidden" name="mode" id="account_mode" value="new">
                            <input type="hidden" name="kode_akun" id="generated_code">
                            <input type="hidden" name="nama_akun" id="final_nama_akun">

                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batalkan</button>
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
                                @foreach($all_accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Setor Ke *</label>
                            <select class="form-select" name="to_account_id" id="to_account_id" required>
                                <option value="">Pilih Akun</option>
                                @foreach($all_accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount" placeholder="Isi nominal transaksi" required>
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
                    <button type="submit" class="btn btn-success">Simpan</button>
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
                            @foreach($all_accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount" placeholder="Isi nominal" required>
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
                    <button type="submit" class="btn btn-success">Simpan</button>
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
                            @foreach($all_accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" class="form-control" name="amount" placeholder="Isi nominal" required>
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
                    <button type="submit" class="btn btn-success">Simpan</button>
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
                if(res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            },
            error: function(err) {
                alert('Terjadi kesalahan: ' + (err.responseJSON ? err.responseJSON.message : 'Unknown error'));
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
        if(!type) return;

        $.ajax({
            url: "{{ route('finance.account.next-code') }}",
            type: "GET",
            data: { type: type },
            success: function(res) {
                if(res.code) {
                    document.getElementById('preview-code').innerText = res.code;
                    document.getElementById('generated_code').value = res.code;
                }
            }
        });
    }

    function submitAccount(e) {
        e.preventDefault();
        let formData = new FormData(e.target);
        
        // Populate Hidden Name field based on Type
        let type = formData.get('tipe_akun');
        if (type === 'Cash') {
            formData.set('nama_akun', formData.get('nama_akun_cash'));
        } else {
            // For Bank, use "Bank Name - Account Name" or just Bank Name?
            // User example: "Nama Bank: BRI", "Nama Kas: Kas Kecil"
            // Let's use Bank Name + Account Number for uniqueness?
            // Or just Bank Name. Let's use the field `bank_name` directly in backend?
            // But COA needs `nama_akun`.
            // Let's combine: "BRI - 123456"
            let bankName = formData.get('bank_name');
            let accName = formData.get('bank_account_name');
            formData.set('nama_akun', bankName + (accName ? ' - ' + accName : ''));
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
                if(res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message);
                }
            },
            error: function(err) {
                 alert('Terjadi kesalahan: ' + (err.responseJSON ? err.responseJSON.message : 'Unknown error'));
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
