<?php
if (!defined('ABSPATH')) {
    exit;
}

// Verificar permisos
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Guardar configuraciones
if (isset($_POST['save_settings']) && check_admin_referer('integrated_popup_settings', 'integrated_popup_settings_nonce')) {
    // Guardar API Key
    if (isset($_POST['ci_api_key'])) {
        update_option('integrated_popup_api_key', sanitize_text_field($_POST['ci_api_key']));
    }
    
    // Guardar URL de la API
    if (isset($_POST['ci_api_url'])) {
        update_option('integrated_popup_api_url', esc_url_raw($_POST['ci_api_url']));
    }
    
    // Guardar intervalo de sincronización
    if (isset($_POST['sync_interval'])) {
        update_option('integrated_popup_sync_interval', absint($_POST['sync_interval']));
    }
    
    // Guardar configuraciones generales
    update_option('integrated_popup_enable_analytics', isset($_POST['enable_analytics']));
    update_option('integrated_popup_enable_cache', isset($_POST['enable_cache']));
    update_option('integrated_popup_cache_duration', absint($_POST['cache_duration']));
    
    // Mostrar mensaje de éxito
    add_settings_error(
        'integrated_popup_messages',
        'settings_updated',
        'Configuración guardada correctamente.',
        'updated'
    );
}

// Obtener valores actuales
$api_key = get_option('integrated_popup_api_key', '');
$api_url = get_option('integrated_popup_api_url', '');
$sync_interval = get_option('integrated_popup_sync_interval', 5);
$enable_analytics = get_option('integrated_popup_enable_analytics', false);
$enable_cache = get_option('integrated_popup_enable_cache', true);
$cache_duration = get_option('integrated_popup_cache_duration', 3600);
?>

<div class="wrap">
    <h1>Configuración del Sistema de Popups</h1>
    
    <?php settings_errors('integrated_popup_messages'); ?>

    <form method="post" action="">
        <?php wp_nonce_field('integrated_popup_settings', 'integrated_popup_settings_nonce'); ?>

        <div class="metabox-holder">
            <!-- Configuración de la API -->
            <div class="postbox">
                <h2 class="hndle"><span>Configuración de la API</span></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="ci_api_key">API Key</label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="ci_api_key" 
                                       name="ci_api_key" 
                                       value="<?php echo esc_attr($api_key); ?>" 
                                       class="regular-text"
                                >
                                <p class="description">Clave de autenticación para la API de CodeIgniter</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="ci_api_url">URL de la API</label>
                            </th>
                            <td>
                                <input type="url" 
                                       id="ci_api_url" 
                                       name="ci_api_url" 
                                       value="<?php echo esc_url($api_url); ?>" 
                                       class="regular-text"
                                >
                                <p class="description">URL base de la API de CodeIgniter (ej: http://tu-sitio.com/api)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sync_interval">Intervalo de Sincronización</label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="sync_interval" 
                                       name="sync_interval" 
                                       value="<?php echo esc_attr($sync_interval); ?>" 
                                       min="1" 
                                       max="60" 
                                       class="small-text"
                                > minutos
                                <p class="description">Frecuencia con la que se sincronizarán los datos con CodeIgniter</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Configuración General -->
            <div class="postbox">
                <h2 class="hndle"><span>Configuración General</span></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Analíticas</th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="enable_analytics" 
                                           value="1" 
                                           <?php checked($enable_analytics); ?>
                                    >
                                    Habilitar seguimiento de analíticas
                                </label>
                                <p class="description">Registra estadísticas de visualizaciones y clics de los popups</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Caché</th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="enable_cache" 
                                           value="1" 
                                           <?php checked($enable_cache); ?>
                                    >
                                    Habilitar caché
                                </label>
                                <p class="description">Almacena en caché las configuraciones de los popups para mejorar el rendimiento</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cache_duration">Duración del Caché</label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="cache_duration" 
                                       name="cache_duration" 
                                       value="<?php echo esc_attr($cache_duration); ?>" 
                                       class="small-text"
                                > segundos
                                <p class="description">Tiempo que se mantendrán en caché las configuraciones</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Estado de la Conexión -->
            <div class="postbox">
                <h2 class="hndle"><span>Estado de la Conexión</span></h2>
                <div class="inside">
                    <?php
                    $connection_status = wp_remote_get(trailingslashit($api_url) . 'status', [
                        'headers' => [
                            'Authorization' => $api_key
                        ],
                        'timeout' => 5
                    ]);

                    if (!is_wp_error($connection_status)) {
                        $status_code = wp_remote_retrieve_response_code($connection_status);
                        if ($status_code === 200) {
                            echo '<div class="notice notice-success inline"><p>✅ Conexión establecida correctamente con la API</p></div>';
                        } else {
                            echo '<div class="notice notice-error inline"><p>❌ Error al conectar con la API. Código de estado: ' . esc_html($status_code) . '</p></div>';
                        }
                    } else {
                        echo '<div class="notice notice-error inline"><p>❌ Error de conexión: ' . esc_html($connection_status->get_error_message()) . '</p></div>';
                    }
                    ?>
                    <p>
                        <button type="button" class="button" id="test-connection">Probar Conexión</button>
                        <span id="connection-status"></span>
                    </p>
                </div>
            </div>
        </div>

        <input type="submit" name="save_settings" class="button button-primary" value="Guardar Configuración">
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#test-connection').on('click', function() {
        var button = $(this);
        var statusSpan = $('#connection-status');
        
        button.prop('disabled', true);
        statusSpan.html('<span class="spinner is-active"></span> Probando conexión...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'test_api_connection',
                nonce: '<?php echo wp_create_nonce('test_api_connection'); ?>',
                api_url: $('#ci_api_url').val(),
                api_key: $('#ci_api_key').val()
            },
            success: function(response) {
                if (response.success) {
                    statusSpan.html('<span class="notice notice-success inline">✅ Conexión exitosa</span>');
                } else {
                    statusSpan.html('<span class="notice notice-error inline">❌ ' + response.data + '</span>');
                }
            },
            error: function() {
                statusSpan.html('<span class="notice notice-error inline">❌ Error al probar la conexión</span>');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
});
</script>