jQuery(document).ready(function($) {
    // Manejar cambio de estado
    // Toggle de estado del popup
    $('.popup-status-toggle').on('change', function() {
        const $toggle = $(this);
        const popupId = $toggle.data('popup-id');
        
        $.ajax({
            url: popupAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'toggle_popup_status',
                popup_id: popupId,
                nonce: popupAdmin.nonce
            },
            beforeSend: function() {
                $toggle.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar el estado visual del toggle
                    $toggle.prop('checked', response.data.status === 1);
                } else {
                    alert('Error al cambiar el estado: ' + response.data);
                    // Revertir el toggle si hubo error
                    $toggle.prop('checked', !$toggle.prop('checked'));
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
                // Revertir el toggle si hubo error
                $toggle.prop('checked', !$toggle.prop('checked'));
            },
            complete: function() {
                $toggle.prop('disabled', false);
            }
        });
    });
    // Cargar vistas disponibles
    function loadAvailableViews() {
        $.ajax({
            url: popupAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_available_views',
                nonce: popupAdmin.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    const viewsContainer = $('#popup-views-container');
                    viewsContainer.empty();
                    
                    response.data.forEach(function(view) {
                        viewsContainer.append(`
                            <label>
                                <input type="checkbox" name="popup_views[]" value="${view.name}">
                                ${view.name}
                            </label><br>
                        `);
                    });
                }
            },
            error: function() {
                alert('Error al cargar las vistas disponibles');
            }
        });
    }

    // Cargar vistas al abrir la página
    if ($('#popup-views-container').length) {
        loadAvailableViews();
    }

    // Preview del popup
    function updatePopupPreview() {
        const style = {
            backgroundColor: $('#background_color').val(),
            color: $('#text_color').val(),
            width: $('#width').val() + 'px',
            height: $('#height').val() + 'px'
        };

        const content = $('#popup_content').val();
        
        $('.popup-preview-content').css(style).html(content);
    }

    // Eventos para actualizar preview
    $('#popup-form input, #popup-form textarea').on('change keyup', updatePopupPreview);
});
