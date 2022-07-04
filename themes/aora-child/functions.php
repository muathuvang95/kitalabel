<?php
/**
 * @version    1.0
 * @package    aora
 * @author     Thembay Team <support@thembay.com>
 * @copyright  Copyright (C) 2019 Thembay.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: https://thembay.com
 */
  require_once  get_stylesheet_directory() .'/woocommerce/classes/class-wc-shop.php';
  require_once  get_stylesheet_directory() .'/custom-nbdesign/functions.php';
  function aora_child_enqueue_styles() {
    wp_enqueue_style( 'aora-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'aora-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'aora-style' ),
        wp_get_theme()->get('Version')
    );
  }

  function my_custom_script_load(){
      wp_enqueue_script( 'my-custom-script', get_stylesheet_directory_uri() . '/js/main.js', array( 'jquery' ) );
  }
  add_action(  'wp_enqueue_scripts', 'aora_child_enqueue_styles', 10000 );
  add_action( 'wp_enqueue_scripts', 'my_custom_script_load' );
  add_action( 'aora_header_mobile_content','kita_the_user_mobile_menu', 6 );
  add_action( 'wp_ajax_load_more_usecase', 'load_more_usecase' );
  add_action( 'wp_ajax_nopriv_load_more_usecase', 'load_more_usecase' );

  add_action( 'wp_ajax_upload_file_tab', 'kita_upload_file_tab' );
  add_action( 'wp_ajax_nopriv_upload_file_tab', 'kita_upload_file_tab' );

  add_filter( 'woocommerce_add_to_cart_redirect', 'kita_upload_file_redirect', 99, 1 );
  add_filter( 'woocommerce_checkout_fields', 'kita_custom_override_checkout_fields' );
  add_filter( 'woocommerce_default_address_fields', 'kita_custom_override_default_locale_fields' );
  add_filter( 'gettext', 'kita_wc_billing_field_strings', 20, 3 );
//   add_filter( 'woocommerce_states', 'custom_woocommerce_states' );
  
  // fix lỗi không empty cart khi logout
  //add_action('wp_logout','auto_redirect_after_logout');

  function auto_redirect_after_logout(){
    wp_safe_redirect( home_url() );
    exit;
  }

  if(!is_user_logged_in()) {
    add_action('wp_footer', 'kita_login_poup_form');
  }

  function kita_the_user_mobile_menu() {

    ob_start();?>

      <div class="mobile-account">
        <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="btn btn-sm">
          <i class="tb-icon tb-icon tb-icon-user"></i>
        </a>
      </div>

    <?php
    echo ob_get_clean();
  }

function kita_login_poup_form()
{
    ob_start();?>
      <div class="kita_login_popup_form woocommerce-account">
        <div class="kita_login_popup_wrapper">
          <button type="button" class="btn btn-close-popup-login">
              <i class="tb-icon tb-icon-close-01"></i>
          </button>
          <?php echo do_shortcode( '[woocommerce_my_account]' );?>
        </div>
      </div>
    <?php
    echo ob_get_clean();
}

function load_more_usecase() {
    $page       = isset($_GET['page']) ? $_GET['page'] : 1;
    $per_page   = isset($_GET['perPage']) ? $_GET['perPage'] : 8;
    $offset     = ($page - 1) * $per_page;
    $template_tags = get_terms( array(
        'taxonomy'      => 'template_tag',
        'hide_empty'    => false,
        'number'        => $per_page,
        'offset'        => $offset,
    ) );
    ?>
    <?php
    ob_start();
    foreach( $template_tags as $index => $tag ):
        $tag_thumbnail_id   = get_term_meta( $tag->term_id, 'thumbnail_id', true);
        $tag_image          = wp_get_attachment_image($tag_thumbnail_id, 'thumbnail');
        $tag_url 			= site_url('templates?tag=' . $tag->term_id);
    ?>
        <div class="template-tag-item">
            <a href="<?php echo $tag_url;?>"><?php echo $tag_image;?></a>
            <a href="<?php echo $tag_url;?>"><h5><?php echo $tag->name;?></h5></a>
            <div class="tag-description-wrapper">
                <p class="tag-description"><?php echo $tag->description;?></p>
            </div>
        </div>
    <?php endforeach;?>
    <?php
    $response = array();
    $response['data'] = ob_get_clean();
    echo json_encode($response);
    exit();
}

function kita_upload_file_tab() {

    $product_id = $_GET['productId'];
    $args = array(
        'post_type' => 'product',
        'post__in'=> array($product_id)
        
    );
    $the_query = new WP_Query( $args );
    ob_start();
    if ( $the_query->have_posts() ) {
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
        ?>
            <form class="cart" action="<?php echo site_url();?>/upload-file/" method="post" enctype="multipart/form-data">
                <div class="kita-uf-nbo-option"><?php do_action('woocommerce_before_add_to_cart_button');?></div></div>
                <?php 
                    if($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
                        echo do_shortcode( '[pafe-template id="8239"]' );
                    }
                    else {
                        echo do_shortcode( '[pafe-template id="8956"]' );
                    }
                ?>
                <div class="kita-tac-wrapper">
                    <input type="checkbox" id="kita-uf-tac" name="kita-uf-tac">
                        <label for="kita-uf-tac"> <?php esc_html_e('I agree with terms and condition and privacy policy', 'aora');?></label>
                </div>
                <input type="hidden" name="is_from_kita_upload_form" value="1">
                <button type="submit" name="add-to-cart" value="<?php echo $product_id;?>" class="single_add_to_cart_button button alt"><?php esc_html_e('Proceed', 'aora');?></button>
            </form>
        <?php
        }
    }

    $response = array();
    $response['data'] = ob_get_clean();
    echo json_encode($response);
    exit();


    wp_reset_postdata();
    
    exit();
}

function kita_upload_file_redirect($url) {
    if(isset($_REQUEST['is_from_kita_upload_form'])) {
        return wc_get_cart_url();   
    }
    return wc_get_cart_url();
}

function kita_custom_override_checkout_fields( $fields ) {
	$fields['billing']['billing_last_name']['required'] = false;
	$fields['billing']['billing_country']['required'] = false;
	$fields['billing']['billing_phone']['required'] = false;

    $fields['billing']['billing_phone']['default'] = '+62';
    $fields['billing']['billing_phone']['maxlength'] = 15;
    
    unset($fields['billing']['billing_company']);
	return $fields;
}

function kita_custom_override_default_locale_fields( $fields ) {
    $fields['state']['priority'] = 41;
    $fields['city']['priority'] = 42;
    $fields['address_1']['priority'] = 43;
    $fields['address_2']['priority'] = 44;
    return $fields;
}

function kita_wc_billing_field_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Country / Region' :
            $translated_text = __( 'Country', 'woocommerce' );
            break;
        case 'Negara/Wilayah' :
            $translated_text = __( 'Negara', 'woocommerce' );
            break;
    }
    return $translated_text;
}

function custom_woocommerce_states( $states ) {

    $states['ID'] = array(
		'BA' => __( 'Bali', 'woocommerce' ),
        'AC' => __( 'Daerah Istimewa Aceh', 'woocommerce' ),
		'SU' => __( 'Sumatera Utara', 'woocommerce' ),
		'SB' => __( 'Sumatera Barat', 'woocommerce' ),
		'RI' => __( 'Riau', 'woocommerce' ),
		'KR' => __( 'Kepulauan Riau', 'woocommerce' ),
		'JA' => __( 'Jambi', 'woocommerce' ),
		'SS' => __( 'Sumatera Selatan', 'woocommerce' ),
		'BB' => __( 'Bangka Belitung', 'woocommerce' ),
		'BE' => __( 'Bengkulu', 'woocommerce' ),
		'LA' => __( 'Lampung', 'woocommerce' ),
		'JK' => __( 'DKI Jakarta', 'woocommerce' ),
		'JB' => __( 'Jawa Barat', 'woocommerce' ),
		'BT' => __( 'Banten', 'woocommerce' ),
		'JT' => __( 'Jawa Tengah', 'woocommerce' ),
		'JI' => __( 'Jawa Timur', 'woocommerce' ),
		'YO' => __( 'Daerah Istimewa Yogyakarta', 'woocommerce' ),
		'NB' => __( 'Nusa Tenggara Barat', 'woocommerce' ),
		'NT' => __( 'Nusa Tenggara Timur', 'woocommerce' ),
		'KB' => __( 'Kalimantan Barat', 'woocommerce' ),
		'KT' => __( 'Kalimantan Tengah', 'woocommerce' ),
		'KI' => __( 'Kalimantan Timur', 'woocommerce' ),
		'KS' => __( 'Kalimantan Selatan', 'woocommerce' ),
		'KU' => __( 'Kalimantan Utara', 'woocommerce' ),
		'SA' => __( 'Sulawesi Utara', 'woocommerce' ),
		'ST' => __( 'Sulawesi Tengah', 'woocommerce' ),
		'SG' => __( 'Sulawesi Tenggara', 'woocommerce' ),
		'SR' => __( 'Sulawesi Barat', 'woocommerce' ),
		'SN' => __( 'Sulawesi Selatan', 'woocommerce' ),
		'GO' => __( 'Gorontalo', 'woocommerce' ),
		'MA' => __( 'Maluku', 'woocommerce' ),
		'MU' => __( 'Maluku Utara', 'woocommerce' ),
		'PA' => __( 'Papua', 'woocommerce' ),
		'PB' => __( 'Papua Barat', 'woocommerce' ),
    );
  
    return $states;
  }