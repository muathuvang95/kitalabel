<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php echo (get_query_var('et_is_customize_preview', false)) ? 'class="no-scrollbar"' : ''; ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0<?php if ( !get_theme_mod('mobile_scalable', false) ) : ?>, maximum-scale=1.0, user-scalable=0<?php endif; ?>"/>
	<?php wp_head(); ?>

	<!-- Meta Pixel Code Marketz by Arief 270723-->
	<script>
	!function(f,b,e,v,n,t,s)
	{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];
	s.parentNode.insertBefore(t,s)}(window, document,'script',
	'https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '587113042598726');
	fbq('track', 'PageView');
	</script>
	
	<!-- Google tag (gtag.js) Marketz G Conv Tag by Arief 270723 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-11277552109"></script>
	<script>
 	 window.dataLayer = window.dataLayer || [];
 	 function gtag(){dataLayer.push(arguments);}
 	 gtag('js', new Date());
 	 gtag('config', 'AW-11277552109');
	</script>
	
	<!-- Google tag (gtag.js) Marketz G Analytics by Arief 270723 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-FCK80QGKDH"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'G-FCK80QGKDH');
	</script>
	
	<!-- Google Tag Manager Marketz -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-5Z39P2HN');</script>
	<!-- End Google Tag Manager -->
	
	
	<!-- Google Tag Manager NKM 8mar2023 Steve-->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-N8XX9Z5');</script>
	<!-- End Google Tag Manager -->
	
	<!-- Added meta tage by Steve 21 feb 2023 -->
	<meta name="facebook-domain-verification" content="hinrle82q18ycp9wk0fvhkwwouyrg2" />
	<!-- End of meta tag -->
</head>
<?php $mode = etheme_get_option('dark_styles', 0) ? 'dark' : 'light'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited valid use case ?>
<body <?php body_class(); ?> data-mode="<?php echo esc_attr( $mode ); ?>">

	<!-- Meta Pixel Code Marketz (noscript) by Arief 270723-->
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=587113042598726&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Meta Pixel Code -->
	

	<!-- Google Tag Manager (noscript) NKM 8mar2023 steve-->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N8XX9Z5"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5Z39P2HN"
				height="0" width="0" style="display:none;visibility:hidden">
		</iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->

<?php if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
	} else {
		do_action( 'wp_body_open' );
} ?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>

<?php do_action( 'et_after_body', true ); ?>

<div class="template-container">

	<?php
		/**
		 * Hook: etheme_header_before_template_content.
		 *
		 * @hooked etheme_top_panel_content - 10
		 * @hooked etheme_mobile_menu_content - 20
		 *
		 * @version 6.0.0 +
		 * @since 6.0.0 +
		 *
		 */
		do_action( 'etheme_header_before_template_content' );
	 ?>
	<div class="template-content">
		<div class="page-wrapper">
			<?php 
			/**
			 * Hook: etheme_header.
			 *
			 * @hooked etheme_header_content - 10
			 *
			 * @version 6.0.0 +
			 * @since 6.0.0 +
			 *
			 */
            do_action( 'etheme_header_start' );
			if ( get_query_var('et_mobile-optimization', false) ) {
			    if ( get_query_var('is_mobile', false) ) {
				    do_action( 'etheme_header_mobile' );
                }
			    else {
				    do_action( 'etheme_header' );
                }
            }
			else {
				do_action( 'etheme_header' );
				do_action( 'etheme_header_mobile' );
			}
            do_action( 'etheme_header_end' );
			
endif;
