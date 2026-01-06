<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Kantor - Premium macOS Style</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/icofont/1.0.1/css/icofont.min.css" rel="stylesheet">

    <style>
        :root {
            --mac-blue: #007AFF;
            --mac-bg: #F5F5F7;
            --card-shadow: 0 8px 30px rgba(0,0,0,0.04);
            --card-hover-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--mac-bg);
            background-image: radial-gradient(circle at top right, #e2e8f0 0%, #F5F5F7 40%);
            min-height: 100vh;
            color: #1d1d1f;
            overflow-x: hidden;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body.is-redirecting {
            transform: scale(0.95);
        }

        .page-header {
            padding: 80px 0 40px;
            text-align: center;
        }

        .page-title {
            font-weight: 700;
            font-size: 2.5rem;
            letter-spacing: -0.03em;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .office-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 30px;
            text-align: left;
            position: relative;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            opacity: 0;
            transform: translateY(30px);
            animation: slideIn 0.8s forwards;
        }

        .office-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--card-hover-shadow);
            background: rgba(255, 255, 255, 0.9);
        }

        @keyframes slideIn {
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
            background: linear-gradient(135deg, #007AFF, #00C6FF);
            color: white;
            box-shadow: 0 8px 15px rgba(0, 122, 255, 0.2);
        }

        .office-status {
            position: absolute;
            top: 30px;
            right: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            background: #e8f5e9;
            color: #2e7d32;
        }

        .office-card h5 { font-weight: 700; margin-bottom: 8px; }
        .office-card p { color: #86868b; font-size: 0.9rem; margin-bottom: 20px; }

        .office-meta {
            font-size: 0.85rem;
            color: #515154;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 25px;
        }

        .office-meta span i { margin-right: 8px; color: var(--mac-blue); }

        .btn-mac-primary {
            background-color: var(--mac-blue);
            color: white;
            border-radius: 14px;
            padding: 10px 24px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .add-card {
            border: 2px dashed #c7c7cc;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

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
            border: 4px solid rgba(0, 122, 255, 0.1);
            border-left-color: var(--mac-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .redirect-text { margin-top: 20px; font-weight: 600; color: #1d1d1f; opacity: 0; transition: all 0.5s ease 0.2s; }
        .redirect-overlay.active .redirect-text { opacity: 1; }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
    </style>
</head>
<body>

<div class="redirect-overlay" id="redirectOverlay">
    <div class="mac-spinner"></div>
    <div class="redirect-text" id="redirectText">Menyiapkan Ruang Kerja...</div>
</div>

<div class="container pb-5" id="mainWrapper">
    <header class="page-header">
        <p class="text-primary fw-bold mb-1">Hello, {{ auth()->user()->name }}</p>
        <h2 class="page-title">Pilih Ruang Kerja</h2>
    </header>

    <div class="cards-container" id="cardsContainer">
        @forelse($availableOffices as $index => $office)
        <div class="office-card delay-{{ $index + 1 }}">
            <span class="office-status">Aktif</span>
            <div class="icon-box">
                <i class="icofont-building"></i>
            </div>
            <h5>{{ $office->name }}</h5>
            <p>{{ $office->description ?? 'Kelola data dan operasional untuk kantor ini.' }}</p>
            <div class="office-meta">
                <span><i class="icofont-code"></i> Kode: {{ $office->code }}</span>
                <span><i class="icofont-location-pin"></i> {{ $office->address ?? 'Lokasi Terdaftar' }}</span>
            </div>
            <a href="javascript:void(0)" onclick="selectOffice({{ $office->id }})" class="btn btn-mac-primary w-100">Masuk ke Kantor</a>
        </div>
        @empty
        <div class="alert alert-light border text-center p-5 w-100">
            <h5 class="fw-bold">Akses Tidak Ditemukan</h5>
            <p class="text-muted">Anda belum memiliki akses ke kantor manapun. Hubungi Admin.</p>
        </div>
        @endforelse

        <div class="office-card add-card delay-3">
            <i class="icofont-plus-circle"></i>
            <h5 class="mt-3">Buka Kantor Baru</h5>
        </div>
    </div>
</div>

<script>
    function selectOffice(officeId) {
        const redirectOverlay = document.getElementById('redirectOverlay');
        const body = document.body;

        body.classList.add('is-redirecting');
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
                }, 1200);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Terjadi kesalahan koneksi.");
            location.reload();
        });
    }
</script>

</body>
</html>