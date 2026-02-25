<?php 
add_action( 'vc_before_init', 'dtlms_student_course_curriculum_details_vc_map' );

function dtlms_student_course_curriculum_details_vc_map() {

	vc_map( array(
		"name" => esc_html__( 'Student Course Curriculum Details', 'dtlms' ),
		"base" => "dtlms_student_course_curriculum_details",
		"icon" => "dtlms_student_course_curriculum_details",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To display student course curriculum details.', 'dtlms'),
	) );

}
?>