<?php 
/**
 * The template Image layout carousel
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Aora
 * @since Aora 1.0
 */
?>
<div class="single-main-content">
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
	<div class="row">
		<div class="image-mains col-lg-8">
			<?php
				/**
				 * woocommerce_before_single_product_summary hook
				 *
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<div class="information col-lg-4">
			<div class="summary entry-summary ">

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

			</div><!-- .summary -->
		</div>
		
	</div>
</div>
<?php
	/**
	 * woocommerce_after_single_product_summary hook
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
?>