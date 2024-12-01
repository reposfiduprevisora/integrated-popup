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

        $sql = "CREATE TABLE {$this->table_name} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            style LONGTEXT NOT NULL,
            conditions LONGTEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            views INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function save_popup($data) {
        global $wpdb;

        $defaults = array(
            'title' => '',
            'content' => '',
            'style' => '{}',
            'conditions' => '{}',
            'status' => 'active'
        );

        $data = wp_parse_args($data, $defaults);

        // Asegurar que los campos JSON sean válidos
        $data['style'] = is_string($data['style']) ? $data['style'] : wp_json_encode($data['style']);
        $data['conditions'] = is_string($data['conditions']) ? $data['conditions'] : wp_json_encode($data['conditions']);

        // Sanitizar datos
        $data = array(
            'title' => sanitize_text_field($data['title']),
            'content' => wp_kses_post($data['content']),
            'style' => $data['style'],
            'conditions' => $data['conditions'],
            'status' => sanitize_text_field($data['status'])
        );

        // Si hay un ID, actualizar, si no, insertar
        if (!empty($data['id'])) {
            $result = $wpdb->update(
                $this->table_name,
                $data,
                array('id' => $data['id']),
                array('%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
        } else {
            $result = $wpdb->insert(
                $this->table_name,
                $data,
                array('%s', '%s', '%s', '%s', '%s')
            );
        }

        return $result ? ($data['id'] ?? $wpdb->insert_id) : false;
    }

    public function get_popup($id) {
        global $wpdb;
        
        $popup = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id),
            ARRAY_A
        );

        if ($popup) {
            $popup['style'] = json_decode($popup['style'], true);
            $popup['conditions'] = json_decode($popup['conditions'], true);
        }

        return $popup;
    }

    public function get_active_popups() {
        global $wpdb;
        
        $popups = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} WHERE status = 'active' ORDER BY created_at DESC",
            ARRAY_A
        );

        foreach ($popups as &$popup) {
            $popup['style'] = json_decode($popup['style'], true);
            $popup['conditions'] = json_decode($popup['conditions'], true);
        }

        return $popups;
    }

    public function increment_views($id) {
        global $wpdb;
        
        return $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$this->table_name} SET views = views + 1 WHERE id = %d",
                $id
            )
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

    public function update_status($id, $status) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_name,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }
}