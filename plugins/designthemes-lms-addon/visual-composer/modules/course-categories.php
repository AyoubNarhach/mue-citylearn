<?php 
add_action( 'vc_before_init', 'dtlms_course_categories_vc_map' );

function dtlms_course_categories_vc_map() {

	vc_map( array(
		"name" => esc_html__( 'Course Categories', 'dtlms' ),
		"base" => "dtlms_course_categories",
		"icon" => "dtlms_course_categories",
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
				'description' => esc_html__( 'Choose type of course category to display.', 'dtlms' ),
				'std' => '',
				'admin_label' => true			
			),

			// Columns
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Columns', 'dtlms'),
				'param_name' => 'columns',
				'value' => array( 
							esc_html__('I Column', 'dtlms') => 1 ,
							esc_html__('II Columns', 'dtlms') => 2 ,
							esc_html__('III Columns', 'dtlms') => 3,
						),
				'description' => esc_html__( 'Number of columns you like to display your course categories.', 'dtlms' ),
				'std' => ''
			),

			// Include
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Include', 'dtlms' ),
				'param_name' => 'include',
				'description' => esc_html__( 'List of category ids separated by commas.', 'dtlms' ),				
			),

			// Use Icon Image
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Use Icon Image','dtlms'),
				'param_name' => 'use-icon-image',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can use icon image instead of icon.', 'dtlms' ),		
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