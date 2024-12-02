<?php
/**
 * Plugin Name: Sistema Integrado de Popups
 * Description: Sistema de gestión de popups integrado con CodeIgniter
 * Version: 1.0.0
 * Author: GS
 * Text Domain: integrated-popup
 */
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('INTEGRATED_POPUP_VERSION', '1.0.0');
define('INTEGRATED_POPUP_FILE', __FILE__);
define('INTEGRATED_POPUP_PATH', plugin_dir_path(__FILE__));
define('INTEGRATED_POPUP_URL', plugin_dir_url(__FILE__));

// Cargar el autoloader
require_once INTEGRATED_POPUP_PATH . 'includes/class-autoloader.php';
require_once INTEGRATED_POPUP_PATH . 'includes/class-api-client.php';
require_once INTEGRATED_POPUP_PATH . 'includes/class-admin.php';
require_once INTEGRATED_POPUP_PATH . 'includes/class-admin-menu.php';
require_once INTEGRATED_POPUP_PATH . 'includes/class-database.php';
require_once INTEGRATED_POPUP_PATH . 'includes/class-core.php';
require_once INTEGRATED_POPUP_PATH . 'includes/class-loader.php';

// Registrar el autoloader
IntegratedPopup_Autoloader::register();

// Función de activación
register_activation_hook(__FILE__, array('IntegratedPopup_Activator', 'activate'));

// Función de desactivación
register_deactivation_hook(__FILE__, array('IntegratedPopup_Deactivator', 'deactivate'));

// Iniciar el plugin
function integrated_popup_init() {
    $plugin = new IntegratedPopup_Core();
    $plugin->run();
}

// Iniciar el plugin después de que todos los plugins estén cargados
add_action('plugins_loaded', 'integrated_popup_init');