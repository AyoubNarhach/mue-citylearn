<?php 
add_action( 'vc_before_init', 'dtlms_instructor_commissions_vc_map' );

function dtlms_instructor_commissions_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );
	$class_singular_label = apply_filters( 'class_label', 'singular' );
	
	vc_map( array(
		"name" => sprintf(esc_html__( '%s Commissions', 'dtlms' ), $instructor_label),
		"base" => "dtlms_instructor_commissions",
		"icon" => "dtlms_instructor_commissions",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => sprintf(esc_html__('To display the commission details of %s.', 'dtlms'), $instructor_label),
		"params" => array(

			// Enable Instructor Filter
			array(
				'type' => 'dropdown',
				'heading' => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_label),
				'param_name' => 'enable-instructor-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => sprintf(esc_html__('If you wish you can enable %s filter option. This option is applicable only for administrator.', 'dtlms'), $instructor_label),
				'std' => ''				
			),

			// Content
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Content', 'dtlms'),
				'param_name' => 'commission-content',
				'value' => array(
					esc_html__('Course', 'dtlms') => 'course',
					sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ) => 'class',
				),
				'description' => esc_html__('Choose content you like to display.', 'dtlms'),
				'std' => 'course'				
			),						

		)		
	) );

}
?>