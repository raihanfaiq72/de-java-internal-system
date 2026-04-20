<!-- Simple DeJava App Promo Modal -->
<style>
    .simple-promo-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .simple-promo-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .simple-promo-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #1d1d1f;
    }

    .simple-promo-message {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .simple-promo-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .simple-promo-btn {
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .simple-promo-btn-primary {
        background: #007AFF;
        color: white;
    }

    .simple-promo-btn-secondary {
        background: #f5f5f7;
        color: #666;
    }
</style>

<div id="simple-promo-modal" class="simple-promo-modal" style="display: none;">
    <div class="simple-promo-content">
        <h3 class="simple-promo-title">DeJava Mobile App</h3>
        <p class="simple-promo-message">
            Dapatkan pengalaman terbaik mengelola bisnis Anda dengan aplikasi mobile DeJava. 
            Download sekarang dan akses laporan kapan saja, di mana saja!
        </p>
        <div class="simple-promo-buttons">
            <a href="/app-release3" class="simple-promo-btn simple-promo-btn-primary">
                <i class="fa-brands fa-android"></i>
                Download Android
            </a>
            <button class="simple-promo-btn simple-promo-btn-secondary" onclick="closeSimplePromo()">
                Nanti Saja
            </button>
        </div>
    </div>
</div>

<script>
    // Check if promo has been shown before
    function showDeJavaPromoOnce() {
        // Check if promo has been shown in this session
        const promoShown = localStorage.getItem('dejava_promo_shown');
        
        if (!promoShown) {
            // Show the promo modal
            document.getElementById('simple-promo-modal').style.display = 'flex';
            
            // Mark as shown in localStorage
            localStorage.setItem('dejava_promo_shown', 'true');
        }
    }

    function closeSimplePromo() {
        document.getElementById('simple-promo-modal').style.display = 'none';
        // Also mark as shown when manually closed
        localStorage.setItem('dejava_promo_shown', 'true');
    }

    // Show promo when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            showDeJavaPromoOnce();
        }, 1000); // Show after 1 second delay for better UX
    });
</script>
