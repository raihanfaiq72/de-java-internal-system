<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Login | Dejava System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Dejava System Login" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{url('')}}/assets/images/favicon.ico">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="{{url('')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('')}}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --mac-blue: #007AFF;
            --mac-glass: rgba(255, 255, 255, 0.72);
            --mac-border: rgba(255, 255, 255, 0.45);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            /* Ganti URL jika ingin menggunakan gambar asli */
            background-image: url('assets/images/auth/bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.15); /* Overlay sangat tipis untuk menjaga vibrance */
            backdrop-filter: blur(10px); /* Efek blur pada background utama */
            z-index: -1;
        }

        .login-card {
            background: var(--mac-glass);
            backdrop-filter: blur(30px) saturate(180%);
            -webkit-backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid var(--mac-border);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .logo-box {
            margin-bottom: 30px;
        }

        .auth-logo {
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        h4 {
            color: #1d1d1f;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #424245;
            margin-left: 5px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: white;
            border-color: var(--mac-blue);
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.15);
        }

        .btn-primary {
            background-color: var(--mac-blue);
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #0063cc;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 122, 255, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .dummy-link {
            color: var(--mac-blue);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .dummy-link:hover {
            opacity: 0.7;
            text-decoration: underline;
        }

        /* Animasi masuk */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .login-card {
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>

<body>
    <div class="bg-overlay"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center align-items-center vh-100">
                
                <div class="login-card">
                    <div class="text-center logo-box">
                        <img src="assets/images/logo-sm.png" height="60" alt="logo" class="auth-logo">
                        <h4 class="mt-3 mb-1">Dejava System</h4>
                        <p class="text-muted small">Silakan masuk ke akun Anda</p>
                    </div>

                    <form action="#" method="post">
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Masukkan username">
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label" for="userpassword">Password</label>
                                <a href="#" class="dummy-link" style="font-size: 0.75rem;">Lupa?</a>
                            </div>
                            <input type="password" class="form-control" name="password"
                                id="userpassword" placeholder="Masukkan password">
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit">
                                Masuk <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>

                        <div class="text-center mt-4 pt-2">
                            <hr style="opacity: 0.1;">
                            <a href="{{url('select-your-outlet')}}" class="dummy-link">
                                <i class="fas fa-flask me-1"></i> Lanjutkan ke Dummy View
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="{{url('')}}/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>