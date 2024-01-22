<?php
/**
 * NBO Quick view template
 */
if (!defined('ABSPATH')) {
    exit;
}
global $product, $post, $woocommerce;
do_action('nbo_quick_view_before_single_product');
?>
<div class="woocommerce quick-view" id="nb-custom-design-quick-view">
    <div class="product" id="product-<?php echo $post->ID; ?>">
        <?php Kitalabel_Order_Label::instance()->option_fields($post->ID , 'quick_view'); ?>
    </div>
</div>