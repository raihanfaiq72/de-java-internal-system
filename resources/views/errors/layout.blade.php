<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('code') | Dejava</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">
</head>

<body>
<div class="container-xxl">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="row">
                <div class="col-lg-4 mx-auto">
                    <div class="card">

                        {{-- Header --}}
                        <div class="card-body p-0 bg-dark auth-header-box rounded-top">
                            <div class="text-center p-3">
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('assets/images/logo-sm.png') }}" height="50">
                                </a>
                                <h4 class="mt-3 text-white">
                                    @yield('title')
                                </h4>
                                <p class="text-muted mb-0">
                                    @yield('subtitle')
                                </p>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="card-body text-center">
                            <img src="{{ asset('assets/images/extra/error.svg') }}" height="170">

                            <h1 class="my-2">@yield('code')</h1>
                            <h5 class="text-muted mb-3">@yield('message')</h5>

                            <a href="{{ url('/') }}" class="btn btn-primary w-100">
                                Kembali ke Dashboard
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
