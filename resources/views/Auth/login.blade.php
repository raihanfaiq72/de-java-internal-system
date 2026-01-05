<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Login | Dejava System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{url('')}}/assets/images/favicon.ico">

    <!-- App css -->
    <link href="{{url('')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('')}}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('')}}/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom Background CSS -->
    <style>
        body {
            background: url("assets/images/auth/bg.jpg") no-repeat center center fixed;
            background-size: cover;
        }

        .bg-overlay {
            background-color: rgba(0, 0, 0, 0.55);
            min-height: 100vh;
        }

        .card {
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <div class="bg-overlay">
        <div class="container-xxl">
            <div class="row vh-100 d-flex justify-content-center">
                <div class="col-12 align-self-center">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mx-auto">
                                <div class="card">
                                    <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                        <div class="text-center p-3">
                                            <a href="index.html" class="logo logo-admin">
                                                <img src="assets/images/logo-sm.png" height="50" alt="logo"
                                                    class="auth-logo">
                                            </a>
                                            <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">
                                                Selamat Datang Kembali
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="card-body pt-0">
                                        <form class="my-4" action="#" method="post">
                                            <div class="form-group mb-3">
                                                <label class="form-label" for="username">Username</label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    placeholder="Enter username">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label" for="userpassword">Password</label>
                                                <input type="password" class="form-control" name="password"
                                                    id="userpassword" placeholder="Enter password">
                                            </div>
                                            <div class="form-group mb-0">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary" type="submit">
                                                        Log In <i class="fas fa-sign-in-alt ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <br>
                                            <a href="{{url('select-your-outlet')}}">Lanjutkan ke dummy</a>
                                        </form>
                                    </div>
                                    <!-- end card-body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
