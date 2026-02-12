@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Periode Penggajian</h4>
                        <p class="text-muted small mb-0">Kelola periode dan generate slip gaji karyawan.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="mdi mdi-plus me-1"></i> Buat Periode Baru
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark">Daftar Periode</h6>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th>Nama Periode</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @forelse($periods as $period)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $period->name }}</td>
                                        <td>{{ $period->start_date->format('d M Y') }}</td>
                                        <td>{{ $period->end_date->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $period->status == 'open' ? 'success' : 'secondary' }} rounded-pill px-3">
                                                {{ ucfirst($period->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="{{ route('salary-periods.show', $period->id) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                                    Detail
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Yakin ingin menghapus?')) document.getElementById('delete-{{ $period->id }}').submit()">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                            <form id="delete-{{ $period->id }}" action="{{ route('salary-periods.destroy', $period->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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

                        <div class="d-flex justify-content-end mt-4">
                            {{ $periods->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('salary-periods.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Buat Periode Gaji Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nama Periode (Opsional)</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Gaji Februari 2026">
                            <small class="text-muted">Jika kosong, akan otomatis dibuat berdasarkan tanggal.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
