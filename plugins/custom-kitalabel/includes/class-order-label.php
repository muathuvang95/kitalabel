<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('Kitalabel_Order_Label')) {

    class Kitalabel_Order_Label {

        protected static $instance;

        public static function instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
        }

        public function init() {
        	add_action('nbo_after_summary', array($this, 'button_request_quote'), 10, 2 );
        	add_filter('nbdesigner_default_settings', array($this, 'default_settings'), 10, 1 );
        	add_filter( 'nbdesigner_general_settings' , array($this, 'general_settings') , 999 , 1);

            $this->kitalabel_ajax();
        }

        public function kitalabel_ajax() {
            $ajax_events = array();

            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));

                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }

        public function general_settings($args) {
        	$args['customization'][] = array(
		        'title'         => esc_html__('Order label product id.', 'web-to-print-online-designer'),
		        'description'   => '',
		        'id'            => 'nbd_order_label_product_id',
		        'default'       => 0,
		        'type'          => 'number',
		        'class'         => 'regular-text',
		    );
		    $args['customization'][] = array(
		        'title'         => esc_html__('Order label page id.', 'web-to-print-online-designer'),
		        'description'   => '',
		        'id'            => 'nbd_order_label_page_id',
		        'default'       => 0,
		        'type'          => 'number',
		        'class'         => 'regular-text',
		    );
		    $args['customization'][] = array(
		        'title'         => esc_html__('Upload page id.', 'web-to-print-online-designer'),
		        'description'   => '',
		        'id'            => 'nbd_upload_page_id',
		        'default'       => 0,
		        'type'          => 'number',
		        'class'         => 'regular-text',
		    );

		    return $args;
        }

        public function default_settings($setting) {
        	$setting['nbd_order_label_product_id'] = 0;
        	$setting['nbd_order_label_page_id'] = 0;
        	$setting['nbd_upload_page_id'] = 0;

        	return $setting;
        }

        public function get_template($template_name, $args = array(), $tempate_path = '', $default_path = '') {
		    if (is_array($args) && isset($args)) :
		        extract($args);
		    endif;
		    $template_file = $this->locate_template($template_name, $tempate_path, $default_path);
		    if (!file_exists($template_file)) :
		        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.3.1');
		        return;
		    endif;
		    include $template_file;
		}
		public function locate_template($template_name, $template_path = '', $default_path = '') {
		    // Set variable to search in web-to-print-online-designer folder of theme.
		    if (!$template_path) :
		        $template_path = 'custom-kitalabel/';
		    endif;
		    // Set default plugin templates path.
		    if (!$default_path) :
		        $default_path = CUSTOM_KITALABEL_PATH . 'order-label/'; // Path to the template folder
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

		public function button_request_quote( $pid ) {
			if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View'){
			    return;
			}

		    $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true );

		    if ( $is_nbdesign ) {
		        $product    = wc_get_product( $pid );
		        $type       = $product->get_type();
		        $option     = unserialize( get_post_meta( $pid, '_nbdesigner_option', true ) );
		        $class                          = nbdesigner_get_option( 'nbdesigner_class_design_button_detail', '' ); 
		        $_enable_upload                 = get_post_meta( $pid, '_nbdesigner_enable_upload', true );
		        $_enable_upload_without_design  = get_post_meta( $pid, '_nbdesigner_enable_upload_without_design', true );
		        $label_design                   = apply_filters( 'nbd_start_design_label', esc_html__( 'Design Sendiri di Editor', 'web-to-print-online-designer' ) );
		        $label_upload                   = apply_filters( 'nbd_start_design_and_upload_label', esc_html__( 'Upload Design', 'web-to-print-online-designer' ) );
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
		        <div class="nbdesigner_frontend_container kita_button_custom_design">
		            <input class="nb-custom-design-page" name="nb-custom-design-page" type="hidden" value="custom_design_page" />
		            <input name="nbd-add-to-cart" type="hidden" value="<?php echo( $pid ); ?>" />
		            <div ng-if="enable_design" class="row nbd-actions-wrap">
		                <?php if( $is_nbdesign ): ?>
		                <div class="col-md-6 nbd-action-wrap">
		                    <div ng-click="NbCustomDesign('start_design')" class="button alt nbdesign-button start-design">
		                        <span><?php echo $label_design; ?></span>
		                    </div>
		                </div>
		                <div class="col-md-6 nbd-action-wrap">
		                    <a href="<?php echo home_url().'/upload-file-modern'; ?>" class="button alt nbdesign-button upload-design kita-link-upload">
		                        <span ><?php echo $label_upload ; ?></span>
		                    </a>
		                </div>
		                <?php endif; ?>
		            </div>
		            <div class="row" ng-if="!enable_design">
		            	<div class="col-md-12">
		            		<button ng-click="requestQuoteHandle()" data-src="<?php echo home_url().'/upload-file-modern'; ?>" data-base-src="<?php echo home_url().'/upload-file-modern'; ?>" class="button alt nbdesign-button" id="buttonRequestQuote">
			            		<?php esc_html_e('Request Quote', 'web-to-print-online-designer'); ?>
			            	</button>
		            	</div>
		            </div>
		        </div>
		        <?php
		    }
		}

        public function option_fields($product_id = 0 , $type_page = ''){
		    $product_id = nbdesigner_get_option('nbd_order_label_product_id');

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
		            $this->get_template('option-builder.php' , array(
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

    }
}

$kitalabel_order_label = Kitalabel_Order_Label::instance();
$kitalabel_order_label->init();