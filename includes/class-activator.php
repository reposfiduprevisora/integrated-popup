<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Activator {
    public static function activate() {
        $database = new IntegratedPopup_Database();
        $database->activate();
        flush_rewrite_rules();
    }
}