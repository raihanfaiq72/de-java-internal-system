@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                        <h4 class="page-title">Dashboard Analisis Penjualan</h4>
                        <div class="text-muted small">Data diperbarui: {{ date('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Periode Tanggal</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                                <span class="input-group-text">s/d</span>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Cari Pelanggan</label>
                            <select name="mitra_id" id="tom-mitra" placeholder="Ketik nama PT atau orang...">
                                <option value="">Semua Pelanggan</option>
                                @foreach($listMitra as $m)
                                    <option value="{{ $m->id }}" {{ request('mitra_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Status Pembayaran</label>
                            <select name="status" id="tom-status">
                                <option value="">Semua Status</option>
                                <option value="NOT_PAID" {{ request('status') == 'NOT_PAID' ? 'selected' : '' }}>Belum Lunas (Bukan Paid)</option>
                                <option value="Unpaid" {{ request('status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="Partially Paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                                <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">
                                <i class="fa fa-filter me-1"></i> TERAPKAN
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
                                <i class="fa fa-sync"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary shadow-sm">
                        <div class="card-body text-white">
                            <p class="mb-1 opacity-75 small text-uppercase">Total Sales Periode</p>
                            <h3 class="fw-bold">Rp {{ number_format($summary->total_sales, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-danger shadow-sm">
                        <div class="card-body text-white">
                            <p class="mb-1 opacity-75 small text-uppercase">Total Piutang Berjalan</p>
                            <h3 class="fw-bold">Rp {{ number_format($summary->total_piutang, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-white shadow-sm">
                        <div class="card-body">
                            <p class="mb-1 text-muted small text-uppercase">Rata-rata Umur Nota</p>
                            <h3 class="fw-bold text-dark">{{ $summary->avg_aging ?? 0 }} Hari</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0">Daftar Nota Penjualan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total Akhir</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aging</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">{{ $inv->nomor_invoice }}</td>
                                    <td>{{ $inv->nama_pelanggan }}</td>
                                    <td>{{ date('d/m/Y', strtotime($inv->tgl_invoice)) }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($inv->total_akhir, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match($inv->status_pembayaran) {
                                                'Paid' => 'bg-success',
                                                'Unpaid' => 'bg-danger',
                                                'Partially Paid' => 'bg-warning text-dark',
                                                'Overdue' => 'bg-dark',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $inv->status_pembayaran }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ $inv->umur_nota > 30 && $inv->status_pembayaran != 'Paid' ? 'text-danger fw-bold' : '' }}">
                                            {{ $inv->umur_nota }} Hari
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary px-3" onclick="loadDetail({{ $inv->id }}, '{{ $inv->nomor_invoice }}')">
                                            Rincian
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-25">
                                        <p class="text-muted">Tidak ada data penjualan ditemukan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            Halaman {{ $invoices->currentPage() }} dari {{ $invoices->lastPage() }}
                        </div>
                        <div>
                            {{ $invoices->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">Rincian Nota: <span id="textNoInv"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Item Produk</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="listDetail"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@push('js')
<script>
    // Inisialisasi Dropdown Tom Select
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#tom-mitra", { create: false, sortField: { field: "text", order: "asc" } });
        new TomSelect("#tom-status", { create: false });
    });

    function loadDetail(id, noInv) {
        document.getElementById('textNoInv').innerText = noInv;
        const container = document.getElementById('listDetail');
        container.innerHTML = '<tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>';
        
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        fetch(`/dashboard-sales/detail/${id}`)
            .then(res => {
                if (!res.ok) throw new Error('Data tidak dapat dimuat');
                return res.json();
            })
            .then(data => {
                container.innerHTML = '';
                if(data.length === 0) {
                    container.innerHTML = '<tr><td colspan="4" class="text-center">Nota ini tidak memiliki rincian item.</td></tr>';
                    return;
                }
                data.forEach(item => {
                    let namaProd = item.nama_produk || item.nama_produk_manual || 'Produk Umum';
                    container.innerHTML += `
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold d-block text-dark">${item.sku_kode || 'TANPA SKU'}</span>
                                <small class="text-muted">${namaProd}</small>
                            </td>
                            <td class="text-center">${parseFloat(item.qty)}</td>
                            <td class="text-end">Rp ${parseFloat(item.harga_satuan).toLocaleString('id-ID')}</td>
                            <td class="text-end pe-3 fw-bold text-dark">Rp ${parseFloat(item.total_harga_item).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });
            })
            .catch(err => {
                container.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">${err.message}</td></tr>`;
            });
    }
</script>
@endpush

<style>
    .pagination { margin-bottom: 0; }
    .table thead th { font-size: 11px; letter-spacing: 0.05rem; }
    .ts-control { border-radius: 0.375rem !important; }
</style>
@endsection