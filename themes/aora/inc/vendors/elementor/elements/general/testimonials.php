<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Aora_Elementor_Testimonials') ) {
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
class Aora_Elementor_Testimonials extends  Aora_Elementor_Carousel_Base{
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
        return 'tbay-testimonials';
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
        return esc_html__( 'Aora Testimonials', 'aora' );
    }

    public function get_script_depends() {
        return [ 'aora-custom-slick', 'slick' ];
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
        return 'eicon-testimonial';
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        $this->register_controls_heading();

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'General', 'aora' ),
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
            'testimonials_align',
            [
                'label' => esc_html__('Align','aora'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','aora'),
                        'icon' => 'fas fa-align-left'
                    ],
                    'center' => [
                        'title' => esc_html__('Center','aora'),
                        'icon' => 'fas fa-align-center'
                    ],
                    'right' => [
                        'title' => esc_html__('Right','aora'),
                        'icon' => 'fas fa-align-right'
                    ],   
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item .testimonials-body'  => 'text-align: {{VALUE}} !important',
                ]
            ]
        );  
        $this->add_responsive_control(
			'testimonial_padding',
			[
				'label' => esc_html__( 'Padding "Name"', 'aora' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $repeater = $this->register_testimonials_repeater();

        $this->add_control(
            'testimonials',
            [
                'label' => esc_html__( 'Testimonials Items', 'aora' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => $this->register_set_testimonial_default(),
                'testimonials_field' => '{{{ testimonials_image }}}',
            ]
        );    

        $this->end_controls_section();

        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => 'carousel']);

    }

    private function register_testimonials_repeater() {
        $repeater = new \Elementor\Repeater();
        $repeater->add_control (
            'testimonial_logo', 
            [
                'label' => esc_html__( 'Choose Image: Logo', 'aora' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control (
            'testimonial_image', 
            [
                'label' => esc_html__( 'Choose Image: Avatar', 'aora' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control (
            'testimonial_name', 
            [
                'label' => esc_html__( 'Name', 'aora' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control (
            'testimonial_address', 
            [
                'label' => esc_html__( 'Address', 'aora' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control (
            'testimonial_excerpt', 
            [
                'label' => esc_html__( 'Excerpt', 'aora' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        return $repeater;
    }

    private function register_set_testimonial_default() {
        $defaults = [
            [
                
                'testimonial_image' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                
                'testimonial_logo' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_name' => esc_html__( 'Name 1', 'aora' ),
                'testimonial_address' => esc_html__( 'Address 1', 'aora' ),
                'testimonial_excerpt' => esc_html__('Lorem ipsum dolor sit amet, in mel unum delicatissimi conclusionemque', 'aora'),
            ],
            [
                'testimonial_image' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_logo' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_name' => esc_html__( 'Name 2', 'aora' ),
                'testimonial_address' => esc_html__( 'Address 2', 'aora' ),
                'testimonial_excerpt' => esc_html__('Lorem ipsum dolor sit amet, in mel unum delicatissimi conclusionemque', 'aora'),
            ],
            [
                'testimonial_image' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_logo' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_name' => esc_html__( 'Name 3', 'aora' ),
                'testimonial_address' => esc_html__( 'Address 3', 'aora' ),
                'testimonial_excerpt' => esc_html__('Lorem ipsum dolor sit amet, in mel unum delicatissimi conclusionemque', 'aora'),
            ],
            [
                'testimonial_image' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_logo' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'testimonial_name' => esc_html__( 'Name 4', 'aora' ),
                'testimonial_address' => esc_html__( 'Address 4', 'aora' ),
                'testimonial_excerpt' => 'Lorem ipsum dolor sit amet, in mel unum delicatissimi conclusionemque',
            ],
        ];

        return $defaults;
    }

    protected function render_item( $item ) {
        ?> 
        <div class="testimonials-body"> 
                <?php
                    if( !empty($item['testimonial_logo']) && isset($item['testimonial_logo']) ) {
                        ?>
                        <div class="testimonial-logo">
                            <?php echo trim($this->get_widget_field_img($item['testimonial_logo'])); ?>
                        </div>
                        <?php
                    }
                ?>
                <?php $this->render_item_excerpt( $item ); ?>
                <div class="testimonial-meta">
                    <?php 
                        echo trim($this->get_widget_field_img($item['testimonial_image']));
                        $this->render_item_name( $item );
                        $this->render_item_address( $item );
                    ?>
                </div>
                
                <?php
                ?>
                <?php
            ?>
        </div>
        <?php
    }    
    

    private function render_item_name( $item ) {
        $testimonial_name  = $item['testimonial_name'];
        if(isset($testimonial_name) && !empty($testimonial_name)) {
            ?>
                <span class="name"><?php echo trim($testimonial_name) ?></span>
            <?php
        }
    }
    private function render_item_address( $item ) {
        $testimonial_address  = $item['testimonial_address'];

        if(isset($testimonial_address) && !empty($testimonial_address)) {
            ?>
                <span class="address"><?php echo trim($testimonial_address) ?></span>
            <?php
        }
    }
    private function render_item_excerpt( $item ) {
        $testimonial_excerpt  = $item['testimonial_excerpt'];

        if(isset($testimonial_excerpt) && !empty($testimonial_excerpt)) {
            ?>
                <span class="excerpt"><?php echo trim($testimonial_excerpt) ?></span>
            <?php
        }
    }

}
$widgets_manager->register_widget_type(new Aora_Elementor_Testimonials());
