
	<?php 
		$_id = aora_tbay_random_key();

		$autocomplete_search 		=  aora_tbay_get_config('mobile_autocomplete_search', true);
		$enable_search_category 	=  aora_tbay_get_config('mobile_enable_search_category', false);
		$enable_image 				=  aora_tbay_get_config('mobile_show_search_product_image', true);
		$enable_price 				=  aora_tbay_get_config('mobile_show_search_product_price', true);
		$search_type 				=  aora_tbay_get_config('mobile_search_type', 'post');
		$number 					=  aora_tbay_get_config('mobile_search_max_number_results', 5);
		$minchars 					=  aora_tbay_get_config('mobile_search_min_chars', 3);

		$text_categories_search 	=  esc_html__('All', 'aora');
		$search_placeholder 		=  '';

		if ($search_type ==='product' ) {
			$search_placeholder 		=  aora_tbay_get_config('mobile_search_placeholder', esc_html__('Search for products...', 'aora'));	
		}else {
			$search_placeholder 		=  aora_tbay_get_config('mobile_search_placeholder', esc_html__('Search for posts...', 'aora'));	
		}


		


		$class_active_ajax = ( $autocomplete_search ) ? 'aora-ajax-search' : '';

		if ( (bool) $enable_search_category ) {
			$class_active_ajax .= ' show-category';
		}
	?>

	<?php $_id = aora_tbay_random_key(); ?>
	<div class="tbay-search-form tbay-search-mobile">
		    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" data-parents=".topbar-device-mobile" class="searchform <?php echo esc_attr($class_active_ajax); ?>" data-appendto=".search-results-<?php echo esc_attr( $_id ); ?>" data-thumbnail="<?php echo esc_attr( $enable_image ); ?>" data-price="<?php echo esc_attr( $enable_price ); ?>" data-minChars="<?php echo esc_attr( $minchars ) ?>" data-post-type="<?php echo esc_attr( $search_type ) ?>" data-count="<?php echo esc_attr( $number ); ?>">
			<div class="form-group">
				<div class="input-group">
					<?php if ( (bool) $enable_search_category ): ?>
						<div class="select-category input-group-addon">
							<?php if ( class_exists( 'WooCommerce' ) && $search_type === 'product' ) :
								$args = array(
									'show_option_none'   => $text_categories_search,
									'hierarchical' => true,
									'id' => 'product-cat-'.$_id,
									'show_uncategorized' => 0
								);
							?> 
							<?php wc_product_dropdown_categories( $args ); ?>
							
							<?php elseif ( $search_type === 'post' ):
								$args = array(
									'show_option_all' => $text_categories_search,
									'hierarchical' => true,
									'show_uncategorized' => 0,
									'name' => 'category',
									'id' => 'blog-cat-'.$_id,
									'class' => 'postform dropdown_product_cat',
								);
							?>
								<?php wp_dropdown_categories( $args ); ?>
							<?php endif; ?>

						</div>
					<?php endif; ?>
					<div class="button-group input-group-addon">
                        <button type="submit" class="button-search btn btn-sm>">
                            <i aria-hidden="true" class="tb-icon tb-icon-search"></i>
                        </button>
                        <div class="tbay-preloader"></div>
                    </div>  
					<input data-style="right" type="text" placeholder="<?php echo esc_attr($search_placeholder); ?>" name="s" required oninvalid="this.setCustomValidity('<?php esc_attr_e('Enter at least 2 characters', 'aora'); ?>')" oninput="setCustomValidity('')" class="tbay-search form-control input-sm"/>

					

					<div class="search-results-wrapper"> 	 
						<div class="aora-search-results search-results-<?php echo esc_attr( $_id ); ?>" data-ajaxsearch="<?php echo esc_attr( $autocomplete_search ) ?>" data-price="<?php echo esc_attr( $enable_price ); ?>"></div>
					</div>
					<input type="hidden" name="post_type" value="<?php echo esc_attr( $search_type ); ?>" class="post_type" />
				</div>
				
			</div>
		</form>
		<div id="search-mobile-nav-cover"></div>

	</div>
