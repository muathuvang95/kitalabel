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
        }

        public function kitalabel_download_pdf_all($order, $order_id) {
            ?>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Download PDF all
            </button>

            <!-- Modal -->
            <div class="modal fade" id="kitalabelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create PDF</h5>
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
                                if($order) {
                                    foreach ( $order->get_items() as $item_id => $item ) {
                                        $nbd_item_key = wc_get_order_item_meta( $item_id, '_nbd' );
                                        $path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key;
                                        $pdf_path = $path . '/customer-pdfs';
                                        $list_pdf = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'pdf');
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
                                                <?php if(count($list_pdf) > 0) {
                                                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>';
                                                } else {
                                                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>';
                                                } ?>   
                                            </td>
                                            <td>
                                                <button class="btn btn-primary" data-item-id="<?php echo esc_attr($item_id);  ?>">Create</button>
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
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