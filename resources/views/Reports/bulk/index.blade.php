@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title">Laporan Massal</h4>
                                <p class="text-muted small mb-0">Daftar laporan massal per periode</p>
                            </div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                                <li class="breadcrumb-item active">Laporan Massal</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">Daftar Laporan Massal</h6>
                            <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreatePeriod">
                                <i class="fa fa-plus-circle me-1"></i> Buat Periode
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px;">
                                    <tr>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Dibuat Tanggal</th>
                                        <th>Di-generate</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bulkReports as $bulkReport)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $bulkReport->period_name }}</div>
                                                <small class="text-muted">{{ $bulkReport->start_date->format('d M Y') }} - {{ $bulkReport->end_date->format('d M Y') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $bulkReport->status == 'draft' ? 'warning' : 
                                                    ($bulkReport->status == 'generated' ? 'info' : 'success') 
                                                }} rounded-pill px-3">
                                                    {{ $bulkReport->status }}
                                                </span>
                                            </td>
                                            <td>{{ $bulkReport->generator->name }}</td>
                                            <td>{{ $bulkReport->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($bulkReport->generated_at)
                                                    <span class="text-success fw-bold">{{ $bulkReport->generated_at->format('d/m/Y H:i') }}</span>
                                                @else
                                                    <span class="text-muted">Belum</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" onclick="openPreview('{{ $bulkReport->slug }}')" 
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fa fa-eye"></i> Preview
                                                    </button>
                                                    
                                                    <a href="{{ route('bulk-reports.detail', $bulkReport->slug) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fa fa-list"></i> Detail
                                                    </a>
                                                    
                                                    @if($bulkReport->status == 'draft')
                                                        <button type="button" onclick="generatePDF('{{ $bulkReport->slug }}')" 
                                                            class="btn btn-sm btn-outline-success">
                                                            <i class="fa fa-file-pdf"></i> Cetak
                                                        </button>
                                                    @endif

                                                    @if($bulkReport->status == 'generated')
                                                        <button type="button" onclick="markAsPrinted('{{ $bulkReport->slug }}')" 
                                                            class="btn btn-sm btn-outline-info">
                                                            <i class="fa fa-check"></i> Tandai Dicetak
                                                        </button>
                                                    @endif

                                                    @if($bulkReport->status == 'generated')
                                                        <a href="{{ route('bulk-reports.generate-pdf', $bulkReport->slug) }}" 
                                                           target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa fa-download"></i> PDF
                                                        </a>
                                                    @endif

                                                    <button type="button" onclick="deleteReport('{{ $bulkReport->slug }}', '{{ $bulkReport->period_name }}')" 
                                                        class="btn btn-sm btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <div class="mb-2"><i class="fa fa-file-alt fs-1 text-secondary opacity-50"></i></div>
                                                Belum ada laporan massal.<br>Silakan klik tombol "Buat Periode" untuk membuat laporan baru.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $bulkReports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Period Modal -->
    <div class="modal fade" id="modalCreatePeriod" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('bulk-reports.store-period') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="fw-bold mb-0">Buat Periode Laporan Massal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Bulan</label>
                                <select name="month" class="form-select" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Tahun</label>
                                <select name="year" class="form-select" required>
                                    <option value="">-- Pilih Tahun --</option>
                                    @for($year = 2020; $year <= date('Y'); $year++)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmPeriod" required>
                                    <label class="form-check-label" for="confirmPeriod">
                                        Saya yakin dengan periode yang dipilih
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary fw-bold">Buat Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="modalBulkPreview" tabindex="-1" aria-labelledby="modalBulkPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalBulkPreviewLabel">
                        <i class="iconoir-print text-primary me-2"></i> Preview Laporan Massal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 ">
                    <div class="ratio ratio-16x9 border rounded bg-light" style="min-height: 70vh;">
                        <iframe id="bulk-preview-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success fw-bold px-4 shadow-sm" onclick="triggerBulkPrint()">
                        <i class="fa fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function openPreview(id) {
    const url = `/bulk-reports/${id}/preview`;
    const modalContainer = document.getElementById('modalBulkPreview');

    if (!modalContainer) {
        console.error('Modal container tidak ditemukan di halaman ini.');
        return;
    }

    const iframe = modalContainer.querySelector('iframe');

    if (!iframe) {
        console.error('Elemen iframe tidak ditemukan di dalam modal.');
        alert('Gagal memuat preview laporan.');
        return;
    }

    iframe.src = url;

    const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
    bModal.show();
}

function triggerBulkPrint() {
    const iframe = document.getElementById('bulk-preview-iframe');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }
}

function generatePDF(id) {
    if (confirm('Apakah Anda yakin ingin generate PDF untuk laporan ini?')) {
        window.open(`/bulk-reports/${id}/generate-pdf`, '_blank');
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

function markAsPrinted(id) {
    if (confirm('Tandai laporan ini sebagai telah dicetak?')) {
        fetch(`/bulk-reports/${id}/mark-printed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteReport(id, periodName) {
    if (confirm(`Apakah Anda yakin ingin menghapus laporan "${periodName}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bulk-reports/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}    }
    }
</script>
@endpush
