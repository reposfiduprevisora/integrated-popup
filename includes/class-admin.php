<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Admin {
    private $version;
    private $plugin_name;
    private $database;
    private $api_client;

    public function __construct($version) {
        $this->version = $version;
        $this->plugin_name = 'integrated-popup';
        $this->database = new IntegratedPopup_Database();
        $this->api_client = new IntegratedPopup_ApiClient();
        
        add_action('admin_post_save_popup', array($this, 'handle_save_popup'));
        add_action('admin_notices', array($this, 'show_admin_notices'));
        add_action('wp_ajax_get_available_views', array($this, 'get_available_views'));
        add_action('wp_ajax_get_popup_data', array($this, 'get_popup_data'));
        add_action('wp_ajax_update_popup_status', array($this, 'update_popup_status'));
        add_action('wp_ajax_toggle_popup_status', array($this, 'handle_toggle_status'));
    }
    public function handle_toggle_status() {
        check_ajax_referer('popup_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }

        $popup_id = isset($_POST['popup_id']) ? absint($_POST['popup_id']) : 0;
        
        if (!$popup_id) {
            wp_send_json_error('ID de popup inválido');
        }

        $new_status = $this->database->toggle_status($popup_id);
        
        if ($new_status !== false) {
            wp_send_json_success(array(
                'status' => $new_status,
                'message' => 'Estado actualizado correctamente'
            ));
            $popup = $this->database->get_popup($popup_id);
            // Actualizar en la API
            if ($popup) {
                $api_response = $this->api_client->send_popup_data(array(
                    'style' => $popup['style'],
                    'content' => $popup['content'],
                    'status' => $new_status,
                    'view_name' => $popup['view_name'] ?? 'default'
                ));
                
                if (!$api_response['success']) {
                    error_log('Error al actualizar estado en la API: ' . $api_response['message']);
                }
            }
        } else {
            wp_send_json_error('Error al actualizar el estado');
        }

    }
    public function show_admin_notices() {
        if (isset($_GET['page']) && $_GET['page'] === 'integrated-popup' && isset($_GET['message'])) {
            if ($_GET['message'] === 'saved') {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Popup guardado correctamente.', 'integrated-popup'); ?></p>
                </div>
                <?php
            } elseif ($_GET['message'] === 'error') {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('Error al guardar el popup.', 'integrated-popup'); ?></p>
                </div>
                <?php
            }
        }
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'integrated-popup') === false) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-admin',
            INTEGRATED_POPUP_URL . 'assets/css/admin.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            INTEGRATED_POPUP_URL . 'assets/js/admin.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script($this->plugin_name . '-admin', 'popupAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('popup_admin_nonce')
        ));
    }

    public function get_available_views() {
        check_ajax_referer('popup_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }
    
        $response = $this->api_client->get_available_views();
        
        if ($response['success'] && isset($response['data']['data'])) {
            $views = $response['data']['data'];
            // Filtrar solo las vistas relevantes (excluir errores y layouts)
            $filtered_views = array_filter($views, function($view) {
                return !strpos($view['name'], 'errors/') && 
                       !strpos($view['name'], 'layouts/') &&
                       !strpos($view['name'], 'partials/');
            });
            wp_send_json_success($filtered_views);
        } else {
            wp_send_json_error('No se pudieron obtener las vistas');
        }
    }
    
    public function get_popup_data() {
        check_ajax_referer('popup_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }

        $popup_id = isset($_POST['popup_id']) ? intval($_POST['popup_id']) : 0;
        if (!$popup_id) {
            wp_send_json_error('ID de popup no válido');
        }

        $popup = $this->database->get_popup($popup_id);
        if (!$popup) {
            wp_send_json_error('Popup no encontrado');
        }

        wp_send_json_success($popup);
    }

    public function handle_save_popup() {
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos suficientes para realizar esta acción.'));
        }

        check_admin_referer('integrated_popup_action', 'integrated_popup_nonce');

        // Preparar los datos del popup
        $popup_data = array(
            'title' => isset($_POST['popup_title']) ? sanitize_text_field($_POST['popup_title']) : '',
            'content' => isset($_POST['popup_content']) ? wp_kses_post($_POST['popup_content']) : '',
            'style' => isset($_POST['popup_style']) ? wp_json_encode($this->sanitize_style_array($_POST['popup_style'])) : '{}',
            'view_name' => isset($_POST['popup_views']) ? array_map('sanitize_text_field', $_POST['popup_views']) : array()
        );

       // Guardar en la base de datos
       if ($popup_id) {
        $result = $this->database->update_popup($popup_id, $popup_data);
        } else {
        $result = $this->database->save_popup($popup_data);
        }
        if ($result) {
            // Enviar a la API
            $api_response = $this->api_client->send_popup_data($popup_data);
            
            if ($api_response['success']) {
                wp_redirect(add_query_arg(
                    array(
                        'page' => 'integrated-popup',
                        'message' => 'saved'
                    ),
                    admin_url('admin.php')
                ));
                exit;
            } else {
                // Si falla la API, mostrar error pero mantener los datos guardados localmente
                wp_redirect(add_query_arg(
                    array(
                        'page' => 'integrated-popup',
                        'message' => 'api_error',
                        'error' => urlencode($api_response['message'])
                    ),
                    admin_url('admin.php')
                ));
                exit;
            }
        }

        wp_redirect(add_query_arg(
            array(
                'page' => 'integrated-popup',
                'message' => 'error'
            ),
            admin_url('admin.php')
        ));
        exit;
    }

    private function generate_popup_html($popup_data) {
        $style = json_decode($popup_data['style'], true);
        
        ob_start();
        ?>
        <div class="custom-popup" style="
            background-color: <?php echo esc_attr($style['background_color']); ?>;
            color: <?php echo esc_attr($style['text_color']); ?>;
            width: <?php echo esc_attr($style['width']); ?>px;
            height: <?php echo esc_attr($style['height']); ?>px;
        ">
            <div class="popup-content">
                <?php echo wp_kses_post($popup_data['content']); ?>
            </div>
            <button class="popup-close">&times;</button>
        </div>
        <?php
        return ob_get_clean();
    }

    private function sanitize_style_array($style) {
        return array(
            'background_color' => sanitize_hex_color($style['background_color'] ?? '#ffffff'),
            'text_color' => sanitize_hex_color($style['text_color'] ?? '#000000'),
            'width' => absint($style['width'] ?? 400),
            'height' => absint($style['height'] ?? 300),
            'position' => sanitize_text_field($style['position'] ?? 'center')
        );
    }
    public function update_popup_status() {
        check_ajax_referer('popup_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }
    
        $popup_id = isset($_POST['popup_id']) ? absint($_POST['popup_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    
        if (!$popup_id || !in_array($status, array('active', 'inactive'))) {
            wp_send_json_error('Datos inválidos');
        }
    
        $result = $this->database->update_status($popup_id, $status);
        echo $result;
        if ($result) {
            wp_send_json_success(array(
                'message' => 'Estado actualizado correctamente',
                'status' => $status
            ));
        } else {
            wp_send_json_error('Error al actualizar el estado');
        }
    }
}
