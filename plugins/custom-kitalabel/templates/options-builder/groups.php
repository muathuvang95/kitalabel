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
<div class="nbo-group-timeline-container" ng-class="totalGroupPage > 1 ? 'paged' : ''">
    <div class="nbo-group-timeline-wrap">
        <div class="nbo-group-timeline-line" ng-style="{'width': <?php echo ( $no_group + 1 ) * 150; ?> + 'px', transform: 'translateX(' + groupTimeLineTranslate + ')'}">
            <?php foreach( $nbo_groups as $g_index => $nbo_group ): ?>
                <div class="nbo-group-timeline-step" 
                    ng-class="{ 'active': current_group_panel == <?php echo $g_index; ?>, 'over': current_group_panel > <?php echo $g_index; ?>}" 
                    ng-style="{'left': <?php echo ( $g_index + 1 ) * 150; ?> + 'px'}"
                    ng-click="changeGroupPanel($event, <?php echo $g_index; ?>)" >
                    <div class="nbo-group-timeline-step-inner">
                        <span class="nbo-group-timeline-tooltip"><?php echo $nbo_group['title']; ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="nbo-group-timeline-fill-line" ng-style="{transform: 'scaleX(' + ( current_group_panel + 1 ) / ( no_of_group + 1 ) + ')'}"></div>
        </div>
    </div>
    <div class="nbo-group-timeline-paged nbo-group-timeline-paged-prev" ng-click="changeGroupPage($event, 'prev')" ng-class="currentGroupPage == 0 ? 'nbo-disabled' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
        </svg>
    </div>
    <div class="nbo-group-timeline-paged nbo-group-timeline-paged-next" ng-click="changeGroupPage($event, 'next')" ng-class="currentGroupPage == ( totalGroupPage - 1 ) ? 'nbo-disabled' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
        </svg>
    </div>
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
<div class="row nbo-group-wrap nbo-flex-col-<?php echo $cols; ?> nbd-column-<?php echo $key; ?>" <?php if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): ?>ng-style="{width: 100 / no_of_group + '%'}"<?php endif; ?> >
    <div class="col-md-5 nbo-group-left">
        <div class="nbo-thumbnail nbo-thumbnail-<?php echo $cols; ?>">
            <?php
                $_thumbnail = NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                $_thumbnail_full = NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                $size = '';
                $descs = array();
                foreach( $group['fields'] as $k => $f1 ){
                    $f1_index    = get_field_index_by_id( $f1, $options["fields"] );
                    $_field      = $options["fields"][$f1_index];
                    $_options_first = $_field['general']['attributes']['options'][0];
                    if( isset($_field['nbd_type']) && ( $_field['nbd_type'] == 'area' || $_field['nbd_type'] == 'shape' || $_field['nbd_type'] ==  'color' ) && isset( $_options_first['image_link'] ) ) {
                        $_thumbnail      = $_options_first['image_link'];
                        $_thumbnail_full = $_options_first['full_src'];
                    } 
                    if( isset($_field['nbd_type']) && $_field['nbd_type'] == 'size' ) {
                        $size = $_options_first['name'];
                    }
                    // if( isset($_field['nbd_type']) && ( $_field['nbd_type'] == 'color' ||  $_field['nbd_type'] == 'finishing') ) {
                    //     $desc = $_options_first['name'];
                    // }
                    $_attrs = $_field['general']['attributes']['options'];
                    foreach( $_attrs as $_attr) {
                        if( (isset($_attr['benefit']) && $_attr['benefit'] != '' ) || ( isset($_attr['un_benefit']) && $_attr['un_benefit'] != '' ) ) {
                            $descs[] = array(
                                'benefit'      => $_attr['benefit'],
                                'un_benefit'   => $_attr['un_benefit'],
                                'name'         => $_attr['name'],
                                'k'            => $k,
                                'id'           => $f1,
                            );
                            break;
                        }
                    }
                } 
            ?>
            <div class="wrap-image nb-custom-box" data-thumb="<?php echo $_thumbnail; ?>">
                <img width="648" height="648" src="<?php echo $_thumbnail; ?>" class="wp-post-image" alt="" loading="lazy" title="32" data-caption="" data-src="<?php echo $_thumbnail_full; ?>" data-large_image="<?php echo $_thumbnail_full; ?>" data-large_image_width="514" data-large_image_height="514" draggable="false" sizes="(max-width: 648px) 100vw, 648px">
            </div>
            <?php
            if(count($descs) > 0) { 
                foreach($descs as $desc) {
            ?>
            <div class="benefit-wrap nb-custom-box benefit-col-<?php echo $desc['k']; ?>" ng-if="nbd_fields['<?php echo $desc['id']; ?>'].enable">
                <div class="title" data-title="<?php echo $desc['name']; ?>">
                    <p><?php echo $desc['name']; ?></p>
                </div>
                <div class="row benefit-item benefit">
                    <div class="col-md-3">
                        <div class="icon">
                            <div class="icon-wrap">
                                <svg width="61" height="60" viewBox="0 0 61 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M27.6322 53.9069C21.219 55.5534 14.7289 62.4605 8.94869 59.081C3.35201 55.8088 5.37144 46.6031 3.88767 40.0243C2.55989 34.1372 -1.61272 28.1489 0.727694 22.6306C3.06007 17.1313 10.5983 17.3726 15.0753 13.646C19.8473 9.67375 21.642 1.66103 27.5746 0.283224C33.6828 -1.13536 39.6417 3.0502 44.8429 6.7327C50.2247 10.5431 55.2613 15.1673 57.6118 21.5501C60.0446 28.1563 61.4172 36.2374 57.9367 42.2887C54.5521 48.1734 46.8392 48.5774 40.7166 50.9247C36.4173 52.573 32.0765 52.7658 27.6322 53.9069Z" fill="#EF8C04"/>
                                </svg>
                            </div>
                            <div class="icon-inner">
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.0837 17.0837V29.5837H12.917V17.0837H0.416992V12.917H12.917V0.416992H17.0837V12.917H29.5837V17.0837H17.0837Z" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="benefit-content" data-benefit="<?php echo str_replace( '/' , '<br>' ,$desc['benefit']); ?>">
                            <?php echo str_replace( '/' , '<br>' ,$desc['benefit']); ?>
                        </div>
                    </div>
                </div>
                <div class="row benefit-item un-benefit">
                    <div class="col-md-3">
                        <div class="icon">
                            <div class="icon-wrap">
                                <svg width="61" height="60" viewBox="0 0 61 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M27.6322 53.9069C21.219 55.5534 14.7289 62.4605 8.94869 59.081C3.35201 55.8088 5.37144 46.6031 3.88767 40.0243C2.55989 34.1372 -1.61272 28.1489 0.727694 22.6306C3.06007 17.1313 10.5983 17.3726 15.0753 13.646C19.8473 9.67375 21.642 1.66103 27.5746 0.283224C33.6828 -1.13536 39.6417 3.0502 44.8429 6.7327C50.2247 10.5431 55.2613 15.1673 57.6118 21.5501C60.0446 28.1563 61.4172 36.2374 57.9367 42.2887C54.5521 48.1734 46.8392 48.5774 40.7166 50.9247C36.4173 52.573 32.0765 52.7658 27.6322 53.9069Z" fill="#EF8C04"/>
                                </svg>
                            </div>
                            <div class="icon-inner">
                                <svg width="30" height="6" viewBox="0 0 30 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.416016 5.08366V0.916992H29.5827V5.08366H0.416016Z" fill="white"/>
                                </svg>

                            </div>
                           
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="benefit-content" data-benefit="<?php echo str_replace( '/' , '<br>' ,$desc['un_benefit']); ?>">
                            <?php echo str_replace( '/' , '<br>' ,$desc['un_benefit']); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            }
        }
        ?>
        </div>
        <div class="nbo-desc nbo-desc-<?php echo $cols; ?>">
            
        </div>
    </div>
    <div class="col-md-7 nbo-group-options">
        <div class="nbo-group-body nb-custom-box">
            <div class="nb-top-icon">
                <svg width="54" height="36" viewBox="0 0 54 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 30.5C36.8 12.1 24 2.5 13 0L14.5 -9.5H48.5L63.5 -2.5L54 36C52 18 17 24.8333 0 30.5Z" fill="#EF8C04"/>
                </svg>
            </div>
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
    font-weight: 800;
    font-size: 34px;
    line-height: 42px;
    color: #3C3C3C;
    font-style: italic;padding: 30px 0 10px 50px;" class="title">Other Options</div>
            <span class="nbo-group-toggle" ng-click="toggle_group($event)">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                    <path style="fill: #ee8c03;" d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                </svg>
            </span>
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
    if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): ?>
        </div>
    </div>
    <div>
        <span class="nbo_group_panel_prev" ng-click="changeGroupPanel($event, 'prev')" ng-class="current_group_panel == 0 ? 'nbo-disabled' : ''">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
            </svg>
            <span><?php _e('Prev', 'web-to-print-online-designer'); ?></span>
        </span>
        <span class="nbo_group_panel_next" ng-click="changeGroupPanel($event, 'next')" ng-class="current_group_panel == ( no_of_group - 1 ) ? 'nbo-disabled' : ''">
            <span><?php _e('Next', 'web-to-print-online-designer'); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
            </svg>
        </span>
    </div>
<?php endif;