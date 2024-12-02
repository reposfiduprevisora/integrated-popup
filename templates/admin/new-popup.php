<?php
if (!defined('ABSPATH')) {
    exit;
}
$popup_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $popup_id > 0;
$popup = $is_edit ? $this->database->get_popup($popup_id) : null;

?>

<div class="wrap">
<h1><?php echo $is_edit ? 'Editar Popup' : 'Añadir Nuevo Popup'; ?></h1>

    <?php if (isset($_GET['message'])): ?>
        <?php if ($_GET['message'] === 'saved'): ?>
            <div class="notice notice-success">
                <p>Popup guardado correctamente.</p>
            </div>
            <?php elseif ($_GET['message'] === 'api_error'): ?>
            <div class="notice notice-warning">
                <p>El popup se guardó localmente pero hubo un error al sincronizar con la API: <?php echo esc_html(urldecode($_GET['error'] ?? 'Error desconocido')); ?></p>
            </div>
        <?php elseif ($_GET['message'] === 'error'): ?>
            <div class="notice notice-error">
                <p>Error al guardar el popup.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="popup-form">
        <input type="hidden" name="action" value="save_popup">
        <?php if ($popup_id): ?>
            <input type="hidden" name="popup_id" value="<?php echo esc_attr($popup_id); ?>">
        <?php endif; ?>
        <?php wp_nonce_field('integrated_popup_action', 'integrated_popup_nonce'); ?>
        
        <div class="metabox-holder">
            <!-- Contenido del Popup -->
            <div class="postbox">
                <h2 class="hndle"><span>Contenido</span></h2>
                <div class="inside">
                    <p>
                        <label for="popup_title">Título</label>
                        <input type="text" 
                               id="popup_title" 
                               name="popup_title" 
                               class="regular-text" 
                               value="<?php echo esc_attr($popup['title'] ?? ''); ?>" 
                               required>
                    </p>
                    <p>
                        <label for="popup_content">Contenido</label>
                        <?php 
                        wp_editor(
                            $popup['content'] ?? '',
                            'popup_content',
                            array(
                                'textarea_name' => 'popup_content',
                                'media_buttons' => true,
                                'textarea_rows' => 10
                            )
                        );
                        ?>
                    </p>
                </div>
            </div>

            <!-- Estilos -->
            <div class="postbox">
                <h2 class="hndle"><span>Estilos</span></h2>
                <div class="inside">
                <?php $style = $popup['style'] ?? array(); ?>
                    <p>
                        <label for="background_color">Color de Fondo</label>
                        <input type="color" 
                               id="background_color" 
                               name="popup_style[background_color]" 
                               value="<?php echo esc_attr($style['background_color'] ?? '#ffffff'); ?>">

                    </p>
                    <p>
                        <label for="text_color">Color de Texto</label>
                        <input type="color" 
                               id="text_color" 
                               name="popup_style[text_color]" 
                               value="<?php echo esc_attr($style['text_color'] ?? '#000000'); ?>">

                    </p>
                    <p>
                        <label for="width">Ancho (px)</label>
                        <input type="number" 
                               id="width" 
                               name="popup_style[width]" 
                               value="<?php echo esc_attr($style['width'] ?? '400'); ?>" 
                               min="200" 
                               max="1200">
                    </p>
                    <p>
                        <label for="height">Alto (px)</label>
                        <input type="number" 
                               id="height" 
                               name="popup_style[height]" 
                               value="<?php echo esc_attr($style['height'] ?? '300'); ?>" 
                               min="200" 
                               max="800">
                    </p>
                </div>
            </div>

            <!-- Vistas Disponibles -->
            <div class="postbox">
                <h2 class="hndle"><span>Vistas Disponibles</span></h2>
                <div class="inside">
                    <div id="popup-views-container">
                    <?php 
                        if (!empty($available_views['data'])) {
                            $selected_views = $popup['views'] ?? array();
                            foreach ($available_views['data'] as $view) {
                                $checked = in_array($view['name'], $selected_views) ? 'checked' : '';
                                ?>
                                <label>
                                    <input type="checkbox" 
                                           name="popup_views[]" 
                                           value="<?php echo esc_attr($view['name']); ?>"
                                           <?php echo $checked; ?>>
                                    <?php echo esc_html($view['name']); ?>
                                </label><br>
                                <?php
                            }
                        } else {
                            echo '<p>No hay vistas disponibles</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Vista Previa -->
            <div class="postbox">
                <h2 class="hndle"><span>Vista Previa</span></h2>
                <div class="inside">
                    <div class="popup-preview">
                        <div class="popup-preview-content"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php submit_button($is_edit ? 'Actualizar Popup' : 'Guardar Popup'); ?>
    </form>
</div>