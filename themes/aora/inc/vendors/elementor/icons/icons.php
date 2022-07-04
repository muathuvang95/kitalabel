<?php

if ( ! function_exists( 'aora_elementor_icon_control_simple_line_icons' ) ) {
	add_action( 'elementor/icons_manager/additional_tabs', 'aora_elementor_icon_control_simple_line_icons' );
	function aora_elementor_icon_control_simple_line_icons( $tabs ) {
		$tabs['simple-line-icons'] = [
			'name'          => 'simple-line-icons',
			'label'         => esc_html__( 'Simple Line Icons', 'aora' ),
			'prefix'        => 'icon-',
			'displayPrefix' => 'icon-',
			'labelIcon'     => 'fa fa-font-awesome',
			'ver'           => '2.4.0',
			'fetchJson'     => get_template_directory_uri() . '/inc/vendors/elementor/icons/json/simple-line-icons.json', 
			'native'        => true,
		];

		return $tabs;
	}
}

if ( ! function_exists( 'aora_elementor_icon_control_material_design_iconic' ) ) {
	add_action( 'elementor/icons_manager/additional_tabs', 'aora_elementor_icon_control_material_design_iconic' );
	function aora_elementor_icon_control_material_design_iconic( $tabs ) {
		$tabs['material-design-iconic'] = [
			'name'          => 'material-design-iconic',
			'label'         => esc_html__( 'Material Design Iconic', 'aora' ),
			'prefix'        => 'zmdi-',
			'displayPrefix' => 'zmdi',
			'labelIcon'     => 'fa fa-font-awesome',
			'ver'           => '2.2.0',
			'fetchJson'     => get_template_directory_uri() . '/inc/vendors/elementor/icons/json/material-design-iconic.json', 
			'native'        => true,
		];

		return $tabs;
	}
}


if ( ! function_exists( 'aora_elementor_icon_control_tbay_custom' ) ) {
	add_action( 'elementor/icons_manager/additional_tabs', 'aora_elementor_icon_control_tbay_custom' );
	function aora_elementor_icon_control_tbay_custom( $tabs ) {
		$tabs['tbay-custom'] = [
			'name'          => 'tbay-custom',
			'label'         => esc_html__( 'Thembay Custom', 'aora' ),
			'prefix'        => 'tb-icon-',
			'displayPrefix' => 'tb-icon',
			'labelIcon'     => 'fa fa-font-awesome',
			'ver'           => '1.0.0',
			'fetchJson'     => get_template_directory_uri() . '/inc/vendors/elementor/icons/json/tbay-custom.json', 
			'native'        => true,
		];

		return $tabs;
	}
}