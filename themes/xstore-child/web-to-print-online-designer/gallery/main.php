<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="row nbd-custom-search" style="display: flex;justify-content: space-between;align-items: center;margin: 20px 0;">
    <div class="col-md-9 nbd-search">
        <?php
        $search_type    = isset( $_GET['search_type'] ) && $_GET['search_type'] != '' ? $_GET['search_type'] : '';
        $selected_type  = $search_type == 'design' ? __( 'Design name', 'web-to-print-online-designer') : ( $search_type == 'artist' ? __( 'Artist', 'web-to-print-online-designer') : __( 'All', 'web-to-print-online-designer') );
        ?>

        <div class="nbdl-search-bar" style="position: inherit;top: 0;left: auto;transform: initial;">
            <label class="nbdl-search-content-wrap">
                <input id="nbdl-search-content" placeholder="<?php esc_attr_e( 'Search design name', 'web-to-print-online-designer'); ?>"/>
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            </label>
            <div class="nbdl-search-type-wrap">
                <span class="nbdl-search-type-selected"><?php echo $selected_type; ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/><path d="M0 0h24v24H0V0z" fill="none"/></svg>
                <ul id="nbdl-search-type">
                    <li data-value="" class="<?php echo $search_type == '' ? 'active' : ''; ?>" ><?php esc_html_e( 'All', 'web-to-print-online-designer'); ?></li>
                    <li data-value="design" class="<?php echo $search_type == 'design' ? 'active' : ''; ?>" ><?php esc_html_e( 'Design name', 'web-to-print-online-designer'); ?></li>
                    <li data-value="artist" class="<?php echo $search_type == 'artist' ? 'active' : ''; ?>" ><?php esc_html_e( 'Artist', 'web-to-print-online-designer'); ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3 nbd-sort">
        <form action="" method="post" class="w-100">
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
        <?php include_once(get_stylesheet_directory()."/web-to-print-online-designer/gallery/sidebar.php"); ?>
    </div>
    <?php endif; ?>
    <div class="nbd-list-designs <?php if( $show_sidebar == 'y' ) echo 'nbd-hidden-sidebar'; ?>">
        <?php if( isset( $_GET['tag'] ) || isset( $_GET['color'] ) || isset( $_GET['search'] ) ): ?>
        <div class="nbd-gallery-filter">
            <span class="nbd-gallery-filter-text"><?php esc_html_e("You've Selected", 'web-to-print-online-designer'); ?></span> <?php do_action( 'nbd_gallery_filter' ); ?> <a href="#" class="nbd-gallery-filter-clear"><?php esc_html_e("Clear All", 'web-to-print-online-designer'); ?></a>
        </div>
        <?php endif; ?>
        <?php $column = absint( get_option( 'nbdesigner_gallery_column', 3 ) ); ?>
        <div class="nbdesigner-gallery nbd-gallery-wrap <?php echo 'nbd-gallery-column-' . $column;?>" id="nbdesigner-gallery">
        <?php 
            if( $pid && count( $templates ) ):
            $link_start_design = add_query_arg(array('product_id' => $pid),  getUrlPageNBD('create'));
        ?>
            <div class="nbdesigner-item">
                <div class="nbd-gallery-item nbd-gallery-item-upload">
                    <div class="nbd-gallery-item-upload-inner">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80">
                            <title>plus-circle</title>
                            <path fill="#ddd" d="M40 3.333c-20.333 0-36.667 16.333-36.667 36.667s16.333 36.667 36.667 36.667 36.667-16.333 36.667-36.667-16.333-36.667-36.667-36.667zM40 70c-16.667 0-30-13.333-30-30s13.333-30 30-30c16.667 0 30 13.333 30 30s-13.333 30-30 30z"></path>
                            <path fill="#ddd" d="M53.333 36.667h-10v-10c0-2-1.333-3.333-3.333-3.333s-3.333 1.333-3.333 3.333v10h-10c-2 0-3.333 1.333-3.333 3.333s1.333 3.333 3.333 3.333h10v10c0 2 1.333 3.333 3.333 3.333s3.333-1.333 3.333-3.333v-10h10c2 0 3.333-1.333 3.333-3.333s-1.333-3.333-3.333-3.333z"></path>
                        </svg>
                    </div>
                    <div class="nbd-gallery-item-upload-inner">
                        <a href="<?php echo esc_url( $link_start_design ); ?>" class="" target="_blank" title="<?php esc_html_e('Start design', 'web-to-print-online-designer'); ?>">
                        <?php esc_html_e('Design or', 'web-to-print-online-designer'); ?><br />
                        <?php esc_html_e('Upload file', 'web-to-print-online-designer'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php include_once(get_stylesheet_directory()."/web-to-print-online-designer/gallery/gallery-item.php"); ?>
        </div> 
        <div>
            <div class="nbd-load-more" id="nbd-load-more"></div>
            <div id="nbd-pagination-wrap" >
                <?php include_once( ABSPATH . 'wp-content/plugins/web-to-print-online-designer/templates/gallery/pagination.php' ); ?>
            </div>
            <?php include_once( ABSPATH . 'wp-content/plugins/web-to-print-online-designer/templates/gallery/popup-wrap.php' ); ?>
        </div>
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
    // if( typeof wp == 'object' && typeof wp.template == 'function' && jQuery('script[id="tmpl-nbdl-search-bar"]').length > 0 ){
    //     var content_search = jQuery.fn.cs_nbd_template( wp.template( 'nbdl-search-bar' ), {} );
    //     jQuery(".nbd-gallery .nbd-custom-search .nbd-search").append(content_search);
    // }
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

