<?php
/*
* Template Name: Kita Usercase Page
*/
get_header();

$sidebar_configs = aora_tbay_get_page_layout_configs();

$class_row = ( get_post_meta( $post->ID, 'tbay_page_layout', true ) === 'main-right' ) ? 'flex-row-reverse' : '';

?>

<section id="main-container" class="<?php echo esc_attr( apply_filters('aora_tbay_page_content_class', 'container') );?>">
	<div class="row <?php echo esc_attr($class_row); ?>">
		<?php if ( isset($sidebar_configs['sidebar']) && is_active_sidebar($sidebar_configs['sidebar']['id']) ) : ?>
		<div class="<?php echo esc_attr($sidebar_configs['sidebar']['class']) ;?>">
		  	<aside class="sidebar" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
		   		<?php dynamic_sidebar( $sidebar_configs['sidebar']['id'] ); ?>
		  	</aside>
		</div>
		<?php endif; ?>
		<div id="main-content" class="main-page <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<div id="main" class="site-main">
				<?php
				// Start the loop.
				while ( have_posts() ) : the_post();
				
					the_content();

					$per_page 	= 8;
					$total_tags = count(get_terms( array('taxonomy' => 'template_tag','hide_empty' => false)));
					$total_page = ceil($total_tags / $per_page);

					$template_tags = get_terms( array(
						'taxonomy' => 'template_tag',
						'hide_empty' => false,
						'number' => $per_page,
						'offset' => 0,
					) );
					?>
					<div class="template-tags-wrapper">
						<?php 
						foreach( $template_tags as $index => $tag ):
							$tag_thumbnail_id 	= get_term_meta( $tag->term_id, 'thumbnail_id', true);
							$tag_image 			= wp_get_attachment_image($tag_thumbnail_id, 'thumbnail');
							$tag_url 			= site_url('templates?tag=' . $tag->term_id);
						?>
							<div class="template-tag-item">
								<a href="<?php echo $tag_url;?>"><?php echo $tag_image;?></a>
								<a href="<?php echo $tag_url;?>"><h5><?php echo $tag->name;?></h5></a>
								<div class="tag-description-wrapper">
									<p class="tag-description"><?php echo $tag->description;?></p>
								</div>
							</div>
						<?php endforeach;?>

					</div>
						<?php if($total_page > 1):?>
							<div class="kita-load-more-wrapper">
								<button class="kita-load-more-btn" data-current-page="1" data-total-page="<?php echo $total_page;?>" data-per-page="<?php echo $per_page;?>">
									<i class="fas fa-spinner fa-spin"></i>
									<span class="load-more-btn-text"><?php esc_html_e( 'Lihat lagi', 'aora' )?></span>
								</button>
							</div>
						<?php endif;?>
					<?php
 

				// End the loop.
				endwhile;
				?>
			</div><!-- .site-main -->

		</div><!-- .content-area -->
	</div>
</section>
<?php 
	if($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
		echo do_shortcode( '[pafe-template id="8396"]' );
	}
	else {
		echo do_shortcode( '[pafe-template id="9593"]' );
	}
?>
<?php get_footer(); ?>