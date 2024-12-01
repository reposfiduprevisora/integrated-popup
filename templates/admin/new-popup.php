<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Añadir Nuevo Popup</h1>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'saved'): ?>
        <div class="notice notice-success">
            <p>Popup guardado correctamente.</p>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="popup-form">
        <input type="hidden" name="action" value="save_popup">
        <?php wp_nonce_field('integrated_popup_action', 'integrated_popup_nonce'); ?>    
        <div class="metabox-holder">
            <!-- Información Básica -->
            <div class="postbox">
                <h2 class="hndle"><span>Información Básica</span></h2>
                <div class="inside">
                    <p>
                        <label for="popup_title">Título:</label>
                        <input type="text" id="popup_title" name="popup_title" class="large-text" required>
                    </p>
                    <p>
                        <label for="popup_content">Contenido:</label>
                        <?php 
                        wp_editor('', 'popup_content', array(
                            'media_buttons' => true,
                            'textarea_rows' => 10
                        ));
                        ?>
                    </p>
                </div>
            </div>

            <!-- Diseño -->
            <div class="postbox">
                <h2 class="hndle"><span>Diseño</span></h2>
                <div class="inside">
                    <p>
                        <label>Color de Fondo:</label>
                        <input type="color" name="popup_style[background_color]" value="#ffffff">
                    </p>
                    <p>
                        <label>Color del Texto:</label>
                        <input type="color" name="popup_style[text_color]" value="#000000">
                    </p>
                    <p>
                        <label>Ancho (px):</label>
                        <input type="number" name="popup_style[width]" value="400">
                    </p>
                    <p>
                        <label>Alto (px):</label>
                        <input type="number" name="popup_style[height]" value="300">
                    </p>
                    <p>
                        <label>Posición:</label>
                        <select name="popup_style[position]">
                            <option value="center">Centro</option>
                            <option value="top">Superior</option>
                            <option value="bottom">Inferior</option>
                        </select>
                    </p>
                </div>
            </div>

            <!-- Condiciones de Visualización -->
            <div class="postbox">
                <h2 class="hndle"><span>Condiciones de Visualización</span></h2>
                <div class="inside">
                    <p>
                        <label>
                            <input type="checkbox" name="popup_conditions[show_once]" value="1">
                            Mostrar solo una vez por usuario
                        </label>
                    </p>
                    <p>
                        <label>Retraso de visualización (segundos):</label>
                        <input type="number" name="popup_conditions[delay]" value="0" min="0">
                    </p>
                    
                    <!-- Selector de Páginas Mejorado -->
                    <p>
                        <label>Mostrar en:</label>
                        <select name="popup_conditions[display_type]">
                            <option value="all">Todas las páginas</option>
                            <option value="specific">Páginas específicas</option>
                            <option value="exclude">Excluir páginas específicas</option>
                        </select>
                    </p>
                    
                    <div id="page-selector" style="display: none;">
                        <p>
                            <input type="text" id="page-search" placeholder="Buscar páginas..." class="widefat">
                        </p>
                        <div id="page-list" style="max-height: 200px; overflow-y: auto;">
                            <?php
                            $pages = get_pages(['sort_column' => 'post_title', 'sort_order' => 'ASC']);
                            foreach ($pages as $page) {
                                echo sprintf(
                                    '<label><input type="checkbox" name="popup_conditions[pages][]" value="%d"> %s</label><br>',
                                    $page->ID,
                                    esc_html($page->post_title)
                                );
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                            
        <?php submit_button('Guardar Popup'); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Mostrar/ocultar selector de páginas
    $('select[name="popup_conditions[display_type]"]').change(function() {
        if ($(this).val() === 'specific' || $(this).val() === 'exclude') {
            $('#page-selector').show();
        } else {
            $('#page-selector').hide();
        }
    });

    // Búsqueda de páginas
    $('#page-search').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        $('#page-list label').each(function() {
            var pageTitle = $(this).text().toLowerCase();
            $(this).toggle(pageTitle.includes(searchText));
        });
    });
});
</script>