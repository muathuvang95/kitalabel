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

        public function zip_files( $file_names, $archive_file_name, $nameZip, $order_id, $products_name ){
            if(file_exists($archive_file_name)){
                unlink($archive_file_name);
            }
            if ( class_exists( 'ZipArchive' ) ) {
                $zip = new ZipArchive();
                if ( $zip->open( $archive_file_name, ZIPARCHIVE::CREATE ) !== TRUE ) {
                  exit( "cannot open <$archive_file_name>\n" );
                }
                foreach( $file_names as $key => $file ) {
                    $pre_name = $order_id . '_';
                    if( isset($products_name[$key]) && $products_name[$key] ) {
                        $product_name = $products_name[$key];
                        $pre_name .= $product_name . '_';
                    }
                    $name = $pre_name . basename( $file );
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
                    $products_name = array();
                    $index = 0;
                    $list_products_name = array();
                    foreach( $products AS $order_item_id => $product ){
                        $product_name = $product->get_name();
                        if(in_array($product->get_name(), $list_products_name)) {
                            $product_name = $product->get_name() . $index;
                        }
                        $list_products_name[] = $product_name;
                        if( wc_get_order_item_meta( $order_item_id, '_nbd' ) || wc_get_order_item_meta( $order_item_id, '_nbu' ) ){
                            $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                            $nbu_item_key = wc_get_order_item_meta( $order_item_id, '_nbu' );
                            $origin_order  = wc_get_order_item_meta( $order_item_id, '_order_again' );
                            if( $nbd_item_key ){
                                $list_pdf = Nbdesigner_IO::get_list_files_by_type(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/customer-pdfs' , 1, 'pdf');
                                if( count( $list_pdf ) > 0 ){
                                    foreach( $list_pdf as $key => $pdf ){
                                        $zip_files[] = $pdf;
                                        $products_name[] = $product_name;
                                    }
                                }
                            }
                            if( $nbu_item_key ){
                                $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                                $files = apply_filters( 'nbu_download_upload_files', $files, $product );
                                if( count( $files ) > 0 ){
                                    foreach( $files as $key => $file ){
                                        $zip_files[] = $file;
                                        $products_name[] = $product_name;
                                    }
                                }
                            }
                        }

                        $index++;
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
                        $this->zip_files( $zip_files, $pathZip, $nameZip, $order_id, $products_name);
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
                'kitalabel_download_all' => true
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

    }
}

$kitalabel_custom_hooks = Kitalabel_Custom_Hooks::instance();
$kitalabel_custom_hooks->init();