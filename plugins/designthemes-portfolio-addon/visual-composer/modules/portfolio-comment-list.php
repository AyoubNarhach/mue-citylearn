<?php add_action( 'vc_before_init', 'dtportfolio_comment_list_vc_map' );
function dtportfolio_comment_list_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Comment List", 'dtportfolio' ),
		"base" => "dtportfolio_comment_list",
		"icon" => "dtportfolio_comment_list",
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

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