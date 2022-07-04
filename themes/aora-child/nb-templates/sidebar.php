<div class="nbd-designers nbd-sidebar-con">
    <div class="nbd-sidebar-con-inner">
        <div class="nbd-tem-list-product-wrap">
            <ul>
            <li class="nbd-tem-list-product">
                <a class="<?php if(!$pid) echo 'active'; ?>" href="<?php echo get_home_url().'/templates' ?>">
                    <span>All</span>
                </a>
            </li>
            <?php
            foreach( $products as $key => $product ): 
                if( nbd_count_total_template($product['product_id'] , 0) == 0 ) { continue; }
                $link_prodcut_templates = add_query_arg(array('pid' => $product['product_id']), getUrlPageNBD('gallery'));
            ?>
                <li class="nbd-tem-list-product <?php if($key > 14) echo 'nbd-hide'; ?>">
                    <a class="<?php if($pid == $product['product_id']) echo 'active'; ?>" href="<?php echo esc_url( $link_prodcut_templates ); ?>">
                        <span><?php esc_html_e( $product['name'] ); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
            <!-- <?php if(count($products) > 15): ?>
            <a class="nbd-see-all" href="javascript:void(0)" onclick="showAllProduct( this )"><?php esc_html_e('See All', 'web-to-print-online-designer'); ?></a>
            <?php endif; ?> -->
        </div>
    </div>
</div>
<script>
    var showAllProduct = function(e){
        jQuery(e).hide();
        jQuery('.nbd-tem-list-product-wrap').addClass('see-all');
        jQuery('.nbd-tem-list-product-wrap ul li').removeClass('nbd-hide');
    }
</script>