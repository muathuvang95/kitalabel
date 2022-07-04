<?php 
global $product;

?>
<div class="product-block list" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
	<?php 
		/**
		* Hook: aora_woocommerce_before_shop_list_item.
		*
		* @hooked aora_remove_add_to_cart_list_product - 10
		*/
		do_action( 'aora_woocommerce_before_shop_list_item' );
	?>
	<div class="product-content row">
		<div class="block-inner col-lg-3 col-4">
			<?php 
				/**
				* Hook: woocommerce_before_shop_loop_item.
				*
				* @hooked woocommerce_template_loop_product_link_open - 10
				*/
				do_action( 'woocommerce_before_shop_loop_item' );
			?>
			<figure class="image <?php aora_product_block_image_class(); ?>">
				<a title="<?php the_title_attribute(); ?>" href="<?php echo the_permalink(); ?>" class="product-image">
					<?php
						/**
						* woocommerce_before_shop_loop_item_title hook
						*
						* @hooked woocommerce_show_product_loop_sale_flash - 10
						* @hooked woocommerce_template_loop_product_thumbnail - 10
						*/
						do_action( 'woocommerce_before_shop_loop_item_title' );
					?>
				</a>

				<?php 
					/**
					* aora_tbay_after_shop_loop_item_title hook
					*
					* @hooked aora_tbay_add_slider_image - 10
					*/
					do_action( 'aora_tbay_after_shop_loop_item_title' );
				?>
				
			</figure>
		</div>
		<div class="caption col-lg-9 col-8">
			<div class="caption-left">
			<?php
				if( $product->is_on_sale() || $product->is_featured() ) {
					?>
					<div class="top-product-caption">
						<?php
							/**
							* tbay_woocommerce_before_content_product hook
							*
							* @hooked woocommerce_show_product_loop_sale_flash - 10
							*/
							do_action( 'tbay_woocommerce_before_content_product' );
						?>
					</div>
					<?php
				}
			?>
			
				<?php aora_the_product_name(); ?>
				<?php 
					do_action('aora_woo_before_shop_list_caption');
				?>
				<?php
					/**
					* aora_woo_list_caption_left hook
					*
					* @hooked woocommerce_template_loop_rating - 5
					*/
					do_action( 'aora_woo_list_caption_left');
				?>
				
				<?php
				if (!empty(get_the_excerpt()) ) {
					?>
					<div class="woocommerce-product-details__short-description">
						<?php echo get_the_excerpt(); ?>
					</div>
					<?php
				}
				?>
				
				   <?php
					/**
					* aora_woo_list_after_short_description hook
					*
					* @hooked the_woocommerce_variable - 5
					* @hooked list_variable_swatches_pro - 5
					* @hooked aora_tbay_total_sales - 15
					*/
					do_action( 'aora_woo_list_after_short_description');
				?>
				
			</div>
			<div class="caption-right">
				<?php
					/**
					* aora_woo_list_caption_right hook
					*
					* @hooked woocommerce_template_loop_price - 5
					*/
					do_action( 'aora_woo_list_caption_right');
				?>
				<div class="group-buttons clearfix">	
					<?php 
						/**
						* aora_tbay_after_shop_loop_item_title hook
						*
						* @hooked aora_the_yith_wishlist - 20
						* @hooked aora_the_quick_view - 30
						* @hooked aora_the_yith_compare - 40
						*/
						do_action( 'aora_woocommerce_group_buttons', $product->get_id() );
					?>
				</div>

			</div>

			<?php 
				/**
				* Hook: woocommerce_after_shop_loop_item.
				*/
				do_action( 'woocommerce_after_shop_loop_item' );
			?>

		</div>

		
	</div>
	<?php 
		do_action( 'aora_woocommerce_after_shop_list_item' );
	?>
</div>


