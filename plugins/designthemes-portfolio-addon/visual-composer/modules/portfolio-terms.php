<?php add_action( 'vc_before_init', 'dtportfolio_terms_vc_map' );
function dtportfolio_terms_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Terms", 'dtportfolio' ),
		"base" => "dtportfolio_terms",
		"icon" => "dtportfolio_terms",
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

			// With Icon
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('With Icon','dtportfolio'),
				'param_name' => 'with_icon',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
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