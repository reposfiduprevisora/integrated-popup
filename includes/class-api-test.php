<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_ApiTest {
    private $api_url;
    private $api_key;

    public function __construct() {
        $this->api_url = get_option('integrated_popup_api_url');
        $this->api_key = get_option('integrated_popup_api_key');
    }

    public function test_connection() {
        $response = wp_remote_get($this->api_url . '/popup-configurations', [
            'headers' => [
                'Authorization' => $this->api_key,
                'Content-Type' => 'application/json'
            ],
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $response->get_error_message()
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code === 200) {
            return [
                'success' => true,
                'message' => 'Conexión exitosa',
                'data' => $data
            ];
        }

        return [
            'success' => false,
            'message' => 'Error en la respuesta. Código: ' . $status_code,
            'data' => $data
        ];
    }

    public function test_post() {
        $test_data = [
            'text' => 'Popup de prueba',
            'style' => json_encode([
                'background_color' => '#ffffff',
                'text_color' => '#000000',
                'width' => 400,
                'height' => 300
            ]),
            'conditions' => json_encode([
                'show_once' => true,
                'delay' => 5
            ])
        ];

        $response = wp_remote_post($this->api_url . '/popup-configurations', [
            'headers' => [
                'Authorization' => $this->api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($test_data),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => 'Error al enviar datos: ' . $response->get_error_message()
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code === 200 || $status_code === 201) {
            return [
                'success' => true,
                'message' => 'Datos enviados correctamente',
                'data' => $data
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al enviar datos. Código: ' . $status_code,
            'data' => $data
        ];
    }
}