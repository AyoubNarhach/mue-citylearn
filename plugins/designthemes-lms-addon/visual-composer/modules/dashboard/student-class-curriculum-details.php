<?php 
add_action( 'vc_before_init', 'dtlms_student_class_curriculum_details_vc_map' );

function dtlms_student_class_curriculum_details_vc_map() {

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => sprintf( esc_html__( 'Student %1$s Curriculum Details', 'dtlms' ), $class_singular_label ),
		"base" => "dtlms_student_class_curriculum_details",
		"icon" => "dtlms_student_class_curriculum_details",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => sprintf( esc_html__( 'To display student %1$s curriculum details.', 'dtlms' ), strtolower($class_singular_label) ),
	) );

}
?>