<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Aora_Elementor_Product_Categories_Tabs') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Aora_Elementor_Product_Categories_Tabs extends  Aora_Elementor_Carousel_Base{
    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'tbay-product-categories-tabs';
    }
   
    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Aora Product Categories Tabs', 'aora' );
    }
    public function get_categories() {
        return [ 'aora-elements', 'woocommerce-elements'];
    }
 
    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-product-tabs';
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    public function get_script_depends()
    {
        return ['slick', 'aora-custom-slick'];
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'product-categories' ];
    }

    protected function register_controls() {
        $this->register_controls_heading();
        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'Product Categories', 'aora' ),
            ]
        );        

        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number of products', 'aora'),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__( 'Number of products to show ( -1 = all )', 'aora' ),
                'default' => 6,
                'min'  => -1,
            ]
        );

        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'aora'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->register_woocommerce_order();
        $this->add_control(
            'product_type',
            [   
                'label'   => esc_html__('Product Type','aora'),
                'type'     => Controls_Manager::SELECT,
                'options' => $this->get_product_type(),
                'default' => 'newest'
            ]
        );
        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'aora'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'grid',
                'options'   => [
                    'grid'      => esc_html__('Grid', 'aora'), 
                    'carousel'  => esc_html__('Carousel', 'aora'), 
                ],
            ]
        );  

        $repeater = $this->register_category_repeater();
        $this->add_control(
            'categories_tabs',
            [
                'label' => esc_html__( 'Categories Items', 'aora' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'categories_field' => '{{{ categories }}}',
            ]
        );   

        $this->add_control(
            'product_style',
            [
                'label' => esc_html__('Product Style', 'aora'),
                'type' => Controls_Manager::SELECT,
                'default' => 'v1',
                'options' => $this->get_template_product(),
                'prefix_class' => 'elementor-product-'
            ]
        );

        $this->add_control(
            'position_tab_categories',
            [
                'label' => esc_html__('Tab Categories Right', 'aora'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'prefix_class' => 'tab-categories-right-'
            ]
        );

        $this->register_button();
        $this->end_controls_section();
        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => 'carousel']);
        $this->register_style_heading();
    }

    private function register_category_repeater() {
        $repeater = new \Elementor\Repeater();
        $categories = $this->get_product_categories();
        $repeater->add_control (
            'category', 
            [
                'label' => esc_html__( 'Select Category', 'aora' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => array_keys($categories)[0],
                'label_block' => true,
                'options'   => $categories,   
            ]
        );

        return $repeater;
    }

    protected function register_style_heading() {
        $this->start_controls_section(
            'section_style_heading_categories_tab',
            [
                'label' => esc_html__('Heading Product Categories Tabs', 'aora'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'heading_categories_tab_align',
            [
                'label' => esc_html__('Alignment', 'aora'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'aora'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'aora'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'aora'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .heading-product-category-tabs .tabs-list' => 'text-align: {{VALUE}};',
                ],
            ]
        );
     

        $this->add_control(
            'heading_categories_tab_background',
            [
                'label'     => esc_html__('Background', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .heading-product-category-tabs'    => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'heading_categories_tab_padding',
            [
                'label'      => esc_html__( 'Padding', 'aora' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .heading-product-category-tabs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

    }
    protected function register_button() {
        $this->add_control(
            'show_more',
            [
                'label'     => esc_html__('Display Show More', 'aora'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );  
        $this->add_control(
            'text_button',
            [
                'label'     => esc_html__('Text Button', 'aora'),
                'type'      => Controls_Manager::TEXT,
                'condition' => [
                    'show_more' => 'yes'
                ]
            ]
        );  
        $this->add_control(
            'icon_button',
            [
                'label'     => esc_html__('Icon Button', 'aora'),
                'type'      => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'tb-icon tb-icon-arrow-right',
					'library' => 'tbay-custom',
                ],
                'condition' => [
                    'show_more' => 'yes'
                ]
            ]
        );  
    }
   

    public function get_template_product() {
        return apply_filters( 'aora_get_template_product', 'v1' );
    }

    public function render_tabs_title($categories_tabs) {
        ?>
            <div class="heading-product-category-tabs">
            <?php
                if(!empty($title_cat_tab) || !empty($sub_title_cat_tab) ) {
                    ?>
                    <h3 class="heading-tbay-title">
                        <?php if( !empty($title_cat_tab) ) : ?>
                            <span class="title"><?php echo trim($title_cat_tab); ?></span>
                        <?php endif; ?>	    	
                        <?php if( !empty($sub_title_cat_tab) ) : ?>
                            <span class="subtitle"><?php echo trim($sub_title_cat_tab); ?></span>
                        <?php endif; ?>
                    </h3>
                    <?php
                }
            ?>

            <ul class="tabs-list nav nav-tabs">
                <?php $_count = 0; ?>
                <?php foreach ( $categories_tabs as $item ) : ?>
                    <?php $this->render_product_tab($item['category'], $item['_id'], $_count); ?>
                    <?php $_count++; ?>
                <?php endforeach; ?>
                
            </ul>
            
        </div>
        <?php
    }
    public function render_product_tab($item, $_id, $_count) {
        
        ?>
        <?php 
            $active = ($_count == 0) ? 'active' : '';
            $category = get_term_by( 'slug', $item, 'product_cat' );
            $title = $category->name;
        ?>
        <li >
            <a class="<?php echo esc_attr( $active ); ?>" href="#<?php echo esc_attr($item.'-'.$_id); ?>" data-toggle="tab" aria-controls="<?php echo esc_attr($item.'-'.$_id);?>"><?php echo trim($title);?></a>
        </li>

       <?php
    }
    public function render_product_tabs_content($categories_tabs) {
        ?>
            <div class="content-product-category-tab">
                <div class="tbay-addon-content tab-content woocommerce">
                 <?php 
                 $_count = 0;
                 foreach ($categories_tabs as $key ) {
                     $tab_active = ($_count == 0) ? ' show active' : '';
                         $this->render_content_tab($key['category'], $tab_active, $key['_id']);
                     $_count++;
                 }
                 ?>
                </div>
            </div>
        <?php
 
    }
    private function  render_content_tab($key, $tab_active, $_id) {
        $settings = $this->get_settings_for_display();
        $cat_operator = $product_type = $limit = $orderby = $order = '';
        extract( $settings );
        
        /** Get Query Products */
        $loop = $this->get_query_products($key,  $cat_operator, $product_type, $limit, $orderby, $order);

        $this->add_render_attribute('row', 'class', ['products']);

        $attr_row = $this->get_render_attribute_string('row');
        ?>
        <div class="tab-pane fade <?php echo esc_attr( $tab_active ); ?>" id="<?php echo esc_attr($key).'-'.$_id; ?>">
        
        <?php wc_get_template( 'layout-products/layout-products.php' , array( 'loop' => $loop, 'product_style' => $product_style, 'attr_row' => $attr_row) ); ?>
        </div>
        <?php
        
    }
    
    public function render_item_button() {
        $settings = $this->get_settings_for_display();
        extract( $settings );
        $url_category =  get_permalink(wc_get_page_id('shop'));
        if(isset($text_button) && !empty($text_button)) {?>
            <div class="readmore-wrapper"><a href="<?php echo esc_url($url_category)?>" class="btn show-all"><?php echo trim($text_button) ?>
                <?php 
                    $this->render_item_icon($icon_button);
                ?>
            </a></div>
            <?php
        }
        
    }

}
$widgets_manager->register_widget_type(new Aora_Elementor_Product_Categories_Tabs());
