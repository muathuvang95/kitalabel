<?php 
/* NB CUSTOM DESIGN
*/
/* Create Custom Design Page*/
// $nb_custom_design_page_id = nbd_get_page_id( 'custom_design' );

// if ( $nb_custom_design_page_id == -1|| !get_post($nb_custom_design_page_id) ){        
//     $post = array(
//         'post_name'         => 'custom-design-page',
//         'post_status'       => 'publish',
//         'post_title'        => __('Custom Design Page', 'web-to-print-online-designer'),
//         'post_type'         => 'page',
//         'post_author'       => 1,
//         'comment_status'    => 'closed',
//         'post_date'         => date('Y-m-d H:i:s')
//     );
//     $nb_custom_design_page_id = wp_insert_post($post, false);    
//     update_option( 'nbdesigner_custom_design_page_id', $nb_custom_design_page_id );    
// } 

// if ( ! function_exists( 'is_nbd_custom_design_page' ) ) {
//     function is_nbd_custom_design_page(){
//         return is_page( nbd_get_page_id( 'custom_design' ) );
//     }
// } 

// add_action( 'template_redirect', '_nb_template_redirect' );
// function _nb_template_redirect(){
//     if( is_nbd_custom_design_page() ){
//         include( get_stylesheet_directory() .'/custom-nbdesign/custom-design.php' ); exit();
//     }
// }

// add_filter( 'display_post_states', '_nb_add_display_post_states' , 10, 2 );
// function _nb_add_display_post_states( $post_states, $post ){
//     if ( nbd_get_page_id( 'custom_design' ) === $post->ID ) {
//         $post_states['nbd_custom_design_page'] = esc_html__( 'NBD Custom Design Page', 'web-to-print-online-designer' );
//     }
//     return $post_states;
// }
function _nb_show_option_fields($product_id = 0 , $type_page = ''){
    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : $product_id;
    if( !$product_id || ($product_id && !wc_get_product($product_id) ) ) {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        get_template_part( 404 ); exit();
    }

    $option_id = _nb_get_product_option( $product_id );
    if( $option_id ){
        $_options = _nb_get_option( $option_id );
        if( $_options ){
            $options = unserialize($_options['fields']);
            if( !isset($options['fields']) ){
                $options['fields'] = array();
            }
            $options['fields'] = _nb_recursive_stripslashes( $options['fields'] );
            foreach ( $options['fields'] as $key => $field ){
                if( !isset( $field['general']['attributes'] ) ){
                    $field['general']['attributes'] = array();
                    $field['general']['attributes']['options'] = array();
                    $options['fields'][$key]['general']['attributes'] = array();
                    $options['fields'][$key]['general']['attributes']['options'] = array();
                }
                if( $field['appearance']['change_image_product'] == 'y' ){
                    foreach ( $field['general']['attributes']['options'] as $op_index => $option ){
                        $option['product_image'] = isset($option['product_image']) ? $option['product_image'] : 0;
                        $attachment_id = absint( $option['product_image'] );
                        if( $attachment_id != 0 ){
                            $image_link         = wp_get_attachment_url( $attachment_id );
                            $attachment_object  = get_post( $attachment_id );
                            $full_src           = wp_get_attachment_image_src( $attachment_id, 'large' );
                            $image_title        = get_the_title( $attachment_id );
                            $image_alt          = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', TRUE ) ) );
                            $image_srcset       = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : FALSE;
                            $image_sizes        = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : FALSE;
                            $image_caption      = $attachment_object->post_excerpt;
                            $options['fields'][$key]['general']['attributes']['options'][$op_index] = array_replace_recursive($options['fields'][$key]['general']['attributes']['options'][$op_index], array(
                                'imagep'        => 'y',
                                'image_link'    => $image_link,
                                'image_title'   => $image_title,
                                'image_alt'     => $image_alt,
                                'image_srcset'  => $image_srcset,
                                'image_sizes'   => $image_sizes,
                                'image_caption' => $image_caption,
                                'full_src'      => $full_src[0],
                                'full_src_w'    => $full_src[1],
                                'full_src_h'    => $full_src[2]
                            ));
                        }else{
                            $options['fields'][$key]['general']['attributes']['options'][$op_index]['imagep'] = 'n';
                        }
                    }
                }
                if( isset($field['nbpb_type']) && $field['nbpb_type'] == 'nbpb_com' ){
                    if( isset($field['general']['pb_config']) ){
                        foreach( $field['general']['pb_config'] as $a_index => $attr ){
                            foreach( $attr as $s_index => $sattr ){
                                foreach( $sattr['views'] as $v_index => $view ){
                                    $pb_image_obj = wp_get_attachment_url( absint($view['image']) );
                                    $options['fields'][$key]['general']['pb_config'][$a_index][$s_index]['views'][$v_index]['image_url'] =  $pb_image_obj ? $pb_image_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                                }
                            }
                        }
                    }else{
                        $field['general']['pb_config'] = array();
                    }
                    foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                        if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                            foreach( $option['sub_attributes'] as $sa_index => $sattr ){
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['sub_attributes'][$sa_index]['image_url'] = nbd_get_image_thumbnail( $sattr['image'] );
                            }
                        }else{
                            $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = nbd_get_image_thumbnail( $option['image'] );
                        }
                    };
                    $options['fields'][$key]['general']['component_icon_url'] = nbd_get_image_thumbnail( $field['general']['component_icon'] );
                }
                if( isset($field['general']['attributes']['bg_type']) && $field['general']['attributes']['bg_type'] == 'i' ){
                    foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                        foreach( $option['bg_image'] as $bg_index => $bg ){
                            $bg_obj = wp_get_attachment_url( absint( $bg ) );
                            $options['fields'][$key]['general']['attributes']['options'][$op_index]['bg_image_url'][$bg_index] = $bg_obj ? $bg_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                        }
                    };
                }
                if( isset( $field['nbd_type'] ) && $field['nbd_type'] == 'overlay' ){
                    foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                        foreach( $option['overlay_image'] as $ov_index => $ov ){
                            $ov_obj = wp_get_attachment_url( absint($ov) );
                            $options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image_url'][$ov_index] = $ov_obj ? $ov_obj : '';
                        }
                    };
                }
                if( isset( $field['nbe_type'] ) && $field['nbe_type'] == 'frame' ){
                    foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                        $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = nbd_get_image_thumbnail( $option['image'] );
                        $fr_obj = wp_get_attachment_url( absint($option['frame_image']) );
                        $options['fields'][$key]['general']['attributes']['options'][$op_index]['frame_image_url'] = $fr_obj ? $fr_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                    };
                }
            }
            if( isset( $options['views'] ) ){
                foreach ($options['views'] as $vkey => $view){
                    $view['base'] = isset($view['base']) ? $view['base'] : 0;
                    $options['views'][$vkey]['base'] = $view['base'];
                    $view_bg_obj = wp_get_attachment_url( absint($view['base']) );
                    $options['views'][$vkey]['base_url'] = $view_bg_obj ? $view_bg_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                }
            }
            $product        = wc_get_product( $product_id );
            $type           = $product->get_type();
            $variations     = array();
            $dimensions     = array();
            $form_values    = array();
            $cart_item_key  = '';
            $quantity       = 1;
            $nbu_item_key   = '';
            $nbau           = '';
            $nbdpb_enable   = get_post_meta($product_id, '_nbdpb_enable', true);
            if($options['quantity_enable'] == 'y'){
                $quantity = absint($options['quantity_breaks'][0]['val']);
                foreach( $options['quantity_breaks'] as $break){
                    if( isset( $break['default'] ) && $break['default'] == 'on' ){
                        $quantity = $break['val'];
                    }
                }
            }

            if( isset($_POST['nbd-field']) ){
                $form_values = $_POST['nbd-field'];
                if( isset($_POST["nbo-quantity"]) ){
                    $quantity = $_POST["nbo-quantity"];
                }
            }else if( isset($_GET['nbo_cart_item_key']) && $_GET['nbo_cart_item_key'] != '' ){
                $cart_item_key  = $_GET['nbo_cart_item_key'];
                $cart_item      = WC()->cart->get_cart_item( $cart_item_key );
                if( isset($cart_item['nbo_meta']) ){
                    $form_values = $cart_item['nbo_meta']['field'];
                }
                if ( isset( $cart_item["quantity"] ) ) {
                    $quantity = $cart_item["quantity"];
                }
                if( isset( $cart_item['nbau'] ) ){
                    $nbau           = stripslashes( $cart_item['nbau'] );
                    $nbu_item_key   = $cart_item["nbd_item_meta_ds"]["nbu"];
                }
            }

            if( isset( $_GET['nbo_values'] ) ){
                $params     = array();
                $value_str  = base64_decode( wc_clean( $_GET['nbo_values'] ) );
                parse_str( $value_str, $params );
                if( isset( $params['nbd-field'] ) ){
                    $form_values = $params['nbd-field'];
                }
                if ( isset( $params["qty"] ) ) {
                    $quantity = $params["qty"];
                }
            }

            // custom kitalabel
            if(count($form_values) == 0 && isset( $_GET['reference']) && $_GET['reference'] ) {
                // $form_values = (array)json_decode(file_get_contents( NBDESIGNER_CUSTOMER_DIR .'/'. $_GET['reference'] . '/printing_options.json' ));
                $printing_options = array();
                parse_str(file_get_contents( NBDESIGNER_CUSTOMER_DIR .'/'. $_GET['reference'] . '/printing_options.json' ), $printing_options);
                if(isset($printing_options['nbd-field']) && is_array($printing_options['nbd-field'])) {
                    $form_values = $printing_options['nbd-field'];
                }
            }

            if( $type == 'variable' ){
                $all = get_posts( array(
                    'post_parent' => $product_id,
                    'post_type'   => 'product_variation',
                    'orderby'     => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
                    'post_status' => 'publish',
                    'numberposts' => -1,
                ));
                foreach ( $all as $child ) {
                    $vid                = $child->ID;
                    $variation          = wc_get_product( $vid );
                    $variations[$vid]   = $variation->get_price( 'edit' );

                    $width = $height = '';
                    $dimensions[$vid]   = array(
                        'width'     => $variation->get_width(),
                        'height'    => $variation->get_length()
                    );
                }
            }
            $width = $height = '';
            if( $type != 'variable' ){
                $width  = $product->get_width();
                $height = $product->get_length();
            }

            $options = apply_filters( 'nbo_product_options', $options, $product_id );
            ob_start();
            custom_kita_get_template('nb-option-builder.php' , array(
                'product_id'            => $product_id,
                'options'               => $options,
                'type'                  => $type,
                'type_page'             => $type_page,
                'quantity'              => $quantity,
                'width'                 => $width,
                'height'                => $height,
                'nbdpb_enable'          => $nbdpb_enable,
                'price'                 => $product->get_price( 'edit' ),
                'is_sold_individually'  => $product->is_sold_individually(),
                'variations'            => json_encode( (array) $variations ),
                'dimensions'            => json_encode( (array) $dimensions ),
                'form_values'           => $form_values,
                'cart_item_key'         => $cart_item_key,
                'nbau'                  => $nbau,
                'nbu_item_key'          => $nbu_item_key,
                'change_base'           => nbdesigner_get_option( 'nbdesigner_change_base_price_html', 'no' ),
                'tooltip_position'      => nbdesigner_get_option( 'nbdesigner_tooltip_position', 'top' ),
                'hide_zero_price'       => nbdesigner_get_option( 'nbdesigner_hide_zero_price', 'no' )
            ));
            $options_form = ob_get_clean();
            echo $options_form;
        }
    }
}
function custom_kita_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '') {
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;
    $template_file = custom_kita_locate_template($template_name, $tempate_path, $default_path);
    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.3.1');
        return;
    endif;
    include $template_file;
}
function custom_kita_locate_template($template_name, $template_path = '', $default_path = '') {
    // Set variable to search in web-to-print-online-designer folder of theme.
    if (!$template_path) :
        $template_path = 'custom-kitalabel/';
    endif;
    // Set default plugin templates path.
    if (!$default_path) :
        $default_path = CUSTOM_KITALABEL_PATH . 'templates/'; // Path to the template folder
    endif;
    // Search template file in theme folder.
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));
    // Get plugins template file.
    if (!$template) :
        $template = $default_path . $template_name;
    endif;
    return $template;
}
function _nb_get_product_option( $product_id ){
    $enable = get_post_meta( $product_id, '_nbo_enable', true );
    if( !$enable ) return false;
    $option_id = get_transient( 'nbo_product_'.$product_id );
    if( false === $option_id ){
        global $wpdb;
        $sql = "SELECT id, priority, apply_for, product_ids, product_cats, date_from, date_to FROM {$wpdb->prefix}nbdesigner_options WHERE published = 1";
        $options = $wpdb->get_results($sql, 'ARRAY_A');
        if($options){
            $_options = array();
            foreach( $options as $option ){
                $execute_option = true;
                $from_date = false;
                if( isset($option['date_from']) ){
                    $from_date = empty( $option['date_from'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_from'] ), false ) );
                }
                $to_date = false;
                if( isset($option['date_to']) ){
                    $to_date = empty( $option['date_to'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_to'] ), false ) );
                }
                $now  = current_time( 'timestamp' );
                if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
                    $execute_option = false;
                } elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
                    $execute_option = false;
                } elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
                    $execute_option = false;
                }
                if( $execute_option ){
                    if( $option['apply_for'] == 'p' ){
                        $products = unserialize($option['product_ids']);
                        $execute_option = in_array($product_id, $products) ? true : false;
                    }else {
                        $categories = $option['product_cats'] ? unserialize($option['product_cats']) : array();
                        $product = wc_get_product($product_id);
                        $product_categories = $product->get_category_ids();
                        $intersect = array_intersect($product_categories, $categories);
                        $execute_option = ( count($intersect) > 0 ) ? true : false;
                    }
                }
                if( $execute_option ){
                    $_options[] = $option;
                }
            }
            $_options = array_reverse( $_options );
            $option_priority = 0;
            foreach( $_options as $_option ){
                if( $_option['priority'] > $option_priority ){
                    $option_priority = $_option['priority'];
                    $option_id = $_option['id'];
                }
            }
            if( $option_id ){
                set_transient( 'nbo_product_'.$product_id , $option_id );
                
                $is_artwork_action = get_transient( 'nbo_action_'.$product_id );
                if( false === $is_artwork_action ){
                    $_selected_options  = _nb_get_option( $option_id );
                    $selected_options   = unserialize( $_selected_options['fields'] );
                    if ( isset( $selected_options['fields'] ) ) {
                        foreach ($selected_options['fields'] as $key => $field) {
                            if ( $field['general']['enabled'] == 'y' && isset( $field['nbe_type'] ) && $field['nbe_type'] == 'actions' ) {
                                $is_artwork_action = true;
                            }
                        }
                    }
                    if( $is_artwork_action ){
                        set_transient( 'nbo_action_'.$product_id , '1' );
                    }
                }
            }
        }
    }
    return $option_id;
}
function _nb_get_option( $id ){
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
    $sql .= " WHERE id = " . esc_sql($id);
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return count($result[0]) ? $result[0] : false;
}
function _nb_recursive_stripslashes( $fields ){
    $valid_fields = array();
    foreach($fields as $key => $field){
        if(is_array($field) ){
            $valid_fields[$key] = _nb_recursive_stripslashes($field);
        }else if(!is_null($field)){
            $valid_fields[$key] = stripslashes($field);
        }
    }
    return $valid_fields;
}

if (!function_exists("sort_template_by_name")) {
    function sort_template_by_name($a, $b)
    {
        if ($a['title'] == $b['title']) {
            return 0;
        }
        return ($a['title'] < $b['title']) ? -1 : 1;
    }
}
if (!function_exists("sort_template_by_id")) {
    function sort_template_by_id($a, $b)
    {
        if ($a['tid'] == $b['tid']) {
            return 0;
        }
        return ($a['tid'] > $b['tid']) ? -1 : 1;
    }
}
add_action( 'nb_button_custom_design' , 'nb_button_custom_design' );
function nb_button_custom_design( $pid ) {
    $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true ); 
    if ( $is_nbdesign ) {
        $product    = wc_get_product( $pid );
        $type       = $product->get_type();
        $option     = unserialize( get_post_meta( $pid, '_nbdesigner_option', true ) );
        $class                          = nbdesigner_get_option( 'nbdesigner_class_design_button_detail', '' ); 
        $_enable_upload                 = get_post_meta( $pid, '_nbdesigner_enable_upload', true );
        $_enable_upload_without_design  = get_post_meta( $pid, '_nbdesigner_enable_upload_without_design', true );
        $label_design                   = apply_filters( 'nbd_start_design_label', esc_html__( 'Mulai Desain', 'web-to-print-online-designer' ) );
        $label_upload                   = apply_filters( 'nbd_start_design_and_upload_label', esc_html__( 'Upload Desain Kamu', 'web-to-print-online-designer' ) );
        $desc_design                    = apply_filters( 'nbd_start_design_desc', 'Create your own design with our friendly and powerful design tool' );
        $desc_upload                    = apply_filters( 'nbd_start_upload_desc', 'Use your own artwork design according to our guideline <a href="http://www.guideline.com">www.guideline.com</a>' );
        $layout                         = nbd_get_product_layout( $pid );
        $show_button_use_our_template = 0;
        if( nbdesigner_get_option('nbdesigner_button_link_product_template', 'no') == 'yes' ){
            $templates = nbd_get_templates( $pid, 0, '', false, false, false, 'all' );
            if( count( $templates ) > 0 ) $show_button_use_our_template = 1;
        }
        $show_button_hire_us = 0;
        if( nbdesigner_get_option('nbdesigner_button_hire_designer', 'no') == 'yes' ){
            $artwork_action = get_transient( 'nbo_action_' . $pid );
            if( false !== $artwork_action ){
                $show_button_hire_us            = 1;
                $show_button_use_our_template   = nbdesigner_get_option( 'nbdesigner_separate_design_buttons', 'no' ) == 'yes' ? $show_button_use_our_template : 0;
            }
        }
        ?>
        <script type="text/javascript">
            var nbd_layout = '<?php echo $layout; ?>';
            var is_nbd_upload = '<?php echo $_enable_upload; ?>';
            var use_our_template = <?php echo $show_button_use_our_template; ?>;
            var hire_us_design_for_you = <?php echo $show_button_hire_us; ?>;
            var is_nbd_upload_without_design = <?php echo $_enable_upload_without_design; ?>;
        </script>
        <div class="nbdesigner_frontend_container nb_button_custom_design">
            <input class="nb-custom-design-page" name="nb-custom-design-page" type="hidden" value="custom_design_page" />
            <input name="nbd-add-to-cart" type="hidden" value="<?php echo( $pid ); ?>" />
            <div class="row nbd-actions-wrap">
                <?php if( $is_nbdesign ): ?>
                <div class="col-md-6 nbd-action-wrap">
                    <div ng-click="NbCustomDesign('start_design')" class="button alt nbdesign-button start-design">
                        <span><?php echo $label_design; ?></span>
                        <span class="nb-cs-help-tip">
                            <span class="data-tip"><?php echo nbdesigner_get_option('nbd_desc_button_design'); ?></span>
                        </span>
                        <!-- <span class="show-desc" ng-click="showDescDesign('design')"><span ng-show="!showDescDesign['design']" class="dashicons dashicons-arrow-down"></span><span ng-show="showDescDesign['design']" class="dashicons dashicons-arrow-up"></span></span> -->
                    </div>
                    <span ng-show="field.isExpand" class="dashicons dashicons-arrow-up"></span>
                    <div ng-if="showDescDesign['design']" class="desc"><?php echo $desc_design; ?></div>
                </div>
                <div class="col-md-6 nbd-action-wrap">
                    <a data-url="<?php echo home_url().'/upload-file'; ?>" href="<?php echo home_url().'/upload-file'; ?>" class="button alt nbdesign-button upload-design kita-link-upload">
                        <span ><?php echo $label_upload ; ?></span>
                        <span class="nb-cs-help-tip">
                            <span class="data-tip"><?php echo nbdesigner_get_option('nbd_desc_button_upload'); ?></span>
                        </span>
                        <!-- <span class="show-desc" ng-click="showDescDesign('upload')" ><span ng-show="!showDescDesign['upload']" class="dashicons dashicons-arrow-down"></span><span ng-show="showDescDesign['upload']" class="dashicons dashicons-arrow-up"></span></span> -->
                    </a>
                    <div ng-if="showDescDesign['upload']" class="desc"><?php echo $desc_upload; ?></div>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('.nb_button_custom_design .nb-cs-help-tip').mouseover(function() {
                            $('.nb_button_custom_design .nb-cs-help-tip').removeClass('active');
                            $(this).addClass('active');
                        })
                        $('.nb_button_custom_design .nb-cs-help-tip').mouseleave(function() {
                            $('.nb_button_custom_design .nb-cs-help-tip').removeClass('active');
                        })
                    })
                </script>
                <?php endif; ?>
            </div>
            <div class="nb-bottom-wrap">
                <div class="content">
                    <span class="text">
                        Perlu cetak bentuk lain? Hubungi kami
                    </span>
                    <a href="<?php echo home_url(). '/product/special-request/'; ?>" class="text">
                        di sini
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
}
add_action( 'nb_button_custom_design_disable' , 'nb_button_custom_design_disable' );
function nb_button_custom_design_disable( $pid ) {
    $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true ); 
    if ( $is_nbdesign ) {
        $_enable_upload                 = get_post_meta( $pid, '_nbdesigner_enable_upload', true );
        $label_design                   = apply_filters( 'nbd_start_design_label', esc_html__( 'Mulai Desain', 'web-to-print-online-designer' ) );
        $label_upload                   = apply_filters( 'nbd_start_design_and_upload_label', esc_html__( 'Upload Desain Kamu', 'web-to-print-online-designer' ) );

        ?>
            <div class="nbdesigner_frontend_container nb_button_custom_design">
                <input name="nbd-add-to-cart" type="hidden" value="<?php echo( $pid ); ?>" />
                <div class="nbd-actions-wrap row">
                    <?php if( $is_nbdesign ): ?>
                    <div class="col-md-6 nbd-action-wrap">
                        <a class="button alt nbdesign-button start-design" style="background-color: #d2b690; color: #8c8989">
                            <span><?php echo $label_design; ?></span>
                        </a>
                    </div>
                    <div class="col-md-6 nbd-action-wrap">
                        <a class="button alt nbdesign-button upload-design" style="background-color: #9a9a9a; color: #8c8989">
                            <span><?php echo $label_upload ; ?></span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="nb-bottom-wrap nb-bottom-wrap-none">
                    <div class="content">
                        <span class="text">
                            Perlu cetak bentuk lain? Hubungi kami
                        </span>
    <!-- <a href="http://staging.kitalabel.com/special-request/" class="text"> -->
    <!--Steve change here 7 APRIL 2022  -->
            <a href="http://staging.kitalabel.com/product/request-print-custom/" class="text">
                            di sini
                        </a>
                    </div>
                </div>
            </div>
        <?php
    }
}

add_action( 'nb_custom_area_design' , 'custom_button_design' );
function custom_button_design($pid) {
    $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true ); 
    $variation_id   = 0;
    if ( $is_nbdesign ) {
        $product    = wc_get_product( $pid );
        $type       = $product->get_type();
        $option     = unserialize( get_post_meta( $pid, '_nbdesigner_option', true ) );
        $layout     = isset( $option['layout'] ) ? $option['layout'] : 'm';
        if( $layout == 'v' ) {
            $_enable_upload_without_design = get_post_meta( $pid, '_nbdesigner_enable_upload_without_design', true );
            if( !$_enable_upload_without_design ) return;
        }
        /* Multi language with WPML */
        if( count( $_REQUEST ) ){
            $attributes = array();
            $layout     = nbdesigner_get_option( 'nbdesigner_design_layout' );
            if( $layout == "c" ){
                foreach ( $_REQUEST as $key => $value ){
                    if ( strpos( $key, 'attribute_' ) === 0 ) {
                        $attributes[$key] = $value;
                    }
                }
                if( count( $attributes ) ){
                    if ( class_exists( 'WC_Data_Store' ) ) {
                        $data_store     = WC_Data_Store::load( 'product' );
                        $variation_id   = $data_store->find_matching_product_variation( $product, $attributes );
                    }else{
                        $variation_id = $product->get_matching_variation( $attributes );
                    }
                }    
            }
        }
        $site_url = site_url();
        if ( class_exists( 'SitePress' ) ) {
            $site_url = home_url();
        }
        $src = add_query_arg( array( 'action' => 'nbdesigner_editor_html', 'product_id' => $pid ), $site_url . '/' );
        if( isset( $_POST['variation_id'] ) &&  $_POST['variation_id'] != '' ){
            $src .= '&variation_id='. absint( $_POST['variation_id'] );
        }
        if( isset( $_GET['nbds-ref'] ) ){
            $src .= '&reference='. $_GET['nbds-ref'];
        }
        if( isset( $_GET['nbo_cart_item_key'] ) && $_GET['nbo_cart_item_key'] !='' ){
            $nbd_item_key = WC()->session->get( $_GET['nbo_cart_item_key'] . '_nbd' );
            if( $nbd_item_key ) {
                $src .= '&task=edit&nbd_item_key=' . $nbd_item_key . '&cik=' . $_GET['nbo_cart_item_key'];
            }
        }
        if( $variation_id != 0 ){
            $src .= '&variation_id='. $variation_id;
        }
        if( $type == 'variable' && isset( $option['bulk_variation'] ) && $option['bulk_variation'] == 1 ){
            $src .= '&variation_id=0';
        }
        if( isset( $_GET['nbo_cart_item_key'] ) && $_GET['nbo_cart_item_key'] != '' ){
            $src .= '&nbo_cart_item_key=' . $_GET['nbo_cart_item_key'];
        }
        $extra_price = '';
        if( $option['extra_price'] && ! $option['request_quote'] ){
            $extra_price = $option['type_price'] == 1 ? wc_price( $option['extra_price'] ) : $option['extra_price'] . ' %';
        }
        include(CUSTOM_KITALABEL_PATH .'templates/nbd-button-design.php');
    }
}

add_filter( 'nbo_show_edit_option_link_in_cart', 'nbo_show_edit_option_link_in_cart' , 1, 2 );
function nbo_show_edit_option_link_in_cart( $result , $cart_item ) {
    if( isset( $cart_item['nbo_meta'] ) ) {
        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
        if( !empty( $fields['combination']['combination_selected']) && count($fields['combination']['combination_selected']) > 0 ) {
            return '';
        }
    }
}


// change custom edit design in the cart
add_filter( 'nb_custom_after_cart_item_name', 'nb_custom_render_cart_1' , 1, 4 );
function nb_custom_render_cart_1( $title = null, $cart_item = null, $cart_item_key = null, $custom_upload = array() ) {
    if (  $cart_item_key && ( is_cart() || is_checkout() )) {
        $nbd_session = WC()->session->get($cart_item_key . '_nbd');
        $nbu_session = WC()->session->get($cart_item_key . '_nbu');
        if( isset($cart_item['nbd_item_meta_ds']) ){
            if( isset($cart_item['nbd_item_meta_ds']['nbd']) ) $nbd_session = $cart_item['nbd_item_meta_ds']['nbd'];
            if( isset($cart_item['nbd_item_meta_ds']['nbu']) ) $nbu_session = $cart_item['nbd_item_meta_ds']['nbu'];
        }
        $_show_design                   = nbdesigner_get_option('nbdesigner_show_in_cart', 'yes');
        $_show_design                   = apply_filters( 'nbd_show_design_section_in_cart', $_show_design, $cart_item );
        $enable_edit_design             = nbdesigner_get_option('nbdesigner_show_button_edit_design_in_cart', 'yes') == 'yes' ? true : false;
        $show_edit_link                 = apply_filters('nbd_show_edit_design_link_in_cart', $enable_edit_design, $cart_item);
        $product_id                     = $cart_item['product_id'];
        $variation_id                   = $cart_item['variation_id'];
        $product_id                     = get_wpml_original_id( $product_id );
        $is_nbdesign                    = get_post_meta($product_id, '_nbdesigner_enable', true);
        $_enable_upload                 = get_post_meta($product_id, '_nbdesigner_enable_upload', true);
        $_enable_upload_without_design  = get_post_meta($product_id, '_nbdesigner_enable_upload_without_design', true);
        $_product                       = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_permalink              = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
        if ( $is_nbdesign && $_show_design == 'yes' ) {
            $html = '';
            // $html .= '<div class="custom-side-quantity">';
            $layout = nbd_get_product_layout( $product_id );
            if( isset( $nbd_session ) ){
                $id             = 'nbd' . $cart_item_key;
                $redirect       = is_cart() ? 'cart' : 'checkout';
                $list           = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/preview' );
                $list           = nbd_sort_file_by_side( $list );
                $list = array_values($list);
                if( isset( $cart_item['nbo_meta'] ) ) {
                    $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
                    if( isset( $fields['combination'] ) && isset( $fields['combination']['side']) && count($fields['combination']['side']) > 0 ) {
                        $side = $fields['combination']['side'];
                        $qty_min = isset( $fields['combination']['min_qty'] ) ? $fields['combination']['min_qty'] : $cart_item['quantity'];
                    }
                }
                $quantity = '';
                $config = nbd_get_data_from_json( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/config.json' );
                $product_config = isset($config->product) ? $config->product : array();
                if( isset($side) ) {
                    foreach ( $side as $key => $qty ) {
                        if( is_cart() && isset($side ) && isset($qty_min) ) {
                            $quantity = '<span class="box"><input type="number" data-min-qty="'.$qty_min.'" data-item-key="'.$cart_item_key.'" class="nb-custom-qty-side input-text qty text" step="1" min="1" max="" name="qty_side['.$cart_item_key.']['.$key.']" value="'.$qty.'" title="Qty"></span>';
                        } else {
                           $quantity = $qty; 
                        }
                        $index = $key + 1;
                        $design_name = 'Design '.$index;
                        if( isset($product_config[$key]->orientation_name) && $product_config[$key]->orientation_name ) {
                            $design_name = $product_config[$key]->orientation_name;
                        }
                        if(isset( $list[$key] ) ) {
                            $src    = Nbdesigner_IO::convert_path_to_url( $list[$key] ) . '?&t=' . round( microtime( true ) * 1000 );
                            $html  .= '<tr class="nb-cart_item_design"><td class="nb-col-hiden"></td><td class="nb-has-border-bottom"><div class="nb-image"><img class="nbd_cart_item_design_preview" src="' . $src . '"/></div></td><td class="nb-has-border-bottom nb-name">'.$design_name.'</td><td class="nb-has-border-bottom nb-col-modile-hiden"></td><td class="nb-has-border-bottom nb-qty">'.$quantity.'</td><td class="nb-col-modile-hiden"></td></tr>';
                        }                      
                    }
                }
            }
            else if( $is_nbdesign && !$_enable_upload_without_design && $show_edit_link ){
                $id = 'nbd' . $cart_item_key; 
                $redirect = is_cart() ? 'cart' : 'checkout';
                $link_create_design = add_query_arg(
                    array(
                        'task'          => 'new',
                        'task2'         => 'update',
                        'product_id'    => $product_id,
                        'variation_id'  => $variation_id,
                        'cik'           => $cart_item_key,
                        'view'          => $layout,
                        'rd'            => $redirect),
                    getUrlPageNBD('create'));
                if( $layout == 'v' ){
                    $link_create_design = add_query_arg(
                        array(
                            'nbdv-task'     => 'new',
                            'task'          => 'new',
                            'task2'         => 'update',
                            'product_id'    => $product_id,
                            'variation_id'  => $variation_id,
                            'cik'           => $cart_item_key,
                            'view'          => $layout,
                            'rd'            => $redirect),
                        $product_permalink );
                }
                if( $product_permalink ){
                    $att_query = parse_url( $product_permalink, PHP_URL_QUERY );
                    $link_create_design .= '&'.$att_query;
                }
            }
            if( isset( $nbu_session ) ){
                $id             = 'nbu' . $cart_item_key; 
                $redirect       = is_cart() ? 'cart' : 'checkout';
                $html          .= '<div id="'.$id.'" class="nbd-cart-upload-file nbd-cart-item-upload-file">';
                $files          = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR . '/' . $nbu_session );
                $create_preview = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
                $upload_html    = '';
                foreach ( $files as $file ) {
                    $ext        = pathinfo( $file, PATHINFO_EXTENSION );
                    $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                    $file_url   = Nbdesigner_IO::wp_convert_path_to_url( $file );
                    if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                        $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                        $filename   = pathinfo( $file, PATHINFO_BASENAME );
                        if( file_exists($dir.'_preview/'.$filename) ){
                            $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename );
                        }else if( $ext == 'pdf' && file_exists($dir.'_preview/'.$filename.'.jpg' ) ){
                            $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename.'.jpg' );
                        }else{
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                    }else {
                        $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                    }
                    $upload_html .= '<div class="nbd-cart-item-upload-preview-wrap"><a target="_blank" href='.$file_url.'><img class="nbd-cart-item-upload-preview" src="' . $src . '"/></a><p class="nbd-cart-item-upload-preview-title">'. basename($file).'</p></div>';
                }
                $upload_html = apply_filters('nbu_cart_item_html', $upload_html, $cart_item, $nbu_session);
                $html .= $upload_html;
                $html .= '</div>';
            }
            if($nbd_session || $nbu_session) {
                return $html;
            }
        } 
    }
    if(count($custom_upload) > 0) {
        $html = '';
        if(isset($custom_upload['val']['files']) && isset($custom_upload['val']['files']) && isset($custom_upload['val']['variants']) && isset($custom_upload['val']['qtys'])) {
            if(is_array($custom_upload['val']['files']) && count($custom_upload['val']['files']) > 0) {
                $pre_html = ( is_cart() && !is_checkout()) ? '' : '<tr><td>Side</td><td>Name</td><td colspan="3">Qty</td></tr>';
                foreach ( $custom_upload['val']['files'] as $key => $file ) {
                    $_file = explode('/', $file);
                    $file_name = isset($_file[1]) ? $_file[1]: $file;
                    if(strlen($file_name) > 20) {
                       $file_name = '...'.substr($file_name, strlen($file_name) - 20); 
                    }
                    $index = $key + 1;
                    $qty = isset($custom_upload['val']['qtys'][$key]) ? $custom_upload['val']['qtys'][$key] : 1;
                    $variant_name = isset($custom_upload['val']['variants'][$key]) ? $custom_upload['val']['variants'][$key] : 'Variant '.$index;
                    $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$file );
                    $qty_min = $custom_upload['min_qty'];
                    if( is_cart() && isset($qty_min) ) {
                        $quantity = '<span class="box"><input type="number" data-min-qty="'.$qty_min.'" data-item-key="'.$cart_item_key.'" class="nb-custom-qty-side input-text qty text" step="1" min="1" max="" name="qty_side['.$cart_item_key.']['.$key.']" value="'.$qty.'" title="Qty"></span>';
                    } else {
                       $quantity = $qty; 
                    }

                    $edit_design = '';
                    $delete_design = '';
                    
                    if(isset($cart_item['nbo_meta']['order_again']) && $cart_item['nbo_meta']['order_again']) {
                        $edit_design = '<div class="button edit-upload-design" href="#">' . esc_html__('Edit design', 'web-to-print-online-designer') . '</div><input data-design-index="'.$key.'" type="file" class="kita-upload-file" style="display:none" />';
                        $delete_design = '<div class="button trash-icon-upload-design" data-design-index="'.$key.'" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/></svg></div';
                    }

                    $html  .= $pre_html . '<tr class="nb-cart_item_design"><td class="nb-col-hiden"></td><td class="nb-has-border-bottom nb-name"><div class="nb-upload-design"><a class="nbd_cart_item_upload_preview" href="' . $file_url . '">'.$file_name.'</a></div></td><td class="nb-has-border-bottom nb-name">'.$variant_name.'</td><td class="nb-has-border-bottom nb-col-modile-hiden"></td><td class="nb-has-border-bottom nb-qty">'.$quantity.'</td><td class="nb-col-modile-hiden">'.$edit_design.'</td><td class="nb-col-modile-hiden">'.$delete_design.'</td></tr>';
                    $pre_html = '';                  
                }
                if($edit_design) {
                    $cancel = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"></path></svg>';
                    $html .= $pre_html . '<tr class="nb-cart_item_design tambah-variant-options"><td></td><td><input type="file" name="variant-file" /></td><td colspan="2"><input placeholder="Variant name" type="text" name="variant-name"/><td><input min="1" value="1" type="number" name="variant-qty" /></td><td colspan="1"><div class="button add-upload-design" data-item-key="'.$cart_item_key.'">Add design</div><td><div class="button cancel-add-upload-design">'.$cancel.'</div></td></tr>'. ( (is_cart() && !is_checkout()) ? '<tr class="nb-cart_item_design tambah-variant-button"><td colspan="7"><div class="button show-add-upload-design">Tambah Variant</div></td></tr>' : '');
                }
            }
        }
        return $html;
    }
}

// remove edit design old in the cart
add_action( 'woocommerce_order_item_meta_end', 'nb_custom_order_item_meta_end' , 30, 3 );
function nb_custom_order_item_meta_end( $item_id, $item, $order ) {
    if( isset( $item["item_meta"]["_nbo_options"] ) ) {
        $fields = unserialize( base64_decode( $item["item_meta"]["_nbo_options"]['fields']) ) ;
        if( isset( $fields['combination'] ) && isset( $fields['combination']['side']) && count($fields['combination']['side']) > 0 ) {
            remove_action( 'woocommerce_order_item_meta_end', array( 'Nbdesigner_Plugin' , 'woocommerce_order_item_meta_end' ) , 30  );
        }
    }
}

add_action( 'nb_custom_order_item_meta_end', 'nb_custom_order_item_meta_end_1' , 30, 2 );
add_action( 'woocommerce_after_order_itemmeta', 'nb_custom_order_item_meta_end_1' , 30, 2 );
function nb_custom_order_item_meta_end_1( $item_id, $item ){
    $nbd_item_key = wc_get_order_item_meta( $item_id, '_nbd' );
    $nbu_item_key = wc_get_order_item_meta( $item_id, '_nbu' );
    if( $nbd_item_key || $nbu_item_key ){
        $html = '';
        $show_design_in_order = nbdesigner_get_option( 'nbdesigner_show_in_order', 'yes' );
        if( ( isset( $item["item_meta"]["_nbd"] ) || isset( $item["item_meta"]["_nbu"] ) ) && $show_design_in_order == 'yes' ){
            $product_id     = $item['product_id'];
            $layout         = nbd_get_product_layout( $product_id );
            if( isset( $item["item_meta"]["_nbd"]  ) ){
                $nbd_item_key   = $item["item_meta"]["_nbd"]; 
                $id             = 'nbd' . $item_id;
                $redirect       = is_cart() ? 'cart' : 'checkout';
                $html          .= '<div id="' . $id . '" class="nbd-custom-dsign nbd-cart-item-design">';
                // $remove_design  = is_cart() ? '<a class="remove nbd-remove-design nbd-cart-item-remove-design" href="#" data-type="custom" data-cart-item="' . $cart_item_key . '">&times;</a>' : '';
                // $html          .= '<p>' . esc_html__('Custom design', 'web-to-print-online-designer') . $remove_design . '</p>';
                $list           = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/preview' , 1 );
                $list           = nbd_sort_file_by_side( $list );
                $list = array_values($list);
                $html .= '<div class="table table-bordered nb-custom-table"><div class="thead"><div class="tr"><div class="th">Side</div><div class="th">Name</div><div class="th">Qty</div>';
                $html .= is_cart() ? '<div class="th">Action</div>' : '';
                $html .= '</div></div><div class="tbody">';
                $link_edit_design = add_query_arg(
                    array(
                        'task'          => 'edit',
                        'product_id'    => $product_id,
                        'view'          => $layout,
                        'oid'           => $item['order_id'],
                        'item_id'       => $id,
                        'design_type'   => 'edit_order',
                        'rd'            => 'order',
                        'nbd_item_key'  => $nbd_item_key ),
                    getUrlPageNBD( 'create' ) );
                if( $layout == 'v' ){
                    $link_edit_design = add_query_arg(
                        array(
                            'nbdv-task'     => 'edit',
                            'task'          => 'edit',
                            'product_id'    => $product_id,
                            'oid'           => $item['order_id'],
                            'item_id'       => $id,
                            'design_type'   => 'edit_order',
                            'rd'            => 'order',
                            'nbd_item_key'  =>  $nbd_item_key),
                        get_permalink( $product_id ) );
                }
                if( $item['variation_id'] > 0){
                    $link_edit_design .= '&variation_id=' . $item['variation_id'];
                }
                $buton = '<a class="button nbd-edit-design" href="'.$link_edit_design.'">'. esc_html__('Edit design', 'web-to-print-online-designer') .'</a>';
                if( isset( $item["item_meta"]["_nbo_options"] ) ) {
                    $fields = unserialize( base64_decode( $item["item_meta"]["_nbo_options"]['fields']) ) ;
                    if( isset( $fields['combination'] ) && isset( $fields['combination']['side']) && count($fields['combination']['side']) > 0 ) {
                        $side = $fields['combination']['side'];
                        $qty_min = isset( $fields['combination']['min_qty'] ) ? $fields['combination']['min_qty'] : $item['quantity'];
                    }
                }
                $quantity = '';
                $product_config = nbd_get_data_from_json( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/config.json' )->product;
                if( isset($side) ) {
                   foreach ( $side as $key => $qty ) {
                        $quantity = $qty; 
                        $index = $key + 1;
                        $design_name = 'Design '.$index;
                        if( isset($product_config[$key]->orientation_name) && $product_config[$key]->orientation_name ) {
                            $design_name = $product_config[$key]->orientation_name;
                        }
                        if(isset( $list[$key] ) ) {
                           $src    = Nbdesigner_IO::convert_path_to_url( $list[$key] ) . '?&t=' . round( microtime( true ) * 1000 );
                            if( $key == 0 && is_cart() ) {
                                $html  .= '<div class="tr"><div class="td"><img class="nbd_cart_item_design_preview" src="' . $src . '"/></div><div class="td">'.$design_name.'</div><div class="td">'.$quantity.'</div><td class="row-middle" rowspan="'.count($side).'">'.$buton.'</div></div>';
                            } else {
                                $html  .= '<div class="tr"><div class="td"><img class="nbd_cart_item_design_preview" src="' . $src . '"/></div><div class="td">'.$design_name.'</div><div class="td">'.$quantity.'</div></div>';
                            } 
                        }                      
                    } 
                }
                $html .= '</div></div></div>';
            }
        }
        echo $html;
    }
    if( isset($item["item_meta"]) && isset($item["item_meta"]['_nbo_option_price']) && isset($item["item_meta"]['_nbo_option_price']['fields']) ) {
        $html = '';
        foreach($item["item_meta"]['_nbo_option_price']['fields'] as $field) {
            if(isset($field['is_custom_upload'])) {
                $html .= '<div class="custom-side-quantity">';
                $html .= '<div class="table table-bordered nb-custom-table"><div class="thead"><div class="tr"><div class="th">Side</div><div class="th">Name</div><div class="th">Qty</div>';
                $html .= '</div></div><div class="tbody">';

                $custom_upload = $field;
                if(isset($custom_upload['val']['files']) && isset($custom_upload['val']['files']) && isset($custom_upload['val']['variants']) && isset($custom_upload['val']['qtys'])) {
                    if(is_array($custom_upload['val']['files']) && count($custom_upload['val']['files']) > 0) {
                        foreach ( $custom_upload['val']['files'] as $key => $file ) {
                            $_file = explode('/', $file);
                            $file_name = isset($_file[1]) ? $_file[1]: $file;

                            if(strlen($file_name) > 20) {
                               $file_name = '...'.substr($file_name, strlen($file_name) - 20);
                            }
                            $index = $key + 1;
                            $qty = isset($custom_upload['val']['qtys'][$key]) ? $custom_upload['val']['qtys'][$key] : 1;
                            $variant_name = isset($custom_upload['val']['variants'][$key]) ? $custom_upload['val']['variants'][$key] : 'Variant '.$index;


                            $ext = pathinfo( $file, PATHINFO_EXTENSION );
                            
                            $new_name = strtoupper($variant_name) . '.' . $ext;

                            $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$file );
                            $qty_min = $custom_upload['min_qty'];
                            if( is_cart() && isset($qty_min) ) {
                                $quantity = '<span class="box"><input type="number" data-min-qty="'.$qty_min.'" data-item-key="'.$cart_item_key.'" class="nb-custom-qty-side input-text qty text" step="1" min="1" max="" name="qty_side['.$cart_item_key.']['.$key.']" value="'.$qty.'" title="Qty"></span>';
                            } else {
                               $quantity = $qty; 
                            }
                            $file_name_download = $item['order_id'] . '_' . $item->get_name() . '_' . $new_name;
                            
                            $html  .= '<div class="tr"><div class="td"><a class="nbd_cart_item_upload_preview" href="' . $file_url . '" download="' . $file_name_download . '">'.$file_name.'</a></div><div class="td">'.$variant_name.'</div><div class="td">'.$quantity.'</div></div>'; 
                                
                        }
                    }

                }
                $html .= '</div></div></div>';
            }
        }
        echo $html;
    }
}
// note in the order 
add_action( 'woocommerce_after_order_itemmeta', 'nb_custom_note_item_in_order' , 40, 2 );
function nb_custom_note_item_in_order( $item_id, $item ){
    $note = '';
    $roll_form ='on';
    if( isset($item['nb_custom_note']) ) {
        $note = $item['nb_custom_note']['comment'];
        $roll_form = $item['nb_custom_note']['roll_form'] == 'on' ? 'yes' : 'no';
        echo '<div><b>Customer\'s note: </b><span>'.$note.'</span></div><div><span><b>Interested in roll form: </b></span><span>'.$roll_form.'</span></div>';
    }
}
// note in the invoice 
add_action( 'wpo_wcpdf_after_item_meta', 'nb_custom_note_item_invoice' , 20, 3 );
function nb_custom_note_item_invoice( $type, $item, $order ){
    $note = '';
    $roll_form ='on';
    if( isset($item['nb_custom_note']) ) {
        $note = $item['nb_custom_note']['comment'];
        $roll_form = $item['nb_custom_note']['roll_form'] == 'on' ? 'yes' : 'no';
        echo '<div><b>Customer\'s note: </b><span>'.$note.'</span></div><div><span><b>Interested in roll form: </b></span><span>'.$roll_form.'</span></div>';
    }
}
// change template side in pdf invoice
add_action( 'wpo_wcpdf_after_item_meta', 'nb_custom_wcpdf_after_item_meta' , 10, 3 );
function nb_custom_wcpdf_after_item_meta( $type, $item, $order ){
    $item_id = $item['item_id'];
    $item = $item['item'];
    $nbd_item_key = wc_get_order_item_meta( $item_id, '_nbd' );
    $nbu_item_key = wc_get_order_item_meta( $item_id, '_nbu' );
    if( $nbd_item_key || $nbu_item_key ){
        $show_design_in_order = nbdesigner_get_option( 'nbdesigner_show_in_order', 'yes' );
        if( ( isset( $item["item_meta"]["_nbd"] ) || isset( $item["item_meta"]["_nbu"] ) ) && $show_design_in_order == 'yes' ){
            $html = '';
            $html .= '<style>.custom-side-quantity{width: 100%;} .custom-side-quantity .nbd_cart_item_design_preview {max-width: 80px;max-height: 80px;object-fit: cover;object-position: 50% 50%;margin: 0;} div.table {text-align: center;width:  100%;display: table;border-collapse: separate;box-sizing: border-box;text-indent: initial;border-spacing: 2px;bordeR: 1px solid #e0e0e0!important; } div.thead {display: table-header-group;vertical-align: middle;bordeR: 1px solid #e0e0e0!important; } div.tbody {display: table-row-group;vertical-align: inherit;bordeR: 1px solid #e0e0e0!important; } div.tr {display: table-row;vertical-align: inherit;bordeR: 1px solid #e0e0e0!important; } div.th {font-weight: 700;padding:  5px;display: table-cell;vertical-align: inherit;bordeR: 1px solid #e0e0e0!important; } div.td {padding:  5px;display: table-cell;vertical-align: inherit;bordeR: 1px solid #e0e0e0!important; }</style>';
            $html .= '<div class="custom-side-quantity">';
            $product_id     = $item['product_id'];
            $layout         = nbd_get_product_layout( $product_id );
            if( isset( $item["item_meta"]["_nbd"]  ) ){
                $nbd_item_key   = $item["item_meta"]["_nbd"]; 
                $id             = 'nbd' . $item_id;
                $redirect       = is_cart() ? 'cart' : 'checkout';
                $html          .= '<div id="' . $id . '" class="nbd-custom-dsign nbd-cart-item-design">';
                // $remove_design  = is_cart() ? '<a class="remove nbd-remove-design nbd-cart-item-remove-design" href="#" data-type="custom" data-cart-item="' . $cart_item_key . '">&times;</a>' : '';
                // $html          .= '<p>' . esc_html__('Custom design', 'web-to-print-online-designer') . $remove_design . '</p>';
                $list           = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/preview' , 1 );
                $list           = nbd_sort_file_by_side( $list );
                $list = array_values($list);
                $html .= '<div class="table table-bordered nb-custom-table"><div class="thead"><div class="tr"><div class="th">Side</div><div class="th">Name</div><div class="th">Qty</div>';
                $html .= is_cart() ? '<div class="th">Action</div>' : '';
                $html .= '</div></div><div class="tbody">';
                $link_edit_design = add_query_arg(
                    array(
                        'task'          => 'edit',
                        'product_id'    => $product_id,
                        'view'          => $layout,
                        'oid'           => $item['order_id'],
                        'item_id'       => $id,
                        'design_type'   => 'edit_order',
                        'rd'            => 'order',
                        'nbd_item_key'  => $nbd_item_key ),
                    getUrlPageNBD( 'create' ) );
                if( $layout == 'v' ){
                    $link_edit_design = add_query_arg(
                        array(
                            'nbdv-task'     => 'edit',
                            'task'          => 'edit',
                            'product_id'    => $product_id,
                            'oid'           => $item['order_id'],
                            'item_id'       => $id,
                            'design_type'   => 'edit_order',
                            'rd'            => 'order',
                            'nbd_item_key'  =>  $nbd_item_key),
                        get_permalink( $product_id ) );
                }
                if( $item['variation_id'] > 0){
                    $link_edit_design .= '&variation_id=' . $item['variation_id'];
                }
                $buton = '<a class="button nbd-edit-design" href="'.$link_edit_design.'">'. esc_html__('Edit design', 'web-to-print-online-designer') .'</a>';
                if( isset( $item["item_meta"]["_nbo_options"] ) ) {
                    $fields = unserialize( base64_decode( $item["item_meta"]["_nbo_options"]['fields']) ) ;
                    if( isset( $fields['combination'] ) && isset( $fields['combination']['side']) && count($fields['combination']['side']) > 0 ) {
                        $side = $fields['combination']['side'];
                        $qty_min = isset( $fields['combination']['min_qty'] ) ? $fields['combination']['min_qty'] : $item['quantity'];
                    }
                }
                $quantity = '';
                $product_config = nbd_get_data_from_json( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/config.json' )->product;
                if( isset($side) ) {
                   foreach ( $side as $key => $qty ) {
                        $quantity = $qty; 
                        $index = $key + 1;
                        $design_name = 'Design '.$index;
                        if( isset($product_config[$key]->orientation_name) && $product_config[$key]->orientation_name ) {
                            $design_name = $product_config[$key]->orientation_name;
                        }
                        if(isset( $list[$key] ) ) {
                           $src    = Nbdesigner_IO::convert_path_to_url( $list[$key] ) . '?&t=' . round( microtime( true ) * 1000 );
                            if( $key == 0 && is_cart() ) {
                                $html  .= '<div class="tr"><div class="td"><img class="nbd_cart_item_design_preview" src="' . $src . '"/></div><div class="td">'.$design_name.'</div><div class="td">'.$quantity.'</div><td class="row-middle" rowspan="'.count($side).'">'.$buton.'</div></div>';
                            } else {
                                $html  .= '<div class="tr"><div class="td"><img class="nbd_cart_item_design_preview" src="' . $src . '"/></div><div class="td">'.$design_name.'</div><div class="td">'.$quantity.'</div></div>';
                            } 
                        }                      
                    } 
                }
                $html .= '</div></div></div>';
            }
        }
        echo $html;
    }
}

function nb_sort_quantity_breaks($a, $b) {
    if ($a['qty'] == $b['qty']) {
        return 0;
    }
    return ($a['qty'] < $b['qty']) ? -1 : 1;
}
function nb_array_diff($array1, $array2) {
    if(count($array1) != count($array2)) return;
    $results = array();
    foreach( $array1 as $key => $value ) {
        if( isset($array2[$key]) && $array2[$key] != $value) {
            $results[$key] = $value;
        }
    }
    return $results;
}
add_action( 'before_nbd_save_customer_design' , 'nb_custom_update_cart' , 1 , 1);
function nb_custom_update_cart() {
    $item_combination = array();
    $cart_item_key = isset($_POST['cart_item_key']) ? $_POST['cart_item_key'] : 0;
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
    $task = isset($_POST['task'])? $_POST['task']: '';
    $options_old = array();
    $cart_items = WC()->cart->get_cart();

    if( isset( $cart_items[$cart_item_key] )) {
        $item =  $cart_items[$cart_item_key];
        if( isset( $item['nbo_meta'] ) ) {
            $options_old = $item['nbo_meta']['field'];
        }
    }
    $data = array();
    if( isset( $_POST['options-update'] ) && $task == 'edit' ) {
        parse_str($_POST['options-update'], $data);
        $options_new = $data['nbd-field'];
        if(count(nb_array_diff($options_new, $options_old)) > 0) {
            $fe_options = new NBD_FRONTEND_PRINTING_OPTIONS;
            $option_id = $fe_options->get_product_option($product_id);
            $options        = $fe_options->get_option( $option_id );
            $option_fields  = unserialize( $options['fields'] );
            if( !empty($option_fields['combination']['enabled']) && $option_fields['combination']['enabled'] == 'on' && !empty($option_fields['combination']['options']) ) {
                $combination_options = $option_fields['combination']['options'];
                foreach($options_new as $key => $val) {
                    if(is_array($val) && isset($val['value']) ) {
                        $val = $val['value'];
                    }
                    $_origin_field   = $fe_options->get_field_by_id( $option_fields, $key );
                    if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'area' ) {
                        $area_name = $_origin_field['general']['attributes']['options'][$val]['name'];
                        $_area_name = $_origin_field['general']['attributes']['options'][$val]['name'];
                        if( $area_name == 'Square' || $area_name == 'Circle' ) {
                            $_area_name = 'Square + Circle';
                        }
                        if( $area_name == 'Rectangle' || $area_name == 'Oval' ) {
                            $_area_name = 'Rectangle + Oval';
                        }
                    }
                    if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'size' ) {
                        $size_name = $_origin_field['general']['attributes']['options'][$val]['name'];
                    }
                    if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'color' ) {
                        $material_name = $_origin_field['general']['attributes']['options'][$val]['name'];
                    }
                }
                if( isset($_area_name) && isset($size_name) && isset($material_name) && !empty($combination_options[$_area_name][$size_name][$material_name]) ) {
                    $side = $combination_options[$_area_name][$size_name][$material_name];
                    if(!$side && isset($combination_options['default'])) {
                        $side = $combination_options['default'];
                    }
                }
            }
            if( isset($item) ) {
                $nbd_field = $fe_options->validate_before_processing( $option_fields, $options_new );
                if(isset($side)) {
                    $quantity =(int) $side['qty'];
                    $min_quantity =(int) $side['qty'];
                    if(!$quantity) {
                        $quantity = (int) $item['quantity'];
                    }
                    //custom kitalabel
                    $number_side = 0;
                    $side_page = array();
                    foreach( $nbd_field as $key => $val) {
                        $origin_field   = $fe_options->get_field_by_id( $option_fields, $key );
                        if( isset($origin_field['nbd_type']) && ( $origin_field['nbd_type'] == 'page' || $origin_field['nbd_type'] == 'page1' ) ) {
                            if($key == $origin_field['id']) {
                                $number_side = (int) ($val);
                            }
                        }
                    }
                    if($number_side > 0) {
                        $a = (int) ( ( $quantity - ( $quantity % $number_side ) ) / $number_side );
                        $b =  (int) ( $quantity % $number_side );
                        for( $i = 1 ; $i <= $number_side ; $i++) {
                            if($i == $number_side) {
                                $side_page[] = $a + $b;
                            } else {
                                $side_page[] = $a;
                            }
                        }
                    } else {
                        $side_page[] = $quantity;
                    }
                    if( isset($option_fields['combination'])) {
                        $option_fields['combination']['side'] = $side_page;
                        $option_fields['combination']['qty_breaks'] = $side['qty_breaks'];
                        $option_fields['combination']['combination_selected'] = $side;
                        $option_fields['combination']['min_qty'] = $min_quantity;
                    }
                    $item['nbo_meta']['original_price'] = $side['price'];
                }
                unset($option_fields['combination']['options']);
                $_fields = serialize($option_fields);
                $item['nbo_meta']['field'] = $nbd_field;
                $_fields = base64_encode( $_fields );
                $item['nbo_meta']['options']['fields']   = $_fields;
                WC()->cart->cart_contents[$cart_item_key] = $item;
                WC()->cart->set_quantity( $cart_item_key, $quantity, true );
                WC()->cart->set_session();
            }
        }
    }
}

add_filter( 'woocommerce_cart_item_quantity' , 'nb_custom_input_cart_item' , 1 , 3);
function nb_custom_input_cart_item( $product_quantity, $cart_item_key, $cart_item ) {
    if( isset( $cart_item['nbo_meta'] ) ) {
        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
        $nbd_session = WC()->session->get($cart_item_key . '_nbd');
        if( isset($cart_item['nbd_item_meta_ds']) ) {
           if( isset($cart_item['nbd_item_meta_ds']['nbd']) ) $nbd_session = $cart_item['nbd_item_meta_ds']['nbd'];
        }
        $has_upload = false;
        if(isset($cart_item['nbo_meta']['option_price']) && $cart_item['nbo_meta']['option_price']['fields'] && is_array($cart_item['nbo_meta']['option_price']['fields'])) {
            foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $field)  {
                if(isset($field['is_custom_upload'])) {
                    $has_upload = true;
                }
            }
        }
        if( !empty( $fields['combination']['combination_selected']) && count($fields['combination']['combination_selected']) > 0  ) {
            if( isset($nbd_session) || $has_upload ) {
                $product_quantity = str_replace('<input' , '<input disabled class="text-center" style="max-width:80px"' , $product_quantity );
                $product_quantity = str_replace('input-text' , 'nb-input-text' , $product_quantity );
                $product_quantity = str_replace('class="quantity"' , 'class="nb-quantity"' , $product_quantity );
            } else {
                $product_quantity = str_replace('class="quantity"' , 'class="nbu-quantity"' , $product_quantity );
                $product_quantity = str_replace('input-text' , 'input-text nb-input-custom-upload' , $product_quantity );
                $product_quantity = str_replace('<input' , '<input data-item-key="'.$cart_item_key.'" data-min-qty="'.$fields['combination']['min_qty'].'"' , $product_quantity );
            }
           return $product_quantity;
       }
    }
    return $product_quantity;
}

// add options description for button design
add_filter( 'nbdesigner_general_settings' , 'nb_custom_add_options_desc_design' , 999 , 1);
function nb_custom_add_options_desc_design( $args ) {
    $args['customization'][] = array(
        'title'         => esc_html__('Description for button Design', 'web-to-print-online-designer'),
        'description'   => esc_html__('Description for button Design.', 'web-to-print-online-designer'),
        'id'            => 'nbd_desc_button_design',
        'default'       => 'no',
        'type'          => 'textarea',
        'class'         => 'regular-text',
    );
    $args['customization'][] = array(
        'title'         => esc_html__('Description for button Upload', 'web-to-print-online-designer'),
        'description'   => esc_html__('Description for button Upload.', 'web-to-print-online-designer'),
        'id'            => 'nbd_desc_button_upload',
        'default'       => 'no',
        'type'          => 'textarea',
        'class'         => 'regular-text',
    );
    return $args;
}

add_filter( 'woocommerce_cart_contents_count' , 'nb_custom_qty_mini_cart' , 10 , 1);
function nb_custom_qty_mini_cart( $data ) {
    $cart_contents = WC()->cart->cart_contents;
    $list_qty = wp_list_pluck( $cart_contents, 'quantity' );
    $have_custom_design = false;
    foreach($cart_contents as $cart_item_key => $cart_item ) {
        $list_qty[$cart_item_key] = 1;
        $have_custom_design = true;
    }
    if( $have_custom_design ) {
        return array_sum( $list_qty );
    } else {
        return $data;
    }
}

// NB custom Loadmore Template
add_action( 'wp_ajax_nb_loadmore_template', 'nb_loadmore_template' );
add_action( 'wp_ajax_nopriv_nb_loadmore_template', 'nb_loadmore_template' );
function nb_loadmore_template() {
    $page       = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $per_page   = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 4;
    $pid   = isset($_POST['pid']) ? (int)$_POST['pid'] : 0;
    $templates  = nb_get_templates_by_page($page , $per_page , $pid);
    ob_start();
    include_once( ABSPATH . 'wp-content/plugins/web-to-print-online-designer/templates/gallery/gallery-item.php' );
    $content = ob_get_clean();
    wp_send_json_success($content);
    die();
}
function nb_get_templates_by_page( $page = 1, $per_page, $pid = false){
    $listTemplates = array();
    global $wpdb;
    $offset = $page * $per_page - 4;
    $sql    = "SELECT p.ID, p.post_title, t.id AS tid, t.name, t.folder, t.product_id, t.variation_id, t.user_id, t.thumbnail, t.type FROM {$wpdb->prefix}nbdesigner_templates AS t";
    $sql   .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
    $sql   .= " WHERE t.publish = 1 AND p.post_status = 'publish' AND publish = 1";

    if( $pid ) {
        $sql .= " AND t.product_id = ".$pid;
    }
    $sql   .= " ORDER BY t.created_date DESC";

    $posts = $wpdb->get_results( $sql, 'ARRAY_A' );
    foreach ( $posts as $key => $p ){
        if( $key >= $offset && $key < $offset + $per_page ) {
            $path_preview = NBDESIGNER_CUSTOMER_DIR .'/'.$p['folder']. '/preview';
            if( $p['thumbnail'] ){
                $image = wp_get_attachment_url( $p['thumbnail'] );
            }else{
                $listThumb = Nbdesigner_IO::get_list_images( $path_preview );
                $image = '';
                if( count( $listThumb ) ){
                    asort( $listThumb );
                    $image = Nbdesigner_IO::wp_convert_path_to_url( reset( $listThumb ) );
                }
            }
            $title = $p['name'] ?  $p['name'] : $p['post_title'];
            $listTemplates[] = array('tid' => $p['tid'], 'id' => $p['ID'], 'title' => $title, 'type' => $p['type'], 'image' => $image, 'folder' => $p['folder'], 'product_id' => $p['product_id'], 'variation_id' => $p['variation_id'], 'user_id' => $p['user_id']);
        }
    }
    return $listTemplates;
}

add_action( 'nb_custom_customer_note' , 'nb_custom_customer_note' , 10 ,2 );
function nb_custom_customer_note( $cart_item, $cart_item_key ) {
    $note = '';
    $roll_form = 'off';
    if( isset($cart_item['nb_custom_note']) ) {
        $note = $cart_item['nb_custom_note']['comment'];
        $roll_form = $cart_item['nb_custom_note']['roll_form'] == 'on' ? $cart_item['nb_custom_note']['roll_form'] : 'off';
    }
    ?>
    <div class="nb-wrap-comment">
        <div class="nb-roll-form">
            <label class="cs-input-checkbox" for="roll-form-<?php echo $cart_item_key; ?>">Interested in roll form ?
                <input type="checkbox" <?php checked($roll_form , 'on'); ?> id="roll-form-<?php echo $cart_item_key; ?>" data-key="<?php echo $cart_item_key; ?>" name="nb_roll_form[<?php echo $cart_item_key; ?>][]">
                <span class="checkmark"></span>
            </label>
            <span data-position="top" class="nb-cs-help-tip">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7227 15.3369H9.25781V13.8721H10.7227V15.3369ZM10.7227 11.0575C12.1389 10.7149 13.1201 9.43005 13.0641 7.9771C13.0033 6.39496 11.7052 5.10841 10.109 5.04868C9.25621 5.01652 8.45077 5.32196 7.84195 5.90824C7.24354 6.48468 6.91406 7.25796 6.91406 8.08594H8.37891C8.37891 7.65965 8.54896 7.26093 8.85795 6.96339C9.17759 6.65554 9.60205 6.4959 10.0541 6.51237C10.8896 6.54385 11.5688 7.21184 11.6004 8.03341C11.6297 8.79902 11.1013 9.47548 10.3436 9.64176C9.70425 9.78207 9.25781 10.3346 9.25781 10.9854V12.4072H10.7227V11.0575ZM16.6193 16.6193C18.3901 14.8487 19.3652 12.4943 19.3652 9.99023C19.3652 7.48615 18.3901 5.13176 16.6193 3.36113C14.8487 1.59039 12.4943 0.615234 9.99023 0.615234C7.48615 0.615234 5.13176 1.59039 3.36113 3.36113C1.59039 5.13176 0.615234 7.48615 0.615234 9.99023C0.615234 12.4943 1.59039 14.8487 3.36113 16.6193C5.13176 18.3901 7.48615 19.3652 9.99023 19.3652C12.4943 19.3652 14.8487 18.3901 16.6193 16.6193ZM17.9004 9.99023C17.9004 14.3518 14.3518 17.9004 9.99023 17.9004C5.62866 17.9004 2.08008 14.3518 2.08008 9.99023C2.08008 5.62866 5.62866 2.08008 9.99023 2.08008C14.3518 2.08008 17.9004 5.62866 17.9004 9.99023Z" fill="#9A9A9A"/>
                </svg>
                <span class="data-tip">Currently printing in roll form is not available online, but if you are interested, we will contact you for more detail.<br>Roll form is not available for ready products</span>
            </span>

        </div>
        <div class="nb-note">
            <textarea style="resize: none;" class="textarea-note" placeholder="Customer’s note..." rows="2" maxlength="5000" data-key="<?php echo $cart_item_key; ?>" name="nb_text_note[<?php echo $cart_item_key; ?>][]"><?php echo esc_html($note); ?></textarea>
        </div>
    </div>
    <?php
}
add_action('wp_ajax_nb_custom_processing_cart', 'nb_custom_processing_cart');
add_action('wp_ajax_nopriv_nb_custom_processing_cart', 'nb_custom_processing_cart');
function nb_custom_processing_cart() {
    $results = array(
        'flag'  => 0
    );
    $item_key   = isset($_POST['item_key']) ? $_POST['item_key'] : '';
    $content   = isset($_POST['content']) ? $_POST['content'] : '';
    $roll_form   = isset($_POST['roll_form']) ? $_POST['roll_form'] : '';
    if( $item_key ) {
        $cart_items = WC()->cart->get_cart();
        if( isset( $cart_items[$item_key] )) {
            $cart_item = $cart_items[$item_key];
            if( isset($cart_item['nb_custom_note']) ) {
                if( $content ) {
                    $cart_item['nb_custom_note']['comment'] = $content;
                    $results['flag'] = 1;
                }
                if( $roll_form ) {
                    $cart_item['nb_custom_note']['roll_form'] = $roll_form;
                    $results['flag'] = 1;
                } 
            } else {
                $cart_item['nb_custom_note'] = array(
                    'comment' => $content,
                    'roll_form' => $roll_form
                );
                $results['flag'] = 1;
            }
            if( $results['flag'] = 1 ) {
                WC()->cart->cart_contents[ $item_key ] = $cart_item;
                WC()->cart->set_session();  
            }
        }
    }

    wp_send_json_success($results);
    die();
}

//edit button remove item
add_filter( 'woocommerce_cart_item_remove_link' , 'nb_custom_button_remove' , 100 , 2 );
function nb_custom_button_remove( $link , $cart_item_key) {
    $text = "'Are you sure you want to delete this item?'";
    $link = str_replace('<a' , '<a onclick="if (confirm('.$text.')){return true;}else{event.stopPropagation(); event.preventDefault();};"' , $link );
    return $link;
}
function nb_custom_show_menu_templates() {
    $args_query = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'meta_key'          => '_nbdesigner_enable',
        'orderby'           => 'date',
        'posts_per_page'    => -1,
        'meta_query'        => array(
            array(
                'key'   => '_nbdesigner_enable',
                'value' => 1,
            )
        ),
    );
    $products = get_posts( $args_query );
    if( !is_single()) :
    ?>
    <div class="nbd-designers nbd-sidebar-con">
        <div class="nbd-sidebar-con-inner">
            <div class="nbd-tem-list-product-wrap">
                <ul>
                <li class="nbd-tem-list-product">
                    <a class="<?php if($pid == $product['product_id']) echo 'active'; ?>" href="<?php echo get_home_url().'/templates' ?>">
                        <span>All</span>
                    </a>
                </li>
                <?php
                foreach( $products as $key => $product ): 
                    if( nbd_count_total_template($product->ID , 0) == 0 ) { continue; }
                    $link_prodcut_templates = add_query_arg(array('pid' => $product->ID), getUrlPageNBD('gallery'));
                ?>
                    <li class="nbd-tem-list-product <?php if($key > 14) echo 'nbd-hide'; ?>">
                        <a class="<?php if($pid == $product->ID) echo 'active'; ?>" href="<?php echo esc_url( $link_prodcut_templates ); ?>">
                            <span><?php esc_html_e( $product->post_title ); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
                <?php if(count($products) > 15): ?>
                <a class="nbd-see-all" href="javascript:void(0)" onclick="showAllProduct( this )"><?php esc_html_e('See All', 'web-to-print-online-designer'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    endif;

}
remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count' , 20);
remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_catalog_ordering' , 30);

add_action( 'aora_woo_template_main_before', 'nb_custom_hook', 25, 0 );
// add_action( 'aora_woo_template_main_before', 'nb_custom_show_menu_templates', 24, 0 );
add_action( 'nb_custom_before_shop_loop', 'woocommerce_result_count', 20, 0 );
add_action( 'nb_custom_before_shop_loop', 'nb_custom_catalog_ordering', 30, 0 );
function nb_custom_hook() {
    if( woocommerce_products_will_display()) {
        echo '<div class="aora-filter-wrapper">';
        do_action( 'nb_custom_before_shop_loop');
        echo  '</div><!-- Close Wrapper -->';
    } 
    do_action( 'nb_custom_before_shop_loop_clear');
}
function nb_custom_catalog_ordering() {
    echo '<div class="tbay-ordering"><span>'. esc_html__('Sort by:', 'aora') .'</span>';
    woocommerce_catalog_ordering();
    echo '</div>';
}
// overridden function orderby
function aora_open_woocommerce_catalog_ordering() {

}
function aora_close_woocommerce_catalog_ordering() {
    
}

add_filter( 'woof_sort_terms_before_out' , 'nb_custom_filter_label' , 10 , 1);
function nb_custom_filter_label($terms) {
    $_terms = array();
    foreach ($terms as $key => $term) {
        if( $term['count'] > 0 ) {
            $_terms[] = $term;
        }
    }
    return $_terms;
}

if(class_exists('WOOF')) {
    $woof = new WOOF();
    remove_action('woocommerce_before_shop_loop' , array( $woof , 'woocommerce_before_shop_loop') , 2);
}

// create button upload the invoice in my order
/*
add_action( 'woocommerce_order_details_before_order_table' , 'nb_custom_upload_invoice_tranf' , 9999 , 1);
function nb_custom_upload_invoice_tranf( $order) {
    if($order) {
        $order_id = $order->get_id();
        $payment_method = $order->get_payment_method();
        if($payment_method == 'bacs') {
            if( isset( $_POST['submit'] ) && $_POST['submit'] == 'submit' ) {
                $upload_dir = wp_upload_dir();
                $basedir    = $upload_dir['basedir']. '/nbdesigner/invoice';
                $file = $_FILES['invoice_upload'];
                $file_name = $file['name'];
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_name       = strtotime( "now" ) . substr( md5( rand( 1111,9999 ) ), 0, 8 ) . '.' .$ext;
                $path           = Nbdesigner_IO::create_file_path( $basedir , $new_name );
                if( move_uploaded_file( $file["tmp_name"], $path['full_path'] ) ){
                    update_post_meta( $order_id , '_invoice_upload' , Nbdesigner_IO::wp_convert_path_to_url($path['full_path']));
                }
            }
            $link_invoice = get_post_meta( $order_id , '_invoice_upload' , true);
            $_ext = pathinfo($link_invoice, PATHINFO_EXTENSION);
            $img = $link_invoice;
            if($_ext != 'png' || $_ext != 'jpg' || $_ext != 'jpeg' || $_ext != 'svg' ) {
                $img  = Nbdesigner_IO::get_thumb_file( $_ext, '' );
            }
            ?>
            <tr>
                <td colspan="1">
                    <div class="upload-invoice" style="border: 1px #e9e9e9 solid; border-radius: 5px; padding: 10px;">
                        <label class="form-label"><b>Upload invoice Payment</b></label>
                        <?php 
                        if($link_invoice) {
                            echo '<div class="invoice" style="padding: 10px 20px"><a href="'.$link_invoice.'"><img style="max-width: 100px;" src="'.$img.'" alt="Invoice"></a></div>';
                        }
                        ?>
                        <form action="" method="post" enctype="multipart/form-data">  
                            <div class="input-group mb-3">
                                <input style="height:auto;line-height:initial;" class="form-control" type="file" id="formFile-<?php echo $order_id; ?>" placeholder="" name="invoice_upload">
                                <input type="hidden" value="submit" name="submit">
                                <button class="btn btn-primary" type="submit">Upload</button>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>
            <?php  
        } 
    }
}

// create button payment authentic in order
add_action( 'add_meta_boxes_shop_order', 'v3_add_meta_boxes' );
function v3_add_meta_boxes() {
    $order_id = $_GET['post'];
    if(wc_get_order($order_id)->get_payment_method() != 'bacs') { return; }
    add_meta_box(
        'nb_authentic_payment',
        'Payment Authentication',
        'nb_output_authentic_payment',
        'shop_order',
        'side',
        'high'
    );

    
}
function nb_output_authentic_payment() {
    $order_id = $_GET['post'];
    $status = get_post_meta($order_id , '_auth_payment_order' , true);
    if(wc_get_order($order_id)->get_payment_method() != 'bacs') { return; }
    $link_invoice = get_post_meta( $order_id , '_invoice_upload' , true);
    $_ext = pathinfo($link_invoice, PATHINFO_EXTENSION);
    $img = $link_invoice;
    if($_ext != 'png' || $_ext != 'jpg' || $_ext != 'jpeg' || $_ext != 'svg' ) {
        $img  = Nbdesigner_IO::get_thumb_file( $_ext, '' );
    }
    ?>
    <style type="text/css">
        #nb_authentic_payment .authentic-wrap {
            display: block;
            height: auto;
        }
        #nb_authentic_payment .authentic-wrap .paid {
            margin-bottom: 10px;
        }
    </style>
    <div class="authentic-wrap">
        <div class="paid">
            <b>Bank Transfer: </b>
            <input type="checkbox" <?php if($status == 'paid') { echo 'checked'; } ?> value="authentic_payment" name="authentic_payment"><span>Paid</span>
            <input type="hidden" data-auth-payment="<?php echo $status; ?>" name="change_authentic_payment">
        </div>
        <?php 
            if($link_invoice) {
                echo '<img style="max-width:50px;display:inline;" src="'.$img.'" alt="Invoice"><a href="'.$link_invoice.'" class="nbdesigner-right button button-small button-secondary" download>Download</a>';
            }
        ?>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var checked = $('input[name="authentic_payment"]').is(":checked");
            $('input[name="authentic_payment"]').on('change' , function() {
                if( checked != $(this).is(":checked") ) {
                    $('input[name="change_authentic_payment"]').val('checked');
                } else {
                    $('input[name="change_authentic_payment"]').val('');
                }
            });
        });
    </script>
    
    <?php 
}
*/
add_action('woocommerce_process_shop_order_meta' , 'nb_save_authentic_payment' , 65 , 1);
function nb_save_authentic_payment($order_id) {
    $authentic_payment = isset($_POST['authentic_payment']) ? $_POST['authentic_payment']: '' ;
    $change_authentic_payment = isset($_POST['change_authentic_payment']) ? $_POST['change_authentic_payment']: '' ;
    if(isset($_POST['action']) && isset($_POST['action']) == 'editpost') {
        if( $change_authentic_payment == 'checked') {
            if( $authentic_payment == 'authentic_payment' ) {
                wp_update_post(array(
                    'ID'    =>  $order_id,
                    'post_status'   =>  'wc-processing'        
                ));
                update_post_meta($order_id , '_auth_payment_order' , 'paid');
                
            } else {
                update_post_meta($order_id , '_auth_payment_order' , 'pendding');
                wp_update_post(array(
                    'ID'    =>  $order_id,
                    'post_status'   =>  'wc-on-hold'        
                ));
            }
        }
    }
}
function kitalabel_get_product_option( $product_id ){
    $enable = get_post_meta( $product_id, '_nbo_enable', true );
    if( !$enable ) return false;
    $option_id = get_transient( 'nbo_product_'.$product_id );
    if( false === $option_id ){
        global $wpdb;
        $sql = "SELECT id, priority, apply_for, product_ids, product_cats, date_from, date_to FROM {$wpdb->prefix}nbdesigner_options WHERE published = 1";
        $options = $wpdb->get_results($sql, 'ARRAY_A');
        if($options){
            $_options = array();
            foreach( $options as $option ){
                $execute_option = true;
                $from_date = false;
                if( isset($option['date_from']) ){
                    $from_date = empty( $option['date_from'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_from'] ), false ) );
                }
                $to_date = false;
                if( isset($option['date_to']) ){
                    $to_date = empty( $option['date_to'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_to'] ), false ) );
                }
                $now  = current_time( 'timestamp' );
                if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
                    $execute_option = false;
                } elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
                    $execute_option = false;
                } elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
                    $execute_option = false;
                }
                if( $execute_option ){
                    if( $option['apply_for'] == 'p' ){
                        $products = unserialize($option['product_ids']);
                        $execute_option = in_array($product_id, $products) ? true : false;
                    }else {
                        $categories = $option['product_cats'] ? unserialize($option['product_cats']) : array();
                        $product = wc_get_product($product_id);
                        $product_categories = $product->get_category_ids();
                        $intersect = array_intersect($product_categories, $categories);
                        $execute_option = ( count($intersect) > 0 ) ? true : false;
                    }
                }
                if( $execute_option ){
                    $_options[] = $option;
                }
            }
            $_options = array_reverse( $_options );
            $option_priority = 0;
            foreach( $_options as $_option ){
                if( $_option['priority'] > $option_priority ){
                    $option_priority = $_option['priority'];
                    $option_id = $_option['id'];
                }
            }
            if( $option_id ){
                set_transient( 'nbo_product_'.$product_id , $option_id );
                
                $is_artwork_action = get_transient( 'nbo_action_'.$product_id );
                if( false === $is_artwork_action ){
                    $_selected_options  = kitalabel_get_option( $option_id );
                    $selected_options   = unserialize( $_selected_options['fields'] );
                    if ( isset( $selected_options['fields'] ) ) {
                        foreach ($selected_options['fields'] as $key => $field) {
                            if ( $field['general']['enabled'] == 'y' && isset( $field['nbe_type'] ) && $field['nbe_type'] == 'actions' ) {
                                $is_artwork_action = true;
                            }
                        }
                    }
                    if( $is_artwork_action ){
                        set_transient( 'nbo_action_'.$product_id , '1' );
                    }
                }
            }
        }
    }
    return $option_id;
}
function kitalabel_get_option( $id ){
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
    $sql .= " WHERE id = " . esc_sql($id);
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return count($result[0]) ? $result[0] : false;
}

add_filter( 'woocommerce_thankyou_order_received_text' , 'kita_custom_thankyou_order_received_text' , 10 , 1);
function kita_custom_thankyou_order_received_text($mes) {
    return esc_html__('Terima kasih, detail pembayaran telah dikirimkan ke email Anda, silahkan upload bukti pembayaran Anda untuk menyelesaikan transaksi.' , 'woocommerce');
}

// Ajax Login form
add_action('wp_ajax_nb_custom_login_ajax', 'nb_custom_login_ajax');
add_action('wp_ajax_nopriv_nb_custom_login_ajax', 'nb_custom_login_ajax');
function nb_custom_login_ajax(){

    // First check the nonce, if it fails the function will break
    $validation_error = new WP_Error();
    $results = array(
        'loggedin' => false,
        'message' => $validation_error,
    );
    $nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
    if ( isset( $_POST['username'], $_POST['password'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
        $creds = array(
            'user_login'    => trim( wp_unslash( $_POST['username'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            'user_password' => $_POST['password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            'remember'      => isset( $_POST['rememberme'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        );

        if ( $validation_error->get_error_code() ) {
            $results['message'] = $validation_error->get_error_message();
        }

        if ( empty( $creds['user_login'] ) ) {
            $results['message'] =  'Username is required.';
        }

        // Perform the login.
        $user = wp_signon( $creds, is_ssl() );

        if ( is_wp_error( $user ) ) {
            $results['message'] = $user->get_error_message();
            $results['loggedin'] = false;
        } else {
            $results['message'] = __('Login successful, redirecting...');
            $results['loggedin'] = true;
        }
    }
    // Nonce is checked, get the POST data and sign user on
    wp_send_json_success($results);
    die();
}
// add_filter('woocommerce_product_get_weight', 'nb_custom_weight_product_design', 90, 2);
// function nb_custom_weight_product_design($value, $product){
//     echo '<pre>';
//     var_dump($product->get_title());
//     echo '</pre>';
//     die;
//     return $value;
// }

if(!function_exists('logg')) {
    function logg($var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}