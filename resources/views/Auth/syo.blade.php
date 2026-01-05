<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Container & Grid */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Office Card Premium Style */
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

        /* Custom Button */
        .btn-mac-primary {
            background-color: var(--mac-blue);
            color: white;
            border-radius: 14px;
            padding: 10px 24px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }
        .btn-mac-primary:hover {
            background-color: #0063CC;
            box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            color: white;
        }

        /* Add Card Style */
        .add-card {
            border: 2px dashed #c7c7cc;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .add-card:hover {
            border-color: var(--mac-blue);
            background: rgba(0, 122, 255, 0.02);
        }
        .add-card i { font-size: 3rem; color: #c7c7cc; transition: color 0.3s; }
        .add-card:hover i { color: var(--mac-blue); }

        /* Fullscreen Form Overlay */
        .full-form-overlay {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100vh;
            background: rgba(245, 245, 247, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            transition: right 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            justify-content: flex-end;
        }

        .full-form-overlay.show { right: 0; }

        .form-content {
            width: 100%;
            max-width: 600px;
            background: white;
            height: 100%;
            padding: 60px;
            box-shadow: -10px 0 50px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .form-header h2 { font-weight: 700; margin-bottom: 10px; }
        .form-header p { color: #86868b; margin-bottom: 40px; }

        .form-label { font-weight: 600; font-size: 0.9rem; color: #1d1d1f; }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #d2d2d7;
            background-color: #f5f5f7;
            margin-bottom: 20px;
        }
        .form-control:focus {
            background-color: white;
            border-color: var(--mac-blue);
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
        }

        .overlay-shift {
            transform: translateX(-200px);
            filter: blur(5px);
            pointer-events: none;
        }

        /* Delay for animation */
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
    </style>
</head>
<body>

<div class="container pb-5" id="mainWrapper">
    <header class="page-header">
        <p class="text-primary fw-bold mb-1">Manajemen Aset</p>
        <h2 class="page-title">Pilih Kantor</h2>
    </header>

    <div class="cards-container" id="cardsContainer">
        <div class="office-card delay-1">
            <span class="office-status">Aktif</span>
            <div class="icon-box">
                <i class="icofont-building"></i>
            </div>
            <h5>Kantor Central</h5>
            <p>Pusat operasional dan data manajemen utama perusahaan.</p>
            <div class="office-meta">
                <span><i class="icofont-location-pin"></i> Jl. Sudirman No. 12, Jakarta</span>
                <span><i class="icofont-users-alt-3"></i> 124 Karyawan</span>
                <span><i class="icofont-phone"></i> (021) 555-0192</span>
            </div>
            <a href="{{url('dashboard')}}" class="btn btn-mac-primary w-100">Masuk ke Kantor</a>
        </div>

        <div class="office-card delay-2">
            <span class="office-status" style="background:#fff3e0; color:#ef6c00;">Cabang</span>
            <div class="icon-box" style="background: linear-gradient(135deg, #FF9500, #FFCC00);">
                <i class="icofont-delivery-house"></i>
            </div>
            <h5>Kantor Regional II</h5>
            <p>Fokus pada distribusi logistik dan layanan area Barat.</p>
            <div class="office-meta">
                <span><i class="icofont-location-pin"></i> Jl. Gatot Subroto No. 45, Bandung</span>
                <span><i class="icofont-users-alt-3"></i> 45 Karyawan</span>
                <span><i class="icofont-phone"></i> (022) 444-0122</span>
            </div>
            <a href="#" class="btn btn-mac-primary w-100">Masuk ke Kantor</a>
        </div>

        <div class="office-card add-card delay-3" id="addBtn">
            <i class="icofont-plus-circle"></i>
            <h5 class="mt-3">Buka Kantor Baru</h5>
            <p class="text-center">Ekspansi bisnis Anda dengan menambah cabang baru.</p>
        </div>
    </div>
</div>

<div class="full-form-overlay" id="fullForm">
    <div class="form-content">
        <div class="form-header">
            <h2>Tambah Kantor Baru</h2>
            <p>Silakan lengkapi data kantor baru di bawah ini untuk didaftarkan ke sistem.</p>
        </div>
        
        <form id="officeForm" onsubmit="event.preventDefault();">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label">Nama Kantor Cabang</label>
                    <input type="text" class="form-control" placeholder="Contoh: Kantor Cabang Surabaya">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Kategori Kantor</label>
                    <select class="form-select">
                        <option value="pusat">Kantor Pusat</option>
                        <option value="cabang">Kantor Cabang</option>
                        <option value="gudang">Gudang Logistik</option>
                        <option value="virtual">Virtual Office</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" placeholder="+62 ...">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Email Kantor</label>
                    <input type="email" class="form-control" placeholder="office@company.com">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" rows="3" placeholder="Nama jalan, gedung, lantai..."></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <select class="form-select">
                        <option>DKI Jakarta</option>
                        <option>Jawa Barat</option>
                        <option>Jawa Timur</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" placeholder="Masukan Kota">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Manager Kantor / Penanggung Jawab</label>
                    <input type="text" class="form-control" placeholder="Nama lengkap">
                </div>

                <div class="col-md-12 mt-4 d-flex gap-3">
                    <button class="btn btn-mac-primary flex-grow-1 py-3" onclick="alert('Data Berhasil Disimpan!')">Simpan Data Kantor</button>
                    <button type="button" class="btn btn-light px-4 border" id="cancelBtn">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const addBtn = document.getElementById('addBtn');
    const fullForm = document.getElementById('fullForm');
    const cardsContainer = document.getElementById('cardsContainer');
    const mainWrapper = document.getElementById('mainWrapper');
    const cancelBtn = document.getElementById('cancelBtn');

    addBtn.addEventListener('click', () => {
        cardsContainer.classList.add('overlay-shift');
        mainWrapper.style.opacity = "0.5";
        fullForm.classList.add('show');
    });

    cancelBtn.addEventListener('click', () => {
        cardsContainer.classList.remove('overlay-shift');
        mainWrapper.style.opacity = "1";
        fullForm.classList.remove('show');
    });

    fullForm.addEventListener('click', (e) => {
        if (e.target === fullForm) {
            cancelBtn.click();
        }
    });
</script>

</body>
</html>