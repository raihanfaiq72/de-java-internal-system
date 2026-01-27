@extends('Layout.main')

@section('main')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Keuangan</h4>
                    <div class="page-title-right">
                         <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCreateAccount">
                            <i class="iconoir-plus-circle me-1"></i> Buat Akun Keuangan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills nav-justified" id="financeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">Dashboard</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab">Transaksi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="menu-cepat-tab" data-bs-toggle="tab" data-bs-target="#menu-cepat" type="button" role="tab">Menu Cepat</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="financeTabContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                <h5 class="mb-3 text-muted">Akun Keuangan (Kas & Bank)</h5>
                <div class="row">
                    @forelse($accounts as $account)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1 fw-bold">{{ $account->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $account->kode_akun }}</p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Buat Transaksi Baru
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="openTransferModal({{ $account->id }})"><i class="iconoir-send-dollars me-2"></i> Pemindahan Internal</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="openIncomeModal({{ $account->id }})"><i class="iconoir-coins me-2"></i> Buat Pemasukan Lain</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="openExpenseModal({{ $account->id }})"><i class="iconoir-wallet me-2"></i> Buat Pengeluaran Lain</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Chart Container -->
                                <div id="chart-{{ $account->id }}" class="apex-charts mb-3 d-flex justify-content-center"></div>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted"><i class="iconoir-arrow-down-circle text-info me-1"></i> Pemasukan Lain</span>
                                        <span class="fw-bold text-success">Rp {{ number_format($account->income_lain, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted"><i class="iconoir-arrow-up-circle text-danger me-1"></i> Pengeluaran Lain</span>
                                        <span class="fw-bold text-danger">Rp {{ number_format($account->expense_lain, 0, ',', '.') }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold"><i class="iconoir-wallet me-1"></i> Sisa Saldo</span>
                                        <span class="fw-bold fs-5 {{ $account->balance < 0 ? 'text-danger' : 'text-primary' }}">
                                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Data" style="height: 150px; opacity: 0.5">
                        <p class="text-muted mt-3">Belum ada akun keuangan. Silakan buat baru.</p>
                        <button type="button" class="btn btn-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#modalCreateAccount">
                            <i class="iconoir-plus-circle me-1"></i> Buat Akun Pertama
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Transaksi Tab (Placeholder) -->
             <div class="tab-pane fade" id="transaksi" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="iconoir-table-rows fs-1 text-muted mb-3"></i>
                        <h5 class="card-title">Riwayat Transaksi</h5>
                        <p class="text-muted">Fitur ini akan menampilkan daftar lengkap mutasi transaksi.</p>
                        <!-- Table can be added here later -->
                    </div>
                </div>
            </div>

            <!-- Menu Cepat Tab (Placeholder) -->
             <div class="tab-pane fade" id="menu-cepat" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Menu Cepat</h5>
                         <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <button class="btn btn-outline-primary w-100 py-4 border-2" data-bs-toggle="modal" data-bs-target="#modalCreateAccount">
                                    <i class="iconoir-plus-circle fs-1 mb-2 d-block"></i>
                                    Buat Akun
                                </button>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    document.addEventListener('DOMContentLoaded', function () {
        @foreach($accounts as $account)
        var options{{ $account->id }} = {
            series: [{{ $account->income_lain }}, {{ $account->expense_lain }}],
            chart: {
                height: 250,
                type: 'donut',
            },
            labels: ['Pemasukan Lain', 'Pengeluaran Lain'],
            colors: ['#0d6efd', '#dc3545'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Saldo',
                                formatter: function (w) {
                                    return "Rp {{ number_format($account->balance, 0, ',', '.') }}";
                                }
                            }
                        }
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#chart-{{ $account->id }}"), options{{ $account->id }}).render();
        @endforeach
    });

    // Modal Functions
    function openTransferModal(fromId) {
        $('#modalTransfer').modal('show');
        $('#from_account_id').val(fromId);
    }
    
    function openIncomeModal(toId) {
         $('#modalIncome').modal('show');
         $('#income_to_account_id').val(toId);
    }
    
    function openExpenseModal(fromId) {
         $('#modalExpense').modal('show');
         $('#expense_from_account_id').val(fromId);
    }
</script>
@endpush
