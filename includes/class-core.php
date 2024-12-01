<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Core {
    private $loader;
    private $version;
    private $plugin_name;
    private $admin;
    private $admin_menu;
    private $database;

    public function __construct() {
        $this->version = INTEGRATED_POPUP_VERSION;
        $this->plugin_name = 'integrated-popup';
        $this->load_dependencies();
        $this->setup_admin();
        $this->setup_public();
    }

    private function load_dependencies() {
        // Inicializar componentes principales
        $this->loader = new IntegratedPopup_Loader();
        $this->database = new IntegratedPopup_Database();
        $this->admin = new IntegratedPopup_Admin($this->version);
        $this->admin_menu = new IntegratedPopup_AdminMenu();

        // Verificar que todas las clases se hayan cargado correctamente
        if (!$this->loader || !$this->database || !$this->admin || !$this->admin_menu) {
            error_log('Error: No se pudieron cargar todas las dependencias del plugin Integrated Popup');
            return;
        }
    }

    private function setup_admin() {
        if ($this->admin && $this->admin_menu) {
            $this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_admin_scripts');
            $this->loader->add_action('admin_menu', $this->admin_menu, 'add_menu_pages');
        }
    }

    private function setup_public() {
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_frontend_scripts');
        $this->loader->add_action('wp_footer', $this, 'render_popups');
    }

    public function run() {
        if ($this->loader) {
            $this->loader->run();
        }
    }

    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            $this->plugin_name,
            INTEGRATED_POPUP_URL . 'assets/css/popup.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            $this->plugin_name,
            INTEGRATED_POPUP_URL . 'assets/js/popup.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script($this->plugin_name, 'popupAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('popup_ajax_nonce')
        ));
    }

    public function render_popups() {
        if ($this->database) {
            $popups = $this->database->get_active_popups();
            if (!empty($popups)) {
                foreach ($popups as $popup) {
                    include INTEGRATED_POPUP_PATH . 'templates/popup-template.php';
                }
            }
        }
    }
}