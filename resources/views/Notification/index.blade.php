@extends('Layout.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Semua Notifikasi</h4>
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Tandai Semua Dibaca</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <a href="{{ route('notifications.show', $notification->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $notification->read_at ? '' : 'bg-light' }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        @if($notification->data['type'] == 'success')
                                            <i class="iconoir-check-circle fs-2 text-success"></i>
                                        @elseif($notification->data['type'] == 'warning')
                                            <i class="iconoir-warning-circle fs-2 text-warning"></i>
                                        @elseif($notification->data['type'] == 'error')
                                            <i class="iconoir-x-circle fs-2 text-danger"></i>
                                        @else
                                            <i class="iconoir-info-circle fs-2 text-primary"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit($notification->data['message'] ?? '', 100) }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @if(!$notification->read_at)
                                    <span class="badge bg-primary rounded-pill">Baru</span>
                                @endif
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="iconoir-bell-off fs-1 text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada notifikasi.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
