<?php 
add_action( 'vc_before_init', 'dtlms_certificate_vc_map' );

function dtlms_certificate_vc_map() {

	vc_map( array(
		"name" => esc_html__( 'Certificate', 'dtlms' ),
		"base" => "dtlms_certificate",
		"icon" => "dtlms_certificate",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			/*// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Type','dtlms'),
				'param_name' => 'type',
				'value' => array ( 
							esc_html__('Type 1','dtlms') => 'type1',
							esc_html__('Type 2','dtlms') => 'type2',
							esc_html__('Type 3','dtlms') => 'type3',
						),
				'std' => 'type1'
			),*/	

			/*// Logo 1
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Logo 1','dtlms'),
				'param_name' => 'logo1',
			),

			// Logo 2
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Logo 2','dtlms'),
				'param_name' => 'logo2',
			),*/

			// Heading
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Heading', 'dtlms' ),
				'param_name' => 'heading',
			),

			// Sub Heading
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Sub Heading', 'dtlms' ),
				'param_name' => 'subheading',
			),

			// Footer Logo
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Footer Logo','dtlms'),
				'param_name' => 'footer_logo',
			),

			// Signature
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Signature','dtlms'),
				'param_name' => 'signature',
			),

			// Content
			array(
				'type' => 'textarea',
				'heading' => esc_html__('Content','dtlms'),
				'param_name' => 'content',
			),						

		)
	) );

}
?>