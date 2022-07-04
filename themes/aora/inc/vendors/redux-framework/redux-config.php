<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */

if( defined('TBAY_ELEMENTOR_ACTIVED') && !TBAY_ELEMENTOR_ACTIVED) return;

if (!class_exists('Aora_Redux_Framework_Config')) {

    class Aora_Redux_Framework_Config
    {
        public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;
        public $output;
        public $default_color; 

        public function __construct()
        {
            if (!class_exists('ReduxFramework')) {
                return; 
            }  

            add_action('init', array($this, 'initSettings'), 10);
        }

        public function redux_output() 
        {
            $this->output = require_once( get_parent_theme_file_path( AORA_INC .'/customizer/output.php') );

            if( !isset($this->output['main_color_second']) ) {
                $this->output['main_color_second'] = '';
            }            

        }        

        public function redux_default_color() 
        {
            $this->default_color = aora_tbay_default_theme_primary_color();

            if( !isset($this->default_color['main_color_second']) ) {
                $this->default_color['main_color_second'] = '';
            }            
        }

        public function initSettings()
        {
            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            //Create output
            $this->redux_output();            

            //Create default color all skins
            $this->redux_default_color();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        public function setSections()
        {

            $output = $this->output;

            $default_color = $this->default_color;

            $sidebars = aora_sidebars_array();

            $columns = array( 
                '1' => esc_html__('1 Column', 'aora'),
                '2' => esc_html__('2 Columns', 'aora'),
                '3' => esc_html__('3 Columns', 'aora'),
                '4' => esc_html__('4 Columns', 'aora'),
                '5' => esc_html__('5 Columns', 'aora'),
                '6' => esc_html__('6 Columns', 'aora')
            );     

            $aspect_ratio = array( 
                '16_9' => '16:9',
                '4_3' => '4:3',
            );        

            $blog_image_size = array( 
                'thumbnail'         => esc_html__('Thumbnail', 'aora'),
                'medium'            => esc_html__('Medium', 'aora'),
                'large'             => esc_html__('Large', 'aora'),
                'full'              => esc_html__('Full', 'aora'),
            );      
          
            
            // General Settings Tab
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-settings',
                'title' => esc_html__('General', 'aora'),
                'fields' => array(
                    array(
                        'id'            => 'config_media',
                        'type'          => 'switch',
                        'title'         => esc_html__('Enable Config Image Size', 'aora'),
                        'subtitle'      => esc_html__('Config image size in WooCommerce and Media Setting', 'aora'),
                        'default'       => false
                    ),
                )
            );
            // Header
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-view-web',
                'title' => esc_html__('Header', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'header_type',
                        'type' => 'select',
                        'title' => esc_html__('Select Header Layout', 'aora'),
                        'options' => aora_tbay_get_header_layouts(),
                        'default' => 'header_default'
                    ),
                    array(
                        'id' => 'media-logo', 
                        'type' => 'media',
                        'title' => esc_html__('Upload Logo', 'aora'),
                        'required' => array('header_type','=','header_default'),
                        'subtitle' => esc_html__('Image File (.png or .gif)', 'aora'),
                    ),
                )
            );
            
            // Footer
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-border-bottom',
                'title' => esc_html__('Footer', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'footer_type',
                        'type' => 'select',
                        'title' => esc_html__('Select Footer Layout', 'aora'),
                        'options' => aora_tbay_get_footer_layouts(),
 						'default' => 'footer_default'
                    ),
                    array(
                        'id' => 'copyright_text',
                        'type' => 'editor',
                        'title' => esc_html__('Copyright Text', 'aora'),
                        'default' => esc_html__('<p>Copyright  &#64; 2021 Aora Designed by ThemBay. All Rights Reserved.</p>', 'aora'),
                        'required' => array('footer_type','=','footer_default')
                    ),
                    array(
                        'id' => 'back_to_top',
                        'type' => 'switch',
                        'title' => esc_html__('Enable "Back to Top" Button', 'aora'),
                        'default' => true,
                    ),
                )
            );



            // Mobile
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-smartphone-iphone',
                'title' => esc_html__('Mobile', 'aora'),
            );

            // Mobile Header settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Header', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'mobile_header',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Mobile Header', 'aora'),
                        'subtitle'  => esc_html__('Only off when use Header Elementor Pro on mobile ', 'aora'),
                        'default' => true
                    ),   
                    array(
                        'id' => 'mobile-logo',
                        'type' => 'media',
                        'required' => array('mobile_header','=', true),
                        'title' => esc_html__('Upload Logo', 'aora'),
                        'subtitle' => esc_html__('Image File (.png or .gif)', 'aora'),
                    ),
                    array(
                        'id'        => 'logo_img_width_mobile',
                        'type'      => 'slider',
                        'required' => array('mobile_header','=', true),
                        'title'     => esc_html__('Logo maximum width (px)', 'aora'),
                        "default"   => 69,
                        "min"       => 50,
                        "step"      => 1,
                        "max"       => 600,
                    ),
                    array(
                        'id'             => 'logo_mobile_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'required' => array('mobile_header','=', true),
                        'units'          => array('px'),
                        'units_extended' => 'false',
                        'title'          => esc_html__('Logo Padding', 'aora'),
                        'desc'           => esc_html__('Add more spacing around logo.', 'aora'),
                        'default'            => array(
                            'padding-top'     => '',
                            'padding-right'   => '',
                            'padding-bottom'  => '',
                            'padding-left'    => '',
                            'units'          => 'px',
                        ),
                    ),
                    array(
                        'id'        => 'always_display_logo',
                        'type'      => 'switch',
                        'required' => array('mobile_header','=', true),
                        'title'     => esc_html__('Always Display Logo', 'aora'),
                        'subtitle'      => esc_html__('Logo displays on all pages (page title is disabled)', 'aora'),
                        'default'   => false
                    ),                    
                    array(
                        'id'        => 'menu_mobile_all_page',
                        'type'      => 'switch',
                        'required'  => array('mobile_header','=', true),
                        'title'     => esc_html__('Always Display Menu', 'aora'),
                        'subtitle'  => esc_html__('Menu displays on all pages (Button Back is disabled)', 'aora'),
                        'default'   => false
                    ),
                    array(
                        'id'        => 'enable_menu_mobile_search',
                        'type'      => 'switch',
                        'required'  => array('mobile_header','=', true),
                        'title'     => esc_html__('Enable Form Search', 'aora'),
                        'default'   => true
                    ),
                    array(
                        'id'        => 'menu_mobile_search',
                        'type'      => 'switch',
                        'required'  => array('enable_menu_mobile_search','=', true),
                        'title'     => esc_html__('Always Display Search', 'aora'),
                        'subtitle'  => esc_html__('Search displays on all pages', 'aora'),
                        'class' =>   'tbay-search-mb-all-page',
                        'default'   => false
                    ),
                    
                    array(
                        'id'        => 'hidden_header_el_pro_mobile',
                        'type'      => 'switch',
                        'title'     => esc_html__('Hide Header Elementor Pro', 'aora'),
                        'subtitle'  => esc_html__('Hide Header Elementor Pro on mobile', 'aora'),
                        'default'   => true
                    ),
                )
            );

             // Mobile Footer settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Footer', 'aora'),
                'fields' => array(                
                    array(
                        'id' => 'mobile_footer',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Desktop Footer', 'aora'),
                        'default' => false
                    ),  
                    array(
                        'id' => 'mobile_footer_collapse',
                        'type' => 'switch',
                        'required' => array('mobile_footer','=', true),
                        'title' => esc_html__('Collapse widgets on mobile', 'aora'),
                        'subtitle'  => esc_html__( 'Widgets added to the footer will be collapsed by default and opened when you click on their titles.', 'aora' ),
                        'default' => false
                    ),   
                    array(
                        'id' => 'mobile_back_to_top',
                        'type' => 'switch',
                        'title' => esc_html__('Enable "Back to Top" Button', 'aora'),
                        'default' => false
                    ),                 
                    array(
                        'id' => 'mobile_footer_icon',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Mobile Footer', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id'          => 'mobile_footer_slides',
                        'type'        => 'slides',
                        'title'       => esc_html__( 'Config List Menu Icon', 'aora' ),
                        'subtitle' => esc_html__( 'Enter icon name of fonts: ', 'aora' ) . '<a href="//fontawesome.com/icons?m=free/" target="_blank">Awesome</a> , <a href="//fonts.thembay.com/simple-line-icons//" target="_blank">Simple Line Icons</a>, <a href="//fonts.thembay.com/material-design-iconic/" target="_blank">Material Design Iconic</a></br></br><b>'. esc_html__( 'List default URLs:', 'aora' ) . '</b></br></br><span class="des-label">'. esc_html__('Home page:', 'aora') .'</span><b class="df-url">{{home}}</b></br><span class="des-label">'. esc_html__('Shop page:', 'aora') .'</span><b class="df-url">{{shop}}</b></br><span class="des-label">'. esc_html__('My account page:', 'aora') .'</span><b class="df-url">{{account}}</b></br><span class="des-label">'. esc_html__('Cart page:', 'aora') .'</span><b class="df-url">{{cart}}</b></br><span class="des-label">'. esc_html__('Checkout page:', 'aora') .'</span><b class="df-url">{{checkout}}</b></br><span class="des-label">'. esc_html__('Wishlist page:', 'aora') .'</span><b class="df-url">{{wishlist}}</b></br></br>'. esc_html__( 'Watch video tutorial: ', 'aora' ) . '<a href="//youtu.be/d7b6dIzV-YI/" target="_blank">here</a>',
                        'class' =>   'tbay-redux-slides',
                        'show' => array(
                            'title' => true,
                            'description' => true,
                            'url' => true,
                        ),
                        'content_title' => esc_html__( 'Menu', 'aora' ),
                        'required' => array('mobile_footer_icon','=', true),
                        'placeholder'   => array(
                            'title'      => esc_html__( 'Title', 'aora' ),
                            'description' => esc_html__( 'Enter icon name', 'aora' ),
                            'url'       => esc_html__( 'Link', 'aora' ),
                        ),
                    ),
                )
            );     

            // Mobile Search settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Search', 'aora'),
                'fields' => array( 
                    array(
                        'id'=>'mobile_search_type',
                        'type' => 'button_set',
                        'title' => esc_html__('Search Result', 'aora'),
                        'options' => array(
                            'post' => esc_html__('Post', 'aora'), 
                            'product' => esc_html__('Product', 'aora')
                        ),
                        'default' => 'product'
                    ),
                    array(
                        'id' => 'mobile_autocomplete_search',
                        'type' => 'switch',
                        'title' => esc_html__('Auto-complete Search?', 'aora'),
                        'default' => 1
                    ),
                    array(
                        'id'       => 'mobile_search_placeholder',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Placeholder', 'aora' ),
                        'default'  => esc_html__( 'Search in 20.000+ products...', 'aora' ),
                    ),   
                    array(
                        'id' => 'mobile_enable_search_category',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Search in Categories', 'aora'),
                        'default' => true
                    ), 
                    array(
                        'id' => 'mobile_show_search_product_image',
                        'type' => 'switch',
                        'title' => esc_html__('Show Image of Search Result', 'aora'),
                        'required' => array('mobile_autocomplete_search', '=', '1'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'mobile_show_search_product_price',
                        'type' => 'switch',
                        'title' => esc_html__('Show Price of Search Result', 'aora'),
                        'required' => array(array('mobile_autocomplete_search', '=', '1'), array('mobile_search_type', '=', 'product')),
                        'default' => true
                    ),  
                    array(
                        'id' => 'mobile_search_min_chars',
                        'type'  => 'slider',
                        'required' => array('mobile_autocomplete_search', '=', '1'),
                        'title' => esc_html__('Search Min Characters', 'aora'),
                        'default' => 2,
                        'min'   => 1,
                        'step'  => 1,
                        'max'   => 6,
                    ), 
                    array(
                        'id' => 'mobile_search_max_number_results',
                        'type'  => 'slider',
                        'required' => array('mobile_autocomplete_search', '=', '1'),
                        'title' => esc_html__('Number of Search Results', 'aora'),
                        'desc'  => esc_html__( 'Max number of results show in Mobile', 'aora' ),
                        'default' => 5,
                        'min'   => 2,
                        'step'  => 1,
                        'max'   => 20,
                    ), 
                )
            );

            // Menu mobile settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Menu Mobile', 'aora'),
                'fields' => array(              
                    array(
                        'id'       => 'menu_mobile_select',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => esc_html__( 'Main Menu Mobile', 'aora' ),
                        'desc'     => esc_html__( 'Select the menu you want to display.', 'aora' ),
                        'default' => 69
                    ),
                    array(
                        'id' => 'enable_mmenu_langue', 
                        'type' => 'switch',
                        'title' => esc_html__('Enable Custom Language', 'aora'),
                        'desc'  => esc_html__( 'If you use WPML will appear here', 'aora' ),
                        'default' => true
                    ),   
                    array(
                        'id' => 'enable_mmenu_currency', 
                        'type' => 'switch',
                        'title' => esc_html__('Enable Currency', 'aora'),
                        'default' => true
                    ),  
                )
            );
            
          

            // Mobile Woocommerce settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Mobile WooCommerce', 'aora'),
                'fields' => array(                
                    array(
                        'id' => 'mobile_product_number',
                        'type' => 'image_select',
                        'title' => esc_html__('Product Column in Shop page', 'aora'),
                        'options' => array(
                            'one' => array(
                                'title' => esc_html__( 'One Column', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/mobile/one_column.jpg'
                            ),                            
                            'two' => array(
                                'title' => esc_html__( 'Two Columns', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/mobile/two_columns.jpg'
                            ),
                        ),
                        'default' => 'two'
                    ),  
					array(
                        'id' => 'enable_add_cart_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show "Add to Cart" Button', 'aora'),
                        'subtitle' => esc_html__('On Home and page Shop', 'aora'),
                        'default' => false
                    ),
                    array(
                        'id' => 'enable_wishlist_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show "Wishlist" Button', 'aora'),
                        'subtitle' => esc_html__('Enable or disable in Home and Shop page', 'aora'),
                        'default' => false
                    ),
                    array(
                        'id' => 'enable_one_name_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show Full Product Name', 'aora'),
                        'subtitle' => esc_html__('Enable or disable in Home and Shop page', 'aora'),
                        'default' => false
                    ),             
                    array(
                        'id' => 'mobile_form_cart_style',
                        'type' => 'select',   
                        'title' => esc_html__('Add To Cart Form Type', 'aora'),
                        'subtitle' => esc_html__('On Page Single Product', 'aora'),
                        'options' => array(
                            'default' => esc_html__('Default', 'aora'),
                            'popup' => esc_html__('Popup', 'aora') 
                        ),
                        'default' => 'popup'
                    ),
                    array(
                        'id' => 'enable_quantity_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show Quantity', 'aora'),
                        'subtitle' => esc_html__('On Page Single Product', 'aora'),
                        'required' => array('mobile_form_cart_style','=', 'default'),
                        'default' => false
                    ),     
                    array(
                        'id' => 'enable_tabs_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show Sidebar Tabs', 'aora'),
                        'subtitle' => esc_html__('On Page Single Product', 'aora'),
                        'default' => true
                    ),
                )
            );

            // Style
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-format-color-text',
                'title' => esc_html__('Style', 'aora'),
            ); 

            // Style
            $this->sections[] = array(
                'title' => esc_html__('Main', 'aora'),
                'subsection' => true,
                'fields' => array(
                    array(
                        'id'       => 'boby_bg',
                        'type'     => 'background',
                        'output'   => array( 'body' ),
                        'title'    => esc_html__( 'Body Background', 'aora' ),
                        'subtitle' => esc_html__( 'Body background with image, color, etc.', 'aora' ),
                    ),
                   
                    array (
                        'title' => esc_html__('Theme Main Color', 'aora'),
                        'id' => 'main_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => $default_color['main_color'],
                        'output' => $output['main_color'],
                    ), 

                    array (
                        'title' => esc_html__('Theme Color Second', 'aora'),
                        'id' => 'main_color_second',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => $default_color['main_color_second'],
                        'output' => $output['main_color_second'],
                    ),                    
                    
                )
            );

            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Typography', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'show_typography',
                        'type' => 'switch',
                        'title' => esc_html__('Edit Typography', 'aora'),
                        'default' => false
                    ),
                    array(
                        'title'    => esc_html__('Font Source', 'aora'),
                        'id'       => 'font_source',
                        'type'     => 'radio',
                        'required' => array('show_typography','=', true),
                        'options'  => array(
                            '1' => 'Standard + Google Webfonts',
                            '2' => 'Google Custom',
                            '3' => 'Custom Fonts'
                        ),
                        'default' => '1'
                    ),
                    array(
                        'id'=>'font_google_code',
                        'type' => 'text',
                        'title' => esc_html__('Google Link', 'aora'), 
                        'subtitle' => '<em>'.esc_html__('Paste the provided Google Code', 'aora').'</em>',
                        'default' => '',
                        'desc' => esc_html__('e.g.: https://fonts.googleapis.com/css?family=Open+Sans', 'aora'),
                        'required' => array('font_source','=','2')
                    ),

                    array (
                        'id' => 'main_custom_font_info',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3 style="margin: 0;">'. sprintf(
                                                                    '%1$s <a href="%2$s">%3$s</a>',
                                                                    esc_html__( 'Video guide custom font in ', 'aora' ),
                                                                    esc_url( 'https://www.youtube.com/watch?v=ljXAxueAQUc' ),
                                                                    esc_html__( 'here', 'aora' )
                                ) .'</h3>',
                        'required' => array('font_source','=','3')
                    ),

                    array (
                        'id' => 'main_font_info',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3 style="margin: 0;"> '.esc_html__('Main Font', 'aora').'</h3>',
                        'required' => array('show_typography','=', true),
                    ),                    

                    // Standard + Google Webfonts
                    array (
                        'title' => esc_html__('Font Face', 'aora'),
                        'id' => 'main_font',
                        'type' => 'typography',
                        'line-height' => false,
                        'text-align' => false,
                        'font-style' => false,
                        'font-weight' => false,
                        'all_styles'=> true,
                        'font-size' => false,
                        'color' => false,
                        'output' => $output['primary-font'],
                        'default' => array (
                            'font-family' => '',
                            'subsets' => '',
                        ),
                        'required' => array( 
                            array('font_source','=','1'), 
                            array('show_typography','=', true) 
                        )
                    ),
                    
                    // Google Custom                        
                    array (
                        'title' => esc_html__('Google Font Face', 'aora'),
                        'subtitle' => '<em>'.esc_html__('Enter your Google Font Name for the theme\'s Main Typography', 'aora').'</em>',
                        'desc' => esc_html__('e.g.: &#39;Open Sans&#39;, sans-serif', 'aora'),
                        'id' => 'main_google_font_face',
                        'type' => 'text',
                        'default' => '',
                        'required' => array( 
                            array('font_source','=','2'), 
                            array('show_typography','=', true) 
                        )
                    ),                    

                    // main Custom fonts                      
                    array (
                        'title' => esc_html__('Main custom Font Face', 'aora'),
                        'subtitle' => '<em>'.esc_html__('Enter your Custom Font Name for the theme\'s Main Typography', 'aora').'</em>',
                        'desc' => esc_html__('e.g.: &#39;Open Sans&#39;, sans-serif', 'aora'),
                        'id' => 'main_custom_font_face',
                        'type' => 'text',
                        'default' => '',
                        'required' => array( 
                            array('font_source','=','3'), 
                            array('show_typography','=', true) 
                        )
                    ),
                )
            );

            // Style
            $this->sections[] = array(
                'title' => esc_html__('Header Mobile', 'aora'),
                'subsection' => true,
                'fields' => array(
                    array(
                        'id'       => 'header_mobile_bg',
                        'type'     => 'background',
                        'output' => $output['header_mobile_bg'],
                        'title'    => esc_html__( 'Header Mobile Background', 'aora' ),
                    ),
                    array (
                        'title' => esc_html__('Header Color', 'aora'),
                        'id' => 'header_mobile_color',
                        'type' => 'color',
                        'transparent' => false,
                        'output' => $output['header_mobile_color'],
                    ),                    
                )
            );


            // WooCommerce
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-shopping-cart',
                'title' => esc_html__('WooCommerce', 'aora'),
                'fields' => array(       
                    array(
                        'title'    => esc_html__('Label Sale Format', 'aora'),
                        'id'       => 'sale_tags',
                        'type'     => 'radio',
                        'options'  => array( 
                            'Sale!' => esc_html__('Sale!' ,'aora'),
                            'Save {percent-diff}%' => esc_html__('Save {percent-diff}% (e.g "Save 50%")' ,'aora'),
                            'Save {symbol}{price-diff}' => esc_html__('Save {symbol}{price-diff} (e.g "Save $50")' ,'aora'),
                            'custom' => esc_html__('Custom Format (e.g -50%, -$50)' ,'aora')
                        ),
                        'default' => 'custom'
                    ),
                    array(
                        'id'        => 'sale_tag_custom',
                        'type'      => 'text',
                        'title'     => esc_html__( 'Custom Format', 'aora' ),
                        'desc'      => esc_html__('{price-diff} inserts the dollar amount off.', 'aora'). '</br>'.
                                       esc_html__('{percent-diff} inserts the percent reduction (rounded).', 'aora'). '</br>'.
                                       esc_html__('{symbol} inserts the Default currency symbol.', 'aora'), 
                        'required'  => array('sale_tags','=', 'custom'),
                        'default'   => '-{percent-diff}%'
                    ), 
                    array(
                        'id' => 'enable_label_featured',
                        'type' => 'switch',
                        'title' => esc_html__('Enable "Featured" Label', 'aora'),
                        'default' => true
                    ),   
                    array(
                        'id'        => 'custom_label_featured',
                        'type'      => 'text',
                        'title'     => esc_html__( '"Featured Label" Custom Text', 'aora' ),
                        'required'  => array('enable_label_featured','=', true),
                        'default'   => esc_html__('Featured', 'aora')
                    ),
                    
                    array(
                        'id' => 'enable_brand',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Brand Name', 'aora'),
                        'subtitle' => esc_html__('Enable/Disable brand name on HomePage and Shop Page', 'aora'),
                        'default' => false
                    ),
                    array(
                        'id' => 'enable_text_time_coutdown',
                        'type' => 'switch', 
                        'title' => esc_html__('Enable the text of Time Countdown', 'aora'),
                        'default' => false
                    ),
                    
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),            
                    array(
                        'id' => 'product_display_image_mode',
                        'type' => 'image_select',
                        'title' => esc_html__('Product Image Display Mode', 'aora'),
                        'options' => array(
                            'one' => array(
                                'title' => esc_html__( 'Single Image', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/image_mode/single-image.png'
                            ),                                  
                            'two' => array(
                                'title' => esc_html__( 'Double Images (Hover)', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/image_mode/display-hover.gif'
                            ),                                                                         
                            'slider' => array(
                                'title' => esc_html__( 'Images (carousel)', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/image_mode/display-carousel.gif'
                            ),                                                      
                        ),
                        'default' => 'slider'
                    ),
                    array(
                        'id' => 'enable_quickview',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Quick View', 'aora'),
                        'default' => 1
                    ),                    
                    array(
                        'id' => 'enable_woocommerce_catalog_mode',
                        'type' => 'switch',
                        'title' => esc_html__('Show WooCommerce Catalog Mode', 'aora'),
                        'default' => false
                    ),    
                    array(
                        'id' => 'enable_woocommerce_quantity_mode',
                        'type' => 'switch',
                        'title' => esc_html__('Enable WooCommerce Quantity Mode', 'aora'),
                        'subtitle' => esc_html__('Enable/Disable show quantity on Home Page and Shop Page', 'aora'),
                        'default' => false
                    ),                   
                    array(
                        'id' => 'ajax_update_quantity',
                        'type' => 'switch',
                        'title' => esc_html__('Quantity Ajax Auto-update', 'aora'),
                        'subtitle' => esc_html__('Enable/Disable quantity ajax auto-update on page Cart', 'aora'),
                        'default' => true
                    ),   
					array(
                        'id' => 'enable_variation_swatch',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Product Variation Swatch', 'aora'),
                        'subtitle' => esc_html__('Enable/Disable Product Variation Swatch on HomePage and Shop page', 'aora'),
                        'default' => true
                    ), 
                    array(
                        'id' => 'variation_swatch',
                        'type' => 'select',
                        'title' => esc_html__('Product Attribute', 'aora'),
                        'options' => aora_tbay_get_variation_swatchs(),
                        'default' => ''
                    ),  					                
                )
            );

            // woocommerce Breadcrumb settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Mini Cart', 'aora'),
                'fields' => array(
                     array(
                        'id' => 'woo_mini_cart_position',
                        'type' => 'select', 
                        'title' => esc_html__('Mini-Cart Position', 'aora'),
                        'options' => array( 
                            'left'       => esc_html__('Left', 'aora'),
                            'right'      => esc_html__('Right', 'aora'),
                            'popup'      => esc_html__('Popup', 'aora'),
                            'no-popup'   => esc_html__('None Popup', 'aora')
                        ),
                        'default' => 'popup'
                    ), 
                )
            ); 

            // woocommerce Breadcrumb settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Breadcrumb', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'show_product_breadcrumb',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Breadcrumb', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id' => 'product_breadcrumb_layout',
                        'type' => 'image_select',
                        'class'     => 'image-two',
                        'compiler' => true,
                        'title' => esc_html__('Breadcrumb Layout', 'aora'),
                        'required' => array('show_product_breadcrumb','=',1),
                        'options' => array(                          
                            'image' => array(
                                'title' => esc_html__( 'Background Image', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/breadcrumbs/image.jpg'
                            ),
                            'color' => array(
                                'title' => esc_html__( 'Background color', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/breadcrumbs/color.jpg'
                            ),
                            'text'=> array(
                                'title' => esc_html__( 'Text Only', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/breadcrumbs/text_only.jpg'
                            ),
                        ),
                        'default' => 'color'
                    ),
                    array (
                        'title' => esc_html__('Breadcrumb Background Color', 'aora'),
                        'subtitle' => '<em>'.esc_html__('The Breadcrumb background color of the site.', 'aora').'</em>',
                        'id' => 'woo_breadcrumb_color',
                        'required' => array('product_breadcrumb_layout','=',array('default','color')),
                        'type' => 'color',
                        'default' => '#f4f9fc',
                        'transparent' => false,
                    ),
                    array( 
                        'id' => 'woo_breadcrumb_image',
                        'type' => 'media',
                        'title' => esc_html__('Breadcrumb Background', 'aora'),
                        'subtitle' => esc_html__('Upload a .jpg or .png image that will be your Breadcrumb.', 'aora'),
                        'required' => array('product_breadcrumb_layout','=','image'),
                        'default'  => array( 
                            'url'=> AORA_IMAGES .'/breadcrumbs-woo.jpg'
                        ),
                    ),
                    array(
                        'id' => 'enable_previous_page_woo',
                        'type' => 'switch',
                        'required' => array('show_product_breadcrumb','=',1),
                        'title' => esc_html__('Previous page', 'aora'),
                        'default' => true
                    ), 
                )
            ); 

            // WooCommerce Archive settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Shop', 'aora'),
                'fields' => array(       
                    array(
                        'id' => 'product_archive_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Shop Layout', 'aora'),
                        'options' => array(
                            'shop-left' => array(
                                'title' => esc_html__( 'Left Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/product_archives/shop_left_sidebar.jpg'
                            ),                                  
                            'shop-right' => array(
                                'title' => esc_html__( 'Right Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/product_archives/shop_right_sidebar.jpg'
                            ),                                                                         
                            'full-width' => array(
                                'title' => esc_html__( 'No Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/product_archives/shop_no_sidebar.jpg'
                            ),                                                      
                        ),
                        'default' => 'shop-left'
                    ),
                    array(
                        'id' => 'product_archive_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Archive Sidebar', 'aora'),
                        'options' => $sidebars,
                        'default' => 'product-archive'
                    ),   
                    array(
                        'id' => 'enable_display_mode',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Products Display Mode', 'aora'),
                        'subtitle' => esc_html__('Enable/Disable Display Mode', 'aora'),
                        'default' => true
                    ),   
                    array(
                        'id' => 'product_display_mode',
                        'type' => 'button_set',
                        'title' => esc_html__('Products Display Mode', 'aora'),
                        'required' => array('enable_display_mode','=',1),
                        'options' => array(
                            'grid' => esc_html__('Grid', 'aora'), 
                            'list' => esc_html__('List', 'aora')
                        ),
                        'default' => 'grid'
                    ),                                
                    array(
                        'id' => 'title_product_archives',
                        'type' => 'switch',
                        'title' => esc_html__('Show Title of Categories', 'aora'),
                        'default' => false 
                    ),                       
                    array(
                        'id' => 'pro_des_image_product_archives',
                        'type' => 'switch',
                        'title' => esc_html__('Show Description, Image of Categories', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id' => 'number_products_per_page',
                        'type' => 'slider',
                        'title' => esc_html__('Number of Products Per Page', 'aora'),
                        'default' => 15,
                        'min' => 1,
                        'step' => 1,
                        'max' => 100,
                    ),
                    array(
                        'id' => 'product_columns',
                        'type' => 'select',
                        'title' => esc_html__('Product Columns', 'aora'),
                        'options' => $columns,
                        'default' => 4
                    ),
                    array(
                        'id' => 'product_pagination_style',
                        'type' => 'select',
                        'title' => esc_html__('Product Pagination Style', 'aora'),
                        'options' => array( 
                            'number' => esc_html__('Pagination Number', 'aora'),
                            'loadmore'  => esc_html__('Load More Button', 'aora'),  
                        ),
                        'default' => 'number' 
                    ),            
                )
            );
            // Product Page
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Single Product', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'product_single_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Select Single Product Layout', 'aora'),
                        'options' => array(
                            'vertical' => array(
                                'title' => esc_html__('Image Vertical', 'aora'),
                                'img' => AORA_ASSETS_IMAGES . '/product_single/verical_thumbnail.jpg'
                            ),                             
                            'horizontal' => array(
                                'title' => esc_html__('Image Horizontal', 'aora'),
                                'img' => AORA_ASSETS_IMAGES . '/product_single/horizontal_thumbnail.jpg'
                            ),                                                                                  
                            'left-main' => array(
                                'title' => esc_html__('Left - Main Sidebar', 'aora'),
                                'img' => AORA_ASSETS_IMAGES . '/product_single/left_main_sidebar.jpg'
                            ),
                            'main-right' => array(
                                'title' => esc_html__('Main - Right Sidebar', 'aora'),
                                'img' => AORA_ASSETS_IMAGES . '/product_single/main_right_sidebar.jpg'
                            ),
                        ),
                        'default' => 'horizontal'
                    ),                   
                    array(
                        'id' => 'product_single_sidebar',
                        'type' => 'select',
                        'required' => array('product_single_layout','=',array('left-main','main-right')),
                        'title' => esc_html__('Single Product Sidebar', 'aora'),
                        'options' => $sidebars,
                        'default' => 'product-single'
                    ),
                )
            );


            // Product Page
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Single Product Advanced Options', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'enable_total_sales',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Total Sales', 'aora'),
                        'default' => true
                    ),                     
                    array(
                        'id' => 'enable_buy_now',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Buy Now', 'aora'),
                        'default' => false
                    ),
                    array( 
                        'id' => 'redirect_buy_now',
                        'required' => array('enable_buy_now','=',true),
                        'type' => 'button_set',
                        'title' => esc_html__('Redirect to page after Buy Now', 'aora'),
                        'options' => array( 
                                'cart'          => 'Page Cart',
                                'checkout'      => 'Page CheckOut',
                        ),
                        'default' => 'cart'
                    ),
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),   
                    array(
                        'id' => 'position_single_tabs',
                        'type' => 'button_set',
                        'title' => esc_html__('Position Tab', 'aora'),
                        'options' => array(
                                'normal'                       => 'Normal',
                                'after_add_to_cart'          => 'After Add To Cart',
                        ),
                        'default' => 'after_add_to_cart'
                    ),
                    array(
                        'id' => 'style_single_tabs_style',
                        'type' => 'button_set',
                        'title' => esc_html__('Tab Mode', 'aora'),
                        'options' => array(
                                'fulltext'          => 'Full Text',
                                'tabs'          => 'Tabs',
                                'accordion'        => 'Accordion',
                                'sidebar'        => 'Sidebar',
                        ),
                        'default' => 'sidebar'
                    ),
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),
                    array(
                        'id' => 'enable_size_guide',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Size Guide', 'aora'),
                        'default' => 1
                    ),
                    array(
                        'id'       => 'size_guide_title',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Size Guide Title', 'aora' ),
                        'required' => array('enable_size_guide','=', true),
                        'default'  => esc_html__( 'Size chart', 'aora' ),
                    ),    
                    array(
                        'id'       => 'size_guide_icon',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Size Guide Icon', 'aora' ),
                        'required' => array('enable_size_guide','=', true),
                        'desc'       => esc_html__( 'Enter icon name of fonts: ', 'aora' ) . '<a href="//fontawesome.com/v4.7.0/" target="_blank">Awesome</a> , <a href="//fonts.thembay.com/simple-line-icons//" target="_blank">simplelineicons</a>, <a href="//fonts.thembay.com/linearicons/" target="_blank">linearicons</a>',
                        'default'  => 'tb-icon tb-icon-angle-right',
                    ),                
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),       
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),    
                    array(
                        'id' => 'enable_sticky_menu_bar',
                        'type' => 'switch',
                        'title' => esc_html__('Sticky Menu Bar', 'aora'),
                        'subtitle' => esc_html__('Enable/disable Sticky Menu Bar', 'aora'),
                        'default' => false
                    ),
                    array(
                        'id' => 'enable_zoom_image',
                        'type' => 'switch',
                        'title' => esc_html__('Zoom inner image', 'aora'),
                        'subtitle' => esc_html__('Enable/disable Zoom inner Image', 'aora'),
                        'default' => false
                    ),    
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide', 
                        'type' => 'divide'
                    ),                 
                    array(
                        'id' => 'video_aspect_ratio',
                        'type' => 'select',
                        'title' => esc_html__('Featured Video Aspect Ratio', 'aora'),
                        'subtitle' => esc_html__('Choose the aspect ratio for your video', 'aora'),
                        'options' => $aspect_ratio,
                        'default' => '16_9'
                    ),     
                    array(
                        'id'      => 'video_position',
                        'title'    => esc_html__( 'Featured Video Position', 'aora' ),
                        'type'    => 'select',
                        'default' => 'last',
                        'options' => array(
                            'last' => esc_html__( 'The last product gallery', 'aora' ),
                            'first' => esc_html__( 'The first product gallery', 'aora' ),
                        ),
                    ),  
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),              
                    array(
                        'id' => 'enable_product_social_share',
                        'type' => 'switch',
                        'title' => esc_html__('Social Share', 'aora'),
                        'subtitle' => esc_html__('Enable/disable Social Share', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id' => 'enable_product_review_tab',
                        'type' => 'switch',
                        'title' => esc_html__('Product Review Tab', 'aora'),
                        'subtitle' => esc_html__('Enable/disable Review Tab', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id' => 'enable_product_releated',
                        'type' => 'switch',
                        'title' => esc_html__('Products Releated', 'aora'),
                        'subtitle' => esc_html__('Enable/disable Products Releated', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id' => 'enable_product_upsells',
                        'type' => 'switch',
                        'title' => esc_html__('Products upsells', 'aora'),
                        'subtitle' => esc_html__('Enable/disable Products upsells', 'aora'),
                        'default' => true
                    ),                    
                    array(
                        'id' => 'enable_product_countdown',
                        'type' => 'switch',
                        'title' => esc_html__('Display Countdown time ', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id' => 'number_product_thumbnail',
                        'type'  => 'slider',
                        'title' => esc_html__('Number Images Thumbnail to show', 'aora'),
                        'default' => 4,
                        'min'   => 2,
                        'step'  => 1,
                        'max'   => 5,
                    ),  
                    array(
                        'id' => 'number_product_releated',
                        'type' => 'slider',
                        'title' => esc_html__('Number of related products to show', 'aora'),
                        'default' => 8,
                        'min' => 1,
                        'step' => 1,
                        'max' => 20,
                    ),                    
                    array(
                        'id' => 'releated_product_columns',
                        'type' => 'select',
                        'title' => esc_html__('Releated Products Columns', 'aora'),
                        'options' => $columns,
                        'default' => 5
                    ),
                    array(
                        'id'       => 'html_before_add_to_cart_btn',
                        'type'     => 'textarea',
                        'title'    => esc_html__( 'HTML before Add To Cart button (Global)', 'aora' ),
                        'desc'     => esc_html__( 'Enter HTML and shortcodes that will show before Add to cart selections.', 'aora' ),
                    ),
                    array(
                        'id'       => 'html_after_add_to_cart_btn',
                        'type'     => 'textarea',
                        'title'    => esc_html__( 'HTML after Add To Cart button (Global)', 'aora' ),
                        'desc'     => esc_html__( 'Enter HTML and shortcodes that will show after Add to cart button.', 'aora' ),
                    ),
                )

            );
          
            // woocommerce Menu Account settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Account', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'show_woocommerce_password_strength',
                        'type' => 'switch',
                        'title' => esc_html__('Show Password Strength Meter', 'aora'),
                        'default' => true
                    ),  
                )
            );

            // woocommerce Multi-vendor settings
            $this->sections[] = $this->multi_vendor_sections( $columns );

            // Blog settings
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-border-color',
                'title' => esc_html__('Blog', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'show_blog_breadcrumb',
                        'type' => 'switch',
                        'title' => esc_html__('Breadcrumb', 'aora'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'blog_breadcrumb_layout',
                        'type' => 'image_select',
                        'class'     => 'image-two',
                        'compiler' => true,
                        'title' => esc_html__('Select Breadcrumb Blog Layout', 'aora'),
                        'required' => array('show_blog_breadcrumb','=',1),
                        'options' => array(                        
                            'image' => array(
                                'title' => esc_html__( 'Background Image', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/breadcrumbs/image.jpg'
                            ),
                            'color' => array(
                                'title' => esc_html__( 'Background color', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/breadcrumbs/color.jpg'
                            ),
                            'text'=> array(
                                'title' => esc_html__( 'Text Only', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/breadcrumbs/text_only.jpg'
                            ),
                        ),
                        'default' => 'color'
                    ),
                    array (
                        'title' => esc_html__('Breadcrumb Background Color', 'aora'),
                        'id' => 'blog_breadcrumb_color',
                        'type' => 'color',
                        'default' => '#fafafa',
                        'transparent' => false,
                        'required' => array('blog_breadcrumb_layout','=',array('default','color')),
                    ),
                    array(
                        'id' => 'blog_breadcrumb_image',
                        'type' => 'media',
                        'title' => esc_html__('Breadcrumb Background Image', 'aora'),
                        'subtitle' => esc_html__('Image File (.png or .jpg)', 'aora'),
                        'default'  => array(
                            'url'=> AORA_IMAGES .'/breadcrumbs-blog.jpg'
                        ),
                        'required' => array('blog_breadcrumb_layout','=','image'),
                    ),
                    array(
                        'id' => 'enable_previous_page_post',
                        'type' => 'switch',
                        'title' => esc_html__('Previous page', 'aora'),
                        'required' => array('show_blog_breadcrumb','=',1),
                        'subtitle' => esc_html__('Enable Previous Page Button', 'aora'),
                        'default' => true
                    ), 
                )
            );

            // Archive Blogs settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Blog Article', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'blog_archive_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Blog Layout', 'aora'),
                        'options' => array(
                            'main' => array(
                                'title' => esc_html__( 'Articles', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/blog_archives/blog_no_sidebar.jpg'
                            ),
                            'left-main' => array(
                                'title' => esc_html__( 'Articles - Left Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/blog_archives/blog_left_sidebar.jpg'
                            ),
                            'main-right' => array(
                                'title' => esc_html__( 'Articles - Right Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/blog_archives/blog_right_sidebar.jpg'
                            ),                   
                        ),
                        'default' => 'main-right'
                    ),
                    array(
                        'id' => 'blog_archive_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Blog Archive Sidebar', 'aora'),
                        'options' => $sidebars,
                        'default' => 'blog-archive-sidebar',
                        'required' => array('blog_archive_layout','!=','main'),
                    ),
                    array(
                        'id' => 'blog_columns',
                        'type' => 'select',
                        'title' => esc_html__('Post Column', 'aora'),
                        'options' => $columns,
                        'default' => '2'
                    ),
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),   
                    array(
                        'id' => 'layout_blog',
                        'type' => 'select',
                        'title' => esc_html__('Layout Blog', 'aora'),
                        'options' => array(
                            'post-style-1' =>  esc_html__( 'Post Style 1', 'aora' ),
                            'post-style-2' =>  esc_html__( 'Post Style 2', 'aora' ),
                            'post-style-3' =>  esc_html__( 'Post Style 3', 'aora' ),
                        ),
                        'default' => 'post-style-1'
                    ),                 
                    array(
                        'id' => 'blog_image_sizes',
                        'type' => 'select',
                        'title' => esc_html__('Post Image Size', 'aora'),
                        'options' => $blog_image_size,
                        'default' => 'full'
                    ),                 
                    array(
                        'id' => 'enable_date',
                        'type' => 'switch',
                        'title' => esc_html__('Date', 'aora'),
                        'default' => true
                    ),                    
                    array(
                        'id' => 'enable_author',
                        'type' => 'switch',
                        'title' => esc_html__('Author', 'aora'),
                        'default' => false
                    ),                        
                    array(
                        'id' => 'enable_categories',
                        'type' => 'switch',
                        'title' => esc_html__('Categories', 'aora'),
                        'default' => true
                    ),                                            
                    array(
                        'id' => 'enable_comment',
                        'type' => 'switch',
                        'title' => esc_html__('Comment', 'aora'),
                        'default' => true
                    ),                    
                    array(
                        'id' => 'enable_comment_text',
                        'type' => 'switch',
                        'title' => esc_html__('Comment Text', 'aora'),
                        'required' => array('enable_comment', '=', true),
                        'default' => false
                    ),                    
                    array(
                        'id' => 'enable_short_descriptions',
                        'type' => 'switch',
                        'title' => esc_html__('Short descriptions', 'aora'),
                        'default' => false
                    ),                    
                    array(
                        'id' => 'enable_readmore',
                        'type' => 'switch',
                        'title' => esc_html__('Read More', 'aora'),
                        'default' => false
                    ),
                    array(
                        'id' => 'text_readmore',
                        'type' => 'text',
                        'title' => esc_html__('Button "Read more" Custom Text', 'aora'),
                        'required' => array('enable_readmore', '=', true),
                        'default' => 'Continue Reading',
                    ),
                )
            );

            // Single Blogs settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Single Blog', 'aora'),
                'fields' => array(
                    
                    array(
                        'id' => 'blog_single_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Blog Single Layout', 'aora'),
                        'options' => array(
                            'main' => array(
                                'title' => esc_html__( 'Main Only', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/single _post/main.jpg'
                            ),
                            'left-main' => array(
                                'title' => esc_html__( 'Left - Main Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/single _post/left_sidebar.jpg'
                            ),
                            'main-right' => array(
                                'title' => esc_html__( 'Main - Right Sidebar', 'aora' ),
                                'img' => AORA_ASSETS_IMAGES . '/single _post/right_sidebar.jpg'
                            ),
                        ),
                        'default' => 'main-right'
                    ),
                    array(
                        'id' => 'blog_single_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Single Blog Sidebar', 'aora'),
                        'options'   => $sidebars,
                        'default'   => 'blog-single-sidebar',
                        'required' => array('blog_single_layout','!=','main'),
                    ),
                    array(
                        'id' => 'show_blog_social_share',
                        'type' => 'switch',
                        'title' => esc_html__('Show Social Share', 'aora'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_blog_releated',
                        'type' => 'switch',
                        'title' => esc_html__('Show Related Posts', 'aora'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'number_blog_releated',
                        'type' => 'slider',
                        'title' => esc_html__('Number of Related Posts', 'aora'),
                        'required' => array('show_blog_releated', '=', '1'),
                        'default' => 4,
                        'min' => 1,
                        'step' => 1,
                        'max' => 20,
                    ),
                    array(
                        'id' => 'releated_blog_columns',
                        'type' => 'select',
                        'title' => esc_html__('Columns of Related Posts', 'aora'),
                        'required' => array('show_blog_releated', '=', '1'),
                        'options' => $columns,
                        'default' => 2
                    ),
                )
            );

            // Social Media
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-share',
                'title' => esc_html__('Social Share', 'aora'),
                'fields' => array(
                    array(
                        'id' => 'enable_code_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Code Share', 'aora'),
                        'default' => true
                    ),
                    array(
                        'id'       => 'select_share_type',
                        'type'     => 'button_set',
                        'title'    => esc_html__( 'Please select a sharing type', 'aora' ),
                        'required'  => array('enable_code_share','=', true),
                        'options'  => array(
                            'custom' => 'TB Share',
                            'addthis' => 'Add This',
                        ),
                        'default'  => 'addthis'
                    ),
                    array(
                        'id'        =>'code_share',
                        'type'      => 'textarea',
                        'required'  => array('select_share_type','=', 'addthis'),
                        'title'     => esc_html__('"Addthis" Your Code', 'aora'), 
                        'desc'      => esc_html__('You get your code share in https://www.addthis.com', 'aora'),
                        'validate'  => 'html_custom',
                        'default'   => '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-59f2a47d2f1aaba2"></script>'
                    ),
                    array(
                        'id'       => 'sortable_sharing',
                        'type'     => 'sortable',
                        'mode'     => 'checkbox',
                        'title'    => esc_html__( 'Sortable Sharing', 'aora' ),
                        'required'  => array('select_share_type','=', 'custom'),
                        'options'  => array(
                            'facebook'      => 'Facebook',
                            'twitter'       => 'Twitter',
                            'linkedin'      => 'Linkedin',
                            'pinterest'     => 'Pinterest',
                            'whatsapp'      => 'Whatsapp',
                            'email'         => 'Email',
                        ),
                        'default'   => array(
                            'facebook'  => true,
                            'twitter'   => true,
                            'linkedin'  => true,
                            'pinterest' => false,
                            'whatsapp'  => false,
                            'email'     => true,
                        )
                    ),
                )
            );

            // Performance
            $this->sections[] = array(
                'icon' => 'el-icon-cog',
                'title' => esc_html__('Performance', 'aora'),
            );  

            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Performance', 'aora'),
                'fields' => array(
                    array (
                        'id'       => 'minified_js',
                        'type'     => 'switch',
                        'title'    => esc_html__('Include minified JS', 'aora'),
                        'subtitle' => esc_html__('Minified version of functions.js and device.js file will be loaded', 'aora'),
                        'default' => true
                    ),
                )
            );


            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Preloader', 'aora'),
                'fields' => array(
                    array(
                        'id'        => 'preload',
                        'type'      => 'switch',
                        'title'     => esc_html__('Preload Website', 'aora'),
                        'default'   => false
                    ),
                    array(
                        'id' => 'select_preloader',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Select Preloader', 'aora'),
                        'subtitle' => esc_html__('Choose a Preloader for your website.', 'aora'),
                        'required'  => array('preload','=',true),
                        'options' => array(
                            'loader1' => array(
                                'title' => esc_html__( 'Loader 1', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/loader1.png'
                            ),         
                            'loader2' => array(
                                'title' => esc_html__( 'Loader 2', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/loader2.png'
                            ),              
                            'loader3' => array(
                                'title' => esc_html__( 'Loader 3', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/loader3.png'
                            ),         
                            'loader4' => array(
                                'title' => esc_html__( 'Loader 4', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/loader4.png'
                            ),          
                            'loader5' => array(
                                'title' => esc_html__( 'Loader 5', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/loader5.png'
                            ),         
                            'loader6' => array(
                                'title' => esc_html__( 'Loader 6', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/loader6.png'
                            ),                        
                            'custom_image' => array(
                                'title' => esc_html__( 'Custom image', 'aora' ),
                                'img'   => AORA_ASSETS_IMAGES . '/preloader/custom_image.png'
                            ),         
                        ),
                        'default' => 'loader1'
                    ),
                    array(
                        'id' => 'media-preloader',
                        'type' => 'media',
                        'required' => array('select_preloader','=', 'custom_image'),
                        'title' => esc_html__('Upload preloader image', 'aora'),
                        'subtitle' => esc_html__('Image File (.gif)', 'aora'),
                        'desc' =>   sprintf( wp_kses( __('You can download some the Gif images <a target="_blank" href="%1$s">here</a>.', 'aora' ),  array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), 'https://loading.io/' ), 
                    ),
                )
            );            

            // Custom Code
            $this->sections[] = array(
                'icon' => 'zmdi zmdi-code-setting',
                'title' => esc_html__('Custom CSS/JS', 'aora'),
            );            

            // Css Custom Code
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Custom CSS', 'aora'),
                'fields' => array(
                    array (
                        'title' => esc_html__('Global Custom CSS', 'aora'),
                        'id' => 'custom_css',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for desktop', 'aora'),
                        'id' => 'css_desktop',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for tablet', 'aora'),
                        'id' => 'css_tablet',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for mobile landscape', 'aora'),
                        'id' => 'css_wide_mobile',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for mobile', 'aora'),
                        'id' => 'css_mobile',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                )
            );

            // Js Custom Code
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Custom Js', 'aora'),
                'fields' => array(
                    array (
                        'title' => esc_html__('Header JavaScript Code', 'aora'),
                        'subtitle' => '<em>'.esc_html__('Paste your custom JS code here. The code will be added to the header of your site.', 'aora').'<em>',
                        'id' => 'header_js',
                        'type' => 'ace_editor',
                        'mode' => 'javascript',
                    ),
                    
                    array (
                        'title' => esc_html__('Footer JavaScript Code', 'aora'),
                        'subtitle' => '<em>'.esc_html__('Here is the place to paste your Google Analytics code or any other JS code you might want to add to be loaded in the footer of your website.', 'aora').'<em>',
                        'id' => 'footer_js',
                        'type' => 'ace_editor',
                        'mode' => 'javascript',
                    ),
                )
            );



            $this->sections[] = array(
                'title' => esc_html__('Import / Export', 'aora'),
                'desc' => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'aora'),
                'icon' => 'zmdi zmdi-download',
                'fields' => array(
                    array(
                        'id' => 'opt-import-export',
                        'type' => 'import_export',
                        'title' => 'Import Export',
                        'subtitle' => 'Save and restore your Redux options',
                        'full_width' => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'type' => 'divide',
            );
        }

        public function multi_vendor_fields( $columns ) {
            $wcmp_array = $fields_dokan = array();

            if( class_exists('WCMp') ) {
                $wcmp_array = array(
                    'id'        => 'show_vendor_name_wcmp',
                    'type'      => 'info',
                    'title'     => esc_html__('Enable Vendor Name Only WCMP Vendor', 'aora'),
                    'subtitle'  => sprintf(__('Go to the <a href="%s" target="_blank">Setting</a> Enable "Sold by" for WCMP Vendor', 'aora'), admin_url('admin.php?page=wcmp-setting-admin')),
                );
            }

            $fields = array(
                array(
                    'id' => 'show_vendor_name',
                    'type' => 'switch',
                    'title' => esc_html__('Enable Vendor Name', 'aora'),
                    'subtitle' => esc_html__('Enable/Disable Vendor Name on HomePage and Shop page only works for Dokan, WCMP Vendor', 'aora'),
                    'default' => true
                ),  
                $wcmp_array
            );


            if( class_exists('WeDevs_Dokan') ) {
                $fields_dokan = array(
                    array(
                        'id'   => 'divide_vendor_1',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),
                    array(
                        'id' => 'show_info_vendor_tab',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Tab Info Vendor Dokan', 'aora'),
                        'subtitle' => esc_html__('Enable/Disable tab Info Vendor on Product Detail Dokan', 'aora'),
                        'default' => true
                    ), 
                    array(
                        'id'        => 'show_seller_tab',
                        'type'      => 'info',
                        'title'     => esc_html__('Enable/Disable Tab Products Seller', 'aora'),
                        'subtitle'  => sprintf(__('Go to the <a href="%s" target="_blank">Setting</a> of each Seller to Enable/Disable this tab of Dokan Vendor.', 'aora'), home_url('dashboard/settings/store/')),
                    ),
                    array(
                        'id' => 'seller_tab_per_page',
                        'type' => 'slider',
                        'title' => esc_html__('Dokan Number of Products Seller Tab', 'aora'),
                        'default' => 4,
                        'min' => 1,
                        'step' => 1,
                        'max' => 10,
                    ),
                    array(
                        'id' => 'seller_tab_columns',
                        'type' => 'select',
                        'title' => esc_html__('Dokan Product Columns Seller Tab', 'aora'),
                        'options' => $columns,
                        'default' => 4
                    ),
                );
            }
            

            $fields = array_merge( $fields, $fields_dokan );

            return $fields;
        }

        public function multi_vendor_sections( $columns ) {
            if( !aora_woo_is_active_vendor() ) return;

            
            $output_array = array(
                'subsection' => true,
                'title' => esc_html__('Multi-vendor', 'aora'),
                'fields' => $this->multi_vendor_fields( $columns )
            );
            
            
            return $output_array;
        }
		
		
		
		
        /**
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         * */
		 
		 /**
     * Custom function for the callback validation referenced above
     * */
		
		 
        public function setArguments()
        {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name' => 'aora_tbay_theme_options',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name' => $theme->get('Name'),
                // Name that appears at the top of your panel
                'display_version' => esc_html__('Version ', 'aora').$theme->get('Version'),
                'ajax_save'     => true,
                // Version that appears at the top of your panel
                'menu_type' => 'menu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu' => true,
                // Show the sections below the admin menu item or not
                'menu_title' => esc_html__('Aora Options', 'aora'),
                'page_title' => esc_html__('Aora Options', 'aora'),

                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography' => false,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar' => true,
                // Show the panel pages on the admin bar
                'admin_bar_icon' => 'dashicons-portfolio',
                // Choose an icon for the admin bar menu
                'admin_bar_priority' => 50,
                // Choose an priority for the admin bar menu
                'global_variable' => 'aora_options',
                // Set a different name for your global variable other than the opt_name
                'dev_mode' => false,
				'forced_dev_mode_off' => false,
                // Show the time the page took to load, etc
                'update_notice' => true,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer' => true,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority' => 61,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent' => 'themes.php',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions' => 'manage_options',
                // Specify a custom URL to an icon
                'last_tab' => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon' => 'icon-themes',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug' => '_options',
                // Page slug used to denote the panel
                'save_defaults' => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show' => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark' => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time' => 60 * MINUTE_IN_SECONDS,
                'output' => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag' => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database' => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info' => false,
                // REMOVE

                // HINTS
                'hints' => array(
                    'icon' => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color' => 'lightgray',
                    'icon_size' => 'normal',
                    'tip_style' => array(
                        'color' => 'light',
                        'shadow' => true,
                        'rounded' => false,
                        'style' => '',
                    ),
                    'tip_position' => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect' => array(
                        'show' => array(
                            'effect' => 'slide',
                            'duration' => '500',
                            'event' => 'mouseover',
                        ),
                        'hide' => array(
                            'effect' => 'slide',
                            'duration' => '500',
                            'event' => 'click mouseleave',
                        ),
                    ),
                )
            );
            
            $this->args['intro_text'] = '';

            // Add content after the form.
            $this->args['footer_text'] = '';
            return $this->args;
			
			if ( ! function_exists( 'redux_validate_callback_function' ) ) {
				function redux_validate_callback_function( $field, $value, $existing_value ) {
					$error   = false;
					$warning = false;

					//do your validation
					if ( $value == 1 ) {
						$error = true;
						$value = $existing_value;
					} elseif ( $value == 2 ) {
						$warning = true;
						$value   = $existing_value;
					}

					$return['value'] = $value;

					if ( $error == true ) {
						$field['msg']    = 'your custom error message';
						$return['error'] = $field;
					}

					if ( $warning == true ) {
						$field['msg']      = 'your custom warning message';
						$return['warning'] = $field;
					}

					return $return;
				}
			}
			
        }
    }

    global $reduxConfig;
    $reduxConfig = new Aora_Redux_Framework_Config();
	
}