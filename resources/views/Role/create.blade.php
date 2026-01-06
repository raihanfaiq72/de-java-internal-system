@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="page-title-box d-flex align-items-center justify-content-between mb-3">
            <h4 class="page-title fw-bold">Tambah Role Baru</h4>
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Role</label>
                                <input type="text" name="name" class="form-control" placeholder="Misal: Superadmin, Sales Admin" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Penjelasan singkat peran ini"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <h6 class="fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">Pengaturan Hak Akses (Permissions)</h6>
                    
                    @foreach($prefixes as $prefix)
                    <div class="card shadow-sm border-0 mb-3 overflow-hidden">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold text-primary">{{ strtoupper($prefix->name) }}</span>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input check-all" data-prefix="{{ $prefix->id }}" id="all-{{ $prefix->id }}">
                                <label class="form-check-label small" for="all-{{ $prefix->id }}">Pilih Semua</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($prefix->permissions as $p)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="form-check-input perm-check-{{ $prefix->id }}" id="perm-{{ $p->id }}">
                                        <label class="form-check-label small" for="perm-{{ $p->id }}">
                                            {{ $p->description ?: $p->name }}
                                            <div class="text-muted" style="font-size: 9px;">Route: {{ $p->name }}</div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow">SIMPAN ROLE</button>
                    </div>
                </div>
            </div>
        </form>
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