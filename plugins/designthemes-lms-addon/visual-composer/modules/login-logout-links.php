<?php 
add_action( 'vc_before_init', 'dtlms_login_logout_links_vc_map' );

function dtlms_login_logout_links_vc_map() {
	vc_map( array(
		"name" => esc_html__( 'Login / Logout Links', 'dtlms' ),
		"base" => "dtlms_login_logout_links",
		"icon" => "dtlms_login_logout_links",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			// Show Registration Link
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Show Registration Link','dtlms'),
				'param_name' => 'show_registration',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable regsitration link here.', 'dtlms' ),
				'std' => 'true'				
			),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtlms' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
				'admin_label' => true
			),

		)
	) );
}
?>