<?php 
    $location = 'mobile-menu';
    $tbay_location  = '';
    if ( has_nav_menu( $location ) ) { 
        $tbay_location = $location;
    }

    
    $mmenu_langue           = aora_tbay_get_config('enable_mmenu_langue', false); 
    $mmenu_currency         = aora_tbay_get_config('enable_mmenu_currency', false); 

    $menu_mobile_select    =  aora_tbay_get_config('menu_mobile_select');

?>
  
<div id="tbay-mobile-smartmenu" data-title="<?php esc_attr_e('Menu', 'aora'); ?>" class="tbay-mmenu d-xl-none"> 


    <div class="tbay-offcanvas-body">
        
        <div id="mmenu-close">
            <?php
                $mobilelogo 			= aora_tbay_get_config('mobile-logo');

                $output 	= '<div class="mobile-logo">';
                    if( isset($mobilelogo['url']) && !empty($mobilelogo['url']) ) { 
                        $url    	= $mobilelogo['url'];
                        $output 	.= '<a href="'. esc_url( home_url( '/' ) ) .'">'; 
        
                        if( isset($mobilelogo['width']) && !empty($mobilelogo['width']) ) {
                            $output 		.= '<img src="'. esc_url( $url ) .'" width="'. esc_attr($mobilelogo['width']) .'" height="'. esc_attr($mobilelogo['height']) .'" alt="'. get_bloginfo( 'name' ) .'">';
                        } else {
                            $output 		.= '<img class="logo-mobile-img" src="'. esc_url( $url ) .'" alt="'. get_bloginfo( 'name' ) .'">';
                        }
        
                        
                        $output 		.= '</a>';
        
                    } else {
                        $output 		.= '<div class="logo-theme">';
                            $output 	.= '<a href="'. esc_url( home_url( '/' ) ) .'">';
                            $output 	.= '<img class="logo-mobile-img" src="'. esc_url_raw( AORA_IMAGES.'/logo-mobile.png') .'" alt="'. get_bloginfo( 'name' ) .'">';
                            $output 	.= '</a>';
                        $output 		.= '</div>';
                    }
                $output 	.= '</div>';
                echo $output;
            ?>
            <button type="button" class="btn btn-toggle-canvas" data-toggle="offcanvas">
                <i class="tb-icon tb-icon-close-01"></i>
            </button>
        </div>

        <div id="mmenu-search-form">
            <form class="form minisearch" action="<?php echo site_url();?>" method="GET">
                <div class="field searchbox">
                    <div class="control">
                        <div class="input-content">
                            <input type="text" name="s" class="input-searchbox nbt-input-search" placeholder="<?php esc_attr_e('Enter keyword to search...', 'aora');?>" maxlength="128" role="combobox" aria-haspopup="false" aria-autocomplete="both" autocomplete="off" aria-expanded="false" value="">
                        </div>

                    </div>
                </div>
                <div class="actions">
                    <button type="submit" title="Search" class="button btn-searchbox"><i class="tb-icon tb-icon-search"></i></button>
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="taxonomy" value="product_cat">
                </div>
            </form>
        </div>

        <div id="mmenu-footer">
            <?php
                if($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
                    echo do_shortcode( '[pafe-template id="7818"]' );
                }
                else {
                    echo do_shortcode( '[pafe-template id="8422"]' );
                }
            ?>
        </div>

        <nav id="tbay-mobile-menu-navbar" class="navbar navbar-offcanvas navbar-static">

            <?php

                $args = array(
                    'fallback_cb' => '',
                );

                if( empty($menu_mobile_select) ) {
                    $args['theme_location']     = $tbay_location;
                } else {
                    $args['menu']               = $menu_mobile_select;
                }

                $args['container_id']       =   'main-mobile-menu-mmenu';
                $args['menu_id']            =   'main-mobile-menu-mmenu-wrapper';
                $args['walker']             =   new Aora_Tbay_mmenu_menu();

                wp_nav_menu($args);


            ?>
        </nav>


    </div>
    <?php if($mmenu_langue || $mmenu_currency ) {
        ?>
         <div id="mm-tbay-bottom">  
    
            <div class="mm-bottom-track-wrapper">

                <?php 
                    ?>
                    <div class="mm-bottom-langue-currency ">
                        <?php if( $mmenu_langue ): ?>
                            <div class="mm-bottom-langue">
                                <?php do_action('aora_tbay_header_custom_language'); ?>
                            </div>
                        <?php endif; ?>
                
                        <?php if( $mmenu_currency && class_exists('WooCommerce') && class_exists( 'WOOCS' ) ): ?>
                            <div class="mm-bottom-currency">
                                <div class="tbay-currency">
                                <?php echo do_shortcode( '[woocs txt_type = "desc"]' ); ?> 
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    <?php
                ?>
            </div>


        </div>
        <?php
    }
    ?>
   
</div>