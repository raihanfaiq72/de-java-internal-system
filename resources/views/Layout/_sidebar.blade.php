<div class="startbar-menu">
    <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
        <div class="d-flex align-items-start flex-column w-100">
            <ul class="navbar-nav mb-auto w-100">
                <li class="menu-label mt-2">
                    <span>Main</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard')}}">
                        <i class="iconoir-dashboard menu-icon"></i>
                        <span>Dashboard</span>
                        <span class="badge text-bg-info ms-auto">New</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#sidebarMaster" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarMaster">
                        <i class="iconoir-database menu-icon"></i>
                        <span>Master Data</span>
                    </a>
                    <div class="collapse" id="sidebarMaster">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('users.index')}}">
                                    <i class="iconoir-user-circle me-2"></i> User / Karyawan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('mitra')}}">
                                    <i class="iconoir-community me-2"></i> Mitra
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('barang')}}">
                                    <i class="iconoir-package me-2"></i> Barang
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-label mt-2 text-danger">
                    <span>Administrator <small class="fw-bold">(Dev Only)</small></span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAuth">
                        <i class="iconoir-lock menu-icon text-danger"></i>
                        <span class="text-danger">Akses & Kantor</span>
                    </a>
                    <div class="collapse" id="sidebarAuth">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('permissions.index')}}">
                                    <i class="iconoir-code-brackets me-2"></i> Mapping Route 
                                    <span class="badge badge-outline-danger ms-auto font-10">Dev</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('roles.index')}}">
                                    <i class="iconoir-shield-check me-2"></i> Role & Permission
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('offices.index')}}">
                                    <i class="iconoir-city me-2"></i> Daftar Kantor (Outlet)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('user_plots.index')}}">
                                    <i class="iconoir-group me-2"></i> Plotting User
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-label mt-2">
                    <span>Transaksi</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard.piutang')}}">
                        <i class="iconoir-coins menu-icon"></i>
                        <span>Dashboard Piutang</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard.sales')}}">
                        <i class="iconoir-shop menu-icon"></i>
                        <span>Dashboard Sales</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#sidePO" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidePO">
                        <i class="iconoir-shop-window menu-icon"></i>
                        <span>Purchase</span>
                    </a>
                    <div class="collapse" id="sidePO">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="transactions.html">
                                    <i class="iconoir-hand-contactless me-2"></i> Purchase Order
                                </a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link" href="transactions.html">
                                    <i class="iconoir-delivery-truck me-2"></i> Receipt PO
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#sideSO" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sideSO">
                        <i class="iconoir-cart menu-icon"></i>
                        <span>Sales Order</span>
                    </a>
                    <div class="collapse" id="sideSO">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('sales')}}">
                                    <i class="iconoir-page-search me-2"></i> Sales Order
                                </a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link" href="transactions.html">
                                    <i class="iconoir-log-out me-2"></i> Receipt SO
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            

                <li class="menu-label mt-2">
                    <span>Report</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="cards.html">
                        <i class="iconoir-reports menu-icon"></i>
                        <span>Laporan Pembelian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cards.html">
                        <i class="iconoir-graph-up menu-icon"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cards.html">
                        <i class="iconoir-archive menu-icon"></i>
                        <span>Laporan Stock</span>
                    </a>
                </li>

                <li class="menu-label mt-2">
                    <span>Setting</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('syo')}}">
                        <i class="iconoir-coins-swap menu-icon"></i>
                        <span>Ganti Kantor</span>
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <a href="#" class="nav-link text-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="iconoir-log-out menu-icon"></i>
                        <span>Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            </ul>
        </div>
    </div>
</div>