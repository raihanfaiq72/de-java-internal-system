@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <i class="iconoir-home dr-breadcrumb-icon"></i>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Penggajian</strong>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Periode Gaji</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Periode Penggajian</h1>
                            <p class="dr-subtitle">Kelola siklus pembayaran gaji karyawan secara berkala.</p>
                        </div>
                        <div class="dr-actions">
                            <button class="dr-btn dr-btn-primary" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                                <i class="iconoir-plus"></i> Buat Periode Baru
                            </button>
                        </div>
                    </div>

                    <!-- Main Table Card -->
                    <div class="dr-card p-0 overflow-hidden bg-white mt-4">
                        <div class="table-responsive">
                            <table class="dr-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama Periode</th>
                                        <th>Rentang Tanggal</th>
                                        <th>Status</th>
                                        <th>Dibuat Pada</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($periods as $period)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold"
                                                        style="width: 32px; height: 32px; font-size: 12px;">
                                                        <i class="iconoir-calendar"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('salary-periods.show', $period->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                            {{ $period->name }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark fw-medium small">
                                                    {{ \Carbon\Carbon::parse($period->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($period->status === 'closed')
                                                    <span class="badge bg-soft-secondary text-secondary px-2 py-1 rounded-pill" style="font-size: 11px;">
                                                        <i class="iconoir-lock me-1"></i> Terkunci
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill" style="font-size: 11px;">
                                                        <i class="iconoir-check-circle me-1"></i> Terbuka
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">
                                                {{ $period->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <a href="{{ route('salary-periods.show', $period->id) }}" class="btn btn-sm btn-icon btn-light">
                                                        <i class="iconoir-eye"></i>
                                                    </a>
                                                    <form action="{{ route('salary-periods.destroy', $period->id) }}" method="POST"
                                                        onsubmit="return confirm('Hapus periode ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light text-danger">
                                                            <i class="iconoir-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">Belum ada periode penggajian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($periods->hasPages())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 bg-white border-top">
                            <span class="text-muted small fw-medium">
                                Menampilkan {{ $periods->firstItem() }} sampai {{ $periods->lastItem() }} dari {{ $periods->total() }} data
                            </span>
                            <nav>
                                {{ $periods->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Period -->
    <div class="modal fade" id="createPeriodModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-white">
                <form action="{{ route('salary-periods.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="iconoir-calendar text-dark" style="font-size: 20px;"></i>
                            <div>
                                <h5 class="dr-title mb-1" style="font-size: 16px;">Buat Periode Baru</h5>
                                <p class="dr-subtitle mb-0" style="font-size: 12px;">Tentukan rentang tanggal penggajian.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 pb-4 pt-0 bg-white">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="dr-label mb-2">Tanggal Mulai *</label>
                                <input type="date" name="start_date" class="dr-input" required value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="dr-label mb-2">Tanggal Selesai *</label>
                                <input type="date" name="end_date" class="dr-input" required value="{{ date('Y-m-t') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                        <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                            <i class="iconoir-xmark text-danger"></i> Batal
                        </button>
                        <button type="submit" class="dr-btn-modal">
                            <i class="iconoir-check text-success"></i> Simpan Periode
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
