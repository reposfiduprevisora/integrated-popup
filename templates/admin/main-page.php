<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Popups</h1>
    <a href="<?php echo admin_url('admin.php?page=integrated-popup-new'); ?>" class="page-title-action">Añadir Nuevo</a>
    <hr class="wp-header-end">

    <?php
    $popups_list = new IntegratedPopup_List_Table();
    $popups_list->prepare_items();
    ?>

    <form method="post">
        <?php
        $popups_list->search_box('Buscar', 'search_id');
        $popups_list->display();
        ?>
    </form>
</div>