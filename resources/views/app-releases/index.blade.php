@extends('Layout.main')

@section('title', 'App Releases')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="page-title fw-bold">
                                    <i class="fa fa-mobile-alt me-2"></i>App Releases
                                </h4>
                                <p class="text-muted mb-0 small">Kelola versi aplikasi DeJava Mobile</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('app-releases.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-1"></i> Tambah Release
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $releases->count() }}</h5>
                                        <p class="text-muted mb-0">Total Releases</p>
                                    </div>
                                    <div class="avatar-sm bg-primary rounded">
                                        <i class="fa fa-code-branch fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $releases->where('status', 'published')->count() }}</h5>
                                        <p class="text-muted mb-0">Published</p>
                                    </div>
                                    <div class="avatar-sm bg-success rounded">
                                        <i class="fa fa-check fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $releases->where('is_latest', true)->count() }}</h5>
                                        <p class="text-muted mb-0">Latest</p>
                                    </div>
                                    <div class="avatar-sm bg-warning rounded">
                                        <i class="fa fa-star fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ number_format($releases->sum('download_count')) }}</h5>
                                        <p class="text-muted mb-0">Total Downloads</p>
                                    </div>
                                    <div class="avatar-sm bg-info rounded">
                                        <i class="fa fa-download fa-lg text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Releases Table -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Version</th>
                                                <th>Platform</th>
                                                <th>Status</th>
                                                <th>Release Date</th>
                                                <th>File Size</th>
                                                <th>Downloads</th>
                                                <th>Latest</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($releases as $release)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <i class="fa {{ $release->platform === 'android' ? 'fa-android' : 'fa-apple' }} text-{{ $release->platform === 'android' ? 'success' : 'primary' }}"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold">{{ $release->version }}</div>
                                                                @if($release->build_number)
                                                                    <small class="text-muted">Build {{ $release->build_number }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $release->platform === 'android' ? 'success' : 'primary' }} bg-opacity-10 text-{{ $release->platform === 'android' ? 'success' : 'primary' }}">
                                                            {{ ucfirst($release->platform) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $release->status === 'published' ? 'success' : ($release->status === 'draft' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst($release->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($release->release_date)->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $bytes = $release->file_size;
                                                            if ($bytes >= 1073741824) {
                                                                $size = number_format($bytes / 1073741824, 2) . ' GB';
                                                            } elseif ($bytes >= 1048576) {
                                                                $size = number_format($bytes / 1048576, 2) . ' MB';
                                                            } elseif ($bytes >= 1024) {
                                                                $size = number_format($bytes / 1024, 2) . ' KB';
                                                            } else {
                                                                $size = $bytes . ' bytes';
                                                            }
                                                        @endphp
                                                        {{ $size }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light">{{ $release->download_count }}</span>
                                                    </td>
                                                    <td>
                                                        @if($release->is_latest)
                                                            <span class="badge bg-warning">
                                                                <i class="fa fa-star me-1"></i>Latest
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('app-releases.show', $release) }}" class="btn btn-outline-primary">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('app-releases.edit', $release) }}" class="btn btn-outline-secondary">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            @if(\Storage::exists($release->file_path))
                                                                <a href="{{ route('app-releases.download', $release) }}" class="btn btn-outline-success">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            @endif
                                                            <form action="{{ route('app-releases.destroy', $release) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">No releases found</h5>
                                                        <p class="text-muted">Create your first app release to get started.</p>
                                                        <a href="{{ route('app-releases.create') }}" class="btn btn-primary">
                                                            <i class="fa fa-plus me-1"></i> Create Release
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
