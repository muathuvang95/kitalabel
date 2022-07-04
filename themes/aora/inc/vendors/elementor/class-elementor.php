<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Aora_Elementor_Addons {
	public function __construct() {
        $this->include_control_customize_widgets();
        $this->include_render_customize_widgets();

		add_action( 'elementor/elements/categories_registered', array( $this, 'add_category' ) );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'include_widgets' ) );

		add_action( 'wp', [ $this, 'regeister_scripts_frontend' ] );

        // frontend
        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [ $this, 'frontend_after_register_scripts' ]);
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'frontend_after_enqueue_scripts' ] );

        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_icons'], 99);

        // editor 
        add_action('elementor/editor/after_register_scripts', [ $this, 'editor_after_register_scripts' ]);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_after_enqueue_scripts'] );

    
        add_action( 'widgets_init', array( $this, 'register_wp_widgets' ) );

        add_action('elementor/widgets/widgets_registered', array( $this, 'unregister_elementor_widgets' ), 15 );
    }  

    public function editor_after_register_scripts() {
        if( aora_is_remove_scripts() ) return;

        $suffix = (aora_tbay_get_config('minified_js', false)) ? '.min' : AORA_MIN_JS;
        // /*slick jquery*/
        wp_register_script( 'slick', AORA_SCRIPTS . '/slick' . $suffix . '.js', array(), '1.0.0', true );
        wp_register_script( 'aora-custom-slick', AORA_SCRIPTS . '/custom-slick' . $suffix . '.js', array( ), AORA_THEME_VERSION, true ); 

        wp_register_script( 'aora-script',  AORA_SCRIPTS . '/functions' . $suffix . '.js', array(),  AORA_THEME_VERSION,  true );


        wp_register_script( 'popper', AORA_SCRIPTS . '/popper' . $suffix . '.js', array( ), '1.12.9', true );       
        wp_register_script( 'bootstrap', AORA_SCRIPTS . '/bootstrap' . $suffix . '.js', array( 'popper' ), '4.0.0', true );

        /*Treeview menu*/
        wp_register_script( 'jquery-treeview', AORA_SCRIPTS . '/jquery.treeview' . $suffix . '.js', array( ), '1.4.0', true ); 
       
        // Add js Sumoselect version 3.0.2
        wp_register_style('sumoselect', AORA_STYLES . '/sumoselect.css', array(), '1.0.0', 'all');
        wp_register_script('jquery-sumoselect', AORA_SCRIPTS . '/jquery.sumoselect' . $suffix . '.js', array(), '3.0.2', TRUE); 
 
    }    

    public function frontend_after_enqueue_scripts() {
    }  

    public function editor_after_enqueue_scripts() { 

    } 

    public function enqueue_editor_icons() {

        wp_enqueue_style( 'simple-line-icons', AORA_STYLES . '/simple-line-icons.css', array(), '2.4.0' );
        wp_enqueue_style( 'aora-font-tbay-custom', AORA_STYLES . '/font-tbay-custom.css', array(), '1.0.0' );
        wp_enqueue_style( 'material-design-iconic-font', AORA_STYLES . '/material-design-iconic-font.css', false, '2.2.0' ); 

        if ( aora_elementor_is_edit_mode() || aora_elementor_is_preview_page() || aora_elementor_is_preview_mode() ) {
            wp_enqueue_style( 'aora-elementor-editor', AORA_STYLES . '/elementor-editor.css', array(), AORA_THEME_VERSION );
        }
    }


    /**
     * @internal Used as a callback
     */
    public function frontend_after_register_scripts() {
        $this->editor_after_register_scripts();
    }


	public function register_wp_widgets() {

	}

	function regeister_scripts_frontend() {
		
    }


    public function add_category() {
        Elementor\Plugin::instance()->elements_manager->add_category(
            'aora-elements',
            array(
                'title' => esc_html__('Aora Elements', 'aora'),
                'icon'  => 'fa fa-plug',
            )
        );
    }

    /**
     * @param $widgets_manager Elementor\Widgets_Manager
     */
    public function include_widgets($widgets_manager) {
        $this->include_abstract_widgets($widgets_manager);
        $this->include_general_widgets($widgets_manager);
        $this->include_header_widgets($widgets_manager);
        $this->include_woocommerce_widgets($widgets_manager);
	} 


    /**
     * Widgets General Theme
     */
    public function include_general_widgets($widgets_manager) {

        $elements = array(
            'template',  
            'heading',  
            'features', 
            'brands',
            'banner',
            'posts-grid',
            'our-team',
            'testimonials',
            'button',
            'list-menu',
            'menu-vertical',
        );

        if( class_exists('MC4WP_MailChimp') ) {
            array_push($elements, 'newsletter');
        }

        
        if( function_exists( 'sb_instagram_feed_init' ) ) {
            array_push($elements, 'instagram-feed');
        }

        $elements = apply_filters( 'aora_general_elements_array', $elements );

        foreach ( $elements as $file ) {
            $path   = AORA_ELEMENTOR .'/elements/general/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        }

    }    

    /**
     * Widgets WooComerce Theme
     */
    public function include_woocommerce_widgets($widgets_manager) {
        if( !aora_is_Woocommerce_activated() ) return;

        $woo_elements = array(
            'products',
            'template-kita',
            'product-category',
            'product-tabs',
            'woocommerce-tags',
            'custom-image-list-tags',
            'product-categories-tabs',
            'list-categories-product',
            'product-recently-viewed',
            'custom-image-list-categories',
            'custom-image-list-categories-width-menu',
            'product-flash-sales',
            'product-count-down',
            'product-list-tags'
        );

        $woo_elements = apply_filters( 'aora_woocommerce_elements_array', $woo_elements );

        foreach ( $woo_elements as $file ) {
            $path   = AORA_ELEMENTOR .'/elements/woocommerce/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        }

    }    

    /**
     * Widgets Header Theme
     */
    public function include_header_widgets($widgets_manager) {

        $elements = array(
            'site-logo',
            'nav-menu',
            'search-form',
            'banner-close',
            'canvas-menu-template',
        );

        if( aora_is_Woocommerce_activated() ) {
            array_push($elements, 'account');

            if( !aora_catalog_mode_active() ) {
                array_push($elements, 'mini-cart');
            }
        }

        if( class_exists('WOOCS_STARTER') ) {
            array_push($elements, 'currency');
        }

        if( class_exists( 'YITH_WCWL' ) ) {
            array_push($elements, 'wishlist');
        }

        if( class_exists( 'YITH_Woocompare' ) ) {
            array_push($elements, 'compare');
        } 

        if( defined('TBAY_ELEMENTOR_DEMO') ) {
            array_push($elements, 'custom-language');
        }

        $elements = apply_filters( 'aora_header_elements_array', $elements );

        foreach ( $elements as $file ) {
            $path   = AORA_ELEMENTOR .'/elements/header/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        }

    }


    /**
     * Widgets Abstract Theme
     */
    public function include_abstract_widgets($widgets_manager) {
        $abstracts = array(
            'image',
            'base',
            'responsive',
            'carousel',
        );

        $abstracts = apply_filters( 'aora_abstract_elements_array', $abstracts );

        foreach ( $abstracts as $file ) {
            $path   = AORA_ELEMENTOR .'/abstract/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        } 
    }

    public function include_control_customize_widgets() {
        $widgets = array(
            'sticky-header',
            'column',
            'column-border', 
            'section-stretch-row',
            'settings-layout',
        );

        $widgets = apply_filters( 'aora_customize_elements_array', $widgets );
 
        foreach ( $widgets as $file ) {
            $control   = AORA_ELEMENTOR .'/elements/customize/controls/' . $file . '.php';
            if( file_exists( $control ) ) {
                require_once $control;
            }            
        } 
    }    

    public function include_render_customize_widgets() {
        $widgets = array(
            'sticky-header',
            'column-border',
        );

        $widgets = apply_filters( 'aora_customize_elements_array', $widgets );
 
        foreach ( $widgets as $file ) {
            $render    = AORA_ELEMENTOR .'/elements/customize/render/' . $file . '.php';         
            if( file_exists( $render ) ) {
                require_once $render;
            }
        } 
    }

    public function unregister_elementor_widgets($widgets_manager){
 
        $elementor_widget_blacklist = array(
            'aora_custom_menu',
            'aora_list_categories',
            'aora_popular_post',
            'aora_popup_newsletter',
            'aora_posts',
            'aora_recent_comment',
            'aora_recent_post',
            'aora_single_image',
            'aora_socials_widget',
            'aora_featured_video_widget',
            'aora_template_elementor',
            'aora_top_rate_widget',
            'aora_woo_carousel',
            'aora_product_brand'
        );

        foreach($elementor_widget_blacklist as $widget_name){
            $widgets_manager->unregister_widget_type('wp-widget-'. $widget_name);
        }

    }
}

new Aora_Elementor_Addons();

