@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                        <h2 class="page-title">Pilih Kantor</h2>
                    </div>
                </div>
            </div>

            <!-- Cards -->
            <div class="row justify-content-center g-4">

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 office-card text-center">
                        <div class="card-body bg-soft-blue rounded-top">
                            <i class="icofont-building d-block mb-3 display-4 text-blue"></i>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold mb-3">Kantor Central</h5>

                            <div class="mt-auto">
                                <hr class="hr-dashed">
                                <a href="#" class="btn btn-dark w-100">
                                    Pilih
                                </a>
                            </div>
                            <br>
                            <a href="{{route('dashboard')}}">Lanjutkan ke dummy</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 office-card text-center">
                        <div class="card-body bg-soft-blue rounded-top">
                            <i class="icofont-building d-block mb-3 display-4 text-blue"></i>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold mb-3">Kantor II</h5>

                            <div class="mt-auto">
                                <hr class="hr-dashed">
                                <a href="#" class="btn btn-dark w-100">
                                    Pilih
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BUKA KANTOR BARU --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 office-card text-center border-dashed">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <i class="icofont-plus-circle display-3 text-primary mb-3"></i>
                            <h5 class="fw-semibold mb-2">Buka Kantor Baru</h5>
                            <p class="text-muted small mb-4">
                                Tambahkan kantor baru ke sistem
                            </p>
                            <a href="#" class="btn btn-outline-primary w-100">
                                Tambah Kantor
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <!-- end row -->

        </div>
    </div>
</div>
@endsection
