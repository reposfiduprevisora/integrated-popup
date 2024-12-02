<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_ApiClient {
    private $api_url;

    public function __construct() {
        $this->api_url = get_option('integrated_popup_api_url', 'http://localhost:8080/api');
    }
    public function send_popup_data($popup_data) {
        $endpoint = $this->api_url . '/html-content';
        
        // Preparar los datos para la API
        $api_data = array(
            'style' => $popup_data['style'],
            'content' => $popup_data['content'],
            'status' => $popup_data['status'],
            'view_name' => $popup_data['view_name'] ?? 'default'
        );

        $response = wp_remote_post($endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($api_data),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code === 200 || $status_code === 201) {
            return array(
                'success' => true,
                'data' => $body
            );
        }

        return array(
            'success' => false,
            'message' => isset($body['message']) ? $body['message'] : 'Error desconocido'
        );
    }
    public function get_available_views() {
        $response = wp_remote_get($this->api_url . '/views', array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return array(
            'success' => true,
            'data' => $data
        );
    }

    public function save_popup_content($content, $views) {
        $data = array(
            'html_content' => $content,
            'views' => $views
        );

        $response = wp_remote_post($this->api_url . '/html-content', array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return array(
            'success' => true,
            'data' => $data
        );
    }

    public function update_api_url($url) {
        $this->api_url = $url;
        return update_option('integrated_popup_api_url', $url);
    }

    public function get_api_url() {
        return $this->api_url;
    }
}
