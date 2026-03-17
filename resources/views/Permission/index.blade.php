@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    .page-wrapper { font-family: 'Inter', sans-serif; background: #f8fafc; }
    .f-mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; }
    
    .permission-card { transition: all 0.2s; border: 1px solid #e2e8f0; }
    .permission-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    
    .table-custom thead th { background: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
    .table-custom tbody td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f8fafc; color: #334155; vertical-align: middle; }
    
    .form-control-minimal { border: 1px dashed #cbd5e1; background: #fafafa; font-size: 13px; padding: 6px 12px; border-radius: 6px; }
    .form-control-minimal:focus { border: 1px solid #3b82f6; background: #fff; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); outline: none; }
    
    .badge-module { font-size: 11px; padding: 5px 10px; border-radius: 6px; font-weight: 600; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
</style>
@endpush

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Admin: Permissions</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Permissions</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button onclick="location.reload()" class="btn btn-white border fw-bold px-3 shadow-sm text-dark">
                    <i class="fa fa-sync me-1 text-primary"></i> Refresh Data
                </button>
            </div>

            <div class="row g-4">
                @foreach($prefixes as $prefix)
                <div class="col-12">
                    <div class="card permission-card shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between" 
                             style="cursor: pointer;" 
                             data-bs-toggle="collapse" 
                             data-bs-target="#collapse-{{ $prefix->id }}" 
                             aria-expanded="true">
                            <div class="d-flex align-items-center">
                                <span class="badge-module me-3 text-uppercase">MODUL: {{ $prefix->name }}</span>
                                <span class="text-muted small">Mengatur {{ count($prefix->permissions) }} rute akses.</span>
                            </div>
                            <i class="fa fa-chevron-down text-muted small"></i>
                        </div>
                        <div id="collapse-{{ $prefix->id }}" class="collapse show">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th width="40%">System Route Name</th>
                                        <th width="45%">Display Label (Nama Tampilan)</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prefix->permissions as $p)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-code text-muted me-2 small opacity-50"></i>
                                                <code class="text-danger f-mono bg-light px-2 py-1 rounded small">{{ $p->name }}</code>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" id="input-{{ $p->id }}" 
                                                    class="form-control form-control-minimal text-dark fw-medium" 
                                                    value="{{ $p->description }}" 
                                                    placeholder="Berikan nama yang mudah dimengerti...">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" 
                                                        onclick="suggestLabel({{ $p->id }}, '{{ $p->name }}')"
                                                        title="Suggest nama berdasarkan route">
                                                    <i class="fa fa-magic"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button onclick="updateLabel({{ $p->id }}, event)" class="btn btn-sm btn-light border text-primary fw-bold shadow-sm px-3 hover-shadow">
                                                <i class="fa fa-save me-1"></i> Simpan
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
