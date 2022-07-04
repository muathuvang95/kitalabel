<?php if ( ! defined('AORA_THEME_DIR')) exit('No direct script access allowed');
/**
 * Custom template tags for aora
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Aora
 * @since Aora 1.0
 */

if ( ! function_exists( 'aora_tbay_comment_nav' ) ) :
/**
 * Display navigation to next/previous comments when applicable.
 *
 * @since Aora 1.0
 */
function aora_tbay_comment_nav() {
	// Are there comments to navigate through?
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
	?>
	<nav class="navigation comment-navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'aora' ); ?></h2>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'aora' ) ) ) :
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				endif;

				if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'aora' ) ) ) :
					printf( '<div class="nav-next">%s</div>', $next_link );
				endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .comment-navigation -->
	<?php
	endif;
}
endif;

if ( ! function_exists( 'aora_edit_link' ) ) :
	/**
	 * Returns an accessibility-friendly link to edit a post or page.
	 *
	 * This also gives us a little context about what exactly we're editing
	 * (post or page?) so that users understand a bit more where they are in terms
	 * of the template hierarchy and their content. Helpful when/if the single-page
	 * layout with multiple posts/pages shown gets confusing.
	 */
	function aora_edit_link() { 
		edit_post_link(
			sprintf(
				/* translators: %s: Post title. */
				__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'aora' ),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;


if ( ! function_exists( 'aora_tbay_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the categories, tags.
 *
 * @since Aora 1.0
 */
function aora_tbay_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		printf( '<span class="sticky-post">%s</span>', esc_html__( 'Featured', 'aora' ) );
	}

	$format = get_post_format();
	if ( current_theme_supports( 'post-formats', $format ) ) {
		printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
			sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'aora' ) ),
			esc_url( get_post_format_link( $format ) ),
			get_post_format_string( $format )
		);
	}

	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			get_the_date(),
			esc_attr( get_the_modified_date( 'c' ) ),
			get_the_modified_date()
		);

		printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
			_x( 'Posted on', 'Used before publish date.', 'aora' ),
			esc_url( get_permalink() ),
			$time_string
		);
	}

	if ( 'post' == get_post_type() ) {
		if ( is_singular() || is_multi_author() ) {
			printf( '<span class="byline"><span class="author vcard"><span class="screen-reader-text">%1$s </span><a class="url fn n" href="%2$s">%3$s</a></span></span>',
				_x( 'Author', 'Used before post author name.', 'aora' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);
		}

		$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'aora' ) );
		if ( $categories_list && aora_tbay_categorized_blog() ) {
			printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Categories', 'Used before category names.', 'aora' ),
				$categories_list
			);
		}

		$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'aora' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Tags', 'Used before tag names.', 'aora' ),
				$tags_list
			);
		}
	}

	if ( is_attachment() && wp_attachment_is_image() ) {
		// Retrieve attachment metadata.
		$metadata = wp_get_attachment_metadata();

		printf( '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s">%3$s &times; %4$s</a></span>',
			_x( 'Full size', 'Used before full size attachment link.', 'aora' ),
			esc_url( wp_get_attachment_url() ),
			$metadata['width'],
			$metadata['height']
		);
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( esc_html__( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'aora' ), get_the_title() ) );
		echo '</span>';
	}
}
endif;

/**
 * Determine whether blog/site has more than one category.
 *
 * @since Aora 1.0
 *
 * @return bool True of there is more than one category, false otherwise.
 */
function aora_tbay_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'aora_tbay_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'aora_tbay_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so aora_tbay_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so aora_tbay_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in {@see aora_tbay_categorized_blog()}.
 *
 * @since Aora 1.0
 */
function aora_tbay_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'aora_tbay_categories' );
}
add_action( 'edit_category', 'aora_tbay_category_transient_flusher' );
add_action( 'save_post',     'aora_tbay_category_transient_flusher' );

if ( ! function_exists( 'aora_tbay_post_thumbnail' ) ) {
	function aora_tbay_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
		?>

		<div class="post-thumbnail">
			<?php 
				the_post_thumbnail('full');
			?>
		</div><!-- .post-thumbnail -->

		<?php else : ?>

			<?php 
				$image_size 	= apply_filters( 'aora_archive_blog_size_image', 'medium' );
			?>

		<a class="post-<?php echo esc_attr($image_size); ?>" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php
				the_post_thumbnail( $image_size, array( 'alt' => get_the_title() ) );
			?>
		</a>

		<?php endif; // End is_singular()
	}
}

if ( ! function_exists( 'aora_tbay_post_categories' ) ) {
	function aora_tbay_post_categories( $post ) {
		$cat = wp_get_post_categories( $post->ID );
		$k   = count( $cat );
		foreach ( $cat as $c ) {
			$categories = get_category( $c );
			$k -= 1;
			if ( $k == 0 ) {
				echo '<a href="' . esc_url( get_category_link( $categories->term_id ) ) . '" class="categories-name">' . esc_html($categories->name) . '</a>';
			} else {
				echo '<a href="' . esc_url( get_category_link( $categories->term_id ) ) . '" class="categories-name">' . esc_html($categories->name) . ', </a>';
			}
		}
	}
}

if ( ! function_exists( 'aora_tbay_short_top_meta' ) ) {
	function aora_tbay_short_top_meta( $post ) {
		
		?>
        <div class="entry-create">
            <span class="entry-date"><?php echo aora_time_link(); ?></span>
            <span class="author"><?php esc_html_e('/ By ', 'aora'); echo get_the_author_posts_link(); ?></span>
        </div>
		<?php
	}
}

if ( ! function_exists( 'aora_tbay_get_link_url' ) ) :
/**
 * Return the post URL.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since Aora 1.0
 *
 * @see get_url_in_content()
 *
 * @return string The Link format URL.
 */
function aora_tbay_get_link_url() {
	$has_url = get_url_in_content( get_the_content() );

	return $has_url ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;

if ( ! function_exists( 'aora_tbay_excerpt_more' ) && ! is_admin() ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Continue reading' link.
 *
 * @since Aora 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function aora_tbay_excerpt_more( $more ) {
	$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( esc_html__( 'Continue reading %s', 'aora' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'aora_tbay_excerpt_more' );
endif;

/**
 * ------------------------------------------------------------------------------------------------
 * Display meta information for a specific post
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'aora_post_meta' )) {
	function aora_post_meta( $atts = array() ) {

		$text_domain =   esc_html__(' comments','aora');    
		if( get_comments_number() == 1) {
		    $text_domain = esc_html__(' comment','aora');
		}

		extract(shortcode_atts(array(
			'date'    	 	=> 1,
			'cats'     		=> 1,
			'author'   		=> 0,
			'author_img'    => 0,
			'comments' 		=> 0,
			'comments_text' => 0,
			'tags'     		=> 0,
			'edit'     		=> 1,
		), $atts));
		?>
		<?php if( get_post_type() === 'post' ): ?>
			<ul class="entry-meta-list">
				<?php 
					if( !$comments_text ) $text_domain ='';
				?>

				<?php // Author ?>
				<?php if ($author == 1): ?>
					<li class="entry-author">
						<?php echo get_avatar(aora_tbay_get_id_author_post(), 70); ?>
						<span><?php esc_html_e('By ', 'aora'); ?></span>
						<?php echo get_the_author_posts_link(); ?></li>
				<?php endif ?>

				<?php // Date ?>
				<?php if( $date == 1): ?>
					<li class="entry-date"><i class="tb-icon tb-icon-clock"></i><?php echo aora_time_link(); ?></li>
				<?php endif ?>

				<?php // Comments ?>
				<?php if( $comments && comments_open() ): ?>
					<li class="comments-link">
						<i class="tb-icon tb-icon-comment-alt"></i>
						<?php comments_popup_link( 
							'0' .'<span>'. $text_domain .'</span>',  
							'1' .'<span>'. $text_domain .'</span>',  
							'%' .'<span>'. $text_domain .'</span>'
						); ?>
					</li>
				<?php endif; ?>

				<?php // Tags ?>
				<?php if(get_the_tag_list( '', ', ' ) && $tags == 1): ?>
					<li class="entry-tags"><?php echo get_the_tag_list( '', ', ' ); ?></li>
				<?php endif; ?>

				<?php // Edit link ?>
				<?php if( is_user_logged_in() && $edit == 1 ): ?>
					<li class="edit-link"><?php edit_post_link( esc_html__( 'Edit', 'aora' )); ?></li>
				<?php endif; ?>

				<?php // Edit categories ?>
				<?php if(get_the_category_list() && $cats == 1): ?>
		            <li class="entry-category"><?php aora_the_post_category_full() ?></li>
		        <?php endif; ?>
			</ul>
			<?php endif; ?>
		<?php
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * The archive title
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'aora_post_archive_the_title' )) {
	function aora_post_archive_the_title() {
	    if (get_the_title()) {
	    ?>
	        <h3 class="entry-title">
			   <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	        </h3>
	    <?php
		}	            

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * The short description archive
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'aora_post_archive_the_short_description' )) {
	function aora_post_archive_the_short_description() {
		if (empty(get_the_excerpt()) || get_the_excerpt() == '&nbsp;') {
            return;
        }
		if ( has_excerpt()) { ?>
			<div class="entry-description">
				<?php echo aora_tbay_substring(get_the_excerpt(), 30, '[...]' ); ?>
			</div>
		<?php } else {
			?>
				<div class="entry-description">
					<?php echo aora_tbay_substring(get_the_content(), 25, '...' ); ?>
				</div>
			<?php
		}         

	}
} 
/**
 * ------------------------------------------------------------------------------------------------
 * The read more archive
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'aora_post_archive_the_read_more' )) {
	function aora_post_archive_the_read_more() {
		?>
		<?php $custom_readmore	= aora_tbay_get_config('text_readmore', esc_html__('Continue Reading', 'aora')); ?>
			<div class="more">
				<a href="<?php the_permalink(); ?>" class="readmore" title="<?php echo trim($custom_readmore); ?>"><?php echo trim($custom_readmore); ?></a>
			</div>
		<?php
	}
}

if ( ! function_exists( 'aora_search_only_title' ) ) {
	function aora_search_only_title( $search, $wp_query ) {
	    if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
	        global $wpdb;

	        $q = $wp_query->query_vars;
	        $n = ! empty( $q['exact'] ) ? '' : '%';

	        $search = array();

	        foreach ( ( array ) $q['search_terms'] as $term )
	            $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

	        if ( ! is_user_logged_in() )
	            $search[] = "$wpdb->posts.post_password = ''";

	        $search = ' AND ' . implode( ' AND ', $search );
	    }

	    return $search;
	}
}

if ( ! function_exists( 'aora_search_options' ) ) {
	function aora_search_options( $search, $wp_query ) {

		if( !(defined('TBAY_ELEMENTOR_ACTIVED') && TBAY_ELEMENTOR_ACTIVED) ) return $search;

		$where = aora_search_only_title($search, $wp_query);

		return $where; 
	}
	add_filter( 'posts_search', 'aora_search_options', 20, 2 );
}

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Fire the wp_body_open action.
	 *
	 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
	 *
	 * @since Aora 1.5
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening <body> tag.
		 *
		 * @since Aora 1.5
		 */
		do_action( 'wp_body_open' );
	}
endif;


if ( ! function_exists( 'aora_time_link' ) ) :
/**
 * Gets a nicely formatted string for the published date.
 */
function aora_time_link() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	$time_string = sprintf( $time_string,
		get_the_date( DATE_W3C ), 
		get_the_date(),
		get_the_modified_date( DATE_W3C ),
		get_the_modified_date()
	);

	// Wrap the time string in a link, and preface it with 'Posted on'.
	return sprintf( 
		/* translators: %s: post date */
		__( '%sPosted on%s %s', 'aora' ), '<span class="screen-reader-text">', '</span>',
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . trim( $time_string ) . '</a>'
	);
}
endif;

if ( ! function_exists( 'aora_tbay_icon_post_formats' ) ) {
	function aora_tbay_icon_post_formats() {
			$post_id 			= get_the_ID();
			$post_format 	= get_post_format($post_id);
			echo '<span class="post-type">';
			switch ($post_format) {			
				case 'aside':
					echo '<i class="tb-icon tb-icon-type-image"></i>';
					break;
				case 'audio':
					echo '<i class="tb-icon tb-icon-type-audio"></i>';
					break;				
				case 'gallery':
					echo '<i class="tb-icon tb-icon-pictures"></i>';
					break;	
				case 'image':
					echo '<i class="tb-icon tb-icon-type-image"></i>';
					break;									
				case 'video':
					echo '<i class="tb-icon tb-icon-type-video"></i>';
					break;	
				default:
					echo '<i class="tb-icon tb-icon-type-image"></i>';
					break;			
			}
			echo '</span>';

	}
}