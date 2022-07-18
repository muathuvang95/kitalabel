<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 1001 );
function theme_enqueue_styles() {
	etheme_child_styles();
}

add_filter( 'woocommerce_cart_item_name', 'nb_custom_render_cart' , 1, 3 );
add_filter( 'woocommerce_add_to_cart_redirect', 'kita_upload_file_redirect', 99, 1 );


function kita_upload_file_redirect($url) {
    if(isset($_REQUEST['is_from_kita_upload_form'])) {
        return wc_get_cart_url();   
    }
    return wc_get_cart_url();
}

function nb_custom_render_cart( $title = null, $cart_item = null, $cart_item_key = null ) {
	$product_custom_design = 9550;
	$enable_edit_design             = nbdesigner_get_option('nbdesigner_show_button_edit_design_in_cart', 'yes') == 'yes' ? true : false;
    $show_edit_link                 = apply_filters('nbd_show_edit_design_link_in_cart', $enable_edit_design, $cart_item);
	$product_id                     = $cart_item['product_id'];
    $variation_id                   = $cart_item['variation_id'];
    $product_id                     = get_wpml_original_id( $product_id );
    $option_type = 'normal';
    if( $product_custom_design == $product_id ) {
        $option_type = 'custom_design_page';
    } 
    $nbd_session = WC()->session->get($cart_item_key . '_nbd');
    $nbu_session = WC()->session->get($cart_item_key . '_nbu');
    $layout = nbd_get_product_layout( $product_id );
    $redirect       = is_cart() ? 'cart' : 'checkout';
    $_product                       = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    $product_permalink              = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
    if( isset( $cart_item['nbo_meta'] ) ) {
        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
        if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
            // return sprintf( '<a class="nb-custom-link" href="%s">%s</a>', esc_url( $cart_item['data']->get_permalink( $cart_item ) ), $cart_item['data']->get_name() );
            $buton = '';
            if( $show_edit_link ){
                $link_edit_design = add_query_arg(
                    array(
                        'task'          => 'edit',
                        'product_id'    => $product_id,
                        'nbd_item_key'  => $nbd_session,
                        'cs'            => $option_type,
                        'cik'           => $cart_item_key,
                        'view'          => $layout,
                        'rd'            => $redirect ),
                    getUrlPageNBD('create'));
                if( $product_permalink ){
                    $att_query = parse_url( $product_permalink, PHP_URL_QUERY );
                    $link_edit_design .= '&'.$att_query;
                }    
                if( $layout == 'v' ){
                    $link_edit_design = add_query_arg(
                        array(
                            'nbdv-task'     => 'edit',
                            'task'          => 'edit',
                            'product_id'    => $product_id,
                            'nbd_item_key'  => $nbd_session,
                            'cik'           => $cart_item_key,
                            'rd'            => $redirect),
                        $product_permalink );
                }
                if($cart_item['variation_id'] > 0){
                    $link_edit_design .= '&variation_id=' . $cart_item['variation_id'];
                }
                $buton = '<a class="button nbd-edit-design" href="'.$link_edit_design.'">'. esc_html__('Edit design', 'web-to-print-online-designer') .'</a>';
            }
            return '<div><span class="nb-custom-link" >'. $cart_item['data']->get_name() .'</span><div>'.$buton;
        } 
    }
    return $title;
}