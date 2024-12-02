<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_AdminMenu {
    private $database;
    private $api_client;
    public function __construct() {
        $this->database = new IntegratedPopup_Database();
        $this->api_client = new IntegratedPopup_ApiClient();
    }
    public function add_menu_pages() {
        add_menu_page(
            'Configuración de Popups',
            'Popups',
            'manage_options',
            'integrated-popup',
            array($this, 'render_main_page'),
            'dashicons-welcome-view-site',
            30
        );

        add_submenu_page(
            'integrated-popup',
            'Añadir Nuevo',
            'Añadir Nuevo',
            'manage_options',
            'integrated-popup-new',
            array($this, 'render_new_page')
        );

        add_submenu_page(
            'integrated-popup',
            'Configuración',
            'Configuración',
            'manage_options',
            'integrated-popup-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_main_page() {
        $popups = $this->database->get_all_popups();
        include INTEGRATED_POPUP_PATH . 'templates/admin/main-page.php';
    }

    public function render_new_page() {

        include INTEGRATED_POPUP_PATH . 'templates/admin/new-popup.php';
    }

    public function render_settings_page() {
        include INTEGRATED_POPUP_PATH . 'templates/admin/settings.php';
    }
}