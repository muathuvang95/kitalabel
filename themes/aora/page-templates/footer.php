<?php

$footer 	= apply_filters( 'aora_tbay_get_footer_layout', 'footer_default' );

?>

	</div><!-- .site-content -->
	<?php if ( aora_tbay_active_newsletter_sidebar() ) : ?>
		<div id="newsletter-popup" class="newsletter-popup">
			<?php dynamic_sidebar( 'newsletter-popup' ); ?>
		</div>
	<?php endif; ?>
	
	<footer id="tbay-footer" <?php aora_tbay_footer_class();?>>
		<?php if ( $footer != 'footer_default' ): ?>
			
			<?php aora_tbay_display_footer_builder($footer); ?>

		<?php else: ?> 
			
			<?php get_template_part( 'page-templates/footer-default' ); ?>
			
		<?php endif; ?>			
	</footer><!-- .site-footer -->
	
	<?php 

	$_id = aora_tbay_random_key();

	?>

	<?php
	if ( aora_tbay_get_config('back_to_top') ) { ?>
		<div class="tbay-to-top">
			<a href="javascript:void(0);" id="back-to-top">
				<i class="tb-icon tb-icon-chevron-up"></i>
			</a>
		</div>
	<?php
	}
	?>

	<?php
	if ( aora_tbay_get_config('mobile_back_to_top') ) { ?>
		<div class="tbay-to-top-mobile tbay-to-top">

			<div class="more-to-top">
			
				<a href="javascript:void(0);" id="back-to-top-mobile">
					<i class="tb-icon tb-icon-chevron-up"></i>
				</a>
				
			</div>
		</div>
		
		
	<?php
	}
	?>
	
	

</div><!-- .site -->

<?php wp_footer(); ?>

</body>
</html>