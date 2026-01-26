@extends('Layout.main')

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('sales') }}" class="btn btn-light border me-3 shadow-sm rounded-circle"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Detail Invoice</h4>
                                <p class="text-muted small mb-0">No. {{ $invoice->nomor_invoice }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span
                            class="badge {{ $invoice->status_pembayaran == 'Paid' ? 'bg-success' : ($invoice->status_pembayaran == 'Unpaid' ? 'bg-danger' : 'bg-warning') }} fs-6 px-3 py-2 rounded-pill me-2">
                            {{ $invoice->status_pembayaran }}
                        </span>
                        <a href="{{ route('sales.print', $invoice->id) }}" target="_blank"
                            class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2">
                            <i class="fa fa-print me-1"></i> Cetak
                        </a>
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="alert('Fitur Edit akan segera hadir')">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </button>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Invoice Visual -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-5 bg-white">
                                <!-- Reusing SalesNota structure but styled for Web -->
                                <div class="d-flex justify-content-between mb-5">
                                    <div>
                                        <h3 class="fw-bold text-primary mb-1">INVOICE</h3>
                                        <p class="text-muted mb-0">#{{ $invoice->nomor_invoice }}</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold text-dark">{{ \Carbon\Carbon::parse($invoice->tgl_invoice)->format('d F Y') }}</h5>
                                        <p class="text-muted small">Tanggal Invoice</p>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Ditagihkan Kepada:</label>
                                        <h6 class="fw-bold text-dark mb-1">{{ $invoice->mitra->nama ?? 'Umum' }}</h6>
                                        <p class="text-muted small mb-0">{{ $invoice->mitra->alamat ?? '-' }}</p>
                                        <p class="text-muted small">{{ $invoice->mitra->no_telp ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Detail Pengiriman:</label>
                                        <p class="mb-1"><span class="text-muted">Salesman:</span> <span class="fw-bold">{{ $invoice->ref_no ?? '-' }}</span></p>
                                        <p class="mb-1"><span class="text-muted">Jatuh Tempo:</span> <span class="fw-bold text-danger">{{ \Carbon\Carbon::parse($invoice->tgl_jatuh_tempo)->format('d/m/Y') }}</span></p>
                                    </div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-borderless align-middle">
                                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                            <tr>
                                                <th class="py-3 ps-4 rounded-start">Item Produk</th>
                                                <th class="py-3 text-center">Qty</th>
                                                <th class="py-3 text-end">Harga</th>
                                                <th class="py-3 text-end pe-4 rounded-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice->items as $item)
                                                <tr class="border-bottom border-light">
                                                    <td class="ps-4 py-3">
                                                        <div class="fw-bold text-dark">{{ $item->product->nama_produk ?? $item->nama_produk_manual }}</div>
                                                        <div class="small text-muted">{{ $item->product->kode_produk ?? '-' }}</div>
                                                    </td>
                                                    <td class="text-center py-3">{{ number_format($item->qty) }} {{ $item->product->unit->nama_unit ?? '' }}</td>
                                                    <td class="text-end py-3">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                    <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($item->total_harga_item, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="border-top">
                                            <tr>
                                                <td colspan="3" class="text-end pt-4 pb-2 text-muted">Subtotal</td>
                                                <td class="text-end pt-4 pb-2 fw-bold pe-4">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-2 text-muted">Diskon</td>
                                                <td class="text-end py-2 fw-bold text-danger pe-4">- Rp {{ number_format($invoice->diskon_global, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-2 text-muted">Pajak (PPN)</td>
                                                <td class="text-end py-2 fw-bold pe-4">Rp {{ number_format($invoice->pajak_ppn, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end py-3 fs-5 fw-bold text-dark">Total Tagihan</td>
                                                <td class="text-end py-3 fs-5 fw-bold text-primary pe-4">Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded border border-light">
                                            <p class="mb-1 fw-bold text-dark small">Catatan:</p>
                                            <p class="mb-0 text-muted small fst-italic">{{ $invoice->catatan ?? 'Tidak ada catatan.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="col-lg-4">
                        <!-- Documents Sidebar -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-folder-open text-warning me-2"></i> Dokumen Pendukung</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-danger">
                                                <i class="fa fa-file-pdf fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Faktur Penjualan</h6>
                                                <small class="text-muted" style="font-size: 11px;">Otomatis - {{ $invoice->nomor_invoice }}</small>
                                            </div>
                                        </div>
                                        <a href="{{ route('sales.print', $invoice->id) }}" target="_blank" class="btn btn-sm btn-light border"><i class="fa fa-eye"></i></a>
                                    </div>

                                    <!-- Placeholder for other docs -->
                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-secondary">
                                                <i class="fa fa-file-invoice fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Surat Jalan</h6>
                                                <small class="text-warning" style="font-size: 11px;">Belum Diunggah</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-white border text-primary"><i class="fa fa-upload"></i></button>
                                    </div>

                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-secondary">
                                                <i class="fa fa-receipt fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Bukti Pembayaran</h6>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    {{ $invoice->payment->count() }} Transaksi
                                                </small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#payment-history"><i class="fa fa-chevron-down"></i></button>
                                    </div>
                                    
                                    <div class="collapse bg-light" id="payment-history">
                                        <div class="p-3">
                                            @if($invoice->payment->count() > 0)
                                                @foreach($invoice->payment as $pay)
                                                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                                        <div>
                                                            <div class="fw-bold small text-dark">{{ $pay->nomor_pembayaran ?? '-' }}</div>
                                                            <div class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($pay->tgl_bayar)->format('d/m/Y') }}</div>
                                                        </div>
                                                        <div class="fw-bold text-success small">Rp {{ number_format($pay->jumlah_bayar, 0, ',', '.') }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-center text-muted small mb-0">Belum ada pembayaran.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-success">
                                                <i class="fa fa-calculator fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Jurnal Akuntansi</h6>
                                                <small class="text-muted" style="font-size: 11px;">Terbentuk Otomatis</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#journal-entries"><i class="fa fa-chevron-down"></i></button>
                                    </div>

                                    <div class="collapse bg-light" id="journal-entries">
                                        <div class="p-3">
                                            <!-- Assuming we can fetch journal entries via relation or service -->
                                            <!-- For now, static or simple check -->
                                            <div class="alert alert-info small mb-0 p-2">
                                                <i class="fa fa-info-circle me-1"></i> Jurnal tercatat di sistem Accounting.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity/Status Sidebar -->
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-history text-primary me-2"></i> Riwayat Status</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="timeline-simple">
                                    <div class="d-flex mb-3">
                                        <div class="me-3 d-flex flex-column align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;"><i class="fa fa-check"></i></div>
                                            <div class="h-100 border-start border-2 mt-1" style="border-color: #e2e8f0;"></div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 small">Invoice Dibuat</h6>
                                            <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y H:i') }}</p>
                                            <p class="text-muted small fst-italic">Dibuat oleh System</p>
                                        </div>
                                    </div>
                                    <!-- Add more timeline items based on status changes if available -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
