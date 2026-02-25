<?php 
add_action( 'vc_before_init', 'dtlms_instructor_list_vc_map' );

function dtlms_instructor_list_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );
	$instructor_plural_label = apply_filters( 'instructor_label', 'plural' );

	vc_map( array(
		"name" => sprintf(esc_html__('%s List', 'dtlms'), $instructor_plural_label),
		"base" => "dtlms_instructor_list",
		"icon" => "dtlms_instructor_list",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Type','dtlms'),
				'param_name' => 'type',
				'value' => array(
					esc_html__('Type 1', 'dtlms') => 'type1',
					esc_html__('Type 2', 'dtlms') => 'type2',
					esc_html__('Type 3', 'dtlms') => 'type3',
					esc_html__('Type 4', 'dtlms') => 'type4',
					esc_html__('Type 5', 'dtlms') => 'type5',
					esc_html__('Type 6', 'dtlms') => 'type6',
					esc_html__('Type 7', 'dtlms') => 'type7',
					esc_html__('Type 8', 'dtlms') => 'type8',
					esc_html__('Type 9', 'dtlms') => 'type9',
					esc_html__('Type 10', 'dtlms') => 'type10',									
				),
				'description' => sprintf(esc_html__('Choose type for your %s list', 'dtlms'), $instructor_plural_label),
				'std' => 'type1',
				'admin_label' => true			
			),

			// Image Types
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Image Types','dtlms'),
				'param_name' => 'image-types',
				'value' => array(
					esc_html__('Default', 'dtlms') => '',
					esc_html__('Default With Border', 'dtlms') => 'with-border',
					esc_html__('Rounded', 'dtlms') => 'rounded',
					esc_html__('Rounded With Border', 'dtlms') => 'rounded-with-border',
				),
				'description' => sprintf(esc_html__('Choose %s image type here.', 'dtlms'), $instructor_plural_label),
				//'dependency' => array( 'element' => 'type', 'value' => array ('type1', 'type2', 'type3', 'type4')),	
				'std' => '',
			),			

			// Social Icon Types
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Social Icon Types','dtlms'),
				'param_name' => 'social-icon-types',
				'value' => array(
					esc_html__('Default', 'dtlms') => 'default',
					esc_html__('Vibrant', 'dtlms') => 'vibrant',
					esc_html__('With Background', 'dtlms') => 'with-bg',
				),
				'description' => esc_html__('Choose social icon types here.', 'dtlms'),
				'std' => 'default',
			),						

			// Columns
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Columns', 'dtlms'),
				'param_name' => 'columns',
				'value' => array( 
							esc_html__('None', 'dtlms') => '' ,
							esc_html__('I Column', 'dtlms') => 1 ,
							esc_html__('II Columns', 'dtlms') => 2 ,
							esc_html__('III Columns', 'dtlms') => 3,
							esc_html__('IV Columns', 'dtlms') => 4
						),
				'description' => sprintf(esc_html__('Number of columns you like to display your %s. "IV Columns" option is applicable only for "Type 2".', 'dtlms'), $instructor_label),
				'std' => ''
			),

			// Include
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Include', 'dtlms' ),
				'param_name' => 'include',
				'description' => sprintf(esc_html__('List of %s ids separated by comma.', 'dtlms'), $instructor_label),				
			),

			// Number Of Users
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Number Of Users', 'dtlms' ),
				'param_name' => 'number',
				'description' => sprintf(esc_html__('Number of %s to display.', 'dtlms'), $instructor_label),				
			),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtlms' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),				
			),

		)
	) );
}
?>