
<?php
/**
 * aora_woocommerce_before_quick_view hook
 */
do_action( 'aora_woocommerce_before_quick_view' );
?>
<div id="tbay-quick-view-modal" class="singular-shop">
    <div id="product-<?php the_ID(); ?>" <?php post_class('product '); ?>>
    	<div id="tbay-quick-view-content" class="woocommerce single-product no-gutters">
            <div class="image-mains product col-12 col-md-6">
                <?php
                   aora_product_quickview_image();
                ?>
            </div>
            <div class="summary entry-summary col-12 col-md-6">
                <div class="information">
                    <div class="top-single-product">
                        <?php
                            /**
                             * aora_top_single_product hook
                             * @hooked woocommerce_template_single_title -10
                             * @hooked woocommerce_show_product_sale_flash -15
                             * @hooked only_feature_product_label -15
                             * @hooked woocommerce_template_single_rating -20
                             * @hooked aora_tbay_woocommerce_share_box - 50
                             */
                            do_action( 'aora_top_single_product' );
                        ?>
                    </div>
                    <?php
                        /**
                         * woocommerce_single_product_summary hook
                         * @hooked the_product_single_time_countdown - 0
                         * @hooked woocommerce_template_single_price - 10
                         * @hooked excerpt_product_variable - 10
                         * @hooked woocommerce_template_single_excerpt - 20
                        * @hooked woocommerce_template_single_add_to_cart - 30
                        * @hooked woocommerce_template_single_meta - 40
                        */
                        do_action( 'woocommerce_single_product_summary' );
                    ?>
                </div>
            </div>
    	</div>
    </div>
</div>
<?php
/**
 * aora_woocommerce_before_quick_view hook
 */
do_action( 'aora_woocommerce_after_quick_view' );

