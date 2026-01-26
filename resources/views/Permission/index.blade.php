@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; }
    
    .permission-card { transition: all 0.2s; border: 1px solid #e2e8f0; }
    .permission-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    
    .table-custom thead th { background: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
    .table-custom tbody td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f8fafc; color: #334155; vertical-align: middle; }
    
    .form-control-minimal { border: 1px dashed #cbd5e1; background: #fafafa; font-size: 13px; padding: 6px 12px; border-radius: 6px; }
    .form-control-minimal:focus { border: 1px solid #3b82f6; background: #fff; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); outline: none; }
    
    .badge-module { font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 600; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
             <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Mapping Akses Rute</h4>
                    <p class="text-muted small mb-0">Tentukan nama tampilan (alias) untuk setiap teknikal rute sistem.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button onclick="location.reload()" class="btn btn-white border fw-bold px-3 shadow-sm text-dark">
                        <i class="fa fa-sync me-1 text-primary"></i> Refresh Data
                    </button>
                    <!-- Expand All Button or other tools could go here -->
                </div>
            </div>

            <div class="row g-4">
                @foreach($prefixes as $prefix)
                <div class="col-12">
                    <div class="card permission-card shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between" 
                             style="cursor: pointer;" 
                             data-bs-toggle="collapse" 
                             data-bs-target="#collapse-{{ $prefix->id }}" 
                             aria-expanded="true">
                            <div class="d-flex align-items-center">
                                <span class="badge-module me-3 text-uppercase">MODUL: {{ $prefix->name }}</span>
                                <span class="text-muted small">Mengatur {{ count($prefix->permissions) }} rute akses.</span>
                            </div>
                            <i class="fa fa-chevron-down text-muted small"></i>
                        </div>
                        <div id="collapse-{{ $prefix->id }}" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th width="40%">System Route Name</th>
                                        <th width="45%">Display Label (Nama Tampilan)</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prefix->permissions as $p)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-code text-muted me-2 small opacity-50"></i>
                                                <code class="text-danger f-mono bg-light px-2 py-1 rounded small">{{ $p->name }}</code>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" id="input-{{ $p->id }}" 
                                                class="form-control form-control-minimal text-dark fw-medium" 
                                                value="{{ $p->description }}" 
                                                placeholder="Berikan nama yang mudah dimengerti...">
                                        </td>
                                        <td class="text-center">
                                            <button onclick="updateLabel({{ $p->id }}, event)" class="btn btn-sm btn-light border text-primary fw-bold shadow-sm px-3 hover-shadow">
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
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function updateLabel(id, event) {
        const description = document.getElementById('input-' + id).value;
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        fetch(`/admin/permissions/${id}`, { // Using Route::put('/permissions/{id}')
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ description: description })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                btn.classList.add('btn-success', 'text-white');
                btn.classList.remove('btn-light', 'text-primary', 'border');
                btn.innerHTML = '<i class="fa fa-check"></i> Saved';
                
                setTimeout(() => {
                    btn.classList.remove('btn-success', 'text-white');
                    btn.classList.add('btn-light', 'text-primary', 'border');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 1500);
            } else {
                alert('Gagal: ' + data.message);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>
@endpush