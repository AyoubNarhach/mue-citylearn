<?php add_action( 'vc_before_init', 'dtportfolio_onepage_navigation_title_holder_vc_map' );
function dtportfolio_onepage_navigation_title_holder_vc_map() {

	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Onepage Navigation Title Holder", 'dtportfolio' ),
		"base" => "dtportfolio_onepage_navigation_title_holder",
		"icon" => "dtportfolio_onepage_navigation_title_holder",
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

			// Navigation IDs
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Navigation IDs', 'dtportfolio' ),
				'param_name' => 'navigation_ids',
				'description' => esc_html__( 'Enter navigation ids separated by commas.', 'dtportfolio' ),
				'admin_label' => true
			),

			// Navigation Titles
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Navigation Titles', 'dtportfolio' ),
				'param_name' => 'navigation_titles',
				'description' => esc_html__( 'Enter navigation titles separated by commas.', 'dtportfolio' ),
				'admin_label' => true
			),			

			// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Type','dtportfolio'),
				'param_name' => 'type',
				'value' => array( 
							esc_html__('Default','dtportfolio') => '', 
							esc_html__('Boxed','dtportfolio') => 'boxed', 
							esc_html__('Rounded','dtportfolio') => 'rounded', 
						),
				'std' => ''
			),

			// Position
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Position','dtportfolio'),
				'param_name' => 'position',
				'value' => array( 
							esc_html__('Left','dtportfolio') => 'left', 
							esc_html__('Right','dtportfolio') => 'right', 
							esc_html__('Bottom Left','dtportfolio') => 'bottom-left', 
							esc_html__('Bottom Right','dtportfolio') => 'bottom-right', 
						),
				'std' => 'left'
			),	
			
		)
	) );

}
?>