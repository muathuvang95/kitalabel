<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( sizeof( $upsells ) == 0 ) {
	return;
}


if( isset($_GET['releated_columns']) ) { 
	$columns = apply_filters( 'loop_shop_columns', 4 );
} else {
	$columns = aora_tbay_get_config('releated_product_columns', 5);
}

$columns_desktopsmall = 5;
$columns_tablet = 3;
$columns_mobile = 2;
$rows = 1;

$show_product_upsells = aora_tbay_get_config('enable_product_upsells', true);

if ( $upsells && $show_product_upsells ) : ?>

	<div class="upsells tbay-element tbay-element-product">
		<h3 class="heading-tbay-title"><?php esc_html_e( 'You may also like&hellip;', 'aora' ) ?></h3>
		<div class="tbay-element-content woocommerce">
		<?php  wc_get_template( 'layout-products/carousel-related.php' , array( 'loops'=>$upsells,'rows' => $rows, 'pagi_type' => 'no', 'nav_type' => 'yes','columns'=>$columns,'screen_desktop'=>$columns,'screen_desktopsmall'=>$columns_desktopsmall,'screen_tablet'=>$columns_tablet,'screen_mobile'=>$columns_mobile ) ); ?>
		</div>
	</div>

<?php endif;

wp_reset_postdata();
