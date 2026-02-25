<?php 
add_action( 'vc_before_init', 'dtlms_instructor_added_courses_vc_map' );

function dtlms_instructor_added_courses_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );

	vc_map( array(
		"name" => sprintf(esc_html__('%s Added Courses', 'dtlms'), $instructor_label),
		"base" => "dtlms_instructor_added_courses",
		"icon" => "dtlms_instructor_added_courses",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To list the instructor and their courses.', 'dtlms'),	
	) );

}
?>