<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="integrated-popup" class="popup-container" style="display: none;">
    <div class="popup-content" style="
        background-color: <?php echo esc_attr($popup_style['background_color']); ?>;
        color: <?php echo esc_attr($popup_style['text_color']); ?>;
        width: <?php echo esc_attr($popup_style['width']); ?>px;
        height: <?php echo esc_attr($popup_style['height']); ?>px;
    ">
        <button class="popup-close">&times;</button>
        <div class="popup-text">
            <?php echo wp_kses_post($popup_text); ?>
        </div>
    </div>
</div>