@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        .page-wrapper {
            background: #ffffff;
        }

        .permission-module-card {
            border: 1px solid var(--dr-line);
            background: #fff;
            border-radius: var(--dr-radius);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .permission-module-card:hover {
            box-shadow: var(--dr-shadow-lg);
            border-color: var(--dr-line-strong);
        }

        .module-header {
            background: #ffffff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-bottom: 1px solid var(--dr-line);
        }

        .module-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--dr-text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .system-route {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            background: var(--dr-panel-soft);
            color: #ef4444;
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <span>Main Menu</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-key"></i></span>
                        <strong>System Permissions</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">System Permissions</h1>
                            <p class="dr-subtitle">Kelola label tampilan untuk rute akses sistem agar lebih mudah dipahami admin.</p>
                        </div>
                        <div class="dr-actions">
                            <button onclick="location.reload()" class="dr-btn dr-btn-outline">
                                <i class="iconoir-refresh"></i> Refresh Data
                            </button>
                        </div>
                    </div>

                    <div class="row g-4">
                        @foreach($prefixes as $prefix)
                        <div class="col-12">
                            <div class="permission-module-card">
                                <div class="module-header" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $prefix->id }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="dr-badge bg-soft-primary text-primary">MODUL: {{ $prefix->name }}</div>
                                        <span class="text-muted small fw-medium">Mengatur {{ count($prefix->permissions) }} rute akses</span>
                                    </div>
                                    <i class="iconoir-nav-arrow-down text-muted"></i>
                                </div>
                                <div id="collapse-{{ $prefix->id }}" class="collapse show">
                                    <div class="table-responsive">
                                        <table class="dr-table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="35%">System Route Name</th>
                                                    <th width="50%">Display Label (Nama Tampilan)</th>
                                                    <th width="15%" class="text-end pe-4">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($prefix->permissions as $p)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="iconoir-code text-muted small"></i>
                                                            <span class="system-route">{{ $p->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" id="input-{{ $p->id }}" 
                                                                class="dr-input" 
                                                                value="{{ $p->description }}" 
                                                                placeholder="Berikan nama yang mudah dimengerti...">
                                                            <button class="dr-btn dr-btn-outline px-2" type="button" 
                                                                    onclick="suggestLabel({{ $p->id }}, '{{ $p->name }}')"
                                                                    title="Suggest nama">
                                                                <i class="iconoir-magic-wand"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <button onclick="updateLabel({{ $p->id }}, event)" class="dr-btn dr-btn-primary py-1 px-3" style="font-size: 12px;">
                                                            <i class="iconoir-check-circle me-1"></i> Simpan
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function suggestLabel(id, routeName) {
        const input = document.getElementById('input-' + id);
        const suggestedLabel = generateDisplayName(routeName);
        
        // Animate the suggestion
        input.style.backgroundColor = '#e8f5e8';
        input.value = suggestedLabel;
        
        // Highlight the input briefly
        setTimeout(() => {
            input.style.backgroundColor = '#fafafa';
        }, 1000);
        
        // Focus on the input for easy editing
        input.focus();
        input.select();
    }
    
    function generateDisplayName(routeName) {
        // Dictionary for common route patterns
        const patterns = {
            // User Management
            'users.index': 'Daftar Pengguna',
            'users.create': 'Tambah Pengguna Baru',
            'users.store': 'Simpan Pengguna Baru',
            'users.show': 'Detail Pengguna',
            'users.edit': 'Edit Pengguna',
            'users.update': 'Update Pengguna',
            'users.destroy': 'Hapus Pengguna',
            
            // Role Management
            'roles.index': 'Daftar Role',
            'roles.create': 'Tambah Role Baru',
            'roles.store': 'Simpan Role Baru',
            'roles.show': 'Detail Role',
            'roles.edit': 'Edit Role',
            'roles.update': 'Update Role',
            'roles.destroy': 'Hapus Role',
            
            // Office Management
            'offices.index': 'Daftar Kantor',
            'offices.create': 'Tambah Kantor Baru',
            'offices.store': 'Simpan Kantor Baru',
            'offices.show': 'Detail Kantor',
            'offices.edit': 'Edit Kantor',
            'offices.update': 'Update Kantor',
            'offices.destroy': 'Hapus Kantor',
            
            // Employee Management
            'employees.index': 'Daftar Karyawan',
            'employees.create': 'Tambah Karyawan Baru',
            'employees.store': 'Simpan Karyawan Baru',
            'employees.show': 'Detail Karyawan',
            'employees.edit': 'Edit Karyawan',
            'employees.update': 'Update Karyawan',
            'employees.destroy': 'Hapus Karyawan',
            
            // Partner/Mitra Management
            'mitra.index': 'Daftar Mitra',
            'mitra.create': 'Tambah Mitra Baru',
            'mitra.store': 'Simpan Mitra Baru',
            'mitra.show': 'Detail Mitra',
            'mitra.edit': 'Edit Mitra',
            'mitra.update': 'Update Mitra',
            'mitra.destroy': 'Hapus Mitra',
            
            // Product Management
            'barang.index': 'Daftar Barang',
            'barang.create': 'Tambah Barang Baru',
            'barang.store': 'Simpan Barang Baru',
            'barang.show': 'Detail Barang',
            'barang.edit': 'Edit Barang',
            'barang.update': 'Update Barang',
            'barang.destroy': 'Hapus Barang',
            
            // Stock Management
            'stok.index': 'Daftar Stok',
            'stok.create': 'Tambah Stok Baru',
            'stok.store': 'Simpan Stok Baru',
            'stok.show': 'Detail Stok',
            'stok.edit': 'Edit Stok',
            'stok.update': 'Update Stok',
            'stok.destroy': 'Hapus Stok',
            
            // Sales Management
            'sales.index': 'Daftar Penjualan',
            'sales.create': 'Tambah Penjualan Baru',
            'sales.store': 'Simpan Penjualan Baru',
            'sales.show': 'Detail Penjualan',
            'sales.edit': 'Edit Penjualan',
            'sales.update': 'Update Penjualan',
            'sales.destroy': 'Hapus Penjualan',
            
            // Purchase Management
            'purchase.index': 'Daftar Pembelian',
            'purchase.create': 'Tambah Pembelian Baru',
            'purchase.store': 'Simpan Pembelian Baru',
            'purchase.show': 'Detail Pembelian',
            'purchase.edit': 'Edit Pembelian',
            'purchase.update': 'Update Pembelian',
            'purchase.destroy': 'Hapus Pembelian',
            
            // Reports
            'report.invoice': 'Laporan Invoice',
            'report.sales': 'Laporan Penjualan',
            'report.purchase': 'Laporan Pembelian',
            'report.stock': 'Laporan Stok',
            'report.supplier-invoices': 'Laporan Tagihan Supplier',
            'report.ar-aging': 'Laporan Umur Piutang',
            'report.balance-sheet': 'Neraca Keuangan',
            'report.profit-loss': 'Laporan Laba Rugi',
            'report.general-ledger': 'Buku Besar',
            'report.cash-book': 'Buku Kas',
            'report.coa-management': 'Manajemen COA',
            
            // Import
            'import.index': 'Halaman Import Data',
            'import.stock': 'Import Stok',
            'import.mitra': 'Import Mitra',
            'import.sales': 'Import Penjualan',
            'import.purchase': 'Import Pembelian',
            'import.employee': 'Import Karyawan',
            'import.template': 'Download Template Import',
            
            // Dashboard
            'dashboard': 'Dashboard Utama',
            
            // Authentication
            'login': 'Halaman Login',
            'logout': 'Logout',
            'register': 'Halaman Registrasi',
            'password.request': 'Request Password',
            'password.email': 'Kirim Password Reset',
            'password.reset': 'Reset Password',
            
            // Settings
            'settings.index': 'Pengaturan Umum',
            'settings.profile': 'Pengaturan Profil',
            'settings.security': 'Pengaturan Keamanan',
            
            // Fleet Management
            'fleets.index': 'Daftar Armada',
            'fleets.create': 'Tambah Armada Baru',
            'fleets.store': 'Simpan Armada Baru',
            'fleets.show': 'Detail Armada',
            'fleets.edit': 'Edit Armada',
            'fleets.update': 'Update Armada',
            'fleets.destroy': 'Hapus Armada',
            
            // Expenses
            'expenses.index': 'Daftar Biaya',
            'expenses.create': 'Tambah Biaya Baru',
            'expenses.store': 'Simpan Biaya Baru',
            'expenses.show': 'Detail Biaya',
            'expenses.edit': 'Edit Biaya',
            'expenses.update': 'Update Biaya',
            'expenses.destroy': 'Hapus Biaya',
            
            // Delivery Order
            'delivery-order.index': 'Daftar Delivery Order',
            'delivery-order.create': 'Tambah Delivery Order Baru',
            'delivery-order.store': 'Simpan Delivery Order Baru',
            'delivery-order.show': 'Detail Delivery Order',
            'delivery-order.edit': 'Edit Delivery Order',
            'delivery-order.update': 'Update Delivery Order',
            'delivery-order.destroy': 'Hapus Delivery Order',
            'delivery-order.print': 'Cetak Delivery Order',
            'delivery-order.track': 'Tracking Delivery Order',
        };
        
        // Check if we have a predefined pattern
        if (patterns[routeName]) {
            return patterns[routeName];
        }
        
        // Generate from pattern if not found
        const parts = routeName.split('.');
        const module = parts[0];
        const action = parts[1] || 'index';
        
        // Module name mapping
        const moduleMap = {
            'users': 'Pengguna',
            'roles': 'Role',
            'permissions': 'Izin Akses',
            'offices': 'Kantor',
            'employees': 'Karyawan',
            'mitra': 'Mitra',
            'barang': 'Barang',
            'stok': 'Stok',
            'sales': 'Penjualan',
            'purchase': 'Pembelian',
            'report': 'Laporan',
            'import': 'Import',
            'expenses': 'Biaya',
            'fleets': 'Armada',
            'delivery-order': 'Delivery Order',
            'settings': 'Pengaturan',
            'dashboard': 'Dashboard',
        };
        
        // Action name mapping
        const actionMap = {
            'index': 'Daftar',
            'create': 'Tambah',
            'store': 'Simpan',
            'show': 'Detail',
            'edit': 'Edit',
            'update': 'Update',
            'destroy': 'Hapus',
            'delete': 'Hapus',
            'print': 'Cetak',
            'export': 'Export',
            'import': 'Import',
            'track': 'Tracking',
        };
        
        const moduleName = moduleMap[module] || module.charAt(0).toUpperCase() + module.slice(1);
        const actionName = actionMap[action] || action.charAt(0).toUpperCase() + action.slice(1);
        
        // Combine module and action
        if (action === 'index') {
            return `Daftar ${moduleName}`;
        } else if (action === 'create' || action === 'store') {
            return `Tambah ${moduleName} Baru`;
        } else if (action === 'show') {
            return `Detail ${moduleName}`;
        } else if (action === 'edit' || action === 'update') {
            return `Edit ${moduleName}`;
        } else if (action === 'destroy' || action === 'delete') {
            return `Hapus ${moduleName}`;
        } else {
            return `${actionName} ${moduleName}`;
        }
    }

    function updateLabel(id, event) {
        const description = document.getElementById('input-' + id).value;
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        fetch(`/admin/permissions/${id}`, { // Using Route::put('/permissions/{id}')
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ description: description })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                btn.classList.add('btn-success', 'text-white');
                btn.classList.remove('btn-light', 'text-primary', 'border');
                btn.innerHTML = '<i class="fa fa-check"></i> Saved';
                
                setTimeout(() => {
                    btn.classList.remove('btn-success', 'text-white');
                    btn.classList.add('btn-light', 'text-primary', 'border');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 1500);
            } else {
                alert('Gagal: ' + data.message);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>
@endpush
