<?php
if (!defined('ABSPATH')) {
    exit;
}
class IntegratedPopup_Admin {
    private $version;
    private $plugin_name;
    private $database;

    public function __construct($version) {
        $this->version = $version;
        $this->plugin_name = 'integrated-popup';
        $this->database = new IntegratedPopup_Database();
        
        // Agregar hooks para guardar popups
        add_action('admin_post_save_popup', array($this, 'handle_save_popup'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    public function admin_notices() {
        if (isset($_GET['page']) && $_GET['page'] === 'integrated-popup' && isset($_GET['message'])) {
            if ($_GET['message'] === 'saved') {
                echo '<div class="notice notice-success is-dismissible"><p>Popup guardado correctamente.</p></div>';
            } elseif ($_GET['message'] === 'error') {
                echo '<div class="notice notice-error is-dismissible"><p>Error al guardar el popup.</p></div>';
            }
        }
    }
    public function enqueue_admin_scripts($hook) {
        // Solo cargar en las páginas del plugin
        if (strpos($hook, 'integrated-popup') === false) {
            return;
        }

        wp_enqueue_style(
            'integrated-popup-admin',
            INTEGRATED_POPUP_URL . 'assets/css/admin.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            'integrated-popup-admin',
            INTEGRATED_POPUP_URL . 'assets/js/admin.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script('integrated-popup-admin', 'popupAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('popup_admin_nonce')
        ));
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
            'conditions' => isset($_POST['popup_conditions']) ? wp_json_encode($this->sanitize_conditions_array($_POST['popup_conditions'])) : '{}'
        );

        // Guardar en la base de datos
        $result = $this->database->save_popup($popup_data);

        if ($result) {
            // Sincronizar con CodeIgniter
            $this->sync_with_codeigniter($popup_data);
            
            wp_redirect(add_query_arg(
                array(
                    'page' => 'integrated-popup',
                    'message' => 'saved'
                ),
                admin_url('admin.php')
            ));
            exit;
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
    private function sync_with_codeigniter($popup_data) {
        $api_url = get_option('integrated_popup_api_url');
        $api_key = get_option('integrated_popup_api_key');

        if (!$api_url || !$api_key) {
            return false;
        }

        $response = wp_remote_post($api_url . '/popup-configurations', array(
            'headers' => array(
                'Authorization' => $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => wp_json_encode($popup_data),
            'timeout' => 30
        ));

        return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
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

    private function sanitize_conditions_array($conditions) {
        return array(
            'show_once' => !empty($conditions['show_once']),
            'delay' => absint($conditions['delay'] ?? 0),
            'pages' => isset($conditions['pages']) ? array_map('absint', (array)$conditions['pages']) : array(),
            'display_type' => sanitize_text_field($conditions['display_type'] ?? 'all')
        );
    }
}