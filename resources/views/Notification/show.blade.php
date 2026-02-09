@extends('Layout.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light me-3">
                        <i class="iconoir-arrow-left"></i> Kembali
                    </a>
                    <h4 class="card-title m-0">Detail Notifikasi</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if(($notification->data['type'] ?? 'info') == 'success')
                            <div class="avatar-lg bg-success-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="iconoir-check-circle fs-1 text-success"></i>
                            </div>
                        @elseif(($notification->data['type'] ?? 'info') == 'warning')
                            <div class="avatar-lg bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="iconoir-warning-circle fs-1 text-warning"></i>
                            </div>
                        @elseif(($notification->data['type'] ?? 'info') == 'error')
                            <div class="avatar-lg bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="iconoir-x-circle fs-1 text-danger"></i>
                            </div>
                        @else
                            <div class="avatar-lg bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="iconoir-info-circle fs-1 text-primary"></i>
                            </div>
                        @endif

                        <h3 class="fw-bold">{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                        <p class="text-muted">{{ $notification->created_at->format('d F Y, H:i') }}</p>
                    </div>

                    <hr>

                    <div class="py-3">
                        <h5 class="fw-bold text-dark">Pesan:</h5>
                        <p class="fs-16 text-muted">{{ $notification->data['message'] ?? '' }}</p>
                    </div>

                    @if(!empty($notification->data['data']))
                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="fw-bold mb-3">Data Terkait:</h6>
                            <table class="table table-sm table-borderless mb-0">
                                @foreach($notification->data['data'] as $key => $value)
                                    <tr>
                                        <td class="text-muted" width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                        <td class="fw-medium">
                                            @if(is_array($value))
                                                <pre class="mb-0 text-muted">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif

                    @if(!empty($notification->data['url']) && $notification->data['url'] != '#')
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ $notification->data['url'] }}" class="btn btn-primary btn-lg">
                                <i class="iconoir-open-new-window me-2"></i> Buka Tautan Terkait
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
