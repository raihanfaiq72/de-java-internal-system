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
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.3);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{url('assets/images/auth/bg.jpg')}}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: #fff;
        }

        /* Top Bar */
        .top-bar {
            padding: 20px 40px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .profile-dropdown .dropdown-toggle {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 6px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .profile-dropdown .dropdown-toggle:hover {
            background: rgba(0, 0, 0, 0.6);
            transform: translateY(-1px);
        }

        .profile-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #007AFF 0%, #0051a8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .dropdown-menu {
            background: rgba(30, 30, 30, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin-top: 10px;
            padding: 8px;
            min-width: 180px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .dropdown-item.text-danger:hover {
            background: rgba(255, 59, 48, 0.2);
            color: #FF3B30;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-bottom: 80px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .page-title {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 60px;
            color: #fff;
            text-shadow: 0 4px 12px rgba(0,0,0,0.6);
            letter-spacing: -0.5px;
        }

        .outlet-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
            justify-content: center;
            max-width: 1200px;
            padding: 0 20px;
        }

        .outlet-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            group: ;
            position: relative;
        }

        .outlet-icon-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: pointer;
            margin-bottom: 15px;
            position: relative;
        }

        .outlet-item:hover .outlet-icon-wrapper {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .outlet-name {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            text-align: center;
            max-width: 140px;
            line-height: 1.3;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
            transition: all 0.3s;
        }

        .outlet-item:hover .outlet-name {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
            transform: translateY(2px);
        }

        .delete-btn {
            position: absolute;
            top: 0;
            left: 0;
            width: 28px;
            height: 28px;
            background: rgba(100, 100, 100, 0.8);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            opacity: 0;
            transition: all 0.2s;
            cursor: pointer;
            z-index: 10;
        }
        
        .delete-btn:hover {
            background: #FF3B30;
            transform: scale(1.1);
            border-color: #FF3B30;
        }

        .outlet-item:hover .delete-btn {
            opacity: 1;
            top: -5px;
            left: -5px;
        }

        .add-outlet-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: transparent;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .add-outlet-wrapper:hover {
            border-color: #fff;
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Loading Overlay */
        .redirect-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
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
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #007AFF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
        }

        .form-control-mac {
            border-radius: 12px;
            border: 1px solid #d1d1d6;
            padding: 10px 15px;
            background: rgba(255,255,255,0.8);
        }

        .form-control-mac:focus {
            background: #fff;
            border-color: #007AFF;
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.15);
        }

        .btn-mac-primary {
            background-color: #007AFF;
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            border: none;
            width: 100%;
        }

        .btn-mac-primary:hover {
            background-color: #0062cc;
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
                <span class="fw-semibold small">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="icofont-logout me-2"></i> Logout
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
                    🏢
                    
                    @if(in_array($office->role_name, ['Superadmin', 'Owner']))
                    <div class="delete-btn" onclick="event.stopPropagation(); deleteOutlet({{ $office->id }}, '{{ $office->name }}')">
                        <i class="icofont-close"></i>
                    </div>
                    @endif
                </div>
                <div class="outlet-name">{{ $office->name }}</div>
            </div>
            @endforeach

            <!-- Add New Outlet -->
            <div class="outlet-item">
                <div class="add-outlet-wrapper" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                    ➕
                </div>
                <div class="outlet-name" style="color: #ddd; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">Buka Outlet</div>
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
                'PERINGATAN!', 
                `Apakah Anda yakin ingin menghapus outlet "${name}"? \n\nSemua data (transaksi, stok, user, dll) yang berhubungan dengan outlet ini akan dihapus permanen.`
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