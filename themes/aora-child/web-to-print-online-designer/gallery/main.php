<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbd-gallery-con">
    <?php 
        $template_sort_name = $templates;
        usort($template_sort_name, "sort_template_by_name");
        if( isset($_POST['action-submit']) && $_POST['action-submit'] == 'submit' ) {
            $option_search = isset( $_POST['option_search'] ) ? $_POST['option_search'] : '';
            switch ($option_search) {
              case 'namea':
                $templates = $template_sort_name;
                break;
              case 'named':
                $templates = array_reverse($template_sort_name);
                break;
              case 'new':
                usort($templates, "sort_template_by_id");
                break;
            }
        }
        $limit = $row * $per_row;
        $limit = $limit == 0 ? 1 : $limit;
        $current_user_id = get_current_user_id();
        if( $pid || $cat ):
            if( $cat ){
                $product_cat = get_term( $cat, 'product_cat' );
                $title = $product_cat->name; 
            }else{
                $title = get_the_title($pid);
            }
    ?>
    <h2><?php esc_html_e( $title ); ?> <?php esc_html_e('designs', 'web-to-print-online-designer'); ?></h2>
    <?php endif; ?>
    <?php 
        $show_sidebar = get_option( 'nbdesigner_gallery_hide_sidebar', 'n' );
        if( $show_sidebar != 'y' ):
    ?>
    <div class="nbd-sidebar">
        <?php //include_once('sidebar.php'); ?>
        <?php include_once(get_stylesheet_directory()."/nb-templates/sidebar.php"); // custom kitalabel : overwrite the file sidebar at theme-child/nb-template/sidebar.php ?>
    </div>
    <?php endif; ?>
    <style>
        .nbd-custom-search .nbdl-search-bar {
            position: static;
            transform: none;
        }
    </style>
    <div class="row nbd-custom-search">
        <div class="col-md-8 nbd-search"></div>
        <div class="col-md-4 nbd-sort">
            <form action="" method="post">
                <input type="hidden" name="action-submit" value="submit">
                <select class="items" name="option_search">
                    <option value="">Sort templates</option>
                    <option value="namea">Sort by name: A - Z</option>
                    <option value="named">Sort by name: Z - A</option>
                    <option value="new">Newest </option>
                </select>
            </form>            
        </div>
    </div>
    <div class="nbd-list-designs <?php if( $show_sidebar == 'y' ) echo 'nbd-hidden-sidebar'; ?>">
        <?php if( isset( $_GET['tag'] ) || isset( $_GET['color'] ) || isset( $_GET['search'] ) ): ?>
        <div class="nbd-gallery-filter">
            <span class="nbd-gallery-filter-text"><?php esc_html_e("You've Selected", 'web-to-print-online-designer'); ?></span> <?php do_action( 'nbd_gallery_filter' ); ?> <a href="#" class="nbd-gallery-filter-clear"><?php esc_html_e("Clear All", 'web-to-print-online-designer'); ?></a>
        </div>
        <?php endif; ?>
        <?php $column = absint( get_option( 'nbdesigner_gallery_column', 3 ) ); ?>
        <div class="nbdesigner-gallery nbd-gallery-wrap <?php echo 'nbd-gallery-column-' . $column;?>" id="nbdesigner-gallery">
        <?php include_once( ABSPATH . 'wp-content/plugins/web-to-print-online-designer/templates/gallery/gallery-item.php' ); ?>
        </div> 
        <div>
            <div class="nbd-see-more" data-per-page="8" data-page="3">
                <span class="loading"></span>
                Lihat lagi
            </div>
        </div>
    </div>  <!-- End. list designs -->
    <div class="custom-design">
        <a href="<?php echo get_home_url().'/custom-design-page/?product_id=9550' ?>"><?php esc_html_e('Custom design', 'web-to-print-online-designer'); ?></a>
    </div>
</div> 
<script>
    var is_nbd_gallery = 1;
    // NB custom Loadmore Template 
    jQuery(document).ready(function(){
        var total = <?php echo count( $templates) ?>;
        var page = jQuery('.nbd-see-more').data('page');
        var per_page = jQuery('.nbd-see-more').data('per-page');
        jQuery('.nbd-see-more').on('click', function(){
            var event = $(this);
            var item  =  jQuery('#nbdesigner-gallery > div');
            $.ajax({
                type : "post", 
                dataType: "json",
                url : '<?php echo admin_url('admin-ajax.php');?>',
                data : {
                    action: "nb_loadmore_template",
                    per_page: per_page,
                    page: page,
                    pid: <?php echo isset($_GET['pid']) ? $_GET['pid'] : 0; ?>,
                    total: total,
                },
                context: this,
                beforeSend: function () { 
                    event.addClass('active');
                },
                success: function(response) {
                    if(response.success) {
                        event.removeClass('active');
                        jQuery('.nbd-gallery-wrap').append(response.data);
                        jQuery('.nbd-see-more').attr('data-page', page++);
                        if (jQuery('#nbdesigner-gallery > div').hasClass("woocommerce-Message")) {
                            jQuery('.nbd-see-more').hide();
                            return false;
                        }
                    }
                    else {
                        alert('Đã có lỗi xảy ra');
                    }
                },
            })
        });
    })

    // custom MTV
    jQuery.fn.cs_nbd_template = function ( template, obj ) {
        var $template_html = template( obj );

        $template_html = $template_html.replace( '/*<![CDATA[*/', '' );
        $template_html = $template_html.replace( '/*]]>*/', '' );

        return $template_html;
    }
    if( typeof wp == 'object' && typeof wp.template == 'function' && jQuery('script[id="tmpl-nbdl-search-bar"]').length > 0 ){
        var content_search = jQuery.fn.cs_nbd_template( wp.template( 'nbdl-search-bar' ), {} );
        jQuery(".nbd-gallery .nbd-custom-search .nbd-search").append(content_search);
    }
    jQuery(".nbd-gallery .nbd-custom-search .nbd-sort .items").on('change' , function() {
        jQuery(".nbd-gallery .nbd-custom-search .nbd-sort form").submit();
    });
</script>

<style>
    .woocommerce-message.woocommerce-message--info {
        margin: auto;
        border: none;
        background: transparent;
    }
    .nbd-see-more.active {
        font-size: 0px;
    }
    .nbd-see-more.active span.loading {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        padding: 3px;
        background: radial-gradient(farthest-side,#ffa516 95%,#0000) 50% 0/12px 12px no-repeat, radial-gradient(farthest-side,#0000 calc(100% - 5px),#ffa516 calc(100% - 4px)) content-box;
        animation: s6 2s infinite;
        display: block;
    }
    @keyframes s6 {to{transform: rotate(1turn)}}
</style>

