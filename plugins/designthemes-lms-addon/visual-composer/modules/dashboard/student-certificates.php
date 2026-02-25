<?php 
add_action( 'vc_before_init', 'dtlms_student_certificates_vc_map' );

function dtlms_student_certificates_vc_map() {

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => esc_html__( 'Student Certificates', 'dtlms' ),
		"base" => "dtlms_student_certificates",
		"icon" => "dtlms_student_certificates",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To display student achieved certificates.', 'dtlms'),
		"params" => array(

			// Item Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Item Type', 'dtlms'),
				'param_name' => 'item-type',
				'value' => array( 
							esc_html__('All', 'dtlms') => 'all', 
							sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ) => 'class', 
							esc_html__('Course', 'dtlms') => 'course', 
						),
				'description' => esc_html__( 'Choose item type to display its corresponding student achieved certificates.', 'dtlms' ),
				'admin_label' => true,
				'std' => 'course'
			),		

			// Include Registration Class
			array(
				'type' => 'dropdown',
				'heading' => sprintf( esc_html__( 'Include Registration %1$s', 'dtlms' ), $class_singular_label ),
				'param_name' => 'include-registration-class',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => sprintf( esc_html__( 'If you wish to include registration %1$s choose "True".', 'dtlms' ), strtolower($class_singular_label) ),
				'std' => '',
				'dependency' => array( 'element' => 'item-type', 'value' => 'class')			
			),							

		)		
	) );

}
?>