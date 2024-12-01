<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class IntegratedPopup_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => 'popup',
            'plural'   => 'popups',
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'cb'         => '<input type="checkbox" />',
            'title'      => 'Título',
            'status'     => 'Estado',
            'views'      => 'Vistas',
            'created_at' => 'Fecha de Creación'
        ];
    }

    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_popups';

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page
        ]);

        $this->items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $per_page,
                ($current_page - 1) * $per_page
            ),
            ARRAY_A
        );
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'title':
                return esc_html($item['title']);
            case 'status':
                return esc_html($item['status']);
            case 'views':
                return intval($item['views']);
            case 'created_at':
                return date_i18n(get_option('date_format'), strtotime($item['created_at']));
            default:
                return print_r($item, true);
        }
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="popup[]" value="%s" />',
            $item['id']
        );
    }

    public function column_title($item) {
        $actions = [
            'edit'   => sprintf('<a href="?page=integrated-popup-new&action=edit&id=%s">Editar</a>', $item['id']),
            'delete' => sprintf(
                '<a href="?page=%s&action=delete&popup=%s&_wpnonce=%s">Eliminar</a>',
                esc_attr($_REQUEST['page']),
                absint($item['id']),
                wp_create_nonce('delete_popup_' . $item['id'])
            )
        ];

        return sprintf(
            '%1$s %2$s',
            '<strong>' . esc_html($item['title']) . '</strong>',
            $this->row_actions($actions)
        );
    }

    public function get_bulk_actions() {
        return [
            'delete' => 'Eliminar'
        ];
    }
}