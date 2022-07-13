<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('Kitalabel_Custom_Hooks')) {

    class Kitalabel_Custom_Hooks {

        protected static $instance;

        protected $config = array();

        public static function instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {}

        public function init() {
            add_action('kitalabel_download_pdf_all' , array($this, 'kitalabel_download_pdf_all'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'kitalabel_frontend_enqueue_scripts'));

            add_filter( 'kitalabel_custom_after_cart_item_name', array($this, 'kitalabel_custom_render_cart') , 1, 3 );

            // enqueue the custom file css & js
            add_action( 'admin_enqueue_scripts', array($this, 'kitalabel_custom_enqueue_scripts'));

            add_action( 'wp_enqueue_scripts', array($this, 'kitalabel_custom_enqueue_scripts'));

             $this->kitalabel_ajax();
        }

        public function kitalabel_download_all($order_id) {
            if( isset( $_GET['download-all'] ) && ( $_GET['download-all'] == 'true' ) ){
                $products = $order->get_items();
                foreach( $products AS $order_item_id => $product ){
                    if( wc_get_order_item_meta( $order_item_id, '_nbd' ) || wc_get_order_item_meta( $order_item_id, '_nbu' ) ){
                        $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                        $nbu_item_key = wc_get_order_item_meta( $order_item_id, '_nbu' );
                        if( $nbd_item_key ){
                            $list_images = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key, 1 );
                            if( count( $list_images ) > 0 ){
                                foreach( $list_images as $key => $image ){
                                    $zip_files[] = $image;
                                }
                            }
                        }
                        if( $nbu_item_key ){
                            $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                            $files = apply_filters( 'nbu_download_upload_files', $files, $product );
                            if( count( $files ) > 0 ){
                                foreach( $files as $key => $file ){
                                    $zip_files[] = $file;
                                }
                            }
                        }
                    }
                }
                if( !count( $zip_files ) ){
                    exit();
                }else{
                    $pathZip = NBDESIGNER_DATA_DIR . '/download/' . $order_id . '.zip';
                    $nameZip =  $order_id . '.zip';
                    nbd_zip_files_and_download( $zip_files, $pathZip, $nameZip );
                }
            }
        }

        public function kitalabel_ajax() {
            $ajax_events = array(
                'kitalbel_convert_pdf_item' => true
            );

            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));

                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }

        public function kitalbel_convert_pdf_item() {
            $nbd_item_key = isset($_POST['item_key']) ? $_POST['item_key'] : array();
            $files = array();

            if($nbd_item_key) {
                $files = nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
            }

            $result = array(
                'created' => count($files) == 0 ? true : false,
            );
            
            wp_send_json_success($result);
            die();
        }

        public function kitalabel_frontend_enqueue_scripts() {

            wp_enqueue_style('kitalabel-scripts', CUSTOM_KITALABEL_URL . '/assets/css/style.css', array(), '1.0.0');


            wp_register_script('kitalabel-scripts', CUSTOM_KITALABEL_URL . '/assets/js/script.js', array(), '1.0.0');

            $args = array(
                'url'   => admin_url('admin-ajax.php'),
            );

            wp_localize_script('kitalabel-scripts', 'kitalabel_frontend', $args);

            wp_enqueue_script('kitalabel-scripts');
        }

        public function kitalabel_custom_enqueue_scripts() {
            wp_enqueue_style('custom-kitalabel-css' , CUSTOM_KITALABEL_URL . '/assets/css/custom.css');

            wp_register_script( 'custom-kitalabel-js',  CUSTOM_KITALABEL_URL . '/assets/js/custom.js', '', '' );

            $args = array(
                'url' => admin_url( 'admin-ajax.php' ),
                'homepage' => home_url(),
            ) ;
            wp_localize_script( 'custom-kitalabel-js', 'nb_custom', $args );

            wp_enqueue_script( 'custom-kitalabel-js' );
        }

        public function kitalabel_download_pdf_all($order, $order_id) {
            ?>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kitalabelModal">
                Download PDF all
            </button>

            <!-- Modal -->
            <div class="modal fade" id="kitalabelModal" tabindex="-1" aria-labelledby="kitalabelModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kitalabelModalLabel">Create PDF</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Variant name</th>
                                    <th scope="col">Preview</th>
                                    <th scope="col">Check pdf</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $index = 1;
                                $can_download_all = true;
                                if($order) {
                                    foreach ( $order->get_items() as $item_id => $item ) {
                                        $nbd_item_key = wc_get_order_item_meta( $item_id, '_nbd' );
                                        $nbu_item_key = wc_get_order_item_meta( $item_id, '_nbu' );

                                        $list_upload = array();
                                        if( $nbu_item_key ){
                                            $list_upload = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                                        }

                                        $path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key;
                                        $pdf_path = $path . '/customer-pdfs';
                                        $list_pdf = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'pdf');

                                        if(count($list_pdf) == 0) {
                                            $can_download_all = false;
                                        }

                                        $list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/preview', 1);
                                        asort( $list_images );
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo esc_html($index); ?></th>
                                            <td><?php echo esc_html($item->get_name()); ?></td>
                                            <td>
                                                <?php foreach($list_images as $key => $image) {
                                                    $src = Nbdesigner_IO::convert_path_to_url($image);
                                                ?>
                                                        <img class="nbdesigner_order_image_design" src="<?php echo esc_url( $src ); ?>" />
                                                <?php } ?>
                                            </td>
                                            <td>
                                                    <div class="kitalabel-has-pdf<?php echo count($list_pdf) > 0 ? ' active': ''; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg></div>
                                                    <div class="kitalabel-no-pdf<?php echo count($list_pdf) > 0 ? '': ' active'; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg></div>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary kitalabel-order-item" data-item-id="<?php echo esc_attr($item_id);  ?>" data-has-pdf="<?php echo esc_attr(count($list_pdf) > 0 ? '1' : '0');  ?>" <?php echo esc_attr(count($list_pdf) > 0 ? 'disabled' : '');  ?> data-item-key="<?php echo esc_attr($nbd_item_key);  ?>"><div class="kitalabel-create active">Create</div><div class="kitalabel-load spinner-border spinner-border-sm text-light" role="status"></div></button>
                                            </td>
                                        </tr>
                                        <?php
                                        $index++;
                                    }
                                } ?>
                            </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary kitalabel-convert-pdf-all">Convert all</button>
                            <button type="button" class="btn btn-primary kitalabel-download-pdf-all" <?php echo esc_attr(!$can_download_all ? 'disabled': ''); ?>>Download all</button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
            <?php
        }

        public function kitalabel_custom_render_cart( $title = null, $cart_item = null, $cart_item_key = null ) {
            $product_custom_design = 9550;
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
                $option_type = 'normal';
                if( $product_custom_design == $product_id ) {
                    $option_type = 'custom_design_page';
                }         
                if ( $is_nbdesign && $_show_design == 'yes' ) {
                    $html = '';
                    $layout = nbd_get_product_layout( $product_id );
                    if( isset( $nbd_session ) ){
                        $id             = 'nbd' . $cart_item_key;
                        $redirect       = is_cart() ? 'cart' : 'checkout';
                        // $remove_design  = is_cart() ? '<a class="remove nbd-remove-design nbd-cart-item-remove-design" href="#" data-type="custom" data-cart-item="' . $cart_item_key . '">&times;</a>' : '';
                        // $html          .= '<p>' . esc_html__('Custom design', 'web-to-print-online-designer') . $remove_design . '</p>';
                        $list           = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/preview' );
                        $list           = nbd_sort_file_by_side( $list );
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
                        if( isset( $cart_item['nbo_meta'] ) ) {
                            $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
                            if( isset( $fields['combination'] ) && isset( $fields['combination']['side']) && count($fields['combination']['side']) > 0 ) {
                                $side = $fields['combination']['side'];
                                $qty_min = isset( $fields['combination']['min_qty'] ) ? $fields['combination']['min_qty'] : $cart_item['quantity'];
                            }
                        }
                        $quantity = '';
                        $product_config = nbd_get_data_from_json( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/config.json' )->product;
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
                                    if( $key == 0 && is_cart() ) {
                                        $html  .= '<tr><td><img class="nbd_cart_item_design_preview" src="' . $src . '"/></td><td>'.$design_name.'</td><td>'.$quantity.'</td><td class="row-middle" rowspan="'.count($side).'">'.$buton.'</td></tr>';
                                    } else {
                                        $html  .= '<tr><td><img class="nbd_cart_item_design_preview" src="' . $src . '"/></td><td>'.$design_name.'</td><td>'.$quantity.'</td></tr>';
                                    } 
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
                        $html .= '<div class="nbd-cart-upload-file nbd-cart-item-add-design">';
                        $html .=    '<a class="button nbd-create-design" href="' . $link_create_design . '">'. esc_html__('Add design', 'web-to-print-online-designer') .'</a>';
                        $html .= '</div>';
                    }
                    if( isset( $nbu_session ) ){
                        $id             = 'nbu' . $cart_item_key; 
                        $redirect       = is_cart() ? 'cart' : 'checkout';
                        $html          .= '<div id="'.$id.'" class="nbd-cart-upload-file nbd-cart-item-upload-file">';
                        // $remove_upload  = is_cart() ? '<a class="remove nbd-cart-item-remove-file" href="#" data-type="upload" data-cart-item="' . $cart_item_key . '">&times;</a>' : '';
                        // $html          .= '<p>' . esc_html__('Upload file', 'web-to-print-online-designer') . $remove_upload . '</p>';
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
                    }
                    // $option = unserialize(get_post_meta($product_id, '_nbdesigner_option', true)); 
                    // if( isset($nbd_session) ) {
                    //     $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/config.json';
                    //     $config = nbd_get_data_from_json($path);
                    //     if( isset( $config->custom_dimension ) && isset( $config->custom_dimension->price ) ){
                    //         $nbd_variation_price = $config->custom_dimension->price;
                    //     }
                    // }
                    // if( ( ( isset( $nbd_variation_price ) && $nbd_variation_price != 0 ) || $option['extra_price'] ) && ! $option['request_quote'] ){
                    //     $decimals = wc_get_price_decimals();
                    //     $extra_price = $option['extra_price'] ? $option['extra_price'] : 0;
                    //     if( (isset($nbd_variation_price) && $nbd_variation_price != 0) ) {
                    //         $extra_price = $option['type_price'] == 1 ? wc_price($extra_price + $nbd_variation_price) : $extra_price . ' % + ' . wc_price($nbd_variation_price);
                    //     }else {
                    //         $extra_price = $option['type_price'] == 1 ? wc_price($extra_price) : $extra_price . '%';
                    //     }
                    //     $html .= '<p id="nbx'.$cart_item_key.'">' . esc_html__('Extra price for design','web-to-print-online-designer') . ' + ' .  $extra_price . '</p>';
                    // }
                    return $html;
                } 
            } 
        }
    }
}

$kitalabel_custom_hooks = Kitalabel_Custom_Hooks::instance();
$kitalabel_custom_hooks->init();