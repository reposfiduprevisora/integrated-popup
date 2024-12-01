<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Ajax {
    public function __construct() {
        add_action('wp_ajax_test_api_get', array($this, 'test_api_get'));
        add_action('wp_ajax_test_api_post', array($this, 'test_api_post'));
    }

    public function test_api_get() {
        check_ajax_referer('test_api', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }

        $api_test = new IntegratedPopup_ApiTest();
        $result = $api_test->test_connection();
        
        wp_send_json($result);
    }

    public function test_api_post() {
        check_ajax_referer('test_api', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permisos insuficientes');
        }

        $api_test = new IntegratedPopup_ApiTest();
        $result = $api_test->test_post();
        
        wp_send_json($result);
    }
}