@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Manajemen Chart of Accounts</h4>
                        <p class="text-muted small mb-0">Struktur akun per {{ date('d F Y', strtotime($date)) }}</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('report.balance-sheet') }}?date={{ $date }}"
                            class="btn btn-outline-primary fw-bold px-3 shadow-sm">
                            <i class="fa fa-table me-1"></i> Lihat Neraca
                        </a>
                        <button class="btn btn-primary fw-bold px-3 shadow-sm ms-2" data-bs-toggle="modal"
                            data-bs-target="#coaModal">
                            <i class="fa fa-plus me-1"></i> Tambah COA
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="accordion accordion-flush" id="accordionKelompok">

                            @foreach ($groupedAccounts as $kelompok => $tipeGroups)
                                @php $kelompokId = Str::slug($kelompok, '_'); @endphp

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $kelompokId }}">
                                        <button class="accordion-button collapsed fw-bold bg-white" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $kelompokId }}">
                                            {{ $kelompok }}
                                        </button>
                                    </h2>

                                    <div id="collapse-{{ $kelompokId }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionKelompok">

                                        <div class="accordion-body p-0">

                                            {{-- LEVEL 2 --}}
                                            <div class="accordion accordion-flush" id="accordionTipe-{{ $kelompokId }}">

                                                @foreach ($tipeGroups as $tipe => $accounts)
                                                    @php $tipeId = Str::slug($kelompok.'-'.$tipe, '_'); @endphp

                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading-{{ $tipeId }}">
                                                            <button class="accordion-button collapsed ps-4" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $tipeId }}">
                                                                {{ $tipe }}
                                                            </button>
                                                        </h2>

                                                        <div id="collapse-{{ $tipeId }}"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionTipe-{{ $kelompokId }}">

                                                            <div class="accordion-body p-0">
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover align-middle mb-0">
                                                                        <tbody>
                                                                            @foreach ($accounts as $acc)
                                                                                <tr>
                                                                                    <td
                                                                                        class="text-end pe-4 font-monospace">
                                                                                        {{ $acc->nama_akun }} -
                                                                                        {{ $acc->kode_akun }}
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <button
                                                                                            class="btn btn-sm btn-warning edit-coa"
                                                                                            data-id="{{ $acc->coa_id }}">Edit</button>
                                                                                        <button
                                                                                            class="btn btn-sm btn-danger delete-coa"
                                                                                            data-id="{{ $acc->coa_id }}">Hapus</button>
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
                                            {{-- END LEVEL 2 --}}

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="modal fade" id="coaModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="coaForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah / Edit COA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="coa_id">

                    <div class="mb-2">
                        <label>Kode Akun</label>
                        <input type="text" id="kode_akun" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Nama Akun</label>
                        <input type="text" id="nama_akun" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Kelompok Akun</label>
                        <select id="kelompok_akun_id" class="form-control" required></select>
                    </div>

                    <div class="mb-2">
                        <label>Tipe Akun</label>
                        <select id="tipe_akun_id" class="form-control" required></select>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="is_kas_bank" class="form-check-input">
                        <label class="form-check-label">Kas / Bank</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <!-- JQUERY HARUS PERTAMA -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const apiUrl = "{{ route('coa-api.index') }}";

        $('#coaForm').submit(function(e) {
            e.preventDefault();

            let id = $('#coa_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = id ? `/coa-api/${id}` : `/coa-api`;

            $.ajax({
                url: url,
                method: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_akun: $('#kode_akun').val(),
                    nama_akun: $('#nama_akun').val(),
                    kelompok_akun_id: $('#kelompok_akun_id').val(),
                    tipe_akun_id: $('#tipe_akun_id').val(),
                    is_kas_bank: $('#is_kas_bank').is(':checked') ? 1 : 0
                },
                success: function(res) {
                    location.reload();
                }
            });
        });

        $(document).on('click', '.edit-coa', function() {
            let id = $(this).data('id');
            $.get(`/coa-api/${id}`, function(data) {
                $('#coa_id').val(data.id);
                $('#kode_akun').val(data.kode_akun);
                $('#nama_akun').val(data.nama_akun);
                $('#kelompok_akun_id').val(data.kelompok_id);
                $('#tipe_akun_id').val(data.tipe_id);
                $('#is_kas_bank').prop('checked', data.is_kas_bank);
                $('#coaModal').modal('show');
            });
        });

        $(document).on('click', '.delete-coa', function() {
            if (!confirm('Yakin hapus COA ini?')) return;
            let id = $(this).data('id');

            $.ajax({
                url: `/coa-api/${id}`,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    location.reload();
                }
            });
        });
    </script>
@endpush
