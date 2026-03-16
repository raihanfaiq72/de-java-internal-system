<li class="dropdown topbar-item">
    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19" id="notification-bell">
        <i class="iconoir-bell"></i>
        <span class="alert-badge" id="notification-badge" style="display: none;"></span>
    </a>
    <div class="dropdown-menu stop dropdown-menu-end dropdown-lg py-0">

        <h5 class="dropdown-item-text m-0 py-3 d-flex justify-content-between align-items-center">
            Notifications 
            {{-- <a href="#" class="badge text-body-tertiary badge-pill">
                <i class="iconoir-plus-circle fs-4"></i>
            </a> --}}
        </h5>
        
        <div class="ms-0" style="max-height:230px; overflow-y: auto;" data-simplebar>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="All" role="tabpanel" aria-labelledby="all-tab"
                    tabindex="0">
                    <div id="notification-list">
                        {{-- Notifications will be injected here --}}
                        <div class="text-center p-3 text-muted" id="no-notifications">
                            <small>Tidak ada notifikasi baru</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dropdown-item text-center py-2">
            <a href="{{ route('notifications.index') }}" class="text-primary text-decoration-underline small">Lihat Semua</a>
        </div>
    </div>
</li>

<style>
    /* Toast Animation Styles */
    .notification-toast {
        position: fixed;
        top: 20px;
        right: -350px; /* Start off-screen */
        width: 320px;
        background: white;
        border-left: 4px solid var(--apple-blue);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        z-index: 9999;
        border-radius: 8px;
        padding: 15px;
        transition: right 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: start;
        opacity: 0;
    }

    .notification-toast.show {
        right: 20px;
        opacity: 1;
    }

    .notification-toast.fly-to-bell {
        transition: all 0.8s ease-in-out;
        top: 15px; /* Adjust to match bell position */
        right: 200px; /* Adjust based on layout */
        transform: scale(0.1);
        opacity: 0;
    }
</style>

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        const userId = "{{ auth()->id() }}";
        if (!userId) return;

        console.log('Listening for notifications for user:', userId);

        window.Echo.private('App.Models.User.' + userId)
            .notification((notification) => {
                console.log('Notification received:', notification);
                handleNotification(notification);
            });

        // Load existing notifications and update badge
        loadNotifications();
        updateBadgeFromServer();

        // Load notifications when dropdown is opened
        const notificationBell = document.getElementById('notification-bell');
        if (notificationBell) {
            notificationBell.addEventListener('click', function() {
                // Load fresh notifications when dropdown opens
                loadNotifications();
            });
        }
    });

    function handleNotification(notification) {
        // 1. Play sound (optional)
        // const audio = new Audio('/assets/sounds/notification.mp3');
        // audio.play();

        // 2. Show Toast
        showToast(notification);

        // 3. Update Dropdown
        addNotificationToDropdown(notification);

        // 4. Update Badge
        updateBadgeCount();
    }

    function showToast(notification) {
        // Remove existing toast if any
        const existingToast = document.querySelector('.notification-toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = 'notification-toast';
        
        let iconClass = 'iconoir-info-circle';
        let bgClass = 'bg-primary-subtle text-primary';
        
        if (notification.type === 'success') { iconClass = 'iconoir-check-circle'; bgClass = 'bg-success-subtle text-success'; }
        if (notification.type === 'warning') { iconClass = 'iconoir-warning-circle'; bgClass = 'bg-warning-subtle text-warning'; }
        if (notification.type === 'error') { iconClass = 'iconoir-x-circle'; bgClass = 'bg-danger-subtle text-danger'; }

        // Special icons for bulk reports
        if(notification.type && notification.type.includes('bulk_report')) {
            iconClass = 'iconoir-file-text';
            if(notification.type === 'bulk_report_created') { bgClass = 'bg-success-subtle text-success'; }
            else if(notification.type === 'bulk_report_generated') { bgClass = 'bg-info-subtle text-info'; }
            else if(notification.type === 'bulk_report_printed') { bgClass = 'bg-warning-subtle text-warning'; }
            else if(notification.type === 'bulk_report_deleted') { bgClass = 'bg-danger-subtle text-danger'; }
        }

        toast.innerHTML = `
            <a href="/notifications/${notification.id}" class="text-decoration-none d-flex align-items-start w-100 text-reset">
                <div class="flex-shrink-0 ${bgClass} thumb-md rounded-circle d-flex align-items-center justify-content-center">
                    <i class="${iconClass} fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="my-0 fw-bold text-dark fs-13">${notification.title}</h6>
                    <small class="text-muted mb-0">${notification.message}</small>
                </div>
            </a>
        `;

        document.body.appendChild(toast);

        // Trigger reflow
        toast.offsetHeight;

        // Slide in
        toast.classList.add('show');

        // After 3 seconds, fly to bell
        setTimeout(() => {
            const bell = document.getElementById('notification-bell');
            if (bell) {
                const rect = bell.getBoundingClientRect();
                
                toast.style.top = (rect.top + 10) + 'px';
                toast.style.right = (window.innerWidth - rect.right + 10) + 'px';
                toast.style.transform = 'scale(0.1)';
                toast.style.opacity = '0';
                
                setTimeout(() => {
                    toast.remove();
                }, 800);
            } else {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    }

    function addNotificationToDropdown(notification) {
        const list = document.getElementById('notification-list');
        const emptyMsg = document.getElementById('no-notifications');
        
        if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }

        let iconClass = 'iconoir-info-circle';
        let bgClass = 'bg-primary-subtle text-primary';
        
        if (notification.type === 'success') { iconClass = 'iconoir-check-circle'; bgClass = 'bg-success-subtle text-success'; }
        if (notification.type === 'warning') { iconClass = 'iconoir-warning-circle'; bgClass = 'bg-warning-subtle text-warning'; }
        if (notification.type === 'error') { iconClass = 'iconoir-x-circle'; bgClass = 'bg-danger-subtle text-danger'; }

        // Special icons for bulk reports
        if(notification.type && notification.type.includes('bulk_report')) {
            iconClass = 'iconoir-file-text';
            if(notification.type === 'bulk_report_created') { bgClass = 'bg-success-subtle text-success'; }
            else if(notification.type === 'bulk_report_generated') { bgClass = 'bg-info-subtle text-info'; }
            else if(notification.type === 'bulk_report_printed') { bgClass = 'bg-warning-subtle text-warning'; }
            else if(notification.type === 'bulk_report_deleted') { bgClass = 'bg-danger-subtle text-danger'; }
        }

        const itemHtml = `
            <a href="/notifications/${notification.id}" class="dropdown-item py-3 border-bottom">
                <small class="float-end text-muted ps-2">Baru saja</small>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 ${bgClass} thumb-md rounded-circle d-flex align-items-center justify-content-center">
                        <i class="${iconClass} fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 text-truncate">
                        <h6 class="my-0 fw-normal text-dark fs-13">${notification.title}</h6>
                        <small class="text-muted mb-0">${notification.message}</small>
                        
                        ${notification.data && notification.data.bulk_report_id ? 
                            `<div class="mt-1">
                                <a href="/bulk-reports/${notification.data.bulk_report_id}/detail" class="text-primary text-decoration-none small">
                                    <i class="fa fa-external-link-alt me-1"></i>Lihat Laporan
                                </a>
                            </div>` : ''
                        }
                    </div>
                </div>
            </a>
        `;

        list.insertAdjacentHTML('afterbegin', itemHtml);
    }

    function updateBadgeCount() {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.style.display = 'inline-block';
            // If it has text, parse it, else 0
            let count = parseInt(badge.innerText) || 0;
            badge.innerText = count + 1;
        }
    }

    function updateBadgeFromServer() {
        // Fetch unread count from server
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.style.display = 'inline-block';
                        badge.innerText = data.count > 99 ? '99+' : data.count;
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Failed to fetch notification count:', error);
            });
    }

    function loadNotifications() {
        // Load recent notifications for dropdown
        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('notification-list');
                const emptyMsg = document.getElementById('no-notifications');
                
                // Clear existing notifications first
                if (list) {
                    list.innerHTML = '';
                }
                
                if (data.notifications && data.notifications.length > 0) {
                    if (emptyMsg) {
                        emptyMsg.style.display = 'none';
                    }
                    
                    // Add notifications to dropdown
                    data.notifications.forEach(notification => {
                        addNotificationToDropdown(notification);
                    });
                } else {
                    // Show empty message
                    if (emptyMsg) {
                        emptyMsg.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Failed to load notifications:', error);
            });
    }

    // Update badge count every 30 seconds
    setInterval(updateBadgeFromServer, 30000);
</script>
