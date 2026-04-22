<!-- DeJava App Tour Guide -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@11.0.1/dist/css/shepherd.css"/>

<style>
    .shepherd-button {
        background: #007AFF !important;
        border: none !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 8px 16px !important;
    }
    
    .shepherd-button:hover {
        background: #0056CC !important;
    }
    
    .shepherd-cancel-icon {
        color: #666 !important;
    }
    
    .shepherd-header {
        background: #f8f9fa !important;
        border-bottom: 1px solid #e9ecef !important;
        padding: 15px 20px !important;
    }
    
    .shepherd-title {
        color: #1d1d1f !important;
        font-weight: 700 !important;
        font-size: 18px !important;
    }
    
    .shepherd-text {
        color: #495057 !important;
        line-height: 1.6 !important;
        font-size: 14px !important;
    }
    
    .shepherd-content {
        border-radius: 12px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
        border: none !important;
    }

    .shepherd-step-wide {
        max-width: 600px !important;
    }
    
    .shepherd-arrow::before {
        background: #ffffff !important;
    }
    
    .shepherd-footer {
        padding: 15px 20px !important;
        border-top: 1px solid #e9ecef !important;
    }
    
    .tour-highlight {
        position: relative;
        z-index: 1001 !important;
    }
    
    .tour-highlight::after {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border: 3px solid #007AFF;
        border-radius: 8px;
        pointer-events: none;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .tour-btn-dashboard {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .tour-btn-dashboard:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
        transform: translateY(-1px);
    }
    
    .tour-btn-dashboard i {
        font-size: 16px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.0.1/dist/js/shepherd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const { Shepherd } = window;
        
        // Check if user has completed tour
        const tourCompleted = localStorage.getItem('dejava-tour-completed');
        const tourStartBtn = document.getElementById('tour-start-btn');
        
        // Show button always if it exists so user can re-run
        if (tourStartBtn) {
            tourStartBtn.style.display = 'inline-flex';
        }
        
        // Initialize tour
        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'shepherd-theme-arrows',
                scrollTo: { behavior: 'smooth', block: 'center' },
                cancelIcon: {
                    enabled: true
                }
            }
        });

        // Function to hide promo modal
        function hidePromoModal() {
            const promoModal = document.getElementById('simple-promo-modal');
            if (promoModal) {
                promoModal.style.display = 'none';
            }
        }

        // Function to show tour button
        function showTourButton() {
            if (tourStartBtn) {
                tourStartBtn.style.display = 'inline-flex';
            }
        }

        // Function to hide tour button
        function hideTourButton() {
            if (tourStartBtn) {
                tourStartBtn.style.display = 'none';
            }
        }

        // Tour Steps
        tour.addSteps([
            {
                id: 'dashboard',
                text: 'Selamat datang di Dashboard DeJava! Di sini Anda bisa melihat ringkasan bisnis Anda secara real-time.',
                title: '🏠 Dashboard',
                classes: 'shepherd-step-wide',
                attachTo: {
                    element: '.page-title-box',
                    on: 'bottom'
                },
                when: {
                    show: function() {
                        hidePromoModal(); // Hide promo modal when tour starts
                        showTourButton(); // Show tour button
                    }
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'main-menu',
                text: 'Ini adalah menu utama navigasi sistem. Semua fitur DeJava bisa diakses dari sini.',
                title: '📋 Menu Navigasi',
                attachTo: {
                    element: '.startbar-menu',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'master-data',
                text: 'Kelola data master bisnis Anda: user, karyawan, mitra, barang, stok, dan armada kendaraan.',
                title: '🗂️ Master & Akses',
                attachTo: {
                    element: 'a[href="#sidebarMaster"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'sales-menu',
                text: 'Kelola semua transaksi penjualan: invoice penjualan dan kuitansi penjualan.',
                title: '💰 Penjualan',
                attachTo: {
                    element: 'a[href="#sidebarSales"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'purchase-menu',
                text: 'Kelola semua transaksi pembelian: invoice pembelian dan kuitansi pembelian.',
                title: '🛒️ Pembelian',
                attachTo: {
                    element: 'a[href="#sidebarPurchase"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'expenses',
                text: 'Catat semua biaya operasional bisnis Anda di sini.',
                title: '💸️ Biaya Operasional',
                attachTo: {
                    element: 'a[href="/expenses"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'finance',
                text: 'Pantau kesehatan keuangan bisnis Anda secara lengkap.',
                title: '🏦️ Keuangan',
                attachTo: {
                    element: 'a[href="/finance"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'delivery',
                text: 'Kelola delivery order dan pantau driver app untuk pengiriman barang.',
                title: '🚚️ Delivery',
                attachTo: {
                    element: 'a[href="/delivery-order"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'reports',
                text: 'Akses semua laporan bisnis: invoice, stok, keuangan, profit/loss, dan lainnya.',
                title: '📊 Laporan',
                attachTo: {
                    element: 'a[href="#sidebarLaporan"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'bulk-reports',
                text: 'Fitur baru! Generate laporan massal untuk periode tertentu dengan satu klik.',
                title: '📑 Laporan Massal',
                attachTo: {
                    element: 'a[href="/bulk-reports"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'import',
                text: 'Import data masal untuk mempercepat setup data bisnis Anda.',
                title: '📥 Import Data',
                attachTo: {
                    element: 'a[href="/import"]',
                    on: 'right'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'notifications',
                text: 'Lihat semua notifikasi sistem, termasuk update laporan massal dan alert penting.',
                title: '🔔 Notifikasi',
                attachTo: {
                    element: '#notification-bell',
                    on: 'bottom'
                },
                buttons: [
                    {
                        text: 'Lanjut',
                        action: tour.next,
                        classes: 'shepherd-button'
                    }
                ]
            },
            {
                id: 'complete',
                text: 'Selamat! Anda sudah mengenal semua fitur utama DeJava. Klik tombol di bawah untuk menyelesaikan tour.',
                title: '🎉 Tour Selesai!',
                attachTo: {
                    element: 'body',
                    on: 'center'
                },
                when: {
                    show: function() {
                        hideTourButton(); // Hide tour button when tour completes
                    }
                },
                buttons: [
                    {
                        text: 'Selesai Tour',
                        action: function() {
                            localStorage.setItem('dejava-tour-completed', 'true');
                            hideTourButton();
                            tour.complete();
                        },
                        classes: 'shepherd-button'
                    }
                ]
            }
        ]);

        // Start tour when button clicked
        if (tourStartBtn) {
            tourStartBtn.addEventListener('click', function() {
                hidePromoModal(); // Hide promo when tour starts
                tour.start();
            });
        }

        // Simple global function for onclick
        window.startDeJavaTour = function() {
            console.log('Starting DeJava Tour...');
            hidePromoModal(); // Hide promo when tour starts
            tour.start();
        };

        // Auto-start tour for new users (optional)
        if (!tourCompleted && !localStorage.getItem('dejava-tour-dismissed')) {
            setTimeout(() => {
                if (confirm('👋 Selamat datang! Apakah Anda ingin tour singkat untuk mengenal fitur DeJava?')) {
                    hidePromoModal(); // Hide promo before starting tour
                    tour.start();
                } else {
                    localStorage.setItem('dejava-tour-dismissed', 'true');
                }
            }, 3000);
        }

        // Listen for tour events
        tour.on('start', function() {
            hidePromoModal();
            showTourButton();
        });

        tour.on('complete', function() {
            hideTourButton();
        });
    });
</script>
