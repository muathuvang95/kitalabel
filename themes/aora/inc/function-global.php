<?php

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Aora 1.0
 */
define( 'AORA_THEME_VERSION', '1.0' );

/**
 * ------------------------------------------------------------------------------------------------
 * Define constants.
 * ------------------------------------------------------------------------------------------------
 */
define( 'AORA_THEME_DIR', 		get_template_directory_uri() );
define( 'AORA_THEMEROOT', 		get_template_directory() );
define( 'AORA_IMAGES', 			AORA_THEME_DIR . '/images' );
define( 'AORA_SCRIPTS', 		AORA_THEME_DIR . '/js' );

define( 'AORA_STYLES', 			AORA_THEME_DIR . '/css' );

define( 'AORA_INC', 				     'inc' );
define( 'AORA_MERLIN', 				     AORA_INC . '/merlin' );
define( 'AORA_CLASSES', 			     AORA_INC . '/classes' );
define( 'AORA_VENDORS', 			     AORA_INC . '/vendors' );
define( 'AORA_ELEMENTOR', 		         AORA_THEMEROOT . '/inc/vendors/elementor' );
define( 'AORA_ELEMENTOR_TEMPLATES',     AORA_THEMEROOT . '/elementor_templates' );
define( 'AORA_PAGE_TEMPLATES',          AORA_THEMEROOT . '/page-templates' );
define( 'AORA_WIDGETS', 			     AORA_INC . '/widgets' );

define( 'AORA_ASSETS', 			         AORA_THEME_DIR . '/inc/assets' );
define( 'AORA_ASSETS_IMAGES', 	         AORA_ASSETS    . '/images' );

define( 'AORA_MIN_JS', 	'' );

if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

function aora_tbay_get_config($name, $default = '') {
	global $aora_options;
    if ( isset($aora_options[$name]) ) {
        return $aora_options[$name];
    }
    return $default;
}

function aora_tbay_get_global_config($name, $default = '') {
	$options = get_option( 'aora_tbay_theme_options', array() );
	if ( isset($options[$name]) ) {
        return $options[$name];
    }
    return $default;
}
