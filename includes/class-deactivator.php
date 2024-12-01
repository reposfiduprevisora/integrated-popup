<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}