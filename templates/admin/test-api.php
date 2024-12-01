<?php
if (!defined('ABSPATH')) {
    exit;
}

$api_test = new IntegratedPopup_ApiTest();
?>

<div class="wrap">
    <h1>Prueba de API</h1>

    <div class="metabox-holder">
        <div class="postbox">
            <h2 class="hndle"><span>Estado de la Conexión</span></h2>
            <div class="inside">
                <p>
                    <button type="button" class="button button-primary" id="test-get">Probar GET</button>
                    <button type="button" class="button button-primary" id="test-post">Probar POST</button>
                </p>
                <div id="test-results" class="notice-container"></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    function showResult(success, message, data) {
        var html = '<div class="notice notice-' + (success ? 'success' : 'error') + ' inline">';
        html += '<p><strong>' + message + '</strong></p>';
        if (data) {
            html += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        }
        html += '</div>';
        $('#test-results').html(html);
    }

    $('#test-get').on('click', function() {
        var button = $(this);
        button.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'test_api_get',
                nonce: '<?php echo wp_create_nonce("test_api"); ?>'
            },
            success: function(response) {
                showResult(response.success, response.message, response.data);
            },
            error: function() {
                showResult(false, 'Error en la solicitud AJAX');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    $('#test-post').on('click', function() {
        var button = $(this);
        button.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'test_api_post',
                nonce: '<?php echo wp_create_nonce("test_api"); ?>'
            },
            success: function(response) {
                showResult(response.success, response.message, response.data);
            },
            error: function() {
                showResult(false, 'Error en la solicitud AJAX');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
});
</script>