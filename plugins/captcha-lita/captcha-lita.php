<?php
/**
 * Plugin Name:       Custom captcha lita
 * Plugin URI:        https://cmsmart.net
 * Description:       Custom captcha lita
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.1
 * Author:            Huy
 * Author URI:        https://cmsmart.net
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       Custom captcha lita
 * Domain Path:       /languages
 */

add_action('nbd_js_config','captlita_add_jss');
function captlita_add_jss(){
  ?>
        var add_apptcha_lita_js = 1;
  <?php 
}

function my_scripts_method_lita() {
    wp_enqueue_script( 'newscript', 'https://www.google.com/recaptcha/api.js', array( 'scriptaculous' ) );
    wp_add_inline_script( 'nbdesigner', 'var eerr_lite = true;');
}
add_action( 'wp_enqueue_scripts', 'my_scripts_method_lita',20 );

add_action('nbd_extra_css','templates_styleee_lita');
function templates_styleee_lita($path){
  ?>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__) . 'assets/style.css'; ?>">
  <?php
}

add_action('woocommerce_register_form','add_acptcha_robot');
function add_acptcha_robot() {  
    require_once "vendor/autoload.php";
    $sitekey  = nbdesigner_get_option('nbdesigner_captchga_sitekey');
    $secret = nbdesigner_get_option('nbdesigner_captchga_secret');
    $captcha = new \Anhskohbo\NoCaptcha\NoCaptcha($secret, $sitekey);
 
?>
      <div class="g-recaptcha" data-sitekey="<?php echo $sitekey; ?>"></div>
<?php
}

add_action('woocommerce_proceed_to_checkout','captcha_check_car');
function captcha_check_car() {

    require_once "vendor/autoload.php";
    $sitekey  = nbdesigner_get_option('nbdesigner_captchga_sitekey');
    $secret = nbdesigner_get_option('nbdesigner_captchga_secret');
    $captcha = new \Anhskohbo\NoCaptcha\NoCaptcha($secret, $sitekey);
 
    ?>
          <div class="g-recaptcha" data-sitekey="<?php echo $sitekey; ?>"></div>
          <br> 
    <?php
}

add_filter('nbdesigner_general_settings','add_input_key_catcha_lita');
function add_input_key_catcha_lita($arr)
{
    $arr['application']['key-captcha-sitekey'] = [
        'title'         => esc_html__( 'Key Captcha Sitekey', 'web-to-print-online-designer'),
        'description'   => esc_html__( 'Add key captcha Sitekey.', 'web-to-print-online-designer').' <a href="https://www.google.com/recaptcha/admin/create" id="nbdesigner_show_captcha">'.__("Where do I get the Key?", 'web-to-print-online-designer').'</a>',
        'id'            => 'nbdesigner_captchga_sitekey',
        'class'         => 'regular-text',
        'default'       => '',
        'type'          => 'text'
    ];
    $arr['application']['key-captcha-secret'] = [
        'title'         => esc_html__( 'Key Captcha Secret', 'web-to-print-online-designer'),
        'description'   => esc_html__( 'Add key captcha Secret.', 'web-to-print-online-designer').' <a href="https://www.google.com/recaptcha/admin/create" id="nbdesigner_show_captchaa">'.__("Where do I get the Key?", 'web-to-print-online-designer').'</a>',
        'id'            => 'nbdesigner_captchga_secret',
        'class'         => 'regular-text',
        'default'       => '',
        'type'          => 'text'
    ];

    return $arr;
}
