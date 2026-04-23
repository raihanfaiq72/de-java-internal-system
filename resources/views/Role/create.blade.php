@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <span>Main Menu</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-user-shield"></i></span>
                        <a href="{{ route('roles.index') }}" class="text-decoration-none text-muted">Role Management</a>
                        <span>/</span>
                        <strong>Tambah Role Baru</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Buat Role Baru</h1>
                            <p class="dr-subtitle">Tentukan nama role dan centang izin akses yang ingin diberikan.</p>
                        </div>
                        <div class="dr-actions">
                            <a href="{{ route('roles.index') }}" class="dr-btn dr-btn-outline">
                                <i class="iconoir-nav-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

        <div class="container-fluid">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="d-flex align-items-center mb-3" style="height: 32px;">
                            <h6 class="fw-bold text-dark mb-0 text-uppercase small ls-1">
                                <i class="iconoir-info-circle me-2 text-primary"></i>Informasi Dasar
                            </h6>
                        </div>
                        <div class="dr-card">
                            <div class="mb-3">
                                <label class="dr-label mb-2">Nama Role *</label>
                                <input type="text" name="name" class="dr-input fw-bold"
                                    placeholder="Misal: Superadmin, Sales Admin" required>
                            </div>
                            <div class="mb-0">
                                <label class="dr-label mb-2">Deskripsi</label>
                                <textarea name="description" class="dr-input" rows="4" placeholder="Penjelasan singkat peran ini"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-3" style="height: 32px;">
                            <h6 class="fw-bold text-dark mb-0 text-uppercase small ls-1">
                                <i class="iconoir-lock me-2 text-primary"></i>Pengaturan Hak Akses (Permissions)
                            </h6>
                        </div>

                        @foreach ($prefixes as $prefix)
                            <div class="dr-card mb-3 p-0 overflow-hidden">
                                <div class="bg-white d-flex justify-content-between align-items-center py-2 px-4 border-bottom">
                                    <span class="fw-bold text-primary small" style="letter-spacing: 0.5px;">{{ strtoupper($prefix->name) }}</span>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input check-all"
                                            data-prefix="{{ $prefix->id }}" id="all-{{ $prefix->id }}">
                                        <label class="form-check-label small text-muted" for="all-{{ $prefix->id }}">Pilih
                                            Semua</label>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="row g-3">
                                        @foreach ($prefix->permissions as $p)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                                        class="form-check-input perm-check-{{ $prefix->id }}"
                                                        id="perm-{{ $p->id }}">
                                                    <label class="form-check-label small" for="perm-{{ $p->id }}">
                                                        <span class="fw-medium text-dark">{{ $p->description ?: $p->name }}</span>
                                                        <div class="text-muted" style="font-size: 10px;">Route: {{ $p->name }}</div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-end mt-4">
                            <button type="submit" class="dr-btn dr-btn-primary px-5">
                                <i class="iconoir-check-circle me-2"></i> SIMPAN ROLE
                            </button>
                        </div>
                    </div>
                </div>
            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.check-all').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const prefixId = this.getAttribute('data-prefix');
                    const children = document.querySelectorAll('.perm-check-' + prefixId);
                    children.forEach(child => child.checked = this.checked);
                });
            });
        });
    </script>
@endpush