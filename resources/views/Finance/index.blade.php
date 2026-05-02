@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Keuangan</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Finance</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-2 d-flex justify-content-between align-items-center">
                        <ul class="nav nav-pills nav-fill" id="financeTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-3 fw-semibold py-2" id="dashboard-tab"
                                    data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                                    <i class="iconoir-dashboard me-2"></i>Dashboard
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-3 fw-semibold py-2" id="transaksi-tab" data-bs-toggle="tab"
                                    data-bs-target="#transaksi" type="button" role="tab">
                                    <i class="iconoir-list me-2"></i>Transaksi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-3 fw-semibold py-2" id="menu-cepat-tab" data-bs-toggle="tab"
                                    data-bs-target="#menu-cepat" type="button" role="tab">
                                    <i class="iconoir-flash me-2"></i>Menu Cepat
                                </button>
                            </li>
                        </ul>
                        <div>
                            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                                onclick="resetCreateModal()" data-bs-toggle="modal" data-bs-target="#modalCreateAccount">
                                <i class="iconoir-plus-circle me-1"></i> Buat Akun Keuangan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="financeTabContent">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Akun Keuangan (Kas & Bank)</h5>
                            <hr class="flex-grow-1 ms-3 text-muted">
                        </div>

                        <div class="row g-4">
                            @forelse($accounts as $account)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 transition-hover">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                                        @if ($account->type == 'Bank')
                                                            <i class="iconoir-bank text-primary fs-4"></i>
                                                        @elseif($account->type == 'Corporate Card')
                                                            <i class="iconoir-credit-card text-primary fs-4"></i>
                                                        @else
                                                            <i class="iconoir-wallet text-primary fs-4"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h5 class="card-title mb-1 fw-bold text-dark">{{ $account->name }}
                                                        </h5>
                                                        <span
                                                            class="badge bg-light text-secondary border">{{ $account->code }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button class="btn btn-icon btn-sm btn-light rounded-circle me-2"
                                                        type="button" title="Export Laporan"
                                                        onclick="openPreviewModal({{ $account->id }}, '{{ $account->name }}')">
                                                        <i class="iconoir-page-search"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-icon btn-sm btn-light rounded-circle"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="iconoir-more-vert"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                            <li>
                                                                <h6 class="dropdown-header">Aksi Cepat</h6>
                                                            </li>
                                                            <li><a class="dropdown-item" href="#"
                                                                    onclick="openTransferModal({{ $account->id }})"><i
                                                                        class="iconoir-send-dollars me-2 text-primary"></i>
                                                                    Transfer Dana</a></li>
                                                            <li><a class="dropdown-item" href="#"
                                                                    onclick="openIncomeModal({{ $account->id }})"><i
                                                                        class="iconoir-coins me-2 text-success"></i> Catat
                                                                    Pemasukan</a></li>
                                                            <li><a class="dropdown-item" href="#"
                                                                    onclick="openExpenseModal({{ $account->id }})"><i
                                                                        class="iconoir-wallet me-2 text-danger"></i> Catat
                                                                    Pengeluaran</a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item" href="#"
                                                                    onclick='openEditModal(@json($account))'><i
                                                                        class="iconoir-edit-pencil me-2"></i> Edit Akun</a>
                                                            </li>
                                                            <li><a class="dropdown-item text-danger" href="#"
                                                                    onclick="deleteAccount({{ $account->id }})"><i
                                                                        class="iconoir-trash me-2"></i> Hapus Akun</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Chart Container -->
                                            <div id="chart-{{ $account->id }}"
                                                class="apex-charts mb-3 d-flex justify-content-center"></div>

                                            <div class="mt-3">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded-3 text-center">
                                                            <small class="text-muted d-block mb-1">Pemasukan</small>
                                                            <span class="fw-bold text-success small text-nowrap">Rp
                                                                {{ number_format($account->income_lain, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 bg-danger bg-opacity-10 rounded-3 text-center">
                                                            <small class="text-muted d-block mb-1">Pengeluaran</small>
                                                            <span class="fw-bold text-danger small text-nowrap">Rp
                                                                {{ number_format($account->expense_lain, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="d-flex justify-content-between align-items-center pt-3 border-top">
                                                    <span class="text-muted fw-medium">Saldo Akhir</span>
                                                    <span
                                                        class="fw-bold fs-4 {{ $account->balance < 0 ? 'text-danger' : 'text-dark' }}">
                                                        Rp {{ number_format($account->balance, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-3 text-center py-5">
                                        <div class="card-body">
                                            <i class="iconoir-bank"></i>
                                            {{-- <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Data"
                                                style="height: 150px; opacity: 0.5" class="mb-3"> --}}
                                            <h5 class="text-muted">Belum ada akun keuangan</h5>
                                            <p class="text-muted mb-4">Silakan buat akun kas atau bank pertama Anda untuk
                                                mulai mencatat transaksi.</p>
                                            <button type="button" class="btn btn-primary rounded-pill px-4"
                                                onclick="resetCreateModal()" data-bs-toggle="modal"
                                                data-bs-target="#modalCreateAccount">
                                                <i class="iconoir-plus-circle me-1"></i> Buat Akun Pertama
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Transaksi Tab -->
                    <div class="tab-pane fade" id="transaksi" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Semua Transaksi Keuangan</h5>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#filterCollapse">
                                        <i class="iconoir-filter me-1"></i> Filter
                                    </button>
                                </div>

                                <!-- Filters -->
                                <div class="collapse mb-4" id="filterCollapse">
                                    <div class="card card-body bg-light border-0">
                                        <form id="filterForm" action="{{ route('finance.index') }}" method="GET"
                                            class="row g-3" data-no-loader>
                                            <div class="col-md-3">
                                                <label class="form-label">Filter Akun</label>
                                                <select class="form-select" name="account_id">
                                                    <option value="">Semua Akun</option>
                                                    @foreach ($all_accounts as $acc)
                                                        <option value="{{ $acc->id }}"
                                                            {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                                            {{ $acc->code }} - {{ $acc->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="">Semua Status</option>
                                                    <option value="posted"
                                                        {{ request('status') == 'posted' ? 'selected' : '' }}>Posted
                                                    </option>
                                                    <option value="draft"
                                                        {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="canceled"
                                                        {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tanggal Mulai</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tanggal Akhir</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                            <div class="col-12 d-flex justify-content-end gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="iconoir-filter me-1"></i> Terapkan Filter
                                                </button>
                                                <button type="submit" formaction="{{ route('finance.export') }}"
                                                    class="btn btn-success text-white">
                                                    <i class="iconoir-page-search me-1"></i> Export Excel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div id="transactionTableContainer" class="position-relative">
                                    <div id="tableContent">
                                        @include('Finance.partials.transaction_table')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Cepat Tab -->
                    <div class="tab-pane fade" id="menu-cepat" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-4">Menu Cepat</h5>
                                <div class="row g-4">
                                    <!-- Create Account -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="card h-100 border-0 shadow-sm rounded-3 transition-hover"
                                            style="cursor: pointer;" onclick="resetCreateModal()" data-bs-toggle="modal"
                                            data-bs-target="#modalCreateAccount">
                                            <div class="card-body p-4 text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3"
                                                    style="width: 64px; height: 64px;">
                                                    <i class="iconoir-plus-circle text-primary fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-1">Buat Akun</h5>
                                                <p class="text-muted small mb-0">Tambahkan akun kas atau bank baru</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transfer -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="card h-100 border-0 shadow-sm rounded-3 transition-hover"
                                            style="cursor: pointer;" onclick="openTransferModal('')">
                                            <div class="card-body p-4 text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle mb-3"
                                                    style="width: 64px; height: 64px;">
                                                    <i class="iconoir-send-dollars text-info fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-1">Transfer Dana</h5>
                                                <p class="text-muted small mb-0">Pindahkan dana antar akun</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Income -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="card h-100 border-0 shadow-sm rounded-3 transition-hover"
                                            style="cursor: pointer;" onclick="openIncomeModal('')">
                                            <div class="card-body p-4 text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3"
                                                    style="width: 64px; height: 64px;">
                                                    <i class="iconoir-coins text-success fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-1">Catat Pemasukan</h5>
                                                <p class="text-muted small mb-0">Catat pendapatan dari sumber lain</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Expense -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="card h-100 border-0 shadow-sm rounded-3 transition-hover"
                                            style="cursor: pointer;" onclick="openExpenseModal('')">
                                            <div class="card-body p-4 text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3"
                                                    style="width: 64px; height: 64px;">
                                                    <i class="iconoir-wallet text-danger fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-1">Catat Pengeluaran</h5>
                                                <p class="text-muted small mb-0">Catat biaya operasional atau lainnya</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Styles -->
        <style>
            .transition-hover {
                transition: all 0.3s ease;
            }

            .transition-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            }

            .cursor-pointer {
                cursor: pointer;
            }

            /* Loading State */
            #transactionTableContainer.is-loading #tableSpinner {
                display: block !important;
            }

            #transactionTableContainer.is-loading #tableContent {
                opacity: 0.3;
                pointer-events: none;
            }

            /* Hide default Laravel pagination info inside our wrapper */
            .finance-pagination .flex-1.d-sm-flex>div:first-child,
            .finance-pagination nav > div:first-child {
                display: none !important;
            }

            .finance-pagination .flex-1.d-sm-flex,
            .finance-pagination nav > div:last-child {
                display: flex !important;
                justify-content: flex-end !important;
                width: 100%;
            }

            .finance-pagination .small.text-muted {
                display: none !important;
            }

            .finance-pagination nav {
                box-shadow: none !important;
                background: transparent !important;
                border: none !important;
            }

            .finance-pagination .pagination {
                margin-bottom: 0 !important;
            }
        </style>

        <!-- Modals -->
        @include('Finance.modals')
    @endsection

    @prepend('js')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @endprepend

    @push('js')
        <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
        <script>
            // Initialize Charts
            document.addEventListener('DOMContentLoaded', function() {
                // Fix Modal Z-Index Issue by moving them to body
                const modals = ['modalTransfer', 'modalIncome', 'modalExpense', 'modalCreateAccount',
                    'modalEditTransaction'
                ];
                modals.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) document.body.appendChild(el);
                });

                @foreach ($accounts as $account)
                    (
                        function() {
                            var income = {{ $account->income_lain }};
                            var expense = {{ $account->expense_lain }};

                            var isZero = (income === 0 && expense === 0);

                            var options{{ $account->id }} = {
                                series: isZero ? [1] : [income, expense],
                                chart: {
                                    height: 250,
                                    type: 'donut',
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '70%',
                                            labels: {
                                                show: true,
                                                name: {
                                                    show: true,
                                                    fontSize: '14px',
                                                    fontWeight: 600,
                                                    offsetY: -10
                                                },
                                                value: {
                                                    show: true,
                                                    fontSize: '14px',
                                                    fontWeight: 500,
                                                    offsetY: 5,
                                                    formatter: function(val) {
                                                        if (isZero) return "Rp 0";
                                                        return "Rp " + new Intl
                                                            .NumberFormat('id-ID')
                                                            .format(val);
                                                    }
                                                },
                                                total: {
                                                    show: true,
                                                    label: isZero ? 'Status' : 'Total Volume',
                                                    fontSize: '12px',
                                                    color: '#6c757d',
                                                    formatter: function(w) {
                                                        if (isZero) return "Rp 0";
                                                        let total = w.globals.seriesTotals.reduce((a, b) => a +
                                                            b, 0);
                                                        return "Rp " + new Intl.NumberFormat('id-ID').format(
                                                            total);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                labels: isZero ? ['Kosong'] : ['In', 'Out'],
                                colors: isZero ? ['#e2e8f0'] : ['#198754', '#dc3545'], // Red/Pink for Expense
                                stroke: {
                                    show: !isZero,
                                    lineCap: 'round',
                                },
                                tooltip: {
                                    enabled: !isZero
                                },
                            };
                            new ApexCharts(document.querySelector("#chart-{{ $account->id }}"),
                                    options{{ $account->id }})
                                .render();
                        }
                    )();
                @endforeach
            });

            // Modal Functions
            function openTransferModal(fromId) {
                var el = document.getElementById('modalTransfer');
                var myModal = bootstrap.Modal.getOrCreateInstance(el);
                myModal.show();
                document.getElementById('from_account_id').value = fromId;
            }

            function openIncomeModal(toId) {
                var el = document.getElementById('modalIncome');
                var myModal = bootstrap.Modal.getOrCreateInstance(el);
                myModal.show();
                document.getElementById('income_to_account_id').value = toId;
            }

            function openExpenseModal(fromId) {
                var el = document.getElementById('modalExpense');
                var myModal = bootstrap.Modal.getOrCreateInstance(el);
                myModal.show();
                document.getElementById('expense_from_account_id').value = fromId;
            }

            function resetCreateModal() {
                document.getElementById('formCreateAccount').reset();
                document.getElementById('modalAccountTitle').innerText = 'Buat Akun Keuangan Baru';
                setMode('new');
                document.getElementById('tipe_akun').disabled = false; // Enable type selection
                handleAccountTypeChange();
            }

            function openEditModal(account) {
                var el = document.getElementById('modalCreateAccount');
                var myModal = bootstrap.Modal.getOrCreateInstance(el);

                // Reset and Title
                document.getElementById('formCreateAccount').reset();
                document.getElementById('modalAccountTitle').innerText = 'Edit Akun Keuangan';

                // Set Mode
                setMode('existing');
                document.getElementById('existing_coa_id').value = account.id;

                // Populate Fields
                document.getElementById('tipe_akun').value = account.type;
                document.getElementById('tipe_akun').disabled = true; // Cannot change type easily

                // Trigger change to show correct fields
                handleAccountTypeChange();

                // Populate specific fields
                if (account.type === 'Cash') {
                    document.getElementById('nama_akun_cash').value = account.name;
                } else {
                    document.getElementById('bank_name').value = account.bank_name;
                    document.getElementById('bank_account_number').value = account.bank_account_number;
                    document.getElementById('bank_account_name').value = account.bank_account_name;
                    document.getElementById('bank_branch').value = account.bank_branch;
                    document.getElementById('bank_city').value = account.bank_city;
                }

                document.getElementById('generated_code').value = account.code;
                document.getElementById('preview-code').innerText = account.code;

                myModal.show();
            }

            async function deleteAccount(id) {
                if (!await macConfirm('Hapus akun', 'Yakin ingin menghapus akun ini?')) return;

                $.ajax({
                    url: "{{ url('finance/account') }}/" + id,
                    type: "POST",
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
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
                    }
                });
            }

            // Report Preview & Export
            let currentPreviewAccountId = null;

            function openPreviewModal(id, name) {
                currentPreviewAccountId = id;
                document.getElementById('preview-account-name').textContent = name;

                // Reset dates to current month
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                const today = now.toISOString().split('T')[0];

                document.getElementById('preview-start-date').value = firstDay;
                document.getElementById('preview-end-date').value = today;

                const modal = new bootstrap.Modal(document.getElementById('modalPreviewReport'));
                modal.show();

                fetchPreviewData();
            }

            function fetchPreviewData() {
                const start = document.getElementById('preview-start-date').value;
                const end = document.getElementById('preview-end-date').value;

                $('#preview-loading').removeClass('d-none');
                $('#preview-table').addClass('d-none');

                $.ajax({
                    url: "{{ route('finance.preview') }}",
                    type: "GET",
                    data: {
                        account_id: currentPreviewAccountId,
                        start_date: start,
                        end_date: end
                    },
                    success: function(res) {
                        if (res.success) {
                            renderPreviewTable(res.data, start);
                        }
                    },
                    complete: function() {
                        $('#preview-loading').addClass('d-none');
                        $('#preview-table').removeClass('d-none');
                    }
                });
            }

            function renderPreviewTable(data, start) {
                const body = document.getElementById('preview-body');
                body.innerHTML = '';

                let balance = data.opening_balance;
                let totalDebit = 0;
                let totalCredit = 0;

                // Opening Balance Row
                body.innerHTML += `
                    <tr class="table-light">
                        <td class="text-center">1</td>
                        <td>${new Date(start).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'2-digit'})}</td>
                        <td class="fw-bold">SALDO AWAL</td>
                        <td class="text-end">${balance > 0 ? formatIDR(balance) : ''}</td>
                        <td class="text-end">${balance < 0 ? formatIDR(Math.abs(balance)) : ''}</td>
                        <td class="text-end fw-bold">${formatIDR(balance)}</td>
                    </tr>
                `;

                data.rows.forEach((row, index) => {
                    totalDebit += row.debit;
                    totalCredit += row.credit;
                    balance += (row.debit - row.credit);

                    body.innerHTML += `
                        <tr>
                            <td class="text-center">${index + 2}</td>
                            <td>${new Date(row.date).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'2-digit'})}</td>
                            <td>
                                <div class="fw-bold small">${row.account_name}</div>
                                <div class="text-muted" style="font-size: 11px;">${row.description}</div>
                            </td>
                            <td class="text-end text-success">${row.debit ? formatIDR(row.debit) : ''}</td>
                            <td class="text-end text-danger">${row.credit ? formatIDR(row.credit) : ''}</td>
                            <td class="text-end">${formatIDR(balance)}</td>
                        </tr>
                    `;
                });

                document.getElementById('total-debit').textContent = formatIDR(totalDebit);
                document.getElementById('total-credit').textContent = formatIDR(totalCredit);
                document.getElementById('final-balance').textContent = formatIDR(balance);
            }

            function downloadReport() {
                const start = document.getElementById('preview-start-date').value;
                const end = document.getElementById('preview-end-date').value;
                const url =
                    `{{ route('finance.export') }}?account_id=${currentPreviewAccountId}&start_date=${start}&end_date=${end}`;
                window.location.href = url;
            }

            function formatIDR(val) {
                return "Rp " + new Intl.NumberFormat('id-ID').format(val);
            }

            // AJAX Filter & Pagination
            $(document).ready(function() {
                // Restore active tab from URL hash
                const hash = window.location.hash;
                if (hash) {
                    const tabBtn = document.querySelector(`button[data-bs-target="${hash}"]`);
                    if (tabBtn) {
                        bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                    }
                }

                // Save active tab to URL hash on tab change
                document.querySelectorAll('#financeTab button[data-bs-toggle="tab"]').forEach(function(btn) {
                    btn.addEventListener('shown.bs.tab', function(e) {
                        history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
                    });
                });
                // Filter Form Submit
                $('#filterForm').on('submit', function(e) {
                    var submitter = e.originalEvent ? e.originalEvent.submitter : null;
                    if (submitter && submitter.getAttribute('formaction')) {
                        return; // Allow default submission for export
                    }

                    e.preventDefault();
                    let url = $(this).attr('action');
                    let data = $(this).serialize();

                    // Show loading state inside table
                    $('#tableContent tbody').html(
                        '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat data...</td></tr>'
                        );
                    $('#tableContent').css('opacity', '0.6');

                    $.ajax({
                        url: url,
                        type: 'GET',
                        global: false,
                        data: data + '&ajax=1',
                        success: function(response) {
                            $('#tableContent').html(response);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Terjadi kesalahan saat memuat data: ' + xhr.statusText);
                        },
                        complete: function() {
                            $('#tableContent').css('opacity', '1');
                            if (window.PageLoader) window.PageLoader.hide();
                        }
                    });
                });

                // Pagination Click
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('href');

                    // Show loading state inside table
                    $('#tableContent tbody').html(
                        '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat data...</td></tr>'
                        );
                    $('#tableContent').css('opacity', '0.6');

                    let ajaxUrl = url;
                    if (ajaxUrl.indexOf('?') === -1) ajaxUrl += '?ajax=1';
                    else ajaxUrl += '&ajax=1';

                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        global: false,
                        success: function(response) {
                            $('#tableContent').html(response);
                            // Scroll to top of table
                            document.getElementById('transactionTableContainer').scrollIntoView({
                                behavior: 'smooth'
                            });
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Terjadi kesalahan saat memuat data: ' + xhr.statusText);
                        },
                        complete: function() {
                            $('#tableContent').css('opacity', '1');
                            if (window.PageLoader) window.PageLoader.hide();
                        }
                    });
                });
            });

            // ===== Edit Transaction =====
            function openEditTransactionModal(trx) {
                // Reset form
                document.getElementById('formEditTransaction').reset();

                // Set ID
                document.getElementById('edit_transaction_id').value = trx.id;

                // Set type badge & hidden field
                document.getElementById('edit_type').value = trx.type;
                const typeBadges = {
                    transfer: '<span class="badge bg-info bg-opacity-10 text-info border border-info">Transfer</span>',
                    income: '<span class="badge bg-success bg-opacity-10 text-success border border-success">Income / Pemasukan</span>',
                    expense: '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Expense / Pengeluaran</span>',
                };
                document.getElementById('edit_type_badge').innerHTML = typeBadges[trx.type] || trx.type;

                // Show/hide account fields based on type
                document.getElementById('edit_transfer_fields').classList.add('d-none');
                document.getElementById('edit_income_fields').classList.add('d-none');
                document.getElementById('edit_expense_fields').classList.add('d-none');

                if (trx.type === 'transfer') {
                    document.getElementById('edit_transfer_fields').classList.remove('d-none');
                    document.getElementById('edit_from_account_id').value = trx.from_account_id ?? '';
                    document.getElementById('edit_to_account_id').value = trx.to_account_id ?? '';
                } else if (trx.type === 'income') {
                    document.getElementById('edit_income_fields').classList.remove('d-none');
                    document.getElementById('edit_income_to_account_id').value = trx.to_account_id ?? '';
                } else if (trx.type === 'expense') {
                    document.getElementById('edit_expense_fields').classList.remove('d-none');
                    document.getElementById('edit_expense_from_account_id').value = trx.from_account_id ?? '';
                }

                // Fill common fields
                document.getElementById('edit_transaction_date').value = trx.transaction_date;
                document.getElementById('edit_amount').value = trx.amount;
                document.getElementById('edit_description').value = trx.description ?? '';
                document.getElementById('edit_status').value = trx.status;

                // Show modal
                const el = document.getElementById('modalEditTransaction');
                bootstrap.Modal.getOrCreateInstance(el).show();
            }

            function submitEditTransaction(e) {
                e.preventDefault();
                const id = document.getElementById('edit_transaction_id').value;
                const formData = new FormData(e.target);

                $.ajax({
                    url: "{{ url('finance/transaction') }}/" + id,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditTransaction'))
                                .hide();
                            location.reload();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(err) {
                        alert('Terjadi kesalahan: ' + (err.responseJSON ? err.responseJSON.message :
                            'Unknown error'));
                    }
                });
            }

            async function deleteTransaction(id) {
                if (!await macConfirm('Hapus Transaksi',
                        'Yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')) return;

                $.ajax({
                    url: "{{ url('finance/transaction') }}/" + id,
                    type: "POST",
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            location.reload();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(err) {
                        alert('Terjadi kesalahan: ' + (err.responseJSON ? err.responseJSON.message :
                            'Unknown error'));
                    }
                });
            }
        </script>
    @endpush
