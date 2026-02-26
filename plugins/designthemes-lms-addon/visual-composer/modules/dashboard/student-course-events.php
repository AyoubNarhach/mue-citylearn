<?php 
add_action( 'vc_before_init', 'dtlms_student_course_events_vc_map' );

function dtlms_student_course_events_vc_map() {

	vc_map( array(
		"name" => esc_html__( 'Student Course Events', 'dtlms' ),
		"base" => "dtlms_student_course_events",
		"icon" => "dtlms_student_course_events",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To display student course events.', 'dtlms'),
	) );

}
?>