<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action( 'woocommerce_before_cart' ); ?>

<div class="row">
	<div class="col-md-8"> 

		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

		<?php do_action( 'woocommerce_before_cart_table' ); ?>
		<div class="table-responsive">
		<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents nb-custom-shop_table" cellspacing="0">
			<thead>
				<tr>
					<th></th>
					<th class="product-details"><?php esc_html_e( 'Product', 'xstore' ); ?></th>
					<th class="product-price"><?php esc_html_e( 'Name', 'xstore' ); ?></th>
					<th class="nb-col-modile-hiden"></th>
					<th class="product-quantity"><?php esc_html_e( 'Quantity', 'xstore' ); ?></th>
					<th class="nb-col-modile-hiden"></th>
				</tr>
			</thead>
			<tbody>
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

						$kita_hook_variant = false;
						$custom_upload = array();
						if( isset( $cart_item['nbo_meta'] ) ) {
					        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) );
					        if( !empty( $fields['combination']['combination_selected']) ) {
					        	$kita_hook_variant = true;
					        }
					        if(isset($cart_item['nbo_meta']['option_price']) && $cart_item['nbo_meta']['option_price']['fields'] && is_array($cart_item['nbo_meta']['option_price']['fields'])) {

					        	foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $field)  {
					        		if(isset($field['is_custom_upload'])) {
					        			$custom_upload = $field;
					        			$kita_hook_variant = true;
					        		}
					        	}
					        }
					    }
					    if($kita_hook_variant) {
					    	echo apply_filters( 'nb_custom_after_cart_item_name', '' , $cart_item, $cart_item_key, $custom_upload); //cutom kitalabel
					    }
						?>
						<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

							<td>
								<?php
                            	echo apply_filters( 'woocommerce_cart_item_remove_link',
                            		sprintf(
		                            	'<a href="%s" class="remove-item text-underline" title="%s">%s</a>',
		                            	esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
		                            	esc_html__( 'Remove this item', 'xstore' ),
		                            	'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/></svg>'
	                            	),
	                            $cart_item_key );
	                            ?>
                            </td>
							<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'xstore' ); ?>">
		                        <div class="product-thumbnail">
		                            <?php
		                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

		                                if ( ! $_product->is_visible() || ! $product_permalink || $kita_hook_variant){
		                                	echo '<div class="nb-image">';
		                                    echo wp_kses_post( $thumbnail );
		                                    echo '</div>';
		                                } else {
		                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
		                                }
		                            ?>
		                        </div>
							</td>
							<td class="product-details">
		                        <div class="cart-item-details">
		                            <?php
		                            if ( ! $_product->is_visible() || ! $product_permalink  ){
			                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) );
		                            } else {
			                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="product-title">%s</a>', esc_url( $product_permalink ) , $_product->get_name() ), $cart_item, $cart_item_key ) );
		                            }

		                            do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

		                            // Meta data
									//if (  etheme_get_option( 'enable_swatch' ) && class_exists( 'St_Woo_Swatches_Base' ) ) {
									//	$Swatches = new St_Woo_Swatches_Base();
									//	echo //$Swatches->st_wc_get_formatted_cart_item_data( $cart_item );
									//} else {
										echo wc_get_formatted_cart_item_data( $cart_item );
									//}

		                    // Backorder notification
		                    if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
		                         	echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'xstore' ) . '</p>', $product_id ) );
		                            ?>
		                            <span class="mobile-price">
		                            	<?php
											echo (int) $cart_item['quantity'] . ' x ' . apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
										?>
		                            </span>
		                        </div>
							</td>

							<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'xstore' ); ?>">
								<?php
									echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
								?>
							</td>

							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'xstore' ); ?>">
								<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input( array(
											'input_name'  => "cart[{$cart_item_key}][qty]",
											'input_value' => $cart_item['quantity'],
											'max_value'   => $_product->get_max_purchase_quantity(),
											'min_value'   => '0',
											'product_name'  => $_product->get_name(),
										), $_product, false );
									}

									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
								?>
							</td>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'xstore' ); ?>">
								<?php
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );

									if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['wait_price'] ) && $cart_item['nbo_meta']['wait_price']  ) {
										$order_again = !empty( $cart_item['nbo_meta']['order_again'] ) ? $cart_item['nbo_meta']['order_again'] : '';
										echo '<div><span>Harga Total:</span><b>"Menunggu Harga"</b></div><div style="cursor: pointer;" class="button nb-create-new-quote" data-item-key="'.$cart_item_key.'" data-order-again="'.$order_again.'"><div>Request quote</div></div>';
									}
								?>
							</td>
						</tr>
						<?php
					}
				}

				do_action( 'woocommerce_cart_contents' );
				?>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</tbody>
		</table>
		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>

				<div class="actions clearfix">
		<?php $cols = 12; ?>
		<?php if ( wc_coupons_enabled() ) : $cols = 6; ?>
			<div class="col-md-<?php echo esc_attr($cols); ?> col-sm-<?php echo esc_attr($cols); ?> text-left mob-center">
				<form class="checkout_coupon" method="post">
				<a href="#" class="et-open to_open-coupon"><i class="et-icon et-coupon"></i><?php esc_html_e('Enter your promotional code', 'xstore'); ?></a>
					<div class="coupon">

						<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_html_e( 'Coupon code', 'xstore' ); ?>" />
						<!-- <input type="submit" class="btn" name="apply_coupon" value="&#9166;" /> -->
						<input type="submit" class="btn" name="apply_coupon" value="<?php esc_attr_e('OK', 'xstore'); ?>" />

						<?php do_action('woocommerce_cart_coupon'); ?>

					</div>
				</form>
			</div>
			<?php endif; ?>
			<div class="col-md-<?php echo esc_attr($cols); ?> col-sm-<?php echo esc_attr($cols); ?> mob-center">
				<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
					<a class="return-shop" href="<?php echo get_permalink(wc_get_page_id('shop')); ?>"><i class="et-icon et-<?php echo (get_query_var('et_is-rtl', false)) ? 'right' : 'left'; ?>-arrow"></i><?php esc_html_e('Return to shop', 'xstore') ?></a>
				<?php endif; ?>
                <a class="clear-cart">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve" width=".8em" height=".8em" fill="currentColor">
                        <g>
                            <path d="M8.8916016,6.215332C8.8803711,6.2133789,8.8735352,6.2143555,8.8666992,6.2148438
                                C8.5517578,6.2197266,8.2988281,6.4799805,8.3037109,6.7944336v13.0141602
                                c-0.0024414,0.1523438,0.0551758,0.296875,0.1621094,0.40625s0.25,0.171875,0.4033203,0.1738281h0.0078125
                                c0.3115234,0,0.5683594-0.2519531,0.5722656-0.5605469c0.0004883-0.0087891,0.0004883-0.0175781,0-0.0195312V6.7954102
                                c0.0019531-0.152832-0.0551758-0.2973633-0.1616211-0.4077148C9.1806641,6.2783203,9.0380859,6.2167969,8.8916016,6.215332z"></path>
                            <path d="M20.8701172,2.578125c-0.0117188-0.0009766-0.0195312-0.0009766-0.0214844,0l-0.9433594,0.0004883
                                c-0.0735035,0-0.1163521-0.0004883-0.1796875-0.0004883h-4.0292969V1.5893555c0-0.8901367-0.7246094-1.6142578-1.6142578-1.6142578
                                H9.9179688c-0.8901367,0-1.6142578,0.7241211-1.6142578,1.6142578V2.578125L4.2807617,2.5786133
                                c-0.0660129,0-0.106863-0.0004883-0.1723633-0.0004883H3.1420898c-0.1494141,0-0.2905273,0.0571289-0.3984375,0.1611328
                                c-0.1098633,0.1074219-0.1713867,0.2504883-0.1733398,0.402832c-0.0024414,0.152832,0.0551758,0.2978516,0.1621094,0.4077148
                                s0.25,0.171875,0.4033203,0.1738281h0.4833984v18.6875c0,0.8896484,0.7241211,1.6142578,1.6137695,1.6142578h13.5336914
                                c0.890625,0,1.6152344-0.7246094,1.6152344-1.6142578v-18.6875h0.4736328c0.1513672,0,0.2939453-0.0576172,0.4003906-0.1621094
                                c0.109375-0.1064453,0.171875-0.2495117,0.1738281-0.402832C21.4335938,2.8427734,21.1816406,2.5820312,20.8701172,2.578125z
                                 M9.4492188,2.578125V1.5893555c0-0.2583008,0.2104492-0.46875,0.46875-0.46875h4.1640625
                                c0.2578125,0,0.4677734,0.2104492,0.4677734,0.46875V2.578125H9.4492188z M19.2353516,3.7236328v18.6875
                                c0,0.2578125-0.2099609,0.4677734-0.46875,0.4677734H5.2329102c-0.2583008,0-0.4682617-0.2099609-0.4682617-0.4677734v-18.6875
                                h4.0161133c0.0634766,0.0097656,0.1254883,0.0097656,0.1782227,0h6.0683594c0.0644531,0.0097656,0.1259766,0.0097656,0.1787109,0
                                H19.2353516z"></path>
                            <path d="M12.0146484,6.215332c-0.0112305-0.0019531-0.0180664-0.0009766-0.0249023-0.0004883
                                c-0.3149414,0.0048828-0.5673828,0.2651367-0.5625,0.5795898v13.0141602
                                c-0.0019531,0.1523438,0.0551758,0.296875,0.1616211,0.4072266c0.105957,0.109375,0.2490234,0.1699219,0.4033203,0.1728516H12
                                c0.3115234,0,0.5683594-0.2539062,0.5727539-0.5654297V6.7954102c0.0019531-0.1533203-0.0551758-0.2978516-0.1616211-0.4077148
                                C12.3041992,6.2783203,12.1616211,6.2167969,12.0146484,6.215332z"></path>
                            <path d="M14.5498047,6.7944336v13.0141602c-0.0019531,0.1523438,0.0566406,0.296875,0.1630859,0.40625
                                c0.1064453,0.1103516,0.25,0.171875,0.4033203,0.1738281h0.0068359c0.3115234,0,0.5683594-0.2539062,0.5732422-0.5654297V6.7954102
                                c0.0019531-0.1542969-0.0556641-0.2988281-0.1621094-0.4077148s-0.2470703-0.1699219-0.3974609-0.1728516
                                c-0.0078125-0.0019531-0.0175781-0.0019531-0.0234375,0C14.7988281,6.2197266,14.5458984,6.4799805,14.5498047,6.7944336z"></path>
                        </g>
                    </svg>
                <?php esc_html_e('Clear shopping cart', 'xstore'); ?></a>
                <button type="submit" class="btn gray medium bordered" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'xstore' ); ?>"><?php esc_html_e( 'Update cart', 'xstore' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
				<?php do_action( 'woocommerce_cart_actions' ); ?>
			</div>
		</div>
		
		</form>
	</div>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

	<div class="col-md-4 cart-order-details">
		<div class="cart-collaterals">
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
		</div>
		<?php  if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('cart-area'))): ?>
        <?php endif; ?>
	</div>
</div>
<!-- end row -->

<?php woocommerce_cross_sell_display(); ?>

<?php do_action( 'woocommerce_after_cart' ); ?>