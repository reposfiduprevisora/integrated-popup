<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Autoloader {
    public static function register() {
        spl_autoload_register(array(new self, 'autoload'));
    }

    public function autoload($class) {
        // Solo cargar clases del plugin
        if (strpos($class, 'IntegratedPopup_') !== 0) {
            return;
        }

        // Convertir el nombre de la clase a un archivo
        $class_name = str_replace('IntegratedPopup_', '', $class);
        $class_name = strtolower(str_replace('_', '-', $class_name));
        $file = INTEGRATED_POPUP_PATH . 'includes/class-' . $class_name . '.php';

        // Cargar el archivo si existe
        if (file_exists($file)) {
            require_once $file;
        }
    }
}