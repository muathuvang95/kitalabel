<?php
if ( !function_exists('aora_tbay_page_metaboxes') ) {
    function aora_tbay_page_metaboxes(){
        $sidebars = aora_sidebars_array();

        $footers = array_merge( array('global' => esc_html__( 'Global Setting', 'aora' )), aora_tbay_get_footer_layouts() );
        $headers = array_merge( array('global' => esc_html__( 'Global Setting', 'aora' )), aora_tbay_get_header_layouts() );

		$prefix = 'tbay_page_';

        $cmb2 = new_cmb2_box( array(
			'id'                        => $prefix . 'display_setting',
			'title'                     => esc_html__( 'Display Settings', 'aora' ),
			'object_types'              => array( 'page' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
        ) );

        
        $cmb2->add_field( array(
            'name' => esc_html__( 'Select Layout', 'aora' ),
            'id'   => $prefix.'layout',
            'type' => 'select',
            'options' => array(
                'main' => esc_html__('Main Content Only', 'aora'),
                'left-main' => esc_html__('Left Sidebar - Main Content', 'aora'),
                'main-right' => esc_html__('Main Content - Right Sidebar', 'aora'),
            )
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'left_sidebar',
            'type' => 'select',
            'name' => esc_html__('Left Sidebar', 'aora'),
            'options' => $sidebars
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'right_sidebar',
            'type' => 'select',
            'name' => esc_html__('Right Sidebar', 'aora'),
            'options' => $sidebars
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'show_breadcrumb',
            'type' => 'select',
            'name' => esc_html__('Show Breadcrumb?', 'aora'),
            'options' => array(
                'no' => esc_html__('No', 'aora'),
                'yes' => esc_html__('Yes', 'aora')
            ),
            'default' => 'yes',
        ) );

        $cmb2->add_field( array(
            'name' => esc_html__( 'Select Breadcrumbs Layout', 'aora' ),
            'id'   => $prefix.'breadcrumbs_layout',
            'type' => 'select',
            'options' => array(
                'image' => esc_html__('Background Image', 'aora'),
                'color' => esc_html__('Background color', 'aora'),
                'text' => esc_html__('Just text', 'aora')
            ),
            'default' => 'text',
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'breadcrumb_color',
            'type' => 'colorpicker',
            'name' => esc_html__('Breadcrumb Background Color', 'aora')
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'breadcrumb_image',
            'type' => 'file',
            'name' => esc_html__('Breadcrumb Background Image', 'aora')
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'header_type',
            'type' => 'select', 
            'name' => esc_html__('Header Layout Type', 'aora'),
            'description' => esc_html__('Choose a header for your website.', 'aora'),
            'options' => $headers,
            'default' => 'global'
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'footer_type',
            'type' => 'select',
            'name' => esc_html__('Footer Layout Type', 'aora'),
            'description' => esc_html__('Choose a footer for your website.', 'aora'),
            'options' => $footers,
            'default' => 'global'
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'header_type',
            'type' => 'select', 
            'name' => esc_html__('Header Layout Type', 'aora'),
            'description' => esc_html__('Choose a header for your website.', 'aora'),
            'options' => $headers,
            'default' => 'global'
        ) );

        $cmb2->add_field( array(
            'id' => $prefix.'extra_class',
            'type' => 'text',
            'name' => esc_html__('Extra Class', 'aora'),
            'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aora')
        ) );

    }
    add_action( 'cmb2_admin_init', 'aora_tbay_page_metaboxes', 10 );
}

if( !function_exists( 'aora_tbay_cmb2_style' ) ) {
	function aora_tbay_cmb2_style() {
		wp_enqueue_style( 'aora-cmb2', AORA_THEME_DIR . '/inc/vendors/cmb2/assets/cmb2.css', array(), '1.0' );
	}
    add_action( 'admin_enqueue_scripts', 'aora_tbay_cmb2_style', 10 );
}
