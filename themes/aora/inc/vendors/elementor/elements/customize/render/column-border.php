<?php

if ( !function_exists('aora_render_element_child_border')) {
    function aora_render_element_child_border( $widget ) {

        $settings = $widget->get_settings_for_display();
        if( !isset($settings['enable_element_child_border']) ) return;

        if( $settings['enable_element_child_border'] === '' ) return;

        $widget->add_render_attribute('_inner_wrapper', 'class', 'column-element-child-border');
    }

    add_action('elementor/frontend/column/before_render', 'aora_render_element_child_border', 10, 2 );
}

