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
    if( isset($cart_item['nbd_item_meta_ds']) ){
        if( isset($cart_item['nbd_item_meta_ds']['nbd']) ) $nbd_session = $cart_item['nbd_item_meta_ds']['nbd'];
        if( isset($cart_item['nbd_item_meta_ds']['nbu']) ) $nbu_session = $cart_item['nbd_item_meta_ds']['nbu'];
    }
    $layout = nbd_get_product_layout( $product_id );
    $redirect       = is_cart() ? 'cart' : 'checkout';
    $_product                       = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    $product_permalink              = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

    if( isset( $cart_item['nbo_meta'] ) ) {
        $is_order_again = false;
        if(isset($cart_item['nbo_meta']['order_again']) && $cart_item['nbo_meta']['order_again']) {
            $is_order_again = true;
        }
        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) );
        $hiden_edit_design = false;
        $buton = '';
        if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
            $hiden_edit_design = true;
            // return sprintf( '<a class="nb-custom-link" href="%s">%s</a>', esc_url( $cart_item['data']->get_permalink( $cart_item ) ), $cart_item['data']->get_name() );
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
                // if(!$is_order_again) {
                    $buton = '<a class="button nbd-edit-design" href="'.$link_edit_design.'">'. esc_html__('Edit design', 'web-to-print-online-designer') .'</a>';
                // }
            }
            
        }
        if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['option_price'] ) && isset( $cart_item['nbo_meta']['option_price']['fields'] ) ) {
            foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $field)  {
                if(isset($field['is_custom_upload'])) {
                    $hiden_edit_design = true;
                    $buton = '';
                }
            }
        }
        if($hiden_edit_design) {
            return '<div><span class="nb-custom-link" >'. $cart_item['data']->get_name() .'</span><div>'.$buton;
        }
    }
    return $title;
}

function etheme_cart_items( $limit = 3 ) {
    ?>
    <?php if ( ! WC()->cart->is_empty() ) :
//          global $etheme_mini_cart_global;
        $cart_content_linked_products_dt = etheme_get_option('cart_content_linked_products_et-desktop', false);
        $cart_content_linked_products_mob = etheme_get_option('cart_content_linked_products_et-mobile', false);
        $cart_content_linked_products_type = etheme_get_option('cart_content_linked_products_type_et-desktop', 'upsell');
        $is_mobile = get_query_var('is_mobile', false);
        $show_cart_content_linked_products =
            ($cart_content_linked_products_dt && !$is_mobile && get_theme_mod( 'cart_content_type_et-desktop', 'dropdown' ) == 'off_canvas') ||
            ($cart_content_linked_products_mob && $is_mobile && get_theme_mod( 'cart_content_type_et-mobile', 'dropdown' ) == 'off_canvas' );
        $etheme_mini_cart_global = array();
        $etheme_mini_cart_global['upsell_ids'] = array();
        $etheme_mini_cart_global['upsell_ids_not_in'] = array();
        
        $etheme_mini_cart_global['cross-sell_ids'] = array();
        $etheme_mini_cart_global['cross-sell_ids_not_in'] = array();
        
        ?>

        <ul class="cart-widget-products clearfix">
            <?php
            $i    = 0;
            $cart =  WC()->cart->get_cart();

            if  ( apply_filters('et_mini_cart_reverse', false) ){
                $cart = array_reverse($cart);
            }

            do_action( 'woocommerce_before_mini_cart_contents' );
            foreach ( $cart as $cart_item_key => $cart_item ) {
                
                if ( $i >= $limit ) {
                    continue;
                }
                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                
                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                    $i ++;
                    $product_name        = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                    $thumbnail           = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                    $product_price       = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                    $product_remove_icon = apply_filters( 'woocommerce_cart_item_remove_icon_html', '<i class="et-icon et-delete et-remove-type1"></i><i class="et-trash-wrap et-remove-type2"><img src="' . ETHEME_BASE_URI . 'theme/assets/images/trash-bin.gif' . '" alt="'. esc_attr( 'Remove this product', 'xstore' ) .'"></i>' ); ?>
                    <li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>"
                        data-key="<?php echo esc_attr( $cart_item_key ); ?>">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                            '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">' . $product_remove_icon . '</a>',
                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                            __( 'Remove this item', 'xstore' ),
                            esc_attr( $product_id ),
                            esc_attr( $cart_item_key ),
                            esc_attr( $_product->get_sku() )
                        ), $cart_item_key );
                        ?>
                        <?php
                            $product_custom = false;
                            if( isset( $cart_item['nbo_meta'] ) ) {
                                $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
                                if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
                                    $product_custom = true;
                                }
                                if(isset($cart_item['nbo_meta']['option_price']) && $cart_item['nbo_meta']['option_price']['fields'] && is_array($cart_item['nbo_meta']['option_price']['fields'])) {
                                    foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $field)  {
                                        if(isset($field['is_custom_upload'])) {
                                            $product_custom = true;
                                        }
                                    }
                                }

                            }
                        ?>
                        <?php if ( ! $_product->is_visible() || $product_custom ) : ?>
                            <a class="product-mini-image">
                                <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . ''; ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>"
                               class="product-mini-image">
                                <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . ''; ?>
                            </a>
                        <?php endif; ?>
                        <div class="product-item-right">
                            <h4 class="product-title">
                                <?php
                                if($product_custom) {
                                    ?>
                                    <div class="product-name"><span><?php echo wp_kses_post($product_name); ?></span></div>
                                    <?php
                                } else {
                                    ?>
                                    <a class="product-name" href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>"><span><?php echo wp_kses_post($product_name); ?></span></a>
                                    <?php
                                } ?>
                            </h4>

                            <div class="descr-box">
                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                <?php
                                if ( ! $_product->is_sold_individually() && $_product->is_purchasable() && ( ( etheme_get_option('cart_content_quantity_input_et-desktop', false) && !$is_mobile ) || ( etheme_get_option('cart_content_quantity_input_et-mobile', false) && $is_mobile ) ) ) {
                                    remove_action( 'woocommerce_before_quantity_input_field', 'et_quantity_minus_icon' );
                                    remove_action( 'woocommerce_after_quantity_input_field', 'et_quantity_plus_icon' );
                                    add_action( 'woocommerce_before_quantity_input_field', 'et_quantity_minus_icon' );
                                    add_action( 'woocommerce_after_quantity_input_field', 'et_quantity_plus_icon' );
                                    echo '<div class="quantity-wrapper clearfix">';
                                    woocommerce_quantity_input(
                                        array(
                                            'input_value' => $cart_item['quantity'],
                                            'min_value' => 1,
                                            'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                        ),
                                        $_product
                                    );
                                    remove_action( 'woocommerce_before_quantity_input_field', 'et_quantity_minus_icon' );
                                    remove_action( 'woocommerce_after_quantity_input_field', 'et_quantity_plus_icon' );
                                    echo '<span class="quantity">' . ' &times; ' . $product_price . '</span>';
                                    echo '</div>';
                                }
                                ?>
                                
                                <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                            </div>
                        </div>
                        
                        <?php
                        
                        if ( $show_cart_content_linked_products ) {
                            $_product_linked     = $_product;
                            $_product_4_linked_ids = array( $product_id );
                            if ( $_product->get_type() == 'variation' ) {
                                $parent_id               = $_product->get_parent_id();
                                $_product_4_linked_ids[] = $parent_id;
                                $_product_linked       = wc_get_product( $parent_id );
                            }
                            
                            if ( $cart_content_linked_products_type == 'upsell' ) {
                                
                                $etheme_mini_cart_global['upsell_ids']        =
                                    array_merge( $etheme_mini_cart_global['upsell_ids'], array_map( 'absint', $_product_linked->get_upsell_ids() ) );
                                $etheme_mini_cart_global['upsell_ids_not_in'] =
                                    array_merge( $etheme_mini_cart_global['upsell_ids_not_in'], $_product_4_linked_ids );
                                
                            }
                            else {
                                $etheme_mini_cart_global['cross-sell_ids']        =
                                    array_merge( $etheme_mini_cart_global['cross-sell_ids'], array_map( 'absint', $_product_linked->get_cross_sell_ids() ) );
                                $etheme_mini_cart_global['cross-sell_ids_not_in'] =
                                    array_merge( $etheme_mini_cart_global['cross-sell_ids_not_in'], $_product_4_linked_ids );
                            }
                            
                        }
                        
                        ?>

                    </li>
                    <?php
                }
            }
            do_action( 'woocommerce_mini_cart_contents' );
            ?>
        </ul>
        
        <?php
        if ( $show_cart_content_linked_products ) {
//                  if ( (!$is_mobile && get_theme_mod( 'cart_content_type_et-desktop', 'dropdown' ) == 'off_canvas')
//                       || ( $is_mobile && get_theme_mod( 'cart_content_type_et-mobile', 'dropdown' ) == 'off_canvas') ) {
//                      et_mini_cart_linked_products();
//                  }
            et_mini_cart_linked_products($cart_content_linked_products_type, $etheme_mini_cart_global);
        }
        ?>
    
    <?php else : ?>
        <?php etheme_get_mini_cart_empty(); ?>
    <?php endif;
}

add_action('activate_plugin', 'kitalabel_activate_plugin', 10, 2);

function kitalabel_activate_plugin($plugin, $network_wide) {
    error_log( "KITALABEL LOG USER ID ACTIVATE PLUGIN:" . get_current_user_id() . ' Plugin Name: ' . $plugin );
}