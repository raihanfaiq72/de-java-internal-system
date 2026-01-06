@extends('Layout.main')
@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="page-wrapper">
    <div class="page-content">
        <h4 class="fw-bold mb-4">Plotting Otoritas User</h4>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('user_plots.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="small fw-bold">Pilih User</label>
                        <select name="user_id" id="select-user" required>
                            @foreach($users as $u) <option value="{{ $u->id }}">{{ $u->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold">Pilih Kantor</label>
                        <select name="office_id" id="select-office" required>
                            @foreach($offices as $o) <option value="{{ $o->id }}">{{ $o->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold">Pilih Role</label>
                        <select name="role_id" id="select-role" required>
                            @foreach($roles as $r) <option value="{{ $r->id }}">{{ $r->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="d-block text-white">.</label>
                        <button type="submit" class="btn btn-dark w-100 fw-bold">Simpan Plotting</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama User</th>
                            <th>Kantor / Outlet</th>
                            <th>Role / Jabatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plots as $p)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $p->user_name }}</td>
                            <td>{{ $p->office_name }}</td>
                            <td><span class="badge bg-soft-primary text-primary border">{{ $p->role_name }}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#select-user");
    new TomSelect("#select-office");
    new TomSelect("#select-role");
</script>
@endsection