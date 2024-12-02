<?php

if (!defined('ABSPATH')) {
    exit;
}

$database = new IntegratedPopup_Database();
$popups = $database->get_all_popups();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Popups</h1>
    <a href="<?php echo admin_url('admin.php?page=integrated-popup-new'); ?>" class="page-title-action">Añadir Nuevo</a>
    <hr class="wp-header-end">

    <?php if (isset($_GET['message'])): ?>
        <?php if ($_GET['message'] === 'saved'): ?>
            <div class="notice notice-success is-dismissible">
                <p>Popup guardado correctamente.</p>
            </div>
        <?php elseif ($_GET['message'] === 'deleted'): ?>
            <div class="notice notice-success is-dismissible">
                <p>Popup eliminado correctamente.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Vistas Asignadas</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($popups)): ?>
                <tr>
                    <td colspan="5">No hay popups creados aún.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($popups as $popup): ?>
                    <tr>
                        <td><?php echo esc_html($popup['title']); ?></td>
                        <td><?php echo esc_html($popup['status']); ?>
                        <input type="checkbox" 
                                   class="popup-status-toggle" 
                                   data-popup-id="<?php echo esc_attr($popup['id']); ?>"
                                   <?php checked($popup['status'], 1); ?>>                   
                    </td>
                        <td>
                            <?php 
                            $views = is_string($popup['views']) ? json_decode($popup['views'], true) : $popup['views'];
                            if (is_array($views)) {
                                echo count($views) . ' vista(s)';
                                if (!empty($views)) {
                                    echo '<br><small>' . esc_html(implode(', ', $views)) . '</small>';
                                }
                            } else {
                                echo '0 vistas';
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($popup['created_at']))); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=integrated-popup-new&action=edit&id=' . $popup['id']); ?>" class="button button-small">Editar</a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=integrated-popup&action=delete&id=' . $popup['id']), 'delete_popup_' . $popup['id']); ?>" class="button button-small button-link-delete" onclick="return confirm('¿Estás seguro de querer eliminar este popup?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>