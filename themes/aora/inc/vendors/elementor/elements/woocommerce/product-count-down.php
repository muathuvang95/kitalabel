<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Aora_Elementor_Product_CountDown') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

 
class Aora_Elementor_Product_CountDown extends Aora_Elementor_Carousel_Base {

    public function get_name() {
        return 'tbay-product-count-down';
    }

    public function get_title() {
        return esc_html__( 'Aora Product CountDown', 'aora' );
    }

    public function get_categories() {
        return [ 'aora-elements', 'woocommerce-elements'];
    }

    public function get_icon() {
        return 'eicon-countdown';
    }

    /**
     * Retrieve the list of scripts the image carousel widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @since 1.3.0
     * @access public
     *
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends()
    {
        return ['slick', 'aora-custom-slick', 'jquery-countdowntimer'];
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'product', 'products', 'countdown'];
    }

    protected function register_controls() {
        $this->register_controls_heading();
        $this->start_controls_section(
            'general',
            [
                'label' => esc_html__( 'General', 'aora' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'countdown_title',
            [
                'label' => esc_html__('Title Date', 'aora'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Deals end in:', 'aora'),
                'label_block' => true,
            ]
        );  

        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'aora'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'grid',
                'options'   => [
                    'grid'      => esc_html__('Grid', 'aora'), 
                    'carousel'  => esc_html__('Carousel', 'aora'), 
                ],
            ]
        ); 

        $this->add_control(
            'product_style',
            [
                'label' => esc_html__('Product Style', 'aora'),
                'type' => Controls_Manager::SELECT,
                'default' => 'v1',
                'options' => $this->get_template_product_grid(),
                'prefix_class' => 'elementor-product-',
            ]
        );
        $products = $this->get_available_on_sale_products();
        
        if (!empty($products)) {
            $this->add_control(
                'products',
                [
                    'label'        => esc_html__('Products', 'aora'),
                    'type'         => Controls_Manager::SELECT2,
                    'options'      => $products,
                    'default'      => array_keys($products)[0],
                    'multiple' => true,
                    'label_block' => true,
                    'save_default' => true,
                    'description' => esc_html( 'Only search for sale products', 'aora' ),
                   
                ]
            );
        } else {
            $this->add_control(
                'html_products',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(__('You do not have any discount products. <br>Go to the <strong><a href="%s" target="_blank">Products screen</a></strong> to create one.', 'aora'), admin_url('edit.php?post_type=product')),
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                   
                ]
            );
        }
        $this->end_controls_section(); 
        
        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => 'carousel']);
    }

    public function render_content_product_count_down() {
        $settings = $this->get_settings_for_display();
        extract($settings);
        $ids = ${'products'};
        if( !is_array($ids) ){
            $atts['ids'] = $ids;
        } else {
            if( count($ids) === 0 ) {
                echo '<div class="not-product-count-down">'. esc_html__('Please select the show product', 'aora')  .'</div>';
                return;
            }

            $atts['ids'] = implode(',', $ids);
        }

        $type = 'products';

        $shortcode = new WC_Shortcode_Products($atts, $type);
        $args = $shortcode->get_query_args();

        $loop = new WP_Query($args); 

        if( !$loop->have_posts() ) return;
        
        $this->add_render_attribute('row', 'class', ['products']);

        $attr_row = $this->get_render_attribute_string('row');

        wc_get_template( 'layout-products/layout-products.php' , array( 'loop' => $loop, 'product_style' => $product_style, 'countdown_title' => $countdown_title, 'countdown' => true, 'attr_row' => $attr_row) );
        
    }
    

}
$widgets_manager->register_widget_type(new Aora_Elementor_Product_CountDown());