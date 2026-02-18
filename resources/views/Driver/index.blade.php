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

                    @if(!$completed->isEmpty())
                        <h5 class="mt-4 mb-3">Riwayat Selesai (10 Terakhir)</h5>
                        <div class="list-group">
                            @foreach($completed as $fleet)
                            <a href="{{ route('driver.delivery.show', $fleet->delivery_order_id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $fleet->deliveryOrder->delivery_order_number }}</div>
                                    <small class="text-muted">{{ $fleet->deliveryOrder->delivery_date->format('d M Y') }} - {{ $fleet->fleet->license_plate }}</small>
                                </div>
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
