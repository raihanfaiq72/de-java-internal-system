<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>Login | Djava System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
            background-image: url('{{url('assets/images/auth/bg.jpg')}}');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            z-index: -1;
        }

        .login-card {
            background: var(--mac-glass);
            backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid var(--mac-border);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .form-control {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 12px 15px;
        }

        .btn-primary {
            background-color: var(--mac-blue);
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
        }

        .alert-mac {
            background: rgba(255, 59, 48, 0.1);
            border: 1px solid rgba(255, 59, 48, 0.2);
            color: #ff3b30;
            border-radius: 12px;
            font-size: 0.85rem;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center align-items-center vh-100">
                <div class="login-card">
                    <div class="text-center mb-4">
                        <img src="{{url('assets/images/logo-sm.png')}}" height="60" alt="logo">
                        <h4 class="mt-3 mb-1">Djava System</h4>
                        <p class="text-muted small">Silakan masuk ke akun Anda</p>
                    </div>

                    {{-- Menampilkan Error Login --}}
                    @if($errors->any())
                        <div class="alert alert-mac mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.proses') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label ms-1 small fw-bold" for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label ms-1 small fw-bold" for="userpassword">Password</label>
                            </div>
                            <input type="password" class="form-control" name="password" 
                                id="userpassword" placeholder="Masukkan password" required>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary shadow-sm" type="submit">
                                Masuk <i class="fas fa-arrow-right ms-2 small"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>