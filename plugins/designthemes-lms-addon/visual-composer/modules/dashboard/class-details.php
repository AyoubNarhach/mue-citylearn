<?php 
add_action( 'vc_before_init', 'dtlms_class_details_vc_map' );

function dtlms_class_details_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );
	$class_singular_label = apply_filters( 'class_label', 'singular' );
	$class_plural_label = apply_filters( 'class_label', 'plural' );

	vc_map( array(
		"name" => sprintf( esc_html__( '%1$s Details', 'dtlms' ), $class_singular_label ),
		"base" => "dtlms_class_details",
		"icon" => "dtlms_class_details",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => sprintf( esc_html__( 'To list overall details of %1$s', 'dtlms' ), $class_plural_label ),
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