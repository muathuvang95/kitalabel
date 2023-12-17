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

            // enqueue the custom file css & js
            add_action( 'admin_enqueue_scripts', array($this, 'kitalabel_custom_enqueue_scripts'));

            add_action( 'wp_enqueue_scripts', array($this, 'kitalabel_custom_enqueue_scripts'));

             $this->kitalabel_ajax();
        }

        public function zip_files( $file_names, $archive_file_name, $nameZip, $output_names ){
            if(file_exists($archive_file_name)){
                unlink($archive_file_name);
            }
            if ( class_exists( 'ZipArchive' ) ) {
                $zip = new ZipArchive();
                if ( $zip->open( $archive_file_name, ZIPARCHIVE::CREATE ) !== TRUE ) {
                  exit( "cannot open <$archive_file_name>\n" );
                }
                
                foreach( $file_names as $key => $file ) {
                    $name = basename( $file );
                    if( isset($output_names[$key]) && $output_names[$key] ) {
                        $file_name = $output_names[$key];
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $name = $file_name . '.' . $ext;
                    }
                    $zip->addFile( $file, $name );
                }

                $zip->close();
            }else{
                require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
                $archive = new PclZip($archive_file_name);
                foreach($file_names as $file){
                    $path_arr = explode('/', $file);
                    $dir = dirname($file).'/';
                    $archive->add($file, PCLZIP_OPT_REMOVE_PATH, $dir, PCLZIP_OPT_ADD_PATH, $path_arr[count($path_arr) - 2]);
                }
            }
        }

        public function kitalabel_download_all() {
            $urlZip = '';
            if( isset( $_POST['order_id'] ) && $_POST['order_id'] ){
                $order_id = $_POST['order_id'];
                $order = wc_get_order($order_id);
                if($order) {
                    $products = $order->get_items();
                    $zip_files = array();
                    $output_names = array();
                    $product_index = 1;
                    foreach( $products AS $order_item_id => $product ){
                        if( wc_get_order_item_meta( $order_item_id, '_nbd' ) || wc_get_order_item_meta( $order_item_id, '_nbu' ) ){
                            $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                            $nbu_item_key = wc_get_order_item_meta( $order_item_id, '_nbu' );
                            $origin_order  = wc_get_order_item_meta( $order_item_id, '_order_again' );
                            $variant_index = 1;
                            if( $nbd_item_key ){
                                $path           = NBDESIGNER_CUSTOMER_DIR .'/' . $nbd_item_key;
                                $config     = json_decode( file_get_contents( $path . '/config.json' ) );
                                $product_config = array();
                                if( isset( $config->product ) && count( $config->product ) ){
                                    $product_config = $config->product;
                                };
                                $list_pdf = Nbdesigner_IO::get_list_files_by_type(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/customer-pdfs' , 1, 'pdf');
                                $list_pdf = array_values(nbd_sort_file_by_side($list_pdf));
                                if( count( $list_pdf ) > 0 ){
                                    foreach( $list_pdf as $key => $pdf ){
                                        $design_name = 'Design' . $key;
                                        if( isset($product_config[$key]) && isset($product_config[$key]->orientation_name) && $product_config[$key]->orientation_name ) {
                                            $design_name = str_replace(' ', '-', $product_config[$key]->orientation_name);
                                            $design_name = str_replace('_', '-', $design_name);
                                        }
                                        $zip_files[] = $pdf;
                                        $output_names[] = $order_id . '_' . $product_index . '_' . $variant_index . '_' . $product->get_name() . '_' . $design_name;
                                        $variant_index ++;
                                    }
                                }
                            }
                            if( $nbu_item_key ){
                                $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                                $files = apply_filters( 'nbu_download_upload_files', $files, $product );
                                if( count( $files ) > 0 ){
                                    foreach( $files as $key => $file ){
                                        $zip_files[] = $file;
                                        $output_names[] = $product_index . '_' . $variant_index . '_' . $product->get_name();
                                        $variant_index ++;
                                    }
                                }
                            }
                        }
                        $product_index ++;
                    }
                    if( count( $zip_files ) > 0 ){
                        if($nbd_item_key && $origin_order) {
                            $file_blank = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/'.$origin_order.'.txt';
                            file_put_contents($file_blank, '');
                            $zip_files[] = $file_blank;
                        }
                        
                        $pathZip = NBDESIGNER_DATA_DIR . '/download/' . $order_id . '.zip';
                        $urlZip = NBDESIGNER_DATA_URL . '/download/' . $order_id . '.zip';
                        $nameZip =  $order_id . '.zip';
                        $this->zip_files( $zip_files, $pathZip, $nameZip, $output_names);
                    }
                }
            }

            $result = array(
                'url' => $urlZip,
            );
            
            wp_send_json_success($result);
            die();
        }

        public function kitalabel_ajax() {
            $ajax_events = array(
                'kitalbel_convert_pdf_item' => true,
                'kitalabel_download_all' => true,
                'kitalabel_edit_upload_design_cart' => true,
                'kitalabel_delete_upload_design_cart' => true,
                'kitalabel_add_upload_design_cart' => true,
                'kitalabel_ajax_qty_cart' => true,
            );

            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));

                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }

        public function kitalbel_convert_pdf_item() {
            $nbd_item_key = isset($_POST['item_key']) ? $_POST['item_key'] : '';
            $files = array();

            if($nbd_item_key) {
                $files = nbd_export_pdfs( $nbd_item_key, false, false, 'no', null, true );
            }
            $has_file = true;
            if( is_array($files)) {
                foreach($files as $file) {
                    if( !$file && !file_exists($file)) {
                        $has_file = false;
                    }
                }
            } else {
                $has_file = false;
            }
            $result = array(
                'created' => $has_file ? true : false,
            );
            
            wp_send_json_success($result);
            die();
        }

        public function kitalabel_frontend_enqueue_scripts() {

            wp_enqueue_style('kitalabel-scripts', CUSTOM_KITALABEL_URL . 'assets/css/style.css', array(), '1.0.0');


            wp_register_script('kitalabel-scripts', CUSTOM_KITALABEL_URL . 'assets/js/script.js', array(), '1.0.0');

            $args = array(
                'url'   => admin_url('admin-ajax.php'),
            );

            wp_localize_script('kitalabel-scripts', 'kitalabel_frontend', $args);

            wp_enqueue_script('kitalabel-scripts');
        }

        public function kitalabel_custom_enqueue_scripts() {
            wp_enqueue_style('custom-kitalabel-css' , CUSTOM_KITALABEL_URL . 'assets/css/custom.css');

            wp_register_script( 'custom-kitalabel-js',  CUSTOM_KITALABEL_URL . 'assets/js/custom.js', '', '' );

            wp_register_script( 'woocommerce-kitalabel-js',  CUSTOM_KITALABEL_URL . 'assets/js/woocommerce.js', '', '' );

            if ( get_query_var('et_is-woocommerce', false)) {
                wp_enqueue_script( 'woocommerce-kitalabel-js');
                    
                // if ( get_query_var('et_is-cart', false) ) {

                // }
            }

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
            <button type="button" class="button button-smaill button-primary" data-bs-toggle="modal" data-bs-target="#kitalabelModal">
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
                                        if($nbd_item_key ||$nbu_item_key) {
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
                                    }
                                } ?>
                            </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary kitalabel-convert-pdf-all">Convert all</button>
                            <button type="button" data-order-id="<?php echo esc_attr($order_id); ?>" class="btn btn-primary kitalabel-download-pdf-all" <?php echo esc_attr(!$can_download_all ? 'disabled': ''); ?>>Download all</button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
            <?php
        }

        public function upload_file( $files, $field_id, $user_folder ){
            $nbd_upload_fields = array();
            global $woocommerce;
            $file = $files['name'];
            // kita upload file
            $ext = pathinfo( $file, PATHINFO_EXTENSION );
            $new_name = strtotime("now").substr(md5(rand(1111,9999)),0,8).'.'.$ext;
            $new_path = NBDESIGNER_UPLOAD_DIR . '/' .$user_folder . '/' .$new_name;
            $mkpath = wp_mkdir_p( NBDESIGNER_UPLOAD_DIR . '/' .$user_folder);
            if( $mkpath ){
                if (move_uploaded_file($files['tmp_name'], $new_path)) {
                    $nbd_upload_field = $user_folder . '/' .$new_name;
                    return $nbd_upload_field;
                }
            }
            return false;
        }

        public function  kitalabel_edit_upload_design_cart() {
            $results = array(
                'flag'  => 0,
                'design'  => '',
            );

            $design_index = isset($_POST['design_index']) ? (int) $_POST['design_index'] : 0;
            $item_key = isset($_POST['item_key']) ? $_POST['item_key'] : '';

            $passed = false;

            if( $item_key ) {
                $cart_items = WC()->cart->get_cart();

                if( isset( $cart_items[$item_key] )) {
                    $cart_item = $cart_items[$item_key];
                    $nbd_field = $cart_item['nbo_meta']['field'] ;

                    if( isset( $cart_item['nbo_meta'] ) ) {
                        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;

                        if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
                            $item_combination = $fields['combination'];
                            $upload_fields = false;
                            if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['option_price'] ) && isset( $cart_item['nbo_meta']['option_price']['fields'] ) ) {
                                $quantity = $cart_item['quantity'];
                                $sum_qty = 0;
                                foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $val)  {
                                    if(isset($val['is_custom_upload']) && $val['is_custom_upload'] == 1) {
                                        $files = $val['value_name']['files'];
                                        if(isset($files[$design_index])) {
                                            $file_data = explode('/', $files[$design_index]);
                                            if(count($file_data) == 2) {
                                                $folder_upload = $file_data[0];
                                                $nbd_upload_field = $this->upload_file($_FILES['file'], $key, $folder_upload);
                                                if($nbd_upload_field) {
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['files'][$design_index] = $nbd_upload_field;
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['files'][$design_index] = $nbd_upload_field;
                                                    $cart_item['nbo_meta']['field'][$key]['files'][$design_index] = $nbd_upload_field;
                                                    $passed = true;
                                                    $results = array(
                                                        'flag'  => 1,
                                                        'design'  => $nbd_upload_field,
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }

                                if( $passed ) {
                                    WC()->cart->cart_contents[ $item_key ] = $cart_item;
                                    WC()->cart->set_session();
                                }
                            }
                        }
                    }
                }
            }
            wp_send_json_success($results);
         
            die();
        }



        public function  kitalabel_add_upload_design_cart() {
            $results = array(
                'flag'  => 0
            );

            $item_key    = isset($_POST['item_key']) ? $_POST['item_key'] : '';
            $name    = isset($_POST['name']) ? $_POST['name'] : '';
            $new_qty   = isset($_POST['qty']) ? (int) $_POST['qty'] : 1;

            $passed = false;

            if( $item_key ) {
                $cart_items = WC()->cart->get_cart();

                if( isset( $cart_items[$item_key] )) {
                    $cart_item = $cart_items[$item_key];
                    $nbd_field = $cart_item['nbo_meta']['field'] ;

                    if( isset( $cart_item['nbo_meta'] ) ) {
                        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;

                        if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
                            $item_combination = $fields['combination'];
                            $upload_fields = false;
                            if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['option_price'] ) && isset( $cart_item['nbo_meta']['option_price']['fields'] ) ) {
                                $quantity = $cart_item['quantity'];
                                $sum_qty = 0;
                                foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $val)  {
                                    if(isset($val['is_custom_upload']) && $val['is_custom_upload'] == 1) {
                                        $files = $val['value_name']['files'];
                                        if(isset($files[0])) {
                                            $file_data = explode('/', $files[0]);
                                            if(count($file_data) == 2) {
                                                $folder_upload = $file_data[0];
                                                $nbd_upload_field = $this->upload_file($_FILES['file'], $key, $folder_upload);
                                                if($nbd_upload_field) {
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['files'][] = $nbd_upload_field;
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['files'][] = $nbd_upload_field;
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['variants'][] = $name;
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['variants'][] = $name;
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['qtys'][] = $new_qty;
                                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['qtys'][] = $new_qty;
                                                    $cart_item['nbo_meta']['field'][$key]['files'][] = $nbd_upload_field;
                                                    $cart_item['nbo_meta']['field'][$key]['variants'][] = $name;
                                                    $cart_item['nbo_meta']['field'][$key]['qtys'][] = $new_qty;

                                                    $min_qty = (int) $val['min_qty'];
                                                    foreach($cart_item['nbo_meta']['field'][$key]['qtys'] as $k => $qty) {
                                                        $qty = (int) $qty;
                                                        $sum_qty += $qty;
                                                    }
                                                    if($sum_qty >= $min_qty) {
                                                        $passed = true;
                                                        $results = array(
                                                            'flag'  => 1,
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                if( $passed ) {
                                    WC()->cart->cart_contents[ $item_key ] = $cart_item;
                                    WC()->cart->set_quantity( $item_key, $sum_qty );
                                    WC()->cart->set_session();
                                }
                            }
                        }
                    }
                }
            }
            wp_send_json_success($results);
         
            die();
        }


        public function  kitalabel_delete_upload_design_cart() {
            $results = array(
                'flag'  => 0
            );

            $design_index    = isset($_POST['design_index']) ? (int) $_POST['design_index'] : 0;
            $item_key   = isset($_POST['item_key']) ? $_POST['item_key'] : '';

            $passed = false;

            if( $item_key ) {
                $cart_items = WC()->cart->get_cart();

                if( isset( $cart_items[$item_key] )) {
                    $cart_item = $cart_items[$item_key];
                    $nbd_field = $cart_item['nbo_meta']['field'] ;

                    if( isset( $cart_item['nbo_meta'] ) ) {
                        $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;

                        if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
                            $item_combination = $fields['combination'];
                            if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['option_price'] ) && isset( $cart_item['nbo_meta']['option_price']['fields'] ) ) {
                                $quantity = $cart_item['quantity'];
                                $sum_qty = 0;
                                foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $val)  {
                                    if(isset($val['is_custom_upload']) && $val['is_custom_upload'] == 1) {
                                        $qty_side = $val['val']['qtys'];
                                        $files_side = $val['val']['files'];
                                        $name_side = $val['val']['variants'];
                                        $min_qty = (int) $val['min_qty'];
                                        foreach($qty_side as $k => $qty) {
                                            $qty = (int) $qty;
                                            if($k != $design_index) {
                                                $sum_qty += $qty;
                                            }
                                        }

                                        if($sum_qty >= $min_qty) {
                                            $passed = true;
                                            unset($qty_side[$design_index]);
                                            unset($files_side[$design_index]);
                                            unset($name_side[$design_index]);
                                            $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['qtys'] = $qty_side;
                                            $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['qtys'] = $qty_side;
                                            $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['files'] = $files_side;
                                            $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['files'][] = $files_side;
                                            $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['variants'] = $name_side;
                                            $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['variants'] = $name_side;

                                            $cart_item['nbo_meta']['field'][$key]['files'] = $files_side;
                                            $cart_item['nbo_meta']['field'][$key]['variants'] = $name_side;
                                            $cart_item['nbo_meta']['field'][$key]['qtys'] = $qty_side;
                                        }
                                    }
                                }
                                if( $passed ) {
                                    $results['flag'] = 1;
                                    WC()->cart->cart_contents[ $item_key ] = $cart_item;
                                    WC()->cart->set_quantity( $item_key, $sum_qty );
                                    WC()->cart->set_session();
                                }
                            }
                        }
                    }
                }
            }
            
            wp_send_json_success($results);
         
            die();
        }

        function kitalabel_ajax_qty_cart() {
            $params = array();
            $results = array(
                'flag'  => 1
            );
            if( isset($_POST['data']) ) {
                $upload_fields = false;
                parse_str($_POST['data'], $params);
                $min_qty    = isset($_POST['min_qty']) ? (int) $_POST['min_qty'] : 0;
                $item_key   = isset($_POST['item_key']) ? $_POST['item_key'] : '';
                $qty_side   = $params['qty_side'][$item_key];
                if( $item_key ) {
                    $cart_items = WC()->cart->get_cart();
                    if( isset( $cart_items[$item_key] )) {
                        $cart_item = $cart_items[$item_key];
                        $nbd_field = $cart_item['nbo_meta']['field'] ;
                        $nbd_fields = $cart_item['nbo_meta']['option_price']['fields'] ;
                        if( isset( $cart_item['nbo_meta'] ) ) {
                            $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
                            if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
                                $item_combination = $fields['combination'];
                                $upload_fields = false;
                            }
                        }
                        if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['option_price'] ) && isset( $cart_item['nbo_meta']['option_price']['fields'] ) ) {
                            foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $field)  {
                                if(isset($field['is_custom_upload'])) {
                                    $upload_fields = true;
                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['val']['qtys'] = $qty_side;
                                    $cart_item['nbo_meta']['option_price']['fields'][$key]['value_name']['qtys'] = $qty_side;
                                    $nbd_field[$key]['qtys'] = $qty_side;
                                }
                            }
                        }
                    }
                }

                $fe_options = new NBD_FRONTEND_PRINTING_OPTIONS;
                if( isset($item_combination) && isset($nbd_fields) ) {
                    foreach($nbd_fields as $key => $val) {
                        $_origin_field   = $fe_options->get_field_by_id( $fields, $key );
                        if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'area' ) {
                            $area_name = $val['value_name'];
                            $_area_name = $val['value_name'];
                            if( $area_name == 'Square' || $area_name == 'Circle' ) {
                                $_area_name = 'Square + Circle';
                            }
                            if( $area_name == 'Rectangle' || $area_name == 'Oval' ) {
                                $_area_name = 'Rectangle + Oval';
                            }
                        }
                        if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'size' ) {
                            $size_name = $val['value_name'];
                        }
                        if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'color' ) {
                            $material_name = $val['value_name'];
                        }
                    }
                    if( isset($_area_name) && isset($size_name) && isset($material_name)  && isset($item_combination['options']) ) {
                        $side = $item_combination['options'][$_area_name][$size_name][$material_name];
                        if(!isset($side) && isset($item_combination['options']['default'])) {
                            $side = $item_combination['options']['default'];
                        }
                    }
                }
                $sum_qty    = 0;
                foreach( $qty_side as $key => $qty ) {
                    $sum_qty += (int) $qty;
                }
                if( $upload_fields ) {
                    if(isset($cart_item) && isset($nbd_field) ) {
                        WC()->cart->cart_contents[ $item_key ] = $cart_item;
                        WC()->cart->cart_contents[ $item_key ]['nbo_meta']['field'] = $nbd_field;
                        WC()->cart->set_quantity( $item_key, $sum_qty );
                        WC()->cart->set_session();
                    }
                } else if(  isset($side) && $sum_qty >= (int)$side['qty'] ) {
                    WC()->cart->set_quantity( $item_key, $sum_qty, true );
                    // set option qty side
                    $option_fields = unserialize( base64_decode( WC()->cart->cart_contents[ $item_key ]['nbo_meta']['options']['fields']) );
                    $qty_breaks = $option_fields['combination']['qty_breaks'];
                    if( $option_fields['combination']['enabled'] == 'on' ) {
                        $option_fields['combination']['side'] = $qty_side;
                        $options['fields'] = serialize($option_fields);
                        $options['fields'] = base64_encode( $options['fields'] );
                        WC()->cart->cart_contents[ $item_key ]['nbo_meta']['options']['fields'] = $options['fields'];
                        WC()->cart->set_session();
                    }
                    
                } else {
                    $results['flag'] = 0;
                }
            }
            
            wp_send_json_success($results);
         
            die();
        }

    }
}

$kitalabel_custom_hooks = Kitalabel_Custom_Hooks::instance();
$kitalabel_custom_hooks->init();