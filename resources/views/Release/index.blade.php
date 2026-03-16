@extends('Layout.main')

@section('title', 'App Release')

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
                                    <i class="fa fa-download me-2"></i>App Release
                                </h4>
                                <p class="text-muted mb-0 small">Download aplikasi DeJava Mobile untuk pengalaman terbaik</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-soft-primary text-primary p-2">
                                    <i class="fa fa-android me-1"></i> Android
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Release Cards -->
                <div class="row" id="releaseContainer">
                    <!-- Dynamic content will be loaded here -->
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat daftar release...</p>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5" style="display: none;">
                    <i class="fa fa-folder-open display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Release</h5>
                    <p class="text-muted">Belum ada versi aplikasi yang tersedia untuk didownload.</p>
                </div>

            </div>
        </div>
    </div>

    <style>
        .release-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .release-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .release-header {
            background: linear-gradient(135deg, #1d1d1f 0%, #636366 100%);
            color: white;
            padding: 20px;
            position: relative;
        }

        .release-date {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .release-version {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 0;
        }

        .release-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .release-body {
            padding: 20px;
        }

        .release-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .release-icon {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #007AFF;
        }

        .release-details h6 {
            margin-bottom: 5px;
            font-weight: 600;
            color: #1d1d1f;
        }

        .release-details p {
            margin-bottom: 0;
            font-size: 14px;
            color: #6c757d;
        }

        .release-actions {
            display: flex;
            gap: 10px;
        }

        .btn-download {
            background: #007AFF;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-download:hover {
            background: #0056CC;
            color: white;
            transform: translateY(-1px);
        }

        .btn-info {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-info:hover {
            background: #e9ecef;
            color: #495057;
        }

        .latest-badge {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .release-actions {
                flex-direction: column;
            }
            
            .btn-download, .btn-info {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadReleaseData();
        });

        async function loadReleaseData() {
            const loadingState = document.getElementById('loadingState');
            const emptyState = document.getElementById('emptyState');
            const releaseContainer = document.getElementById('releaseContainer');

            try {
                // Fetch release data from API
                const response = await fetch('/api/releases');
                const releases = await response.json();

                loadingState.style.display = 'none';

                if (releases.length === 0) {
                    emptyState.style.display = 'block';
                    return;
                }

                // Sort releases by date (newest first)
                releases.sort((a, b) => new Date(b.date) - new Date(a.date));

                // Render release cards
                releaseContainer.innerHTML = releases.map((release, index) => {
                    const isLatest = index === 0;
                    const formattedDate = formatDate(release.date);
                    
                    return `
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="release-card">
                                <div class="release-header">
                                    <div class="release-date">${formattedDate}</div>
                                    <div class="release-version">
                                        ${release.version}
                                        ${isLatest ? '<span class="latest-badge">TERBARU</span>' : ''}
                                    </div>
                                    <div class="release-badge">
                                        <i class="fa fa-android me-1"></i> Android
                                    </div>
                                </div>
                                <div class="release-body">
                                    <div class="release-info">
                                        <div class="release-icon">
                                            <i class="fa fa-android"></i>
                                        </div>
                                        <div class="release-details">
                                            <h6>DeJava Mobile App</h6>
                                            <p>Version ${release.version} • ${release.fileSize}</p>
                                        </div>
                                    </div>
                                    <div class="release-actions">
                                        <a href="${release.downloadUrl}" class="btn-download" onclick="trackDownload('${release.version}')">
                                            <i class="fa fa-download"></i>
                                            Download
                                        </a>
                                        <a href="#" class="btn-info" onclick="showReleaseInfo('${release.version}', '${release.date}', '${release.fileSize}')">
                                            <i class="fa fa-info-circle"></i>
                                            Info
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

            } catch (error) {
                console.error('Error loading release data:', error);
                loadingState.style.display = 'none';
                emptyState.style.display = 'block';
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            return date.toLocaleDateString('id-ID', options);
        }

        function trackDownload(version) {
            console.log(`Download tracked for version: ${version}`);
            // You can add analytics tracking here
        }

        function showReleaseInfo(version, date, fileSize) {
            const formattedDate = formatDate(date);
            
            // Create modal content
            const modalHtml = `
                <div class="modal fade" id="releaseInfoModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fa fa-android me-2"></i>DeJava Mobile App
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                        <i class="fa fa-android" style="font-size: 36px; color: #007AFF;"></i>
                                    </div>
                                    <h6>Version ${version}</h6>
                                    <p class="text-muted">Aplikasi mobile untuk mengelola bisnis Anda</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Tanggal Release</small>
                                        <p class="mb-0 fw-semibold">${formattedDate}</p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Ukuran File</small>
                                        <p class="mb-0 fw-semibold">${fileSize}</p>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Fitur Utama</small>
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fa fa-check text-success me-2"></i>Dashboard real-time</li>
                                        <li><i class="fa fa-check text-success me-2"></i>Manajemen transaksi</li>
                                        <li><i class="fa fa-check text-success me-2"></i>Laporan mobile</li>
                                        <li><i class="fa fa-check text-success me-2"></i>Notifikasi push</li>
                                    </ul>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <small>Pastikan perangkat Android Anda memiliki versi minimal 5.0 (Lollipop) untuk menginstall aplikasi ini.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="downloadFromModal('${version}')">
                                    <i class="fa fa-download me-2"></i>Download Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('releaseInfoModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to body and show
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('releaseInfoModal'));
            modal.show();
        }

        function downloadFromModal(version) {
            // Find the download link for this version
            const downloadLink = document.querySelector(`[onclick*="trackDownload('${version}')"]`);
            if (downloadLink) {
                downloadLink.click();
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('releaseInfoModal'));
            modal.hide();
        }
    </script>
@endsection
