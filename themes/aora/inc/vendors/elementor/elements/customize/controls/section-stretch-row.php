<?php

if ( !function_exists('aora_section_stretch_row')) {
    function aora_section_stretch_row( $widget ) {

        $widget->start_controls_section(
            'section_stretch_row',
            [
                'label' => esc_html__( 'Stretch Row', 'aora' ),
                'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED, 
                 'condition' => [
                    'stretch_section!' => '',
                ],
            ]
        );
  
        $widget->add_responsive_control(
            'section_stretch_margin', 
            [
                'label' => esc_html__( 'Margin', 'aora' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],  
                'selectors' => [
                    '{{WRAPPER}} >.elementor-container > div' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );        

        $widget->add_responsive_control(
            'section_stretch_padding', 
            [
                'label' => esc_html__( 'Padding', 'aora' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],  
                'selectors' => [
                    '{{WRAPPER}} >.elementor-container > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
 
        $widget->end_controls_section();  

    }

    add_action( 'elementor/element/section/section_effects/before_section_start', 'aora_section_stretch_row', 10, 1 );
}

