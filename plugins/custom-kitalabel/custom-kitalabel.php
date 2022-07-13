<?php
/**
 * Plugin Name: Custom Kitalabel
 * Plugin URI: https://cmsmart.net
 * Description: An plugin custom for WordPress.
 * Version: 1.0.0
 * Author: cmsmart.net
 * Author URI: https://cmsmart.net
 * WC requires at least: 3.0
 * WC tested up to: 3.7.0
 * License: GPL2
 * TextDomain: custom-kitalabel
 */
define('CUSTOM_KITALABEL_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_KITALABEL_URL', plugin_dir_url(__FILE__));

require_once(CUSTOM_KITALABEL_PATH .    'includes/class-kitalabel-hooks.php');
require_once(CUSTOM_KITALABEL_PATH .    'includes/functions.php');