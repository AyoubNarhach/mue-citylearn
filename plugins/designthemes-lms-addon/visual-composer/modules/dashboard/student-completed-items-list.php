<?php 
add_action( 'vc_before_init', 'dtlms_student_completed_items_list_vc_map' );

function dtlms_student_completed_items_list_vc_map() {

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => esc_html__( 'Student Completed Items List', 'dtlms' ),
		"base" => "dtlms_student_completed_items_list",
		"icon" => "dtlms_student_completed_items_list",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To display student completed items list.', 'dtlms'),
		"params" => array(

			// Item Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Item Type', 'dtlms'),
				'param_name' => 'item-type',
				'value' => array( 
							esc_html__('None', 'dtlms') => '', 
							sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ) => 'class', 
							esc_html__('Course', 'dtlms') => 'course', 
						),
				'description' => esc_html__( 'Choose item type to display its completed list.', 'dtlms' ),
				'admin_label' => true
			),						

		)		
	) );

}
?>