<?php add_action( 'vc_before_init', 'dtportfolio_navigation_links_vc_map' );
function dtportfolio_navigation_links_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Navigation Links", 'dtportfolio' ),
		"base" => "dtportfolio_navigation_links",
		"icon" => "dtportfolio_navigation_links",
		"description" => esc_html__( 'Should be used only inside portfolio single page.', 'dtportfolio' ),
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

			// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Type', 'dtportfolio' ),
				'description' => esc_html__( 'Select type of gallery to list.', 'dtportfolio' ),
				'param_name' => 'type',
				'value' => array(
					esc_html__( 'Default', 'dtportfolio' ) => '',
					esc_html__( 'Type 2', 'dtportfolio' ) => 'type2',
					esc_html__( 'Type 3', 'dtportfolio' ) => 'type3',
					esc_html__( 'Type 4', 'dtportfolio' ) => 'type4',
				),
			),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtportfolio' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtportfolio' ),
			),

		)
	) );
}?>