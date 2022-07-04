<?php if ( ! defined('AORA_THEME_DIR')) exit('No direct script access allowed');
/**
 * Aora woocommerce Template Hooks
 *
 * Action/filter hooks used for Aora woocommerce functions/templates.
 *
 */


/**
 * Aora Header Mobile Content.
 *
 * @see aora_the_button_mobile_menu()
 * @see aora_the_logo_mobile()
 */
add_action( 'aora_header_mobile_content', 'aora_the_button_mobile_menu', 5 );
add_action( 'aora_header_mobile_content', 'aora_the_icon_home_page_mobile', 10 );
add_action( 'aora_header_mobile_content', 'aora_the_logo_mobile', 15 );
add_action( 'aora_header_mobile_content', 'aora_the_icon_mini_cart_header_mobile', 20 );


/**
 * Aora Header Mobile before content
 *
 * @see aora_the_hook_header_mobile_all_page
 */
add_action( 'aora_before_header_mobile', 'aora_the_hook_header_mobile_all_page', 5 );
add_action( 'aora_before_header_mobile', 'aora_the_hook_header_mobile_menu_all_page', 10 );
