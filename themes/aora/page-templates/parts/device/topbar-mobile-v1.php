<?php   
	$class_top_bar 	=  '';

	$always_display_logo 			= aora_tbay_get_config('always_display_logo', false);
	if( !$always_display_logo && !aora_catalog_mode_active() && aora_is_Woocommerce_activated() && (is_product() || is_cart() || is_checkout()) ) {
		$class_top_bar .= ' active-home-icon';
	}
?>
<div class="topbar-device-mobile d-xl-none clearfix <?php echo esc_attr( $class_top_bar ); ?>">

	<?php
		/**
		* aora_before_header_mobile hook
		*/
		do_action( 'aora_before_header_mobile' );

		/**
		* Hook: aora_header_mobile_content.
		*
		* @hooked aora_the_button_mobile_menu - 5
		* @hooked aora_the_logo_mobile - 10
		* @hooked aora_the_title_page_mobile - 10
		*/

		do_action( 'aora_header_mobile_content' );

		/**
		* aora_after_header_mobile hook
		
		* @hooked aora_the_search_header_mobile - 5
		*/		
		
		do_action( 'aora_after_header_mobile' );
	?>
</div>