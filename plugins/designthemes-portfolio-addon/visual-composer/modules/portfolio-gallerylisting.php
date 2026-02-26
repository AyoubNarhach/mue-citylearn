<?php add_action( 'vc_before_init', 'dtportfolio_gallery_listing_vc_map' );
function dtportfolio_gallery_listing_vc_map() {

	global $dtportfolio_animationtypes;

	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Gallery Listings", 'dtportfolio' ),
		"base" => "dtportfolio_gallery_listing",
		"icon" => "dtportfolio_gallery_listing",
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

			// Portfolio ID
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Portfolio ID', 'dtportfolio' ),
				'param_name' => 'portfolio_id',
				'description' => esc_html__( 'Enter portfolio id here. If you are going to use this shortcode in portfolio single page no need to give portfolio id.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'admin_label' => true
			),

			// Gallery ID(s)
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Gallery ID', 'dtportfolio' ),
				'param_name' => 'gallery_ids',
				'description' => esc_html__( 'Comma separated gallery item ids to display particular gallery items only.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'admin_label' => true
			),			
			
			// Post Layout
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Post Layout','dtportfolio'),
				'param_name' => 'portfolio-post-layout',
				'value' => array( 
							esc_html__('I Column','dtportfolio') => 'dtportfolio-one-column' ,
							esc_html__('II Columns','dtportfolio') => 'dtportfolio-one-half-column' ,
							esc_html__('III Columns','dtportfolio') => 'dtportfolio-one-third-column',
							esc_html__('IV Columns','dtportfolio') => 'dtportfolio-one-fourth-column',
							esc_html__('V Columns','dtportfolio') => 'dtportfolio-one-fifth-column',
							esc_html__('VI Columns','dtportfolio') => 'dtportfolio-one-sixth-column',
							esc_html__('VII Columns','dtportfolio') => 'dtportfolio-one-seventh-column',
							esc_html__('VIII Columns','dtportfolio') => 'dtportfolio-one-eight-column',
							esc_html__('IX Columns','dtportfolio') => 'dtportfolio-one-nineth-column',
							esc_html__('X Columns','dtportfolio') => 'dtportfolio-one-tenth-column',
						),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'dtportfolio-one-half-column'
			),	

			// Grid Space
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Grid Space','dtportfolio'),
				'param_name' => 'portfolio-grid-space',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Hover Design
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hover Design','dtportfolio'),
				'param_name' => 'portfolio-hover-design',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),			

			// Animation Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Animation Effect', 'dtportfolio' ),
				'param_name' => 'animationeffect',
				'value' => $dtportfolio_animationtypes,
				'edit_field_class' => 'vc_column vc_col-sm-6',
				),

			// Animation Delay
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Animation Delay', 'dtportfolio' ),
				'param_name' => 'animationdelay',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				),

			// Repeat Animation
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Repeat Animation','dtportfolio'),
				'param_name' => 'repeat-animation',
				'value' => array( 
							esc_html__('False','dtportfolio') => '',
							esc_html__('True','dtportfolio') => 'true',
						),
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

}
?>