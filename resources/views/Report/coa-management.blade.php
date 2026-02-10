@extends('Layout.main')

@section('main')
    <div class="page-wrapper" style="font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Manajemen Chart of Accounts</h4>
                        <p class="text-muted small mb-0">Struktur akun per {{ date('d F Y', strtotime($date)) }}</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('report.balance-sheet') }}?date={{ $date }}"
                            class="btn btn-outline-primary fw-bold px-3 shadow-sm rounded-3">
                            <i class="fa fa-table me-1"></i> Lihat Neraca
                        </a>
                        <button class="btn btn-primary fw-bold px-3 shadow-sm ms-2 rounded-3" onclick="openAddModal()">
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
                                        <button class="accordion-button collapsed fw-bold text-uppercase bg-light" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $kelompokId }}">
                                            {{ $kelompok }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $kelompokId }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionKelompok">
                                        <div class="accordion-body p-0">

                                            {{-- LEVEL 2: TIPE --}}
                                            <div class="accordion accordion-flush" id="accordionTipe-{{ $kelompokId }}">
                                                @foreach ($tipeGroups as $tipe => $accounts)
                                                    @php $tipeId = Str::slug($tipe, '_') . '-' . rand(100, 999); @endphp
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button
                                                                class="accordion-button collapsed ps-4 fw-semibold text-secondary"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $tipeId }}">
                                                                {{ $tipe }}
                                                            </button>
                                                        </h2>

                                                        <div id="collapse-{{ $tipeId }}" class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionTipe-{{ $kelompokId }}">

                                                            <div class="accordion-body p-0">
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover align-middle mb-0">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th class="ps-5">Kode & Nama Akun</th>
                                                                                <th class="text-center" width="150">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($accounts as $acc)
                                                                                <tr>
                                                                                    <td class="ps-5">
                                                                                        <span
                                                                                            class="badge bg-light text-dark font-monospace border me-2">{{ $acc->kode_akun }}</span>
                                                                                        <span
                                                                                            class="fw-medium">{{ $acc->nama_akun }}</span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <button
                                                                                            class="btn btn-sm btn-outline-warning rounded-circle edit-coa"
                                                                                            data-id="{{ $acc->coa_id }}" title="Edit">
                                                                                            <i class="fa fa-pencil"></i>
                                                                                        </button>
                                                                                        <button
                                                                                            class="btn btn-sm btn-outline-danger rounded-circle delete-coa"
                                                                                            data-id="{{ $acc->coa_id }}" title="Hapus">
                                                                                            <i class="fa fa-trash"></i>
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

    <!-- Modal -->
    <div class="modal fade" id="coaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="coaForm" class="modal-content rounded-3 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah COA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="coa_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kelompok Akun</label>
                        <select id="kelompok_akun_id" class="form-select" required>
                            <option value="">-- Pilih Kelompok --</option>
                            @foreach($dropdownGroups as $grp)
                                <option value="{{ $grp->id }}">{{ $grp->nama_kelompok }} ({{ $grp->kode_kelompok }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tipe Akun</label>
                        <select id="tipe_akun_id" class="form-select" required disabled>
                            <option value="">-- Pilih Kelompok Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Kode Akun</label>
                            <input type="text" id="kode_akun" class="form-control" placeholder="Contoh: 1101" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold small">Nama Akun</label>
                            <input type="text" id="nama_akun" class="form-control" placeholder="Contoh: Kas Kecil" required>
                        </div>
                    </div>

                    <div class="form-check p-3 bg-light rounded border">
                        <input type="checkbox" id="is_kas_bank" class="form-check-input">
                        <label class="form-check-label fw-medium" for="is_kas_bank">
                            Tandai sebagai Kas / Bank
                            <div class="text-muted small fw-normal">Akun ini akan muncul di pilihan pembayaran.</div>
                        </label>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Pass PHP data to JS
        const allTypes = @json($dropdownTypes);

        function openAddModal() {
            $('#coaForm')[0].reset();
            $('#coa_id').val('');
            $('#modalTitle').text('Tambah COA');
            $('#kelompok_akun_id').val('').trigger('change');
            $('#coaModal').modal('show');
        }

        // Filter Tipe based on Kelompok
        $('#kelompok_akun_id').change(function () {
            let groupId = $(this).val();
            let $typeSelect = $('#tipe_akun_id');

            $typeSelect.empty();

            if (!groupId) {
                $typeSelect.append('<option value="">-- Pilih Kelompok Terlebih Dahulu --</option>');
                $typeSelect.prop('disabled', true);
                return;
            }

            $typeSelect.prop('disabled', false);
            $typeSelect.append('<option value="">-- Pilih Tipe --</option>');

            let filteredTypes = allTypes.filter(t => t.kelompok_id == groupId);

            filteredTypes.forEach(t => {
                $typeSelect.append(`<option value="${t.id}">${t.nama_tipe}</option>`);
            });
        });

        $('#coaForm').submit(function (e) {
            e.preventDefault();

            let id = $('#coa_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = id ? `/api/coa-api/${id}` : `/api/coa-api`;

            // Loading state
            let $btn = $(this).find('button[type="submit"]');
            let originalText = $btn.text();
            $btn.prop('disabled', true).text('Menyimpan...');

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
                success: function (res) {
                    $('#coaModal').modal('hide');
                    // Show toast or alert? Reload for now
                    location.reload();
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).text(originalText);
                    let msg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Get first error
                        let errors = xhr.responseJSON.errors;
                        let firstKey = Object.keys(errors)[0];
                        msg = errors[firstKey][0];
                    }
                    alert(msg);
                }
            });
        });

        $(document).on('click', '.edit-coa', function () {
            let id = $(this).data('id');
            $.get(`/api/coa-api/${id}`, function (data) {
                $('#coa_id').val(data.id);
                $('#kode_akun').val(data.kode_akun);
                $('#nama_akun').val(data.nama_akun);
                $('#is_kas_bank').prop('checked', data.is_kas_bank);

                // Find group from type
                let type = allTypes.find(t => t.id == data.tipe_id);
                if (type) {
                    $('#kelompok_akun_id').val(type.kelompok_id).trigger('change');
                    // Wait for change to populate types, then set value
                    setTimeout(() => {
                        $('#tipe_akun_id').val(data.tipe_id);
                    }, 0);
                }

                $('#modalTitle').text('Edit COA');
                $('#coaModal').modal('show');
            });
        });

        $(document).on('click', '.delete-coa', async function () {
            if (!await macConfirm('Hapus COA', 'Yakin hapus COA ini?')) return;

            let id = $(this).data('id');
            let $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: `/api/coa-api/${id}`,
                method: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function () {
                    location.reload();
                },
                error: function (xhr) {
                    $btn.prop('disabled', false);
                    let msg = 'Gagal menghapus';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });
    </script>
@endpush