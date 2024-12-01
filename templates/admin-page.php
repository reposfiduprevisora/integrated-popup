<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('integrated_popup_action', 'integrated_popup_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="popup_text">Popup Text</label>
                </th>
                <td>
                    <textarea 
                        id="popup_text" 
                        name="popup_text" 
                        rows="5" 
                        cols="50"
                    ><?php echo esc_textarea(get_option('popup_text')); ?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row">Styles</th>
                <td>
                    <?php $style = json_decode(get_option('popup_style'), true); ?>
                    <p>
                        <label>Background Color:</label>
                        <input 
                            type="color" 
                            name="popup_style[background_color]" 
                            value="<?php echo esc_attr($style['background_color'] ?? '#ffffff'); ?>"
                        >
                    </p>
                    <p>
                        <label>Text Color:</label>
                        <input 
                            type="color" 
                            name="popup_style[text_color]" 
                            value="<?php echo esc_attr($style['text_color'] ?? '#000000'); ?>"
                        >
                    </p>
                    <p>
                        <label>Width (px):</label>
                        <input 
                            type="number" 
                            name="popup_style[width]" 
                            value="<?php echo esc_attr($style['width'] ?? '400'); ?>"
                        >
                    </p>
                    <p>
                        <label>Height (px):</label>
                        <input 
                            type="number" 
                            name="popup_style[height]" 
                            value="<?php echo esc_attr($style['height'] ?? '300'); ?>"
                        >
                    </p>
                    <p>
                        <label>Position:</label>
                        <select name="popup_style[position]">
                            <option value="center" <?php selected($style['position'] ?? 'center', 'center'); ?>>Center</option>
                            <option value="top" <?php selected($style['position'] ?? 'center', 'top'); ?>>Top</option>
                            <option value="bottom" <?php selected($style['position'] ?? 'center', 'bottom'); ?>>Bottom</option>
                        </select>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">Display Conditions</th>
                <td>
                    <?php $conditions = json_decode(get_option('popup_conditions'), true); ?>
                    <p>
                        <label>
                            <input 
                                type="checkbox" 
                                name="popup_conditions[show_once]" 
                                value="1"
                                <?php checked($conditions['show_once'] ?? false); ?>
                            >
                            Show only once per user
                        </label>
                    </p>
                    <p>
                        <label>Display delay (seconds):</label>
                        <input 
                            type="number" 
                            name="popup_conditions[delay]" 
                            value="<?php echo esc_attr($conditions['delay'] ?? '0'); ?>"
                        >
                    </p>
                    <p>
                        <label>Show on specific pages:</label>
                        <?php
                        $pages = get_pages();
                        foreach ($pages as $page) {
                            ?>
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="popup_conditions[pages][]" 
                                    value="<?php echo $page->ID; ?>"
                                    <?php checked(in_array($page->ID, $conditions['pages'] ?? array())); ?>
                                >
                                <?php echo esc_html($page->post_title); ?>
                            </label><br>
                            <?php
                        }
                        ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>