<?php if (!defined('ABSPATH')) exit; ?>

<div class="kitalabel-order-label-page">
    <div class="row">
        <div class="col-md-6 nbo-group-left">
            <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );?>
                                            
            <div class="nbo-thumbnail">
                <div class="wrap-image nb-custom-box" data-thumb="<?php echo $image[0]; ?>">
                    <img width="648" height="648" src="<?php echo $image[0]; ?>" class="wp-post-image" alt="" loading="lazy" title="32" data-caption="" data-src="<?php echo $image[0]; ?>" data-large_image="<?php echo $image[0]; ?>" data-large_image_width="514" data-large_image_height="514" draggable="false" sizes="(max-width: 648px) 100vw, 648px">
                </div>
            </div>
        </div>
        <div class="col-md-6 nbo-group-right">
            <div class="kitalabel-options">
                <div class="nb-title-page">
                    <h3 class="title"><?php the_title(); ?></h3>
                </div>
                <?php
                foreach( $options["fields"] as $key => $field ){
                    $tempalte = $options["fields"][$key]['template'];
                    $need_show = $options["fields"][$key]['need_show'];
                    $class = $options["fields"][$key]['class'];
                    if( !( isset( $field['show_in_group'] ) || isset( $field['show_in_popup'] ) ) ){
                        if( $field['general']['enabled'] == 'y' && $need_show ) include( $tempalte );
                    }
                }
                $disable_quantity_input = false;
                $show_quantity_option   = false;
                if( $options['quantity_enable'] == 'y' && !$is_sold_individually && !($options['display_type'] == 3 && count($options['bulk_fields'])) ){
                    $disable_quantity_input = $options['quantity_type'] != 'r' ? true : false;
                    $show_quantity_option = true;
                    if( !$has_delivery ) include($tempalte = CUSTOM_KITALABEL_PATH .'templates/options-builder/quantity.php');
                }
                ?>
            </div>
            <?php do_action('nbo_after_summary' , $product_id ); ?>
        </div>
    </div>
</div>

