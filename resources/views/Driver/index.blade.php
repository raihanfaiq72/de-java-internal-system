@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Tugas Pengiriman Saya</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Driver App</li>
                        </ol>
                    </div>
                    <p class="text-muted mb-3 small">Kelola pengiriman dan rute perjalanan Anda</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    @if($assigned->isEmpty() && $completed->isEmpty())
                        <div class="alert alert-info">Belum ada tugas pengiriman yang diberikan.</div>
                    @endif

                    @if(!$assigned->isEmpty())
                        <h5 class="mb-3">Tugas Aktif</h5>
                        <div class="row">
                            @foreach($assigned as $fleet)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title text-primary">{{ $fleet->deliveryOrder->delivery_order_number }}</h5>
                                            <span class="badge bg-{{ $fleet->status == 'assigned' ? 'warning' : 'info' }}">
                                                {{ ucfirst(str_replace('_', ' ', $fleet->status)) }}
                                            </span>
                                        </div>
                                        <p class="card-text text-muted mb-1">
                                            <i class="iconoir-calendar me-1"></i> {{ $fleet->deliveryOrder->delivery_date->format('d M Y') }}
                                        </p>
                                        <p class="card-text text-muted mb-3">
                                            <i class="iconoir-truck me-1"></i> {{ $fleet->fleet->fleet_name }} ({{ $fleet->fleet->license_plate }})
                                        </p>
                                        <a href="{{ route('driver.delivery.show', $fleet->delivery_order_id) }}" class="btn btn-primary w-100">
                                            <i class="iconoir-map me-1"></i> Lihat Rute & Tugas
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4">
                        <button class="btn btn-light border fw-bold" data-bs-toggle="modal" data-bs-target="#completedHistoryModal">
                            <i class="iconoir-clock-solid me-1"></i> Riwayat Selesai (10 Terakhir)
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Riwayat Selesai -->
<div class="modal fade" id="completedHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold mb-0">Riwayat Selesai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="completedList" class="list-group list-group-flush">
                    <div class="p-4 text-center">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const FLEET_API_INDEX = "{{ route('delivery-order-fleet-api.index') }}";

    async function loadCompletedHistory(page = 1) {
        const container = document.getElementById('completedList');
        container.innerHTML = '<div class="p-4 text-center"><div class="spinner-border text-primary"></div></div>';
        try {
            const url = new URL(FLEET_API_INDEX, window.location.origin);
            url.searchParams.set('status', 'completed');
            url.searchParams.set('mine', '1');
            url.searchParams.set('per_page', '10');
            url.searchParams.set('page', page);

            const res = await fetch(url);
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Gagal memuat data');

            const data = json.data?.data || [];
            if (!data.length) {
                container.innerHTML = '<div class="p-4 text-center text-muted">Belum ada riwayat selesai.</div>';
                return;
            }

            let html = '';
            const linkBase = "{{ route('driver.delivery.show', '__ID__') }}";
            data.forEach(item => {
                const doNum = item.delivery_order?.delivery_order_number || '-';
                const dateStr = item.delivery_order?.delivery_date ? new Date(item.delivery_order.delivery_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                const plate = item.fleet?.license_plate || '-';
                const href = linkBase.replace('__ID__', item.delivery_order_id);
                html += `
                    <a href="${href}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">${doNum}</div>
                            <small class="text-muted">${dateStr} - ${plate}</small>
                        </div>
                        <span class="badge bg-success rounded-pill">Selesai</span>
                    </a>
                `;
            });
            container.innerHTML = html;
        } catch (e) {
            console.error(e);
            container.innerHTML = '<div class="p-4 text-center text-danger">Terjadi kesalahan saat memuat riwayat.</div>';
        }
    }

    document.getElementById('completedHistoryModal').addEventListener('shown.bs.modal', function () {
        loadCompletedHistory();
    }, { once: true });
</script>
@endpush
@endsection
