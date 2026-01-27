@extends('Layout.main')

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Biaya Operasional</h4>
                        <p class="text-muted small mb-0">Kelola pengeluaran operasional dan kas kecil.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalExpense">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Biaya
                        </button>
                    </div>
                </div>

                <ul class="nav nav-tabs nav-tabs-custom mb-4" id="expenseTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="biaya-tab" data-bs-toggle="tab"
                            data-bs-target="#biaya" type="button" role="tab" aria-controls="biaya"
                            aria-selected="true">Biaya</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="analitik-tab" data-bs-toggle="tab"
                            data-bs-target="#analitik" type="button" role="tab" aria-controls="analitik"
                            aria-selected="false">Analitik</button>
                    </li>
                </ul>

                <div class="tab-content" id="expenseTabContent">
                    <!-- Tab Biaya -->
                    <div class="tab-pane fade show active" id="biaya" role="tabpanel" aria-labelledby="biaya-tab">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="tableExpenses">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Nama Biaya</th>
                                                <th>Kategori</th>
                                                <th>Vendor</th>
                                                <th>Akun Bayar</th>
                                                <th>Akun Beban</th>
                                                <th class="text-end">Jumlah</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="expenseList">
                                            <!-- Data will be loaded via JS -->
                                            <tr>
                                                <td colspan="8" class="text-center py-4">Memuat data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3" id="pagination">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Analitik -->
                    <div class="tab-pane fade" id="analitik" role="tabpanel" aria-labelledby="analitik-tab">
                        <div class="row">
                            <!-- Rincian Biaya (Donut) -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold mb-0">Rincian Biaya</h5>
                                        <button class="btn btn-sm btn-link text-muted text-decoration-none" onclick="resetFilters('pie')">Hapus Filter</button>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="small fw-bold text-muted">Tanggal Mulai <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm filter-pie" id="filter_start_date_pie" value="{{ date('Y-m-01') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="small fw-bold text-muted">Tanggal Berakhir <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm filter-pie" id="filter_end_date_pie" value="{{ date('Y-m-t') }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="small fw-bold text-muted">Kategori <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm filter-pie" id="filter_category_pie">
                                                    <option value="">Semua Kategori</option>
                                                    @foreach ($categories as $cat) <option value="{{ $cat->name }}">{{ $cat->name }}</option> @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div id="chartRincian" class="apex-charts"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jumlah Biaya (Bar) -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold mb-0">Jumlah Biaya</h5>
                                        <button class="btn btn-sm btn-link text-muted text-decoration-none" onclick="resetFilters('bar')">Hapus Filter</button>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="small fw-bold text-muted">Tanggal Mulai <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm filter-bar" id="filter_start_date_bar" value="{{ date('Y-m-01') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="small fw-bold text-muted">Tanggal Berakhir <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm filter-bar" id="filter_end_date_bar" value="{{ date('Y-m-t') }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="small fw-bold text-muted">Kategori <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm filter-bar" id="filter_category_bar">
                                                    <option value="">Semua Kategori</option>
                                                    @foreach ($categories as $cat) <option value="{{ $cat->name }}">{{ $cat->name }}</option> @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div id="chartJumlah" class="apex-charts"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tren Biaya (Line) -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold mb-0">Tren Biaya</h5>
                                        <button class="btn btn-sm btn-link text-muted text-decoration-none" onclick="resetFilters('trend')">Hapus Filter</button>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row justify-content-end mb-3">
                                            <div class="col-auto">
                                                <label class="small fw-bold text-muted">Tahun <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm filter-trend" id="filter_year">
                                                    @for($y = date('Y'); $y >= 2020; $y--)
                                                        <option value="{{ $y }}">{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div id="chartTren" class="apex-charts"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Create Expense -->
    <div class="modal fade" id="modalExpense" tabindex="-1" aria-labelledby="modalExpenseLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="modalExpenseLabel">Catat Biaya Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formExpense">
                        @csrf
                        <input type="hidden" name="id" id="expense_id">
                        <div class="row g-3">
                            <!-- Akun Keuangan (Mandatory) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Akun Keuangan (Sumber Dana) <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="akun_keuangan_id" required>
                                    <option value="">-- Pilih Kas / Bank --</option>
                                    @foreach ($financialAccounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->kode_akun }} -
                                            {{ $acc->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal (Mandatory) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Tanggal <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tgl_biaya"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Nama Biaya (Mandatory) -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Nama Biaya <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_biaya"
                                    placeholder="Contoh: Beli Kertas A4" required>
                            </div>

                            <!-- Nama Vendor (Optional) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nama Vendor</label>
                                <input type="text" class="form-control" name="nama_vendor"
                                    placeholder="Nama Toko / Vendor">
                            </div>

                            <!-- Kategori Biaya (Dynamic Master) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Kategori Biaya</label>
                                <input class="form-control" list="categoryOptions" name="kategori_biaya"
                                    placeholder="Pilih atau ketik baru...">
                                <datalist id="categoryOptions">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->name }}">
                                    @endforeach
                                </datalist>
                                <small class="text-muted" style="font-size: 0.75rem;">Ketik baru untuk menambahkan ke master otomatis.</small>
                            </div>

                            <!-- Akun Beban (All COA) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Akun Beban <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="akun_beban_id" required>
                                    <option value="">-- Pilih Akun Beban --</option>
                                    @foreach ($expenseAccounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->kode_akun }} -
                                            {{ $acc->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jumlah (Mandatory) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Jumlah (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="jumlah" step="0.01"
                                    min="0" placeholder="0" required>
                            </div>

                            <!-- Keterangan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small">Keterangan Biaya</label>
                                <textarea class="form-control" name="keterangan" rows="2" placeholder="Detail tambahan..."></textarea>
                            </div>

                            <!-- Lampiran -->
                            <div class="col-12">
                                <label class="form-label fw-bold small">Lampiran</label>
                                <input type="file" class="form-control" name="lampiran">
                                <small class="text-muted" style="font-size: 0.75rem;">Maksimal 10MB (Gambar/Dokumen)</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveExpense()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Show Expense -->
    <div class="modal fade" id="modalShowExpense" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-bottom-0 rounded-top-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fa fa-receipt me-2"></i> Detail Biaya
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Left Column: Main Info -->
                        <div class="col-md-7">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Informasi Utama</h6>
                            
                            <div class="mb-3">
                                <label class="small text-muted d-block">Nama Biaya</label>
                                <span class="fw-bold fs-5 text-dark" id="show_nama_biaya"></span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="small text-muted d-block">Tanggal</label>
                                    <span class="fw-bold" id="show_tgl_biaya"></span>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted d-block">Jumlah</label>
                                    <span class="fw-bold text-primary fs-5" id="show_jumlah"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted d-block">Kategori</label>
                                <span class="badge bg-light text-dark border px-3 py-2" id="show_kategori_biaya"></span>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted d-block">Keterangan</label>
                                <p class="text-secondary bg-light p-3 rounded-3 border mb-0" id="show_keterangan"></p>
                            </div>
                        </div>

                        <!-- Right Column: Details & Attachment -->
                        <div class="col-md-5 border-start">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Detail Akun & Vendor</h6>

                            <div class="mb-3">
                                <label class="small text-muted d-block">Vendor</label>
                                <span class="fw-bold" id="show_nama_vendor"></span>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted d-block">Akun Keuangan (Sumber)</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-wallet text-muted me-2"></i>
                                    <span id="show_akun_keuangan"></span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="small text-muted d-block">Akun Beban</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-file-invoice-dollar text-muted me-2"></i>
                                    <span id="show_akun_beban"></span>
                                </div>
                            </div>

                            <div class="card bg-light border-0 rounded-3">
                                <div class="card-body">
                                    <h6 class="fw-bold small mb-2"><i class="fa fa-paperclip me-1"></i> Lampiran</h6>
                                    <div id="show_lampiran_container">
                                        <span class="text-muted small fst-italic">Tidak ada lampiran.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ url('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
            $(document).ready(function() {
                console.log("Expense script loaded");
                loadExpenses();
                initCharts();
                
                // Reset Modal on Close
                $('#modalExpense').on('hidden.bs.modal', function () {
                    $('#formExpense')[0].reset();
                    $('#expense_id').val('');
                    $('#modalExpenseLabel').text('Catat Biaya Baru');
                });

                // Filter Events
                $('.filter-pie').change(loadPieChart);
                $('.filter-bar').change(loadBarChart);
                $('.filter-trend').change(loadTrendChart);
            });

            var chartPie, chartBar, chartTrend;

            function initCharts() {
                // Pie/Donut Options
                var optionsPie = {
                    series: [],
                    chart: { type: 'donut', height: 320 },
                    labels: [],
                    colors: ['#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6c757d'],
                    legend: { position: 'bottom' },
                    noData: { text: 'Loading...' }
                };
                chartPie = new ApexCharts(document.querySelector("#chartRincian"), optionsPie);
                chartPie.render();

                // Bar Options
                var optionsBar = {
                    series: [],
                    chart: { type: 'bar', height: 320, toolbar: { show: false } },
                    plotOptions: { bar: { borderRadius: 4, horizontal: true, } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: [] },
                    colors: ['#0d6efd'],
                    noData: { text: 'Loading...' }
                };
                chartBar = new ApexCharts(document.querySelector("#chartJumlah"), optionsBar);
                chartBar.render();

                // Trend Options
                var optionsTrend = {
                    series: [],
                    chart: { type: 'area', height: 320, toolbar: { show: false } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth' },
                    xaxis: { categories: [] },
                    tooltip: { x: { format: 'dd/MM/yy HH:mm' }, },
                    colors: ['#0d6efd'],
                    noData: { text: 'Loading...' }
                };
                chartTrend = new ApexCharts(document.querySelector("#chartTren"), optionsTrend);
                chartTrend.render();

                // Load Initial Data
                loadPieChart();
                loadBarChart();
                loadTrendChart();
            }

            function loadPieChart() {
                let params = {
                    tgl_mulai: $('#filter_start_date_pie').val(),
                    tgl_selesai: $('#filter_end_date_pie').val(),
                    kategori_biaya: $('#filter_category_pie').val()
                };
                $.get("{{ route('expense-api.analytics-summary') }}", params, function(res) {
                    if(res.success) {
                        chartPie.updateOptions({
                            labels: res.data.labels
                        });
                        chartPie.updateSeries(res.data.series);
                    }
                });
            }

            function loadBarChart() {
                let params = {
                    tgl_mulai: $('#filter_start_date_bar').val(),
                    tgl_selesai: $('#filter_end_date_bar').val(),
                    kategori_biaya: $('#filter_category_bar').val()
                };
                $.get("{{ route('expense-api.analytics-summary') }}", params, function(res) {
                    if(res.success) {
                        chartBar.updateOptions({
                            xaxis: { categories: res.data.labels }
                        });
                        chartBar.updateSeries([{ name: 'Total', data: res.data.series }]);
                    }
                });
            }

            function loadTrendChart() {
                let params = {
                    year: $('#filter_year').val()
                };
                $.get("{{ route('expense-api.analytics-trend') }}", params, function(res) {
                    if(res.success) {
                        chartTrend.updateOptions({
                            xaxis: { categories: res.data.categories }
                        });
                        chartTrend.updateSeries(res.data.series);
                    }
                });
            }

            function resetFilters(type) {
                let today = new Date();
                let firstDay = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                let lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];

                if(type === 'pie') {
                    $('#filter_start_date_pie').val(firstDay);
                    $('#filter_end_date_pie').val(lastDay);
                    $('#filter_category_pie').val('');
                    loadPieChart();
                } else if(type === 'bar') {
                    $('#filter_start_date_bar').val(firstDay);
                    $('#filter_end_date_bar').val(lastDay);
                    $('#filter_category_bar').val('');
                    loadBarChart();
                } else if(type === 'trend') {
                    $('#filter_year').val(today.getFullYear());
                    loadTrendChart();
                }
            }

            function loadExpenses(page = 1) {
                console.log("Loading expenses page " + page);
                $.ajax({
                    url: "{{ route('expense-api.index') }}?page=" + page,
                    method: 'GET',
                    success: function(response) {
                        if (typeof response === 'string') {
                             $('#expenseList').html('<tr><td colspan="8" class="text-center text-danger">Sesi mungkin telah berakhir.</td></tr>');
                            return;
                        }

                        if (response.success) {
                            let rows = '';
                            if (response.data && response.data.data && Array.isArray(response.data.data)) {
                                response.data.data.forEach(item => {
                                    rows += `
                                    <tr>
                                        <td>${item.tgl_biaya}</td>
                                        <td class="fw-bold text-dark">${item.nama_biaya}</td>
                                        <td><span class="badge bg-light text-dark border">${item.kategori_biaya || '-'}</span></td>
                                        <td>${item.nama_vendor || '-'}</td>
                                        <td>${item.akun_keuangan?.nama_akun || item.akun_keuangan_id || '-'}</td>
                                        <td>${item.akun_beban?.nama_akun || item.akun_beban_id || '-'}</td>
                                        <td class="text-end fw-bold">Rp ${parseFloat(item.jumlah).toLocaleString('id-ID')}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-light border text-info" onclick="showExpense(${item.id})">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-light border text-primary" onclick="editExpense(${item.id})">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-light border text-danger" onclick="deleteExpense(${item.id})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                                });

                                if (response.data.data.length === 0) {
                                    rows = '<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data biaya.</td></tr>';
                                }

                                $('#expenseList').html(rows);
                                renderPagination(response.data);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error("AJAX Error:", xhr);
                        $('#expenseList').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
                    }
                });
            }

            function saveExpense() {
                // Use FormData for file upload
                let form = document.getElementById('formExpense');
                let formData = new FormData(form);
                let id = $('#expense_id').val();
                let url = "{{ route('expense-api.store') }}";
                
                // For Update, Laravel method spoofing is needed if using FormData with POST
                if(id) {
                    url = "{{ url('api/expense-api') }}/" + id;
                    formData.append('_method', 'PUT'); 
                }

                $.ajax({
                    url: url,
                    method: 'POST', // Always POST when using FormData
                    data: formData,
                    processData: false, // Important for FormData
                    contentType: false, // Important for FormData
                    success: function(response) {
                        if (response.success) {
                            var modalEl = document.getElementById('modalExpense');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide();
                            $('#formExpense')[0].reset();
                            $('#expense_id').val('');
                            loadExpenses();
                            // Refresh Charts too if on analytics tab
                            loadPieChart();
                            loadBarChart();
                            loadTrendChart();
                            alert('Biaya berhasil disimpan!');
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        let msg = 'Gagal menyimpan.';
                        if (errors) {
                            msg = Object.values(errors).join('\n');
                        }
                        alert(msg);
                    }
                });
            }

            function editExpense(id) {
                $.get("{{ url('api/expense-api') }}/" + id, function(res) {
                    if(res.success) {
                        let d = res.data;
                        $('#expense_id').val(d.id);
                        $('input[name="tgl_biaya"]').val(d.tgl_biaya);
                        $('input[name="nama_biaya"]').val(d.nama_biaya);
                        $('input[name="nama_vendor"]').val(d.nama_vendor);
                        $('input[name="kategori_biaya"]').val(d.kategori_biaya);
                        $('select[name="akun_keuangan_id"]').val(d.akun_keuangan_id);
                        $('select[name="akun_beban_id"]').val(d.akun_beban_id);
                        $('input[name="jumlah"]').val(d.jumlah);
                        $('textarea[name="keterangan"]').val(d.keterangan);
                        $('input[name="lampiran"]').val(''); // Reset file input
                        
                        $('#modalExpenseLabel').text('Edit Biaya');
                        var modal = new bootstrap.Modal(document.getElementById('modalExpense'));
                        modal.show();
                    }
                });
            }

            function showExpense(id) {
                $.get("{{ url('api/expense-api') }}/" + id, function(res) {
                    if(res.success) {
                        let d = res.data;
                        $('#show_tgl_biaya').text(d.tgl_biaya);
                        $('#show_nama_biaya').text(d.nama_biaya);
                        $('#show_kategori_biaya').text(d.kategori_biaya || '-');
                        $('#show_nama_vendor').text(d.nama_vendor || '-');
                        
                        // Use relationship data if available, otherwise fallback to ID
                        let akunKeuangan = d.akun_keuangan ? (d.akun_keuangan.kode_akun + ' - ' + d.akun_keuangan.nama_akun) : (d.akun_keuangan_id || '-');
                        let akunBeban = d.akun_beban ? (d.akun_beban.kode_akun + ' - ' + d.akun_beban.nama_akun) : (d.akun_beban_id || '-');
                        
                        $('#show_akun_keuangan').text(akunKeuangan);
                        $('#show_akun_beban').text(akunBeban);
                        $('#show_jumlah').text('Rp ' + parseFloat(d.jumlah).toLocaleString('id-ID'));
                        $('#show_keterangan').text(d.keterangan || '-');

                        // Show Attachment
                        let lampiranHtml = '<span class="text-muted small fst-italic">Tidak ada lampiran.</span>';
                        if (d.lampiran) {
                            let url = "{{ asset('storage') }}/" + d.lampiran;
                            lampiranHtml = `
                                <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white">
                                    <div class="d-flex align-items-center text-truncate me-2">
                                        <i class="fa fa-file-alt text-primary fs-4 me-2"></i>
                                        <div class="small text-truncate" style="max-width: 150px;">
                                            ${d.lampiran.split('/').pop()}
                                        </div>
                                    </div>
                                    <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-download"></i> Unduh
                                    </a>
                                </div>
                            `;
                        }
                        $('#show_lampiran_container').html(lampiranHtml);

                        var modal = new bootstrap.Modal(document.getElementById('modalShowExpense'));
                        modal.show();
                    }
                });
            }

            function deleteExpense(id) {
                if (!confirm('Hapus biaya ini?')) return;
                
                $.ajax({
                    url: "{{ url('api/expense-api') }}/" + id,
                    method: 'DELETE',
                    success: function(response) {
                        loadExpenses();
                        loadPieChart();
                        loadBarChart();
                        loadTrendChart();
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus.');
                    }
                });
            }

            function renderPagination(data) {
                let html = '';
                if (data.prev_page_url) {
                    html += `<button class="btn btn-sm btn-light border" onclick="loadExpenses(${data.current_page - 1})">Prev</button>`;
                }
                html += `<span class="mx-2 small text-muted">Halaman ${data.current_page} dari ${data.last_page}</span>`;
                if (data.next_page_url) {
                    html += `<button class="btn btn-sm btn-light border" onclick="loadExpenses(${data.current_page + 1})">Next</button>`;
                }
                $('#pagination').html(html);
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        </script>
    @endpush
@endsection
