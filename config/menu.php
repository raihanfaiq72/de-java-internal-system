<?php

return [
    [
        'type' => 'label',
        'title' => 'Main',
    ],
    [
        'type' => 'item',
        'title' => 'Dashboard',
        'route' => 'dashboard',
        'icon' => 'iconoir-dashboard',
        'badge' => [
            'text' => 'New',
            'class' => 'text-bg-info ms-auto'
        ],
    ],
    [
        'type' => 'item',
        'title' => 'Master Data',
        'icon' => 'iconoir-database',
        'id' => 'sidebarMaster',
        'submenu' => [
            [
                'title' => 'User / Karyawan',
                'route' => 'users.index',
                'icon' => 'iconoir-user-circle',
            ],
            [
                'title' => 'Mitra',
                'route' => 'mitra',
                'icon' => 'iconoir-community',
            ],
            [
                'title' => 'Barang',
                'route' => 'barang',
                'icon' => 'iconoir-package',
            ],
            [
                'title' => 'Stok',
                'route' => 'stok',
                'icon' => 'iconoir-archive',
            ],
            [
                'title' => 'Armada',
                'route' => 'fleets.index',
                'icon' => 'iconoir-truck',
            ],
        ],
    ],
    [
        'type' => 'label',
        'title' => 'Administrator <small class="fw-bold">(Dev Only)</small>',
        'class' => 'text-danger',
        'is_html' => true,
    ],
    [
        'type' => 'item',
        'title' => 'Akses & Kantor',
        'icon' => 'iconoir-lock',
        'id' => 'sidebarAuth',
        'class' => 'text-danger',
        'submenu' => [
            [
                'title' => 'Mapping Route',
                'route' => 'permissions.index',
                'icon' => 'iconoir-code-brackets',
                'badge' => [
                    'text' => 'Dev',
                    'class' => 'badge-outline-danger ms-auto font-10'
                ],
            ],
            [
                'title' => 'Role & Permission',
                'route' => 'roles.index',
                'icon' => 'iconoir-shield-check',
            ],
            [
                'title' => 'Daftar Kantor (Outlet)',
                'route' => 'offices.index',
                'icon' => 'iconoir-city',
            ],
            [
                'title' => 'Plotting User',
                'route' => 'user_plots.index',
                'icon' => 'iconoir-group',
            ],
        ],
    ],
    [
        'type' => 'label',
        'title' => 'Transaksi',
    ],
    [
        'type' => 'item',
        'title' => 'Penjualan',
        'icon' => 'iconoir-coins',
        'id' => 'sidebarSales',
        'submenu' => [
            [
                'title' => 'Invoice Penjualan',
                'route' => 'sales',
                'icon' => 'iconoir-coins',
            ],
            [
                'title' => 'Kuitansi Penjualan',
                'route' => 'sales.receipt',
                'icon' => 'iconoir-hand-contactless',
            ],
        ]
    ],
    [
        'type' => 'item',
        'title' => 'Pembelian',
        'icon' => 'iconoir-shop',
        'id' => 'sidebarPurchase',
        'submenu' => [
            [
                'title' => 'Invoice Pembelian',
                'route' => 'purchase',
                'icon' => 'iconoir-shop',
            ],
            [
                'title' => 'Kuitansi Pembelian',
                'route' => 'purchase.receipt',
                'icon' => 'iconoir-delivery-truck',
            ],
        ]
    ],
    [
        'type' => 'item',
        'title' => 'Biaya Operasional',
        'route' => 'expenses.index',
        'icon' => 'iconoir-wallet',
    ],
    [
        'type' => 'item',
        'title' => 'Keuangan',
        'icon' => 'iconoir-coins',
        'route' => 'finance.index'
    ],
    [
        'type' => 'item',
        'title' => 'Delivery Order',
        'route' => 'delivery-order.index',
        'icon' => 'iconoir-delivery-truck',
    ],
    [
        'type' => 'label',
        'title' => 'Report',
    ],
    [
        'type' => 'item',
        'title' => 'Laporan',
        'icon' => 'iconoir-graph-up',
        'id' => 'sidebarLaporan',
        'submenu' => [
            [
                'title' => 'Laporan Penjualan',
                'route' => 'report.sales',
                'icon' => 'iconoir-graph-up',
            ],
            [
                'title' => 'Laporan Pembelian',
                'route' => 'report.purchase',
                'icon' => 'iconoir-reports',
            ],
            [
                'title' => 'Laporan Stock',
                'route' => 'report.stock',
                'icon' => 'iconoir-archive',
            ],
            [

                'title' => 'Neraca Keuangan',
                'route' => 'report.balance-sheet',
                'icon' => 'iconoir-table-rows',
            ],
            [

                'title' => 'Manajemen COA',
                'route' => 'report.coa-management',
                'icon' => 'iconoir-list-select',
            ],
            [

                'title' => 'Laba Rugi',
                'route' => 'report.profit-loss',
                'icon' => 'iconoir-calculator',
            ],
            [

                'title' => 'Umur Piutang (AR Aging)',
                'route' => 'report.ar-aging',
                'icon' => 'iconoir-calendar',
            ],
            [

                'title' => 'Buku Besar',
                'route' => 'report.general-ledger',
                'icon' => 'iconoir-book',
            ],
            [

                'title' => 'Simulasi Harga Average',
                'route' => '#',
                'icon' => 'iconoir-info-circle',
            ],
        ]

    ],

    [
        'type' => 'label',
        'title' => 'Setting',
    ],
    [
        'type' => 'item',
        'title' => 'Ganti Kantor',
        'route' => 'syo',
        'icon' => 'iconoir-coins-swap',
    ],
];
