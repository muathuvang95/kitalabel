<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Aora_Elementor_Product_Recently_Viewed') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


class Aora_Elementor_Product_Recently_Viewed extends Aora_Elementor_Carousel_Base {

    public function get_name() {
        return 'tbay-product-recently-viewed';
    }

    public function get_title() {
        return esc_html__( 'Aora Product Recently Viewed', 'aora' );
    }

    public function get_categories() {
        return [ 'aora-elements', 'woocommerce-elements'];
    }

    public function get_icon() {
        return 'eicon-clock';
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
        return ['slick', 'aora-custom-slick'];
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'product', 'products', 'Recently Viewed', 'Recently' ];
    }

    protected function register_controls() {
        $this->register_controls_heading(['position_displayed' => 'main']);

        $this->start_controls_section(
            'general',
            [
                'label' => esc_html__( 'General', 'aora' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'position_displayed',
            [
                'label'     => esc_html__('Position Displayed', 'aora'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'header',
                'options'   => [
                    'header'      => esc_html__('Header', 'aora'), 
                    'main'  => esc_html__('Main Content', 'aora'), 
                ],
            ]
        ); 

        $this->register_control_header();

        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'aora'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'empty',
            [
                'label' => esc_html__( 'Empty Result - Custom Paragraph', 'aora' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('You have no recently viewed item.', 'aora'), 
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );
        
        $this->add_control(
            'align_empty',
            [
                'label' => esc_html__( 'Align Empty Result', 'aora' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left','aora'),
                    'center' => esc_html__('Center','aora'),
                    'right' => esc_html__('Right','aora')
                ],
                'default' => 'center', 
                'dynamic' => [
                    'active' => true,
                ],
                'selectors' => [
					'{{WRAPPER}} .content-empty' => 'text-align: {{VALUE}};',
				]  
            ]
        );


        $this->register_control_main();

        $this->add_control(
            'enable_readmore',
            [
                'label' => esc_html__( 'Enable Button "Read More" ', 'aora' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        ); 

        $this->end_controls_section(); 
        $this->register_control_style_header_title();

        $this->add_control_responsive(['position_displayed' => 'main']);

        $this->add_control_carousel(['layout_type' => 'carousel']);
        $this->register_control_viewall();
    }

    private function register_control_main() {

        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number of products', 'aora'),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__( 'Number of products to show ( -1 = all )', 'aora' ),
                'default' => 8,
                'min'  => -1,
                'condition' => [
                    'position_displayed' => 'main' 
                ],
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
                'condition' => [
                    'position_displayed' => 'main'
                ],
            ]
        ); 

        $this->add_control(
            'product_style',
            [
                'label' => esc_html__('Product Style', 'aora'),
                'type' => Controls_Manager::SELECT,
                'default' => 'v1',
                'options' => $this->get_template_product(),
                'prefix_class' => 'elementor-product-',
                'condition' => [
                    'position_displayed' => 'main'
                ],
            ]
        );
    }
 
    private function register_control_viewall() {
        $this->start_controls_section(
            'section_readmore',
            [
                'label' => esc_html__( 'Read More Options', 'aora' ),
                'type'  => Controls_Manager::SECTION,
                'condition' => [
                    'enable_readmore' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label' => esc_html__('Button "Read More" Custom Text', 'aora'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read More', 'aora'),
                'label_block' => true,
            ]
        );  

        $pages = $this->get_available_pages();

        if (!empty($pages)) {
            $this->add_control(
                'readmore_page',
                [
                    'label'        => esc_html__('Page', 'aora'),
                    'type'         => Controls_Manager::SELECT2,
                    'options'      => $pages,
                    'default'      => array_keys($pages)[0],
                    'save_default' => true,
                    'label_block' => true,
                    'separator'    => 'after',
                ]
            );
        } else {
            $this->add_control(
                'readmore_page',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(__('<strong>There are no pages in your site.</strong><br>Go to the <a href="%s" target="_blank">pages screen</a> to create one.', 'aora'), admin_url('edit.php?post_type=page')),
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );
        }
        $this->end_controls_section();
    }
    protected function register_control_style_header_title() {

        $this->start_controls_section(
            'section_style_heading_header',
            [
                'label' => esc_html__( 'Heading', 'aora' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'position_displayed' => 'header'
                ],
            ]
        );
        $this->add_responsive_control(
            'heading_header_size',
            [
                'label' => esc_html__('Font Size', 'aora'),
                'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-recently-viewed-header h3' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'heading_header_line_height',
            [
                'label' => esc_html__('Line Height', 'aora'),
                'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-recently-viewed-header h3' => 'line-height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        $this->add_responsive_control(
            'heading_header_style_margin',
            [
                'label' => esc_html__( 'Margin', 'aora' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'selectors' => [
                    '{{WRAPPER}} .product-recently-viewed-header h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );        

        $this->add_responsive_control(
            'heading_header_style_padding',
            [
                'label' => esc_html__( 'Padding', 'aora' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'selectors' => [
                    '{{WRAPPER}} .product-recently-viewed-header h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'heading_header_style_color',
            [
                'label' => esc_html__( 'Color', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .product-recently-viewed-header h3' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_header_style_color_hover',
            [
                'label' => esc_html__( 'Hover Color', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .product-recently-viewed-header:hover h3,
                    {{WRAPPER}} .product-recently-viewed-header:hover h3:after' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_header_style_bg',
            [
                'label' => esc_html__( 'Background', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .product-recently-viewed-header h3' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_header_style_bg_hover',
            [
                'label' => esc_html__( 'Hover Background', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .product-recently-viewed-header:hover h3' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }
    private function register_control_header() {
        $this->add_control(
            'advanced_header',
            [
                'label' => esc_html__('Header', 'aora'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'position_displayed' => 'header'
                ],
            ]
        );

        $this->add_control(
            'header_title',
            [
                'label' => esc_html__('Title', 'aora'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Recently Viewed', 'aora'),
                'label_block' => true,
                'condition' => [
                    'position_displayed' => 'header'
                ],
            ]
        );  

        $this->add_control(
            'header_column',
            [
                'label'     => esc_html__('Columns and max item', 'aora'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 8,
                'separator'    => 'after',
                'options'   => $this->get_max_columns(),
            ]
        );

 
    }

    public function get_max_columns() {

        $value = apply_filters( 'aora_admin_elementor_recently_viewed_header_columns', [
           4 => 4,
           5 => 5,
           6 => 6,
           7 => 7,
           8 => 8,
           9 => 9,
           10 => 10,
           11 => 11,
           12 => 12,
        ] ); 

        return $value;
    }  

    private function get_recently_viewed( $limit ) {
        
        $args = aora_tbay_get_products_recently_viewed($limit);
        $args = apply_filters( 'aora_list_recently_viewed_products_args', $args );

        $products = new WP_Query( $args );

        ob_start();

        ?>
            <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                <?php wc_get_template_part( 'content', 'recent-viewed' ); ?>

            <?php endwhile; // end of the loop. ?>

        <?php

        $content = ob_get_clean();

        wp_reset_postdata();

        return $content;
    }

    public function render_content_header() {
        $header_title = '';

        $settings = $this->get_settings_for_display();
        extract($settings);

        if( $position_displayed === 'main' ) return;

        $content                    =  trim($this->get_recently_viewed($header_column));

        $class                      =  '';

        if( empty($content) ) {
            $content = $empty;
            $class   = 'empty';
        } 

        $content = ( !empty($content) ) ? $content : $empty;

        $this->add_render_attribute('wrapper', 'data-column', $header_column);
        ?>

        <h3 class="header-title">
            <?php echo trim($header_title); ?>
        </h3>
        <div class="content-view <?php echo esc_attr( $class ); ?>">
            <div class="list-recent">
                <?php echo trim($content); ?>
            </div>

            <?php $this->render_btn_readmore($header_column); ?>
        </div>

        <?php
    }

    private function render_empty() {
        $settings = $this->get_settings_for_display();
        echo '<div class="content-empty">'. trim($settings['empty']) .'</div>';
    }

    private function render_btn_readmore($count) {
        $settings = $this->get_settings_for_display();
        extract($settings);
        $products_list              =  aora_tbay_wc_track_user_get_cookie();
        $all                        =  count($products_list);

        if( !empty($readmore_page) ) {
            $link = get_permalink($readmore_page);
        }

        if( $enable_readmore && ($all > $count) && !empty($link) ) : ?>
            <a class="btn-readmore" href="<?php echo esc_url($link); ?>" title="<?php esc_attr( $readmore_text ); ?>"><?php echo trim($readmore_text); ?></a>
        <?php endif;
    }

    public function render_content_main() {
        $settings = $this->get_settings_for_display();
        extract($settings);

        if( $position_displayed === 'header' ) return;

        $args   = aora_tbay_get_products_recently_viewed($limit);

        $args   =  apply_filters( 'aora_list_recently_viewed_products_args', $args );
        $loop   = new WP_Query( $args );

        if( !$loop->have_posts() ) $this->render_empty();
        
        $this->add_render_attribute('row', 'class', ['products']);

        $attr_row = $this->get_render_attribute_string('row');

        wc_get_template( 'layout-products/layout-products.php' , array( 'loop' => $loop, 'product_style' => $product_style, 'attr_row' => $attr_row) );

        $this->render_btn_readmore($limit);
    }
}
$widgets_manager->register_widget_type(new Aora_Elementor_Product_Recently_Viewed());