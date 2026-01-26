<div class="startbar-menu">
    <div class="startbar-collapse" id="startbarCollapse" style="flex: 1; overflow-y: auto; padding: 15px;">
        <div class="d-flex align-items-start flex-column w-100">
            <ul class="navbar-nav mb-auto w-100">
                <li class="menu-label mt-2">
                    <span>Main</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
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
                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <i class="iconoir-user-circle me-2"></i> User / Karyawan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('mitra') }}">
                                    <i class="iconoir-community me-2"></i> Mitra
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('barang') }}">
                                    <i class="iconoir-package me-2"></i> Barang
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('stok') }}">
                                    <i class="iconoir-archive me-2"></i> Stok
                                </a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="iconoir-money-square me-2"></i> Pajak
                                </a>
                            </li> -->
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
                                <a class="nav-link" href="{{ route('permissions.index') }}">
                                    <i class="iconoir-code-brackets me-2"></i> Mapping Route
                                    <span class="badge badge-outline-danger ms-auto font-10">Dev</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('roles.index') }}">
                                    <i class="iconoir-shield-check me-2"></i> Role & Permission
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('offices.index') }}">
                                    <i class="iconoir-city me-2"></i> Daftar Kantor (Outlet)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user_plots.index') }}">
                                    <i class="iconoir-group me-2"></i> Plotting User
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-label">
                    <span>Transaksi</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sales') }}">
                        <i class="iconoir-coins menu-icon"></i>
                        <span>Penjualan</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('purchase') }}">
                        <i class="iconoir-shop menu-icon"></i>
                        <span>Pembelian</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#sidePO" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="sidePO">
                        <i class="iconoir-shop-window menu-icon"></i>
                        <span>Finance</span>
                    </a>
                    <div class="collapse" id="sidePO">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('sales.receipt') }}">
                                    <i class="iconoir-hand-contactless me-2"></i> Kuitansi Penjualan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('purchase.receipt') }}">
                                    <i class="iconoir-delivery-truck me-2"></i> Kuitansi Pembelian
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-label mt-2">
                    <span>Report</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.sales') }}">
                        <i class="iconoir-graph-up menu-icon"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.purchase') }}">
                        <i class="iconoir-reports menu-icon"></i>
                        <span>Laporan Pembelian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.stock') }}">
                        <i class="iconoir-archive menu-icon"></i>
                        <span>Laporan Stock</span>
                    </a>
                </li>

                <li class="menu-label mt-3">
                    <small class="text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Laporan Keuangan</small>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.balance-sheet') }}">
                        <i class="iconoir-table-rows menu-icon"></i>
                        <span>Neraca Keuangan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.profit-loss') }}">
                        <i class="iconoir-calculator menu-icon"></i>
                        <span>Laba Rugi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.ar-aging') }}">
                        <i class="iconoir-calendar menu-icon"></i>
                        <span>Umur Piutang (AR Aging)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report.general-ledger') }}">
                        <i class="iconoir-book menu-icon"></i>
                        <span>Buku Besar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="iconoir-info-circle menu-icon"></i>
                        <span>Simulasi Harga Average</span>
                    </a>
                </li>

                <li class="menu-label mt-2">
                    <span>Setting</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('syo') }}">
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
