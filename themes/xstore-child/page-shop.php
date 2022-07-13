<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Aora
 * @since Aora 1.0
 */
/*

*Template Name: 404 Page
*/
get_header();

?>

<section id="main-container" class=" container inner page-404">
	<div id="main-content" class="main-page">

		<section class="error-404">
			<h1 class="title-404"><?php esc_html_e('404','aora') ?></h1>
			<div class="page-content">
				<p class="title"><?php esc_html_e( 'Page not found', 'aora') ?> </p>
				<p class="sub-title"><?php printf( __('The page you’re looking for might have been removed. Back to <a class="back" href="%s">Homepage</a> or search for something else', 'aora'),  home_url( '/' ) ); ?></p>
				<?php get_search_form(); ?>
			</div><!-- .page-content -->
			
		</section><!-- .error-404 -->
	</div>
</section>

<?php get_footer(); ?>