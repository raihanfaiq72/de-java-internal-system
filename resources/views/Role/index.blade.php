@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row mb-3">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="page-title fw-bold">Manajemen Role & Hak Akses</h4>
                        <p class="text-muted mb-0 small">Atur tingkatan jabatan dan batasan akses modul untuk tiap role.</p>
                    </div>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary fw-bold shadow-sm">
                        <i class="fa fa-plus-circle me-1"></i> TAMBAH ROLE BARU
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">
                                        <th class="ps-4 py-3">Nama Role</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center">Jumlah Izin</th>
                                        <th class="text-center">Dibuat Pada</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <i class="fa fa-shield-alt"></i>
                                                </div>
                                                <span class="fw-bold text-dark">{{ $role->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $role->description ?: '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-primary text-primary px-3">{{ $role->permissions_count }} Modul</span>
                                        </td>
                                        <td class="text-center small text-muted">
                                            {{ $role->created_at->format('d M Y') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit Role">
                                                    <i class="fa fa-edit"></i>
                                                </div>
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus role ini? User yang terhubung akan kehilangan akses.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus Role">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fa fa-user-shield fa-3x mb-3 opacity-25"></i>
                                            <p class="text-muted">Belum ada role yang dibuat. Silakan tambahkan role pertama Anda.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: #e7f0ff;
        color: #3b7ddd;
        border: 1px solid #c6dcff;
    }
    .table thead th {
        font-size: 11px;
    }
</style>
@endsection