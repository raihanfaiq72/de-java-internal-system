@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    
    .detail-card {
        border: 1px solid #e2e8f0;
    }
    .data-table th {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .data-table td {
        padding-top: 8px;
        font-size: 13px;
        color: #334155;
    }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-7">
                            <h4 class="fw-bold text-dark mb-1">Detail Notifikasi</h4>
                            <p class="text-muted small mb-0">Informasi lengkap mengenai notifikasi terpilih.</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('notifications.index') }}" class="btn btn-white border fw-bold px-3 shadow-sm text-dark">
                                <i class="fa fa-arrow-left me-1 text-muted"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
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
                                @endphp
                                
                                <div class="{{ $bgClass }} rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
        
                                <h3 class="fw-bold text-dark mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                                <p class="text-muted f-mono mb-0">
                                    <i class="fa fa-clock me-1"></i> {{ $notification->created_at->format('d F Y, H:i') }}
                                </p>
                            </div>
        
                            <div class="py-4 border-top border-bottom">
                                <span class="f-label">Pesan Notifikasi</span>
                                <p class="fs-6 text-dark mb-0" style="line-height: 1.6;">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
        
                            @if(!empty($notification->data['data']))
                                <div class="mt-4">
                                    <div class="bg-light p-4 rounded-3 border border-light">
                                        <span class="f-label mb-3">Data Terkait</span>
                                        <table class="table table-borderless table-sm mb-0 data-table w-100">
                                            @foreach($notification->data['data'] as $key => $value)
                                                <tr>
                                                    <td width="30%" class="align-top text-muted">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                                    <td class="fw-medium text-dark">
                                                        @if(is_array($value))
                                                            <pre class="mb-0 bg-white p-2 border rounded f-mono small text-secondary">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
        
                            @if(!empty($notification->data['url']) && $notification->data['url'] != '#')
                                <div class="d-grid gap-2 mt-4">
                                    <a href="{{ $notification->data['url'] }}" class="btn btn-primary fw-bold py-2 shadow-sm">
                                        <i class="fa fa-external-link-alt me-2"></i> Buka Tautan Terkait
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light py-3 text-center border-top">
                             <small class="text-muted">Notifikasi ID: <span class="f-mono">{{ $notification->id }}</span></small>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
