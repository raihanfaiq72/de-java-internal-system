<!-- Apple-style Toast Notification Helper -->
<style>
    #mac-toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .mac-toast {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 18px;
        padding: 16px;
        width: 340px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.02);
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transform: translateX(120%);
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
        pointer-events: auto;
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .mac-toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .mac-toast.hiding {
        transform: translateX(120%);
        opacity: 0;
    }

    .mac-toast-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .mac-toast-icon svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .mac-toast-content {
        flex-grow: 1;
        padding-top: 2px;
    }

    .mac-toast-title {
        font-weight: 600;
        font-size: 14px;
        color: #1d1d1f;
        margin-bottom: 3px;
        letter-spacing: -0.01em;
    }

    .mac-toast-message {
        font-size: 13px;
        color: #86868b;
        line-height: 1.4;
    }

    /* Types */
    .mac-toast-success .mac-toast-icon { background: #E4F9E9; color: #00C853; }
    .mac-toast-error .mac-toast-icon { background: #FFEBEA; color: #FF3B30; }
    .mac-toast-warning .mac-toast-icon { background: #FFF8E1; color: #FFCC00; }
    .mac-toast-info .mac-toast-icon { background: #E3F2FD; color: #007AFF; }
</style>

<div id="mac-toast-container"></div>

<script>
    /**
     * Show Apple-style Notification
     * @param {string} type - 'success', 'error', 'warning', 'info'
     * @param {string} message - The message body
     * @param {string} [title] - Optional title
     */
    function showNotification(type, message, title = null) {
        const container = document.getElementById('mac-toast-container');
        
        // SVG Icons
        const icons = {
            success: `<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>`,
            error: `<svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>`,
            warning: `<svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>`,
            info: `<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>`
        };

        // Default titles
        if (!title) {
            const titles = {
                success: 'Berhasil',
                error: 'Terjadi Kesalahan',
                warning: 'Perhatian',
                info: 'Informasi'
            };
            title = titles[type] || 'Notifikasi';
        }

        const toast = document.createElement('div');
        toast.className = `mac-toast mac-toast-${type}`;
        toast.innerHTML = `
            <div class="mac-toast-icon">${icons[type] || icons.info}</div>
            <div class="mac-toast-content">
                <div class="mac-toast-title">${title}</div>
                <div class="mac-toast-message">${message}</div>
            </div>
        `;

        container.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Auto remove
        setTimeout(() => {
            toast.classList.add('hiding');
            toast.addEventListener('transitionend', () => {
                toast.remove();
            });
        }, 5000);
    }

    // Helper to replace standard alert
    window.macAlert = showNotification;

    // Override default browser alert
    window.originalAlert = window.alert;
    window.alert = function(message) {
        if (!message) return;
        
        let type = 'info';
        let title = 'Informasi';
        const msgLower = String(message).toLowerCase();
        
        if (msgLower.includes('success') || msgLower.includes('berhasil') || msgLower.includes('sukses') || msgLower.includes('saved')) {
            type = 'success';
            title = 'Berhasil';
        } else if (msgLower.includes('error') || msgLower.includes('gagal') || msgLower.includes('kesalahan') || msgLower.includes('fail') || msgLower.includes('salah')) {
            type = 'error';
            title = 'Terjadi Kesalahan';
        } else if (msgLower.includes('warning') || msgLower.includes('perhatian') || msgLower.includes('alert')) {
            type = 'warning';
            title = 'Perhatian';
        }
        
        showNotification(type, message, title);
    };

    // Check for Session Flash Messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showNotification('success', "{{ session('success') }}");
        @endif

        @if(session('error'))
            showNotification('error', "{{ session('error') }}");
        @endif

        @if(session('warning'))
            showNotification('warning', "{{ session('warning') }}");
        @endif

        @if(session('info'))
            showNotification('info', "{{ session('info') }}");
        @endif

        // Also check for validation errors
        @if($errors->any())
            @foreach($errors->all() as $error)
                showNotification('error', "{{ $error }}", 'Validasi Gagal');
            @endforeach
        @endif
    });
</script>
