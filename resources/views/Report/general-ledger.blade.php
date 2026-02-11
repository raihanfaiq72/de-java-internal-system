@extends('Layout.main')

@section('main')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content py-4">
            <div class="container-fluid">
                <!-- Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Buku Besar (General Ledger)</h4>
                        <p class="text-muted small mb-0">Laporan detail transaksi per akun.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2" onclick="window.print()">
                            <i class="iconoir-printer me-1"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('report.general-ledger') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Akun (COA)</label>
                                <select name="coa_id" id="select-coa" class="form-select">
                                    <option value="">Semua Akun</option>
                                    @foreach ($coas as $coa)
                                        <option value="{{ $coa->id }}" {{ request('coa_id') == $coa->id ? 'selected' : '' }}>
                                            {{ $coa->kode_akun }} - {{ $coa->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small text-uppercase">Status Dokumen</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small text-uppercase">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small text-uppercase">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary fw-bold w-100">
                                    <i class="iconoir-filter me-1"></i> Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Content -->
                @forelse($reportData as $data)
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $data['account']['kode_akun'] }} - {{ $data['account']['nama_akun'] }}</h6>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark border">
                                        Total Saldo: Rp {{ number_format($data['total_saldo'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="px-4 py-3">Tanggal</th>
                                            <th class="py-3">No. Journal</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Deskripsi</th>
                                            <th class="py-3 text-end">Debit</th>
                                            <th class="py-3 text-end">Kredit</th>
                                            <th class="py-3 text-end px-4">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['lines'] as $line)
                                            <tr>
                                                <td class="px-4 py-3 text-nowrap">{{ \Carbon\Carbon::parse($line->tgl_jurnal)->format('d/m/Y') }}</td>
                                                <td class="fw-bold text-primary small">{{ $line->nomor_journal ?? '-' }}</td>
                                                <td>
                                                    @if($line->status_dok)
                                                        <span class="badge bg-soft-{{ $line->status_dok == 'approved' ? 'success' : 'secondary' }} text-{{ $line->status_dok == 'approved' ? 'success' : 'secondary' }} rounded-pill px-2">
                                                            {{ ucfirst($line->status_dok) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-muted small" style="max-width: 300px;">{{ $line->keterangan }}</td>
                                                <td class="text-end text-dark">{{ number_format($line->debit, 0, ',', '.') }}</td>
                                                <td class="text-end text-dark">{{ number_format($line->kredit, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold px-4 {{ $line->balance < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($line->balance, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light fw-bold">
                                        <tr>
                                            <td colspan="4" class="px-4 text-end text-uppercase text-muted small">Total</td>
                                            <td class="text-end text-dark">{{ number_format($data['total_debit'], 0, ',', '.') }}</td>
                                            <td class="text-end text-dark">{{ number_format($data['total_credit'], 0, ',', '.') }}</td>
                                            <td class="text-end px-4 text-dark">{{ number_format($data['total_saldo'], 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-5 text-center">
                            <div class="mb-3"><i class="iconoir-file-not-found fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada data transaksi</h6>
                            <p class="text-muted small mb-0">Coba sesuaikan filter akun atau rentang tanggal.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-wrapper.form-select { border: none; padding: 0; }
        .ts-dropdown { z-index: 1050; }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect('#select-coa', {
                create: false,
                allowEmptyOption: true,
                placeholder: "Cari Akun...",
                dropdownParent: 'body'
            });
        });
    </script>
@endpush
