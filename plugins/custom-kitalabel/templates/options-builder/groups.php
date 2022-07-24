<?php if (!defined('ABSPATH')) exit; ?>
<?php 
    if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): 
        $no_group   = 0;
        $nbo_groups = array();
        foreach( $options['groups'] as $group ){
            if( isset( $group['fields'] ) && count( $group['fields'] ) > 0 ){
                $no_group++;
                $nbo_groups[] = $group;
            }
        }
?>
<div class="nb-title-page">
    <div class="heading">
        <div class="heading-kita-title">
            <span class="title"><?php the_title(); ?></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <!-- <div class="nb-product-thumbnail">
            <?php woocommerce_show_product_images(); ?>
        </div> -->
        <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );?>
                                        
        <div class="nbo-thumbnail">
            <div class="wrap-image nb-custom-box" data-thumb="<?php echo $image[0]; ?>">
                <img width="648" height="648" src="<?php echo $image[0]; ?>" class="wp-post-image" alt="" loading="lazy" title="32" data-caption="" data-src="<?php echo $image[0]; ?>" data-large_image="<?php echo $image[0]; ?>" data-large_image_width="514" data-large_image_height="514" draggable="false" sizes="(max-width: 648px) 100vw, 648px">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="nbo-group-timeline-container nb-group-timeline-container" ng-class="totalGroupPage > 1 ? 'paged' : ''">
            <div class="nbo-group-timeline-wrap">
                <!-- <div class="nbo-group-timeline-line" style="transform: translateX(-50%);left: 50%;" ng-style="{'width': <?php echo ( $no_group + 1 ) * 100; ?> + 'px'}">
                    <?php foreach( $nbo_groups as $g_index => $nbo_group ): ?>
                        <div class="nbo-group-timeline-step" 
                            ng-class="{ 'active': current_group_panel == <?php echo $g_index; ?>, 'over': current_group_panel > <?php echo $g_index; ?>}" 
                            ng-style="{'left': <?php echo ( $g_index ) * 100; ?> + 'px'}"
                            ng-click="changeGroupPanel($event, <?php echo $g_index; ?>)" >
                            <div class="nb-group-timeline-step-inner">
                                <span class="nb-group-timeline-number"><?php echo $g_index + 1; ?></span>
                                <span class="nb-group-timeline-tooltip"><?php echo $nbo_group['title']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="nbo-group-timeline-fill-line" ng-style="{transform: 'scaleX(' + ( current_group_panel + 1 ) / ( no_of_group + 1 ) + ')'}"></div>
                </div> -->
                <div class="nbo-group-timeline-line" style="transform: translateX(-50%);left: 50%; display: flex; justify-content: space-between; width: calc(100% - 56px);">
                    <?php foreach( $nbo_groups as $g_index => $nbo_group ): ?>
                        <div class="nbo-group-timeline-step" 
                            ng-class="{ 'active': current_group_panel == <?php echo $g_index; ?>, 'over': current_group_panel > <?php echo $g_index; ?>}" 
                            ng-click="changeGroupPanel($event, <?php echo $g_index; ?>)"
                            style="position: initial;"
                        >
                            <div class="nb-group-timeline-step-inner" style="position: relative;">
                                <span class="nb-group-timeline-number"><?php echo $g_index + 1; ?></span>
                                <span class="nb-group-timeline-tooltip"><?php echo $nbo_group['title']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
      <!--       <div class="nbo-group-timeline-paged nbo-group-timeline-paged-prev" ng-click="changeGroupPage($event, 'prev')" ng-class="currentGroupPage == 0 ? 'nbo-disabled' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                </svg>
            </div>
            <div class="nbo-group-timeline-paged nbo-group-timeline-paged-next" ng-click="changeGroupPage($event, 'next')" ng-class="currentGroupPage == ( totalGroupPage - 1 ) ? 'nbo-disabled' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                </svg>
            </div> -->
        </div>
        <div class="nbo_group_panel_wrap">
            <div class="nbo_group_panel_wrap_inner" ng-init="no_of_group=<?php echo $no_group; ?>" ng-style="{'width': <?php echo $no_group * 100; ?> + '%', transform: 'translateX(-' + current_group_panel * 100 / no_of_group + '%)'}">
        <?php endif; ?>
        <?php 
            $_count = 1;
            
            foreach( $options['groups'] as $key => $group ): 
                if( isset( $group['fields'] ) && count( $group['fields'] ) > 0 ):
                    $cols = (int) $group['cols'];
                    if( count( $group['fields'] ) < $cols ) $cols = count( $group['fields'] );
        ?>
                <div class="nbo-group-wrap nbo-flex-col-<?php echo $cols; ?> nbd-column-<?php echo $key; ?>" <?php if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): ?>ng-style="{width: 100 / no_of_group + '%'}"<?php endif; ?> >
                    <div class="nbo-group-options">
                        <div class="nbo-group-body nb-custom-box">
                            <?php 
                                foreach( $group['fields'] as $f ){
                                    $f_index    = get_field_index_by_id( $f, $options["fields"] );
                                    $field      = $options["fields"][$f_index];
                                    $class      = $field['class'];
                                    if( $field['general']['enabled'] == 'y' && $field['need_show'] ) include( $field['template'] );
                                }
                            ?>
                        </div>
                        <?php if(count($options['groups']) == $_count ): ?>
                        <div class="nbo-group-body nb-custom-box nbo-collapse" style="margin-top: 60px;">
                            <div class="nb-top-icon">
                                <svg width="54" height="36" viewBox="0 0 54 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 30.5C36.8 12.1 24 2.5 13 0L14.5 -9.5H48.5L63.5 -2.5L54 36C52 18 17 24.8333 0 30.5Z" fill="#EF8C04"/>
                                </svg>
                            </div>
                                <div style="
                                width:  100%;
                    font-weight: 700;
                    font-size: 24px;
                    line-height: 30px;
                    color: #3C3C3C;
                    font-style: italic;padding: 10px;" class="title">Other Options</div>
                            <?php 
                                foreach( $options["fields"] as $key => $field ){
                                    $tempalte = $options["fields"][$key]['template'];
                                    $need_show = $options["fields"][$key]['need_show'];
                                    $class = $options["fields"][$key]['class'];
                                    if( !( isset( $field['show_in_group'] ) || isset( $field['show_in_popup'] ) ) ){
                                        if( $field['general']['enabled'] == 'y' && $need_show ) include( $tempalte );
                                    }
                                }
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
        <?php $_count++; ?>
        <?php endif; endforeach;
        ?>
            </div>
        </div>
        <?php
            if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): ?>
                
            <div class="nb-group_panel-wrap">
                <span class="nb_group_panel_prev" ng-click="changeGroupPanel($event, 'prev')" ng-class="current_group_panel == 0 ? 'nbo-disabled' : ''">
                 <!--    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                    </svg> -->
                    <span><?php _e('Prev', 'web-to-print-online-designer'); ?></span>
                </span>
                <span class="nb_group_panel_next" ng-click="changeGroupPanel($event, 'next')" ng-class="current_group_panel == ( no_of_group - 1 ) ? 'nbo-disabled' : ''">
                    <span><?php _e('Next', 'web-to-print-online-designer'); ?></span>
<!--                     <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                    </svg> -->
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>