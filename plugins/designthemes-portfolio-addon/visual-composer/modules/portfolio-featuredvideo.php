<?php add_action( 'vc_before_init', 'dtportfolio_feature_video_vc_map' );
function dtportfolio_feature_video_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Featured Video", 'dtportfolio' ),
		"base" => "dtportfolio_feature_video",
		"icon" => "dtportfolio_feature_video",
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