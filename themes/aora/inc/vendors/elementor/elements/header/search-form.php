<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Aora_Elementor_Search_Form') ) {
    exit; // Exit if accessed directly.
}


use Elementor\Controls_Manager;


class Aora_Elementor_Search_Form extends Aora_Elementor_Widget_Base {

    protected $nav_menu_index = 1;

    public function get_name() {
        return 'tbay-search-form';
    }

    public function get_title() {
        return esc_html__('Aora Search Form', 'aora');
    }
    
    public function get_icon() {
        return 'eicon-search';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Search Form', 'aora'),
            ]
        ); 
       
        $this->_register_form_search();
        $this->_register_button_search();
        $this->_register_category_search();

        $this->add_control(
            'advanced_show_result',
            [
                'label' => esc_html__('Show Result', 'aora'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'show_image_search',
            [
                'label'   => esc_html__('Show Image of Search Result', 'aora'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'show_price_search',
            [
                'label'              => esc_html__('Show Price of Search Result', 'aora'),
                'type'               => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->end_controls_section();
        $this->register_section_style_search_form();
    }

    protected function register_section_style_search_form() {
        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Style Search Form', 'aora'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'search_form_line_height',
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
                    '{{WRAPPER}} .tbay-search-form .tbay-search,
                    {{WRAPPER}} .tbay-search-form .select-category,{{WRAPPER}} .tbay-search-form .button-search:not(.icon),
                    {{WRAPPER}} .tbay-search-form .select-category > select' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tbay-search-form .select-category,{{WRAPPER}} .tbay-search-form .button-search:not(.icon),
                    {{WRAPPER}} .tbay-preloader,{{WRAPPER}} .tbay-search-form .button-search:not(.icon) i,{{WRAPPER}} .tbay-search-form .SumoSelect' => 'line-height: {{SIZE}}{{UNIT}}'
                ],
            ]
        );
        $this->add_control(
            'search_form_width',
            [
                'label' => esc_html__('Width', 'aora'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                    ]
                ],
                'size_units' => [ 'px' ,'%'],
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .form-group .input-group,
                    {{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'border_style_tbay_search_form',
            [
                'label' => esc_html__( 'Border Type', 'aora' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'None', 'aora' ),
                    'solid' => esc_html__( 'Solid', 'aora' ),
                    'double' => esc_html__( 'Double', 'aora' ),
                    'dotted' => esc_html__( 'Dotted', 'aora' ),
                    'dashed' => esc_html__( 'Dashed', 'aora' ),
                    'groove' => esc_html__( 'Groove', 'aora' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .form-group .input-group' => 'border-style: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'border_width_tbay_search_form',
            [
                'label' => esc_html__( 'Width', 'aora' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],

                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .form-group .input-group' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .SumoSelect.open>.optWrapper,{{WRAPPER}} .autocomplete-suggestions' => 'margin-top: {{BOTTOM}}{{UNIT}};'
                ],
                'condition' => [
                    'border_style_tbay_search_form!' => '',
                ],
            ]
        );
        $this->add_control(
            'border_color_tbay_search_form',
            [
                'label' => esc_html__( 'Color', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .form-group .input-group' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_style_tbay_search_form!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'border_radius_tbay_search_form',
            [
                'label'     => esc_html__('Border Radius Search Form', 'aora'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .form-group .input-group' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .tbay-search-form .select-category .CaptionCont' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .tbay-search-form .button-group,{{WRAPPER}} .tbay-search-form .button-search:not(.icon)' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0 ;',
                    '.rtl {{WRAPPER}} .tbay-search-form .button-group, .rtl {{WRAPPER}} .tbay-search-form .button-search:not(.icon)' => 'border-radius: {{RIGHT}}{{UNIT}} 0 0 {{BOTTOM}}{{UNIT}};',
                ],
            ]
        ); 

        $this->add_control(
            'advanced_categories_search_style',
            [
                'label' => esc_html__('Categories Search', 'aora'),
                'type' => Controls_Manager::HEADING,
                'separator'    => 'before',
                'condition' => [
                    'enable_categories_search' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'bg_category_search',
            [
                'label'     => esc_html__('Background', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .select-category'    => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_category_search',
            [
                'label'     => esc_html__('Color', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .select-category','{{WRAPPER}} .tbay-search-form .select-category > select'    => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'border_style_category_search',
            [
                'label' => esc_html__( 'Border Type', 'aora' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'None', 'aora' ),
                    'solid' => esc_html__( 'Solid', 'aora' ),
                    'double' => esc_html__( 'Double', 'aora' ),
                    'dotted' => esc_html__( 'Dotted', 'aora' ),
                    'dashed' => esc_html__( 'Dashed', 'aora' ),
                    'groove' => esc_html__( 'Groove', 'aora' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .select-category' => 'border-style: {{VALUE}};',
                ],
                'condition' => [
                    'border_style_tbay_search_form' => '',
                ],
            ]
        );
        $this->add_control(
            'border_width_category_search',
            [
                'label' => esc_html__( 'Width', 'aora' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],

                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .select-category' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    
                ],
                'condition' => [
                    'border_style_category_search!' => '',
                ],
            ]
        );
        $this->add_control(
            'border_color_category_search',
            [
                'label' => esc_html__( 'Color', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .select-category' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_style_category_search!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'border_radius_category_search',
            [
                'label'     => esc_html__('Border Radius Search Form', 'aora'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .select-category' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    
                ],
                'condition' => [
                    'border_style_category_search!' => '',
                ],
            ]
        ); 

        $this->add_control(
            'advanced_btn_search_style',
            [
                'label' => esc_html__('Button Search', 'aora'),
                'type' => Controls_Manager::HEADING,
                'separator'    => 'before',
            ]
        );
        $this->add_control(
            'padding_btn',
            [
                'label'     => esc_html__('Padding Button Search', 'aora'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .button-search:not(.icon)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        ); 
        
        
        $this->start_controls_tabs('tabs_style_btn_search');

        $this->start_controls_tab(
            'tab_btn_search_normal',
            [
                'label' => esc_html__('Normal', 'aora'),
            ]
        ); 
        $this->add_control(
            'color_btn_search',
            [
                'label'     => esc_html__('Color', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .button-search i,
                    {{WRAPPER}} .tbay-search-form .button-search .text, {{WRAPPER}} .tbay-search-form .button-group:before'    => 'color: {{VALUE}}',
                ],
            ]
        );   
        $this->add_control(
            'bg_btn_search',
            [
                'label'     => esc_html__('Background Color', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .button-search,{{WRAPPER}} .tbay-search-form .button-group'    => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_btn_search_hover',
            [
                'label' => esc_html__('Hover', 'aora'),
            ]
        );
        $this->add_control(
            'hover_color_btn_search',
            [
                'label'     => esc_html__('Color', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .button-search:hover i,
                    {{WRAPPER}} .tbay-search-form .button-search:hover .text'    => 'color: {{VALUE}}',
                ], 
            ]
        );   
        $this->add_control(
            'hover_bg_btn_search',
            [
                'label'     => esc_html__('Background Color', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .button-search:hover'    => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
       
        $this->add_control(
            'advanced_input_search_style',
            [
                'label' => esc_html__('Input Search', 'aora'),
                'type' => Controls_Manager::HEADING,
                'separator'    => 'before',
            ]
        );
        $this->add_control(
            'bg_input',
            [
                'label'     => esc_html__('Background Input Search', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search'    => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_input',
            [
                'label'     => esc_html__('Color Input Search', 'aora'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search'    => 'color: {{VALUE}}',
                    '{{WRAPPER}} .form-control::placeholder'    => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'border_style_input_search',
            [
                'label' => esc_html__( 'Border Type', 'aora' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'None', 'aora' ),
                    'solid' => esc_html__( 'Solid', 'aora' ),
                    'double' => esc_html__( 'Double', 'aora' ),
                    'dotted' => esc_html__( 'Dotted', 'aora' ),
                    'dashed' => esc_html__( 'Dashed', 'aora' ),
                    'groove' => esc_html__( 'Groove', 'aora' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search' => 'border-style: {{VALUE}};',
                ],
                'condition' => [
                    'border_style_tbay_search_form' => '',
                ],
            ]
        );
        $this->add_control(
            'border_width_input_search',
            [
                'label' => esc_html__( 'Width', 'aora' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],

                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    
                ],
                'condition' => [
                    'border_style_input_search!' => '',
                ],
            ]
        );
        $this->add_control(
            'border_color_input_search',
            [
                'label' => esc_html__( 'Color', 'aora' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_style_input_search!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'border_radius_input_search',
            [
                'label'     => esc_html__('Border Radius Search Form', 'aora'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    
                ],
                'condition' => [
                    'border_style_input_search!' => '',
                ],
            ]
        ); 

        $this->add_responsive_control(
            'input_search_padding',
            [
                'label'      => esc_html__( 'Padding', 'aora' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tbay-search-form .tbay-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        
    }

    protected function _register_form_search() {
        $this->add_control(
            'advanced_type_search',
            [
                'label' => esc_html__('Form', 'aora'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'search_type',
            [
                'label'              => esc_html__('Search Result', 'aora'),
                'type'               => Controls_Manager::SELECT,
                'default' => 'product',
                'options' => [
                    'product'  => esc_html__('Product','aora'),
                    'post'  => esc_html__('Blog','aora')
                ]
            ]
        );

        
        $this->add_control(
            'autocomplete_search',
            [
                'label'              => esc_html__('Auto-complete Search', 'aora'),
                'type'               => Controls_Manager::SWITCHER,
                'default' => 'yes', 
            ]
        );
        $this->add_control(
            'placeholder_text',
            [
                'label'              => esc_html__('Placeholder Text', 'aora'),
                'type'               => Controls_Manager::TEXT,
                'default'            => esc_html__('Search products...', 'aora'),
            ]
        );  
        $this->add_control(
            'vali_input_search',
            [
                'label'              => esc_html__('Text Validate Input Search', 'aora'),
                'type'               => Controls_Manager::TEXT,
                'default'            => esc_html__('Enter at least 2 characters', 'aora'),
            ]
        );
        $this->add_control(
            'min_characters_search',
            [
                'label'              => esc_html__('Search Min Characters', 'aora'),
                'type'               => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 6,
                        'step' => 1,
                    ],
                    
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
            ]
        );
        $this->add_control(
            'search_max_number_results',
            [
                'label'              => esc_html__('Max Number of Search Results', 'aora'),
                'type'               => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 10,
                        'step' => 1,
                    ],
                    
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
            ]
        );

    }

    protected function _register_button_search() {
        $this->add_control(
            'advanced_button_search',
            [
                'label' => esc_html__('Button Search', 'aora'),
                'type' => Controls_Manager::HEADING,
                'separator'    => 'before',
            ]
        );
        $this->add_control(
            'text_button_search',
            [
                'label'              => esc_html__('Button Search Text', 'aora'),
                'type'               => Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        $this->add_control(
            'icon_button_search',
            [
                'label'              => esc_html__('Button Search Icon', 'aora'),
                'type'               => Controls_Manager::ICONS,
                'default' => [
                    'library' => 'tb-icon',
                    'value'   => 'tb-icon tb-icon-search'
                ],
            ]
        );
        $this->add_control(
            'icon_button_search_size',
            [
                'label' => esc_html__('Font Size Icon', 'aora'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .button-search i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );
    }

    protected function _register_category_search() {
        $this->add_control(
            'advanced_categories_search',
            [
                'label'         => esc_html__('Categories Search', 'aora'),
                'type'          => Controls_Manager::HEADING,
                'separator'     => 'before',
            ]
        );
        $this->add_control(
            'enable_categories_search',
            [
                'label'              => esc_html__('Enable Search in Categories', 'aora'),
                'type'               => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        $this->add_control(
            'text_categories_search',
            [
                'label'              => esc_html__('Search in Categories Text', 'aora'),
                'type'               => Controls_Manager::TEXT,
                'default'            =>  esc_html__('All Categories', 'aora'),
                'condition' => [
                    'enable_categories_search' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'count_categories_search',
            [
                'label'              => esc_html__('Show count in Categories', 'aora'),
                'type'               => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_categories_search' => 'yes'
                ]
            ]
        );
    }
    public function get_script_depends() {
        return ['jquery-sumoselect'];
    }
    public function get_style_depends() {
        return ['sumoselect'];
    }
    

    public function render_search_form() {
        $settings = $this->get_settings_for_display();
        extract($settings);
        
        $_id = aora_tbay_random_key();
        $class_active_ajax = ( aora_switcher_to_boolean($autocomplete_search) ) ? 'aora-ajax-search' : '';

        $this->add_render_attribute(
            'search_form',
            [
                'class' => [
                    $class_active_ajax,
                    'searchform'
                ],
                'data-thumbnail' => aora_switcher_to_boolean($show_image_search),
                'data-appendto' => '.search-results-'.$_id,
                'data-price' => aora_switcher_to_boolean($show_price_search),
                'data-minChars' => $min_characters_search['size'],
                'data-post-type' => $search_type,
                'data-count' => $search_max_number_results['size'],
            ]
        );
        ?>
            <div class="tbay-search-form">
                <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" <?php echo trim($this->get_render_attribute_string( 'search_form' )); ?> >
                    <div class="form-group">
                        <div class="input-group">
                            
                                <input data-style="right" type="text" placeholder="<?php echo esc_attr($placeholder_text); ?>" name="s" required oninvalid="this.setCustomValidity('<?php echo esc_attr($vali_input_search) ?>')" oninput="setCustomValidity('')" class="tbay-search form-control input-sm"/>
                                <?php if ( $enable_categories_search === 'yes' ): ?>
                                <div class="select-category input-group-addon">
                                    <?php if ( class_exists( 'WooCommerce' ) && $search_type === 'product' ) :
                                        $args = array(
                                            'show_option_none'   => $text_categories_search,
                                            'show_count' => aora_switcher_to_boolean($count_categories_search),
                                            'hierarchical' => true,
                                            'id' => 'product-cat-'.$_id,
                                            'show_uncategorized' => 0
                                        );
                                    ?> 
                                    <?php wc_product_dropdown_categories( $args ); ?>
                                    
                                    <?php elseif ( $search_type === 'post' ):
                                        $args = array(
                                            'show_option_all' => $text_categories_search,
                                            'show_count' => aora_switcher_to_boolean($count_categories_search),
                                            'hierarchical' => true,
                                            'show_uncategorized' => 0,
                                            'name' => 'category',
                                            'id' => 'blog-cat-'.$_id,
                                            'class' => 'postform dropdown_product_cat',
                                        );
                                    ?>
                                        <?php wp_dropdown_categories( $args ); ?>
                                    <?php endif; ?>

                                </div>
                            <?php endif; ?>
                                <div class="search-results-wrapper">
                                    <div class="aora-search-results search-results-<?php echo esc_attr( $_id );?>" ></div>
                                </div>
                                <div class="button-group input-group-addon">
                                    <button type="submit" class="button-search btn btn-sm>">
                                        <?php $this->render_item_icon($icon_button_search) ?>
                                        <?php if(!empty($text_button_search) && isset($text_button_search) ) {
                                            ?>
                                                <span class="text"><?php echo trim($text_button_search); ?></span>
                                            <?php
                                        } ?>
                                    </button>
                                    <div class="tbay-preloader"></div>
                                </div>

                                <input type="hidden" name="post_type" value="<?php echo esc_attr($search_type); ?>" class="post_type" />
                        </div>
                        
                    </div>
                </form>
            </div>
        <?php
    }
}
$widgets_manager->register_widget_type(new Aora_Elementor_Search_Form());

