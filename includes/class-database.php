<?php
if (!defined('ABSPATH')) {
    exit;
}

class IntegratedPopup_Database {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'custom_popups';
    }

    public function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            style LONGTEXT NOT NULL,
            view_name LONGTEXT NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    public function toggle_status($id) {
        global $wpdb;
        
        // Primero obtenemos el estado actual
        $current_status = $wpdb->get_var($wpdb->prepare(
            "SELECT status FROM {$this->table_name} WHERE id = %d",
            $id
        ));

        // Cambiamos el estado (si era 1 pasa a 0 y viceversa)
        $new_status = $current_status == 1 ? 0 : 1;
        
        $result = $wpdb->update(
            $this->table_name,
            array('status' => $new_status),
            array('id' => $id),
            array('%d'),
            array('%d')
        );

        return $result !== false ? $new_status : false;
    }

    public function get_all_popups() {
        global $wpdb;
        
        return $wpdb->get_results(
            "SELECT * FROM {$this->table_name} ORDER BY created_at DESC",
            ARRAY_A
        );
    }

    public function save_popup($data) {
        global $wpdb;
    
        $defaults = array(
            'title' => '',
            'content' => '',
            'style' => '{}',
            'view_name' => '[]',
            'status' => 1
        );
    
        $data = wp_parse_args($data, $defaults);
    // Sanitizar datos
    $data = array(
        'title' => sanitize_text_field($data['title']),
        'content' => wp_kses_post($data['content']),
        'style' => is_string($data['style']) ? $data['style'] : wp_json_encode($data['style']),
        'view_name' => is_string($data['view_name']) ? $data['view_name'] : wp_json_encode($data['view_name']),
        'status' => absint($data['status'])
    );
        // Asegurar que los campos JSON y arrays sean guardados correctamente
        $data['style'] = is_string($data['style']) ? $data['style'] : wp_json_encode($data['style']);
        $data['view_name'] = is_array($data['view_name']) ? wp_json_encode($data['view_name']) : $data['view_name'];
    
        // Si hay un ID, actualizar, si no, insertar
        if (!empty($data['id'])) {
            $result = $wpdb->update(
                $this->table_name,
                $data,
                array('id' => $data['id']),
                array('%s', '%s', '%s', '%s'),
                array('%d')
            );
        } else {
            $result = $wpdb->insert(
                $this->table_name,
                $data,
                array('%s', '%s', '%s', '%s')
            );
        }

        return $result ? ($data['id'] ?? $wpdb->insert_id) : false;

    }
    

    public function get_popup($popup_id) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $popup_id
            ),
            ARRAY_A
        );
    }
    public function get_active_popups() {
        global $wpdb;
        
        return $wpdb->get_results(
            "SELECT * FROM {$this->table_name} WHERE status = 1 ORDER BY created_at DESC",
            ARRAY_A
        );
    }
    public function delete_popup($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }

    public function update_popup($id, $data) {
        global $wpdb;
        
        $data['updated_at'] = current_time('mysql');
        
        return $wpdb->update(
            $this->table_name,
            $data,
            array('id' => $id),
            array('%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );
    }
    public function update_status($id, $status) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_name,
            array('status' => absint($status)),
            array('id' => $id),
            array('%d'),
            array('%d')
        );
    }
}