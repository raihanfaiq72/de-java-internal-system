@extends('Layout.main')

@push('css')
<link href="{{url('')}}/assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
@endpush

@section('main')
<div class="page-wrapper">

    <!-- Page Content-->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                        <h4 class="page-title">Users</h4>
                        <div class="">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Approx</a>
                                </li>
                                <!--end nav-item-->
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                    <!--end page-title-box-->
                </div>
                <!--end col-->
            </div>
            <!--end row-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Users Details</h4>
                                </div>
                                <!--end col-->
                                <div class="col-auto">
                                    <button class="btn bg-primary text-white" 
                                        data-ui-open
                                        data-content="user-form"
                                        data-action="add"
                                    ><i class="fas fa-plus me-1"></i> Add User</button>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-header-->
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table mb-0" id="datatable_1">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile No</th>
                                            <th>Registered On</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/users/avatar-1.jpg"
                                                        class="me-2 thumb-md align-self-center rounded" alt="...">
                                                    <div class="flex-grow-1 text-truncate">
                                                        <h6 class="m-0">Unity Pugh</h6>
                                                        <p class="fs-12 text-muted mb-0">USA</p>
                                                    </div>
                                                    <!--end media body-->
                                                </div>
                                            </td>
                                            <td><a href="#"
                                                    class="text-body text-decoration-underline">dummy@gmail.com</a></td>
                                            <td>+1 234 567 890</td>
                                            <td>22 August 2024</td>
                                            <td><span class="badge rounded text-success bg-success-subtle">Active</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                data-ui-open
                                                data-content="user-form"
                                                data-action="edit"
                                                data-data='{"name":"Unity","email":"dummy@gmail.com","date":"22 August 2024","phone":"+1 234"}'
                                                >
                                                    <i class="las la-pen text-secondary fs-18"></i>
                                                </a>

                                                <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->

        </div><!-- container -->
        <!--end footer-->
    </div>
    <!-- end page content -->
</div>
<!-- end page-wrapper -->

<script type="text/template" id="user-form">
    @include('Users.modal.user-form')
</script>

@include('Ui.modal')

@endsection

@push('js')
<script src="{{url('')}}/assets/libs/simple-datatables/umd/simple-datatables.js"></script>
<script src="{{url('')}}/assets/js/pages/datatable.init.js"></script>
<script src="{{ url('') }}/assets/js/ui-modal.js"></script>
@endpush
