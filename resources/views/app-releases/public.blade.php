@extends('Layout.main')

@section('title', 'DeJava Mobile App')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Header -->
                <div class="row">
                    <div class="col-12">
                        <div class="text-center mb-4">
                            <h1 class="page-title fw-bold mb-3">DeJava Mobile App</h1>
                            <p class="text-muted">
                                Download aplikasi DeJava untuk Android dan nikmati kemudahan mengelola bisnis Anda di mana saja.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-primary mb-1">{{ number_format($releases->sum('download_count')) }}</h4>
                                <small class="text-muted">Total Download</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-success mb-1">{{ $releases->count() }}</h4>
                                <small class="text-muted">Versi Tersedia</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-warning mb-1">{{ $releases->where('is_latest', true)->count() }}</h4>
                                <small class="text-muted">Versi Terbaru</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-info mb-1">{{ $releases->where('platform', 'android')->count() }}</h4>
                                <small class="text-muted">Aplikasi Android</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Releases -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-3">Versi Aplikasi</h4>
                        
                        <div class="row">
                            @forelse($releases as $release)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body"> 
                                            <div class="text-center mb-3">
                                                <i class="fa-brands fa-android fa-3x text-success"></i>
                                            </div>
                                            
                                            <h5 class="text-center mb-2">{{ $release->version }}</h5>
                                            
                                            <div class="text-center mb-3">
                                                @if($release->is_latest)
                                                    <span class="badge bg-warning me-2">Terbaru</span>
                                                @endif
                                                
                                                @if($release->is_force_update)
                                                    <span class="badge bg-danger me-2">Wajib Update</span>
                                                @endif
                                            </div>
                                            
                                            <div class="small text-muted text-center mb-3">
                                                <div>{{ \Carbon\Carbon::parse($release->release_date)->format('d M Y') }}</div>
                                                <div>
                                                    @php
                                                        $bytes = $release->file_size;
                                                        if ($bytes >= 1048576) {
                                                            $size = number_format($bytes / 1048576, 1) . ' MB';
                                                        } elseif ($bytes >= 1024) {
                                                            $size = number_format($bytes / 1024, 1) . ' KB';
                                                        } else {
                                                            $size = $bytes . ' bytes';
                                                        }
                                                    @endphp
                                                    {{ $size }} • {{ $release->download_count }} download
                                                </div>
                                            </div>
                                            
                                            @if($release->description)
                                                <p class="text-muted small text-center mb-3">{{ $release->description }}</p>
                                            @endif
                                            
                                            @if($release->changelog && count($release->changelog) > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted">Apa yang baru:</small>
                                                    <ul class="small text-muted mb-0">
                                                        @foreach($release->changelog as $item)
                                                            <li>{{ $item }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            
                                            <div class="text-center">
                                                <a href="{{ route('public.download', $release) }}" 
                                                   class="btn btn-{{ $release->is_latest ? 'success' : 'primary' }} w-100 download-btn"
                                                   data-loading-text="Downloading...">
                                                    <i class="fa fa-download me-1"></i>
                                                    {{ $release->is_latest ? 'Download Terbaru' : 'Download' }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center py-5">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum Ada Versi Tersedia</h5>
                                            <p class="text-muted">Saat ini belum ada versi aplikasi yang tersedia untuk diunduh.</p>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle download buttons
            const downloadBtns = document.querySelectorAll('.download-btn');
            
            downloadBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    const originalText = this.innerHTML;
                    const loadingText = this.getAttribute('data-loading-text');
                    
                    // Show loading state
                    this.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>' + loadingText;
                    this.classList.add('disabled');
                    this.style.pointerEvents = 'none';
                    
                    // Create temporary link for download
                    const downloadUrl = this.href;
                    const tempLink = document.createElement('a');
                    tempLink.href = downloadUrl;
                    tempLink.style.display = 'none';
                    document.body.appendChild(tempLink);
                    
                    // Trigger download
                    tempLink.click();
                    
                    // Remove temporary link
                    document.body.removeChild(tempLink);
                    
                    // Reset button after delay
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('disabled');
                        this.style.pointerEvents = 'auto';
                    }, 2000);
                    
                    e.preventDefault();
                });
            });
        });
    </script>
@endsection
