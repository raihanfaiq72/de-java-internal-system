@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="page-title-box d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="page-title fw-bold">Mapping Nama Rute</h4>
                <p class="text-muted small mb-0">Berikan nama kustom pada rute sistem agar mudah dipahami saat pembuatan role.</p>
            </div>
            <button onclick="location.reload()" class="btn btn-sm btn-outline-secondary"><i class="fa fa-sync me-1"></i> Refresh Data</button>
        </div>

        @foreach($prefixes as $prefix)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary text-uppercase"><i class="fa fa-folder-open me-2"></i>Modul: {{ $prefix->name }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" width="35%">Route Name (Sistem)</th>
                                <th width="50%">Nama Custom (Label Tampilan)</th>
                                <th class="text-center" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prefix->permissions as $p)
                            <tr>
                                <td class="ps-4">
                                    <code class="text-danger small">{{ $p->name }}</code>
                                </td>
                                <td>
                                    <input type="text" id="input-{{ $p->id }}" class="form-control form-control-sm border-dashed" value="{{ $p->description }}" placeholder="Contoh: Lihat Daftar Invoice">
                                </td>
                                <td class="text-center">
                                    <button onclick="updateLabel({{ $p->id }})" class="btn btn-sm btn-primary shadow-sm px-3">
                                        <i class="fa fa-save me-1"></i> Simpan
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('js')
<script>
    function updateLabel(id) {
        const description = document.getElementById('input-' + id).value;
        const btn = event.currentTarget;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        fetch(`/permissions/update/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ description: description })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                btn.classList.replace('btn-primary', 'btn-success');
                btn.innerHTML = '<i class="fa fa-check"></i>';
                setTimeout(() => {
                    btn.classList.replace('btn-success', 'btn-primary');
                    btn.innerHTML = '<i class="fa fa-save me-1"></i> Simpan';
                    btn.disabled = false;
                }, 2000);
            }
        })
        .catch(err => {
            alert('Gagal memperbarui label');
            btn.disabled = false;
        });
    }
</script>
@endpush