<?php
/**
 *
 * The default template for displaying content
 * @since 1.0
 * @version 1.2.0
 *
 */

$columns					= aora_tbay_blog_loop_columns('');
$date 						= aora_tbay_get_boolean_query_var('enable_date');
$author 					= aora_tbay_get_boolean_query_var('enable_author');
$categories 				= aora_tbay_get_boolean_query_var('enable_categories');
$cat_type 					= aora_tbay_categories_blog_type();
$short_descriptions 		= aora_tbay_get_boolean_query_var('enable_short_descriptions');
$read_more 					= aora_tbay_get_boolean_query_var('enable_readmore');
$comment					= aora_tbay_get_boolean_query_var('enable_comment');
$comment_text				= aora_tbay_get_boolean_query_var('enable_comment_text');

$layout_blog   			= apply_filters( 'aora_archive_layout_blog', 10,2 );

$class_main = $class_left = '';

?>
<!-- /post-standard -->
<?php if ( ! is_single() ) : ?>
<div  class="post clearfix <?php echo esc_attr($layout_blog); ?>">
<?php endif; ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class($class_main); ?>>
<?php if ( is_single() ) : ?>
	<div class="entry-single">
<?php endif; ?>
		
        <?php
			if ( is_single() ) : ?>
				
	        	<div class="entry-header">
	        		
	        		<?php
		                if (get_the_title()) {
		                ?>
		                    <h1 class="entry-title">
		                       <?php the_title(); ?>
		                    </h1>
		                <?php
		            	}
		            ?>

					<?php aora_post_meta(array(
						'date'     		=> 1,
						'author'   		=> 1,
						'comments' 		=> 1,
						'comments_text' => 1,
						'tags'     		=> 0,
						'cats'     		=> 1,
						'edit'     		=> 0,
					)); ?>
		            
				</div>
				<?php
				if ( has_post_thumbnail() ) {
					?>
					<figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">

						<?php aora_tbay_post_thumbnail(); ?>
					</figure>
					<?php
				}
				?>
				<div class="post-excerpt entry-content">
					 

					<?php the_content( esc_html__( 'Continue reading', 'aora' ) ); ?>

					<div class="aora-tag-socials-box"><?php do_action('aora_tbay_post_tag_socials') ?></div>

					<?php do_action('aora_tbay_post_bottom') ?>
					
				</div><!-- /entry-content -->
				
				<?php
					wp_link_pages( array(
						'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'aora' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'aora' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					) );
				?>
		<?php endif; ?>
		
		
    <?php if ( ! is_single() ) : ?>
	

		<?php
		 	if ( has_post_thumbnail() ) {
		  	?>
		  	<figure class="entry-thumb <?php echo esc_attr( $class_left ); ?> <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
				   <?php aora_tbay_post_thumbnail();
				   aora_tbay_icon_post_formats(); 
				   ?>
		  	</figure>
		  	<?php
		 	}
		?>
		<div class="entry-content <?php echo esc_attr( $class_left ); ?> <?php echo ( !has_post_thumbnail() ) ? 'no-thumb' : ''; ?>">

			<div class="entry-header">
				<?php aora_post_meta(array(
					'author'     => $author,
					'date'     => $date,
					'tags'     => 0,
					'comments' 		=> $comment,
					'cats'         => $categories,
					'comments_text' 		=> $comment_text,
					'edit'     => 0,
				)); ?>

				<?php aora_post_archive_the_title(); ?>


				<?php if( $short_descriptions ) : ?>
					<?php aora_post_archive_the_short_description(); ?>
				<?php endif; ?>

				<?php if( $read_more ) : ?>
					<?php aora_post_archive_the_read_more(); ?>
				<?php endif; ?>

		    </div>

		</div>

    <?php endif; ?>
    <?php if ( is_single() ) : ?>
</div>
<?php endif; ?>
</article>

<?php if ( ! is_single() ) : ?>
</div>
<?php endif; ?>