<?php if ( ! defined('AORA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( AORA_INC . '/class-primary-color.php') );

$main_font 				= $theme_primary['main_font']; 
$main_color 			= $theme_primary['color']; 
$main_bg 				= $theme_primary['background'];
$main_border 			= $theme_primary['border'];
$main_top_border 		= $theme_primary['border-top-color'];
$main_right_border 		= $theme_primary['border-right-color'];
$main_bottom_border 	= $theme_primary['border-bottom-color'];
$main_left_border 		= $theme_primary['border-left-color'];


/**
 * ------------------------------------------------------------------------------------------------
 * Prepare CSS selectors for theme settions (colors, borders, typography etc.)
 * ------------------------------------------------------------------------------------------------
 */

$output = array();

/*CustomMain color*/
$output['main_color'] = array( 
	'color' => aora_texttrim($main_color),
	'background-color' => aora_texttrim($main_bg),
	'border-color' => aora_texttrim($main_border),
);
if( !empty($main_top_border) ) {

	$bordertop = array(
		'border-top-color' => aora_texttrim($main_top_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$bordertop);
}
if( !empty($main_right_border) ) {
	
	$borderright = array(
		'border-right-color' => aora_texttrim($main_right_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$borderright);
}
if( !empty($main_bottom_border) ) {
	
	$borderbottom = array(
		'border-bottom-color' => aora_texttrim($main_bottom_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$borderbottom);
}
if( !empty($main_left_border) ) {
	
	$borderleft = array(
		'border-left-color' => aora_texttrim($main_left_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$borderleft);
}
/*Custom Main Color Second*/
$output['main_color_second'] = array( 
	'background-color' => aora_texttrim('.tbay-search-form .button-group , .post .post-type , .woocommerce #shop-now.has-buy-now .tbay-buy-now,.mobile-btn-cart-click > *#tbay-click-buy-now,.tbay-to-top a i,.tbay-dropdown-cart .group-button p.buttons a.button.view-cart,.cart-dropdown .group-button p.buttons a.button.view-cart, .search-form > form .input-group-btn'),
	'border-color' => aora_texttrim('.custom-image-list-categories + .show-all , .product-recently-viewed-main a.btn-readmore,.tbay-dropdown-cart .group-button p.buttons a.button.view-cart,.cart-dropdown .group-button p.buttons a.button.view-cart,.tbay-dropdown-cart .group-button p.buttons a.button.view-cart:hover,.tbay-dropdown-cart .group-button p.buttons a.button.view-cart:focus,.tbay-dropdown-cart .group-button p.buttons a.button.view-cart:active:hover,.cart-dropdown .group-button p.buttons a.button.view-cart:hover,.cart-dropdown .group-button p.buttons a.button.view-cart:focus,.cart-dropdown .group-button p.buttons a.button.view-cart:active:hover')
);
$output['main_color_second_hover'] = array( 
	'background-color' => aora_texttrim('.woocommerce #shop-now.has-buy-now .tbay-buy-now:hover,.woocommerce #shop-now.has-buy-now .tbay-buy-now:focus, .woocommerce #shop-now.has-buy-now .tbay-buy-now:active:hover, .woocommerce #shop-now.has-buy-now .tbay-buy-now:active:focus, .tbay-dropdown-cart .group-button p.buttons a.button.view-cart:hover, .tbay-dropdown-cart .group-button p.buttons a.button.view-cart:focus, .tbay-dropdown-cart .group-button p.buttons a.button.view-cart:active:hover, .cart-dropdown .group-button p.buttons a.button.view-cart:hover, .cart-dropdown .group-button p.buttons a.button.view-cart:focus, .cart-dropdown .group-button p.buttons a.button.view-cart:active:hover'),
	'border-color' => aora_texttrim('.tbay-dropdown-cart .group-button p.buttons a.button.view-cart:hover, .tbay-dropdown-cart .group-button p.buttons a.button.view-cart:focus, .tbay-dropdown-cart .group-button p.buttons a.button.view-cart:active:hover, .cart-dropdown .group-button p.buttons a.button.view-cart:hover, .cart-dropdown .group-button p.buttons a.button.view-cart:focus, .cart-dropdown .group-button p.buttons a.button.view-cart:active:hover')
);
/*End Main Color Second*/

/*Custom Fonts*/
$output['primary-font'] = $main_font;

/*Background hover*/
$output['background_hover']  	= $theme_primary['background_hover'];
/*Tablet*/
$output['tablet_color'] 	 	= $theme_primary['tablet_color'];
$output['tablet_background'] 	= $theme_primary['tablet_background'];
$output['tablet_border'] 		= $theme_primary['tablet_border'];
/*Mobile*/
$output['mobile_color'] 		= $theme_primary['mobile_color'];
$output['mobile_background'] 	= $theme_primary['mobile_background'];
$output['mobile_border'] 		= $theme_primary['mobile_border'];

/*Header Mobile*/
$output['header_mobile_bg'] = array( 
	'background-color' => aora_texttrim('.topbar-device-mobile')
);
$output['header_mobile_color'] = array( 
	'color' => aora_texttrim('.topbar-device-mobile i, .topbar-device-mobile.active-home-icon .topbar-title')
);

return apply_filters( 'aora_get_output', $output);
