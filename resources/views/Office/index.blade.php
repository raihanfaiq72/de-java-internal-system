@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    
    #officeTable thead th { border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; background: #f8fafc; padding-top: 12px; padding-bottom: 12px; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
    #officeTable tbody td { font-size: 13px; color: #334155; padding-top: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .badge-code { background: #fee2e2; color: #b91c1c; font-family: 'JetBrains Mono', monospace; font-size: 11px; padding: 4px 8px; border-radius: 4px; border: 1px solid #fecaca; }
    .f-input { border: 1px solid #e2e8f0; padding: 0.6rem 0.8rem; border-radius: 6px; font-size: 13px; transition: all 0.2s; }
    .f-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
             <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Daftar Kantor / Outlet</h4>
                    <p class="text-muted small mb-0">Kelola lokasi kantor dan cabang perusahaan.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openOfficeModal()">
                        <i class="fa fa-plus-circle me-1"></i> Tambah Kantor Baru
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                   <h6 class="mb-0 fw-bold text-dark">Data Kantor</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="officeTable">
                            <thead>
                                <tr>
                                    <th width="150" class="ps-4">Kode Kantor</th>
                                    <th>Nama Kantor</th>
                                    <th width="200" class="text-center">Tanggal Dibuat</th>
                                    <th width="100" class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($offices as $o)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge-code">{{ $o->code }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $o->name }}</span>
                                    </td>
                                    <td class="text-center text-muted small f-mono">
                                        {{ $o->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button onclick="openOfficeModal({{ $o->id }})" class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-primary me-1" title="Edit Kantor">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <form action="{{ route('offices.destroy', $o->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kantor ini? Data terkait mungkin akan error.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-danger" title="Hapus Kantor">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fa fa-building fa-3x mb-3 opacity-25"></i>
                                        <p class="text-muted small">Belum ada data kantor.</p>
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

<div class="modal fade" id="officeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="officeForm" action="{{ route('offices.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="officeModalTitle">Tambah Kantor Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="f-label">Nama Kantor</label>
                    <input type="text" name="name" id="officeName" class="form-control f-input fw-bold" placeholder="Misal: Kantor Pusat Semarang" required>
                </div>
                <div class="mb-3">
                    <label class="f-label">Kode Kantor (Singkatan)</label>
                    <input type="text" name="code" id="officeCode" class="form-control f-input f-mono" placeholder="Misal: KCP-SMG" required>
                    <small class="text-muted font-11">Kode unik untuk identifikasi sistem.</small>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
    async function openOfficeModal(id = null) {
        const form = document.getElementById('officeForm');
        const modalTitle = document.getElementById('officeModalTitle');
        const methodInput = document.getElementById('formMethod');
        const nameInput = document.getElementById('officeName');
        const codeInput = document.getElementById('officeCode');
        
        form.reset();
        
        if (id) {
            modalTitle.innerText = 'Edit Data Kantor';
            form.action = `/admin/offices/${id}`; 
            methodInput.value = 'PUT'; 

            try {
                 const res = await fetch(`/admin/offices/${id}`); 
                 const json = await res.json();
                 if(json.success) {
                     nameInput.value = json.data.name;
                     codeInput.value = json.data.code;
                 }
            } catch(e) { console.error(e); alert('Gagal load data kantor'); return; }

        } else {
            modalTitle.innerText = 'Tambah Kantor Baru';
            form.action = "{{ route('offices.store') }}";
            methodInput.value = 'POST';
        }

        new bootstrap.Modal(document.getElementById('officeModal')).show();
    }
</script>
@endpush
@endsection