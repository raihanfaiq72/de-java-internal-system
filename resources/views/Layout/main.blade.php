<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">


<head>


    <meta charset="utf-8" />
    <title>Dashboard | Approx - Admin & Dashboard Template</title>
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
    <link href="{{url('')}}/assets/css/custom.css" rel="stylesheet" type="text/css" />

    @stack('css')

</head>

<body>

    <!-- Top Bar Start -->
    @unless(Request::is('select-your-outlet'))
    <div class="topbar d-print-none">
        <div class="container-fluid">
            <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


                <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                    <li>
                        <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                            <i class="iconoir-menu"></i>
                        </button>
                    </li>
                    <li class="hide-phone app-search">
                        <form role="search" action="#" method="get">
                            <input type="search" name="search" class="form-control top-search mb-0"
                                placeholder="Search here...">
                            <button type="submit"><i class="iconoir-search"></i></button>
                        </form>
                    </li>
                </ul>
                <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">

                    @include('Layout._darkmode')

                    @include('Layout._notification')

                    @include('Layout._profile')
                </ul>
            </nav>
        </div>
    </div>
    @endunless
    @unless(Request::is('select-your-outlet'))
    <div class="startbar d-print-none">
        <div class="brand text-center py-3">
            <a href="{{ route('dashboard') }}" class="logo text-decoration-none d-block">

                <span class="logo-sm fw-bold text-white">
                    DJ
                </span>

                <span class="logo-lg d-block">
                    <h4 class="mb-0 fw-bold text-white">Dejava</h4>
                    <small class="text-muted">
                        Outlet {{ session('outlet_name', 'Pusat') }}
                    </small>
                </span>

            </a>
        </div>
        @include('Layout._sidebar')
    </div>
    <div class="startbar-overlay d-print-none"></div>
    @endunless
    @yield('main')

    @include('Layout._alert_helper')

    <script src="{{url('')}}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{url('')}}/assets/libs/simplebar/simplebar.min.js"></script>
    @stack('js')
    <script src="{{url('')}}/assets/js/app.js"></script>


</body>


</html>
