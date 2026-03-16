@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    
    .notification-list-item {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
    }
    .notification-list-item:hover {
        background-color: #f8fafc;
    }
    .notification-list-item:last-child {
        border-bottom: none;
    }
    .notification-list-item.unread {
        background-color: #f0f9ff;
    }
    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
    }
    .bulk-report-link {
        color: #007AFF;
        text-decoration: none;
        font-weight: 600;
    }
    .bulk-report-link:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Notifikasi Sistem</h4>
                    <p class="text-muted small mb-0">Pusat informasi dan pembaruan aktivitas sistem.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-white border fw-bold px-3 shadow-sm text-dark">
                            <i class="fa fa-check-double me-1 text-primary"></i> Tandai Semua Dibaca
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">Riwayat Notifikasi</h6>
                    <small class="text-muted">
                        @if(request('show_all'))
                            <a href="{{ route('notifications.index') }}" class="text-decoration-none">Tampilkan 4 Baris</a>
                        @else
                            <a href="{{ route('notifications.index', ['show_all' => true]) }}" class="text-decoration-none">Tampilkan Semua</a>
                        @endif
                    </small>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <a href="{{ route('notifications.show', $notification->id) }}" class="text-decoration-none notification-list-item d-flex align-items-start {{ $notification->read_at ? '' : 'unread' }}">
                                <div class="flex-shrink-0 me-3">
                                    @php
                                        $type = $notification->data['type'] ?? 'info';
                                        $iconClass = 'fa fa-info-circle';
                                        $bgClass = 'bg-soft-primary text-primary';
                                        
                                        if($type == 'success') {
                                            $iconClass = 'fa fa-check-circle';
                                            $bgClass = 'bg-soft-success text-success';
                                        } elseif($type == 'warning') {
                                            $iconClass = 'fa fa-exclamation-triangle';
                                            $bgClass = 'bg-soft-warning text-warning';
                                        } elseif($type == 'error') {
                                            $iconClass = 'fa fa-times-circle';
                                            $bgClass = 'bg-soft-danger text-danger';
                                        }
                                        
                                        // Special icons for bulk reports
                                        if(str_contains($type, 'bulk_report')) {
                                            $iconClass = 'fa fa-file-pdf';
                                            if($type == 'bulk_report_created') $bgClass = 'bg-soft-success text-success';
                                            elseif($type == 'bulk_report_generated') $bgClass = 'bg-soft-info text-info';
                                            elseif($type == 'bulk_report_printed') $bgClass = 'bg-soft-warning text-warning';
                                            elseif($type == 'bulk_report_deleted') $bgClass = 'bg-soft-danger text-danger';
                                        }
                                    @endphp
                                    <div class="notification-icon {{ $bgClass }} border border-opacity-25">
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 text-dark fw-bold" style="font-size: 14px;">{{ $notification->data['title'] ?? 'Notifikasi' }}</h6>
                                        <small class="text-muted f-mono" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                                        {{ Str::limit($notification->data['message'] ?? '', 120) }}
                                        
                                        @if(isset($notification->data['bulk_report_id']))
                                            <br>
                                            <a href="{{ route('bulk-reports.detail', $notification->data['bulk_report_id']) }}" class="bulk-report-link">
                                                <i class="fa fa-external-link-alt me-1"></i>Lihat Detail Laporan
                                            </a>
                                        @endif
                                    </p>
                                    
                                    @if(isset($notification->data['period_name']))
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark f-label">PERIODE: {{ $notification->data['period_name'] }}</span>
                                        </div>
                                    @endif
                                    
                                    @if(isset($notification->data['created_by']))
                                        <div class="mt-1">
                                            <small class="text-muted">Oleh: {{ $notification->data['created_by'] ?? $notification->data['marked_by'] ?? $notification->data['deleted_by'] ?? 'System' }}</small>
                                        </div>
                                    @endif
                                </div>
                                @if(!$notification->read_at)
                                    <div class="ms-3 align-self-center">
                                        <span class="badge bg-primary rounded-pill" style="font-size: 10px;">Baru</span>
                                    </div>
                                @endif
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fa fa-bell-slash text-muted opacity-25" style="font-size: 48px;"></i>
                                </div>
                                <h6 class="text-muted fw-bold">Tidak ada notifikasi</h6>
                                <p class="text-muted small mb-0">Anda belum memiliki notifikasi baru saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(!request('show_all') && $notifications->hasPages())
                        <div class="p-3 border-top bg-light">
                            <div class="text-center">
                                <a href="{{ route('notifications.index', ['show_all' => true]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-list me-1"></i> Tampilkan Semua Notifikasi ({{ $notifications->total() }})
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3 border-top bg-light">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
