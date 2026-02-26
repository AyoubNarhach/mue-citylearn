<?php add_action( 'vc_before_init', 'dtportfolio_custom_details_vc_map' );
function dtportfolio_custom_details_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Custom Details", 'dtportfolio' ),
		"base" => "dtportfolio_custom_details",
		"icon" => "dtportfolio_custom_details",
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

			// Portfolio ID
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Portfolio ID', 'dtportfolio' ),
				'param_name' => 'portfolio_id',
				'description' => esc_html__( 'Enter portfolio id here. If you are going to use this shortcode in portfolio single page no need to give portfolio id.', 'dtportfolio' ),
				'admin_label' => true
			),

			# Types
      		array(
      			'type' => 'dropdown',
      			'heading' => esc_html__( 'Types', 'dtportfolio' ),
      			'param_name' => 'type',
      			'value' => array( 
      				esc_html__('Default','dtportfolio') => '',		
      				esc_html__('Type 2','dtportfolio') => 'type2',
      				esc_html__('Type 3','dtportfolio') => 'type3',
      			),
      			'description' => esc_html__( 'Select the type of custom details.', 'dtportfolio' ),
      		),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtportfolio' ),
				'param_name' => 'class',
				'description' => esc_html__( 'Add additional class name here.', 'dtportfolio' ),
			),

		)
	) );
}?>