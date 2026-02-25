<?php add_action( 'vc_before_init', 'dtportfolio_comment_form_vc_map' );
function dtportfolio_comment_form_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Comment Form", 'dtportfolio' ),
		"base" => "dtportfolio_comment_form",
		"icon" => "dtportfolio_comment_form",
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

			// Form Title
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Form Title', 'dtportfolio' ),
				'param_name' => 'form_title',
				'description' => esc_html__( 'If you wish you can provide form title here.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Form Comment Field Placeholder
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Form Comment Field Placeholder', 'dtportfolio' ),
				'param_name' => 'form_comment_field_placeholder',
				'description' => esc_html__( 'If you wish you can provide form comment field placeholder here.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Form Submit Button Label
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Form Submit Button Label', 'dtportfolio' ),
				'param_name' => 'form_submit_button_label',
				'description' => esc_html__( 'If you wish you can provide form submit button label here.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtportfolio' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',				
			),

		)
	) );
}?>