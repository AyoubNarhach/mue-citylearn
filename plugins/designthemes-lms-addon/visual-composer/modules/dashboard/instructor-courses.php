<?php 
add_action( 'vc_before_init', 'dtlms_instructor_courses_vc_map' );

function dtlms_instructor_courses_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );
	
	vc_map( array(
		"name" => sprintf(esc_html__('%s Courses', 'dtlms'), $instructor_label),
		"base" => "dtlms_instructor_courses",
		"icon" => "dtlms_instructor_courses",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To list the courses instructed by respective user.', 'dtlms'),
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

		)		
	) );

}
?>