<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Outlet - Dejava System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ url('assets/css/icons.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
            --macos-radius: 24px;
            --app-icon-radius: 22px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: url('{{url('assets/images/auth/bg.jpg')}}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: #1d1d1f;
            margin: 0;
        }

        /* Backdrop Blur Overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            z-index: -1;
        }

        /* Top Bar - Menu Bar Style */
        .top-bar {
            padding: 10px 24px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            height: 44px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .profile-dropdown .dropdown-toggle {
            padding: 4px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1d1d1f;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 13px;
            font-weight: 500;
        }

        .profile-dropdown .dropdown-toggle:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .profile-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #007AFF 0%, #0051a8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 11px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .dropdown-menu {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(50px);
            -webkit-backdrop-filter: blur(50px);
            border: 0.5px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 8px;
            padding: 5px;
            min-width: 160px;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 400;
            color: #1d1d1f;
        }

        .dropdown-item:hover {
            background: #007AFF;
            color: #fff;
        }

        .dropdown-item.text-danger:hover {
            background: #FF3B30;
            color: #fff !important;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-top: 60px; /* Offset for fixed top bar */
            animation: scaleIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }

        .page-title {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 50px;
            color: #1d1d1f;
            letter-spacing: -0.02em;
            text-shadow: 0 1px 2px rgba(255,255,255,0.5);
        }

        .outlet-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 48px;
            justify-content: center;
            max-width: 1000px;
            padding: 0 20px;
        }

        .outlet-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        .outlet-item:active {
            transform: scale(0.95);
            filter: brightness(0.9);
        }

        .outlet-icon-wrapper {
            width: 100px;
            height: 100px;
            border-radius: var(--app-icon-radius);
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #1d1d1f;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }
        
        /* App Icon Style Gradient for specific roles or just general */
        .outlet-icon-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--app-icon-radius);
            background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
            z-index: -1;
        }

        .outlet-item:hover .outlet-icon-wrapper {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.02);
            background: rgba(255, 255, 255, 0.9);
        }

        .outlet-name {
            font-size: 14px;
            font-weight: 500;
            color: #1d1d1f;
            text-align: center;
            max-width: 110px;
            line-height: 1.3;
            text-shadow: 0 1px 1px rgba(255,255,255,0.8);
        }

        /* Delete Badge - iOS Style */
        .delete-btn {
            position: absolute;
            top: -8px;
            left: -8px;
            width: 26px;
            height: 26px;
            background: rgba(120, 120, 128, 0.8);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: none;
            opacity: 0;
            transition: all 0.2s ease;
            cursor: pointer;
            z-index: 10;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        
        .delete-btn:hover {
            background: #FF3B30;
            transform: scale(1.1);
        }

        .outlet-item:hover .delete-btn {
            opacity: 1;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            50% { transform: rotate(10deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }

        /* Add Outlet Style */
        .add-outlet-wrapper {
            width: 100px;
            height: 100px;
            border-radius: var(--app-icon-radius);
            background: rgba(245, 245, 247, 0.5);
            border: 2px dashed rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: rgba(0, 0, 0, 0.4);
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 12px;
        }

        .add-outlet-wrapper:hover {
            border-color: #007AFF;
            color: #007AFF;
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* Loading Overlay */
        .redirect-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(0px);
            z-index: 2000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            visibility: hidden;
            opacity: 0;
            transition: all 0.5s ease;
        }

        .redirect-overlay.active {
            visibility: visible;
            opacity: 1;
            backdrop-filter: blur(25px);
        }

        .mac-spinner {
            width: 32px;
            height: 32px;
            color: #666;
            animation: spin 1s linear infinite;
        }
        
        .mac-spinner::after {
            content: '';
            display: block;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 3px solid currentColor;
            border-right-color: transparent;
            border-top-color: transparent;
            animation: spin 0.8s ease-in-out infinite;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 18px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 16px 20px;
            background: rgba(255,255,255,0.5);
            border-radius: 18px 18px 0 0;
        }
        
        .modal-title {
            font-size: 15px;
            font-weight: 600;
            color: #1d1d1f;
        }

        .btn-close {
            transform: scale(0.8);
        }

        .form-control-mac {
            border-radius: 8px;
            border: 1px solid #d1d1d6;
            padding: 8px 12px;
            background: rgba(255,255,255,0.8);
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control-mac:focus {
            background: #fff;
            border-color: #007AFF;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
            outline: none;
        }

        .btn-mac-primary {
            background: #007AFF;
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-mac-primary:hover {
            background: #0062cc;
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="profile-dropdown dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span>{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">Pilih Outlet</h1>

        <div class="outlet-grid" id="outletGrid">
            @foreach($availableOffices as $office)
            <div class="outlet-item">
                <div class="outlet-icon-wrapper" onclick="selectOffice({{ $office->id }})">
                    <span style="font-size: 42px;">🏢</span>
                    
                    @if(in_array($office->role_name, ['Superadmin', 'Owner']))
                    <button class="delete-btn" onclick="event.stopPropagation(); deleteOutlet({{ $office->id }}, '{{ $office->name }}')">
                        ✕
                    </button>
                    @endif
                </div>
                <div class="outlet-name">{{ $office->name }}</div>
            </div>
            @endforeach

            <!-- Add New Outlet -->
            <div class="outlet-item">
                <div class="add-outlet-wrapper" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                    <span style="font-size: 32px; font-weight: 300;">+</span>
                </div>
                <div class="outlet-name" style="color: #86868b;">Buka Outlet</div>
            </div>
        </div>
    </div>

    <!-- Redirect Overlay -->
    <div class="redirect-overlay" id="redirectOverlay">
        <div class="mac-spinner"></div>
        <div class="mt-3 fw-semibold text-secondary">Menyiapkan Ruang Kerja...</div>
    </div>

    <!-- Modal Add Office -->
    <div class="modal fade" id="addOfficeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Outlet Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addOfficeForm" action="{{ route('offices.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control form-control-mac" placeholder="Nama Outlet" required>
                        </div>
                        <div class="mb-4">
                            <input type="text" name="code" class="form-control form-control-mac" placeholder="Kode Outlet" required>
                        </div>
                        <button type="submit" class="btn btn-mac-primary">Buat Outlet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('Layout._alert_helper')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectOffice(officeId) {
            const redirectOverlay = document.getElementById('redirectOverlay');
            redirectOverlay.classList.add('active');

            fetch("{{ route('set.outlet') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ office_id: officeId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 800);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi kesalahan koneksi.");
                location.reload();
            });
        }

        async function deleteOutlet(id, name) {
            const confirmed = await macConfirm(
                'Hapus Outlet?', 
                `Apakah Anda yakin ingin menghapus outlet "${name}"? Tindakan ini tidak dapat dibatalkan dan semua data terkait akan hilang.`
            );

            if (!confirmed) return;

            const redirectOverlay = document.getElementById('redirectOverlay');
            // Show loading
            redirectOverlay.classList.add('active');
            
            fetch("{{ url('delete-outlet') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                redirectOverlay.classList.remove('active');
                if(data.success) {
                    showNotification('success', 'Outlet berhasil dihapus.');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', 'Gagal: ' + data.message);
                }
            })
            .catch(err => {
                redirectOverlay.classList.remove('active');
                console.error(err);
                showNotification('error', 'Terjadi kesalahan koneksi.');
            });
        }

        // Handle Add Office Form
        document.getElementById('addOfficeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Menyimpan...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>