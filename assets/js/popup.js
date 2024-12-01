jQuery(document).ready(function($) {
    function showPopup() {
        const popup = $('#integrated-popup');
        const conditions = JSON.parse(localStorage.getItem('popup_conditions') || '{}');
        
        if (conditions.show_once && localStorage.getItem('popup_shown')) {
            return;
        }

        if (conditions.delay) {
            setTimeout(() => {
                popup.fadeIn();
            }, conditions.delay * 1000);
        } else {
            popup.fadeIn();
        }

        if (conditions.show_once) {
            localStorage.setItem('popup_shown', 'true');
        }
    }

    function syncPopupConfig() {
        $.ajax({
            url: popupAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'sync_popup_config',
                nonce: popupAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    localStorage.setItem('popup_conditions', JSON.stringify(response.data.conditions));
                }
            }
        });
    }

    // Close popup
    $('.popup-close').on('click', function() {
        $('#integrated-popup').fadeOut();
    });

    // Close on background click
    $('.popup-container').on('click', function(e) {
        if (e.target === this) {
            $(this).fadeOut();
        }
    });

    // Initial sync and display
    syncPopupConfig();
    showPopup();

    // Periodic sync (every 5 minutes)
    setInterval(syncPopupConfig, 5 * 60 * 1000);
});