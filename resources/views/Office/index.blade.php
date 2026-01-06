@extends('Layout.main')
@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Daftar Kantor / Outlet</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOffice">Tambah Kantor</button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kantor</th>
                            <th>Dibuat Pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offices as $o)
                        <tr>
                            <td><span class="badge bg-dark">{{ $o->code }}</span></td>
                            <td class="fw-bold">{{ $o->name }}</td>
                            <td>{{ $o->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addOffice" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('offices.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5>Tambah Kantor Baru</h5></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="small fw-bold">Nama Kantor</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Kode Kantor</label>
                    <input type="text" name="code" class="form-control" placeholder="Contoh: KCP-SMG" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection