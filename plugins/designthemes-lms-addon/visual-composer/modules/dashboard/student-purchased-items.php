<?php 
add_action( 'vc_before_init', 'dtlms_student_purchased_items_vc_map' );

function dtlms_student_purchased_items_vc_map() {

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => esc_html__( 'Student Purchased Items', 'dtlms' ),
		"base" => "dtlms_student_purchased_items",
		"icon" => "dtlms_student_purchased_items",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To display student purchased items.', 'dtlms'),
		"params" => array(

			// Item Title
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Item Title', 'dtlms' ),
				'param_name' => 'item-title',
				'description' => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
			),	

			// Item Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Item Type', 'dtlms'),
				'param_name' => 'item-type',
				'value' => array( 
							esc_html__('None', 'dtlms') => '', 
							sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ) => 'class', 
							esc_html__('Course', 'dtlms') => 'course', 
							esc_html__('Package', 'dtlms') => 'package', 
						),
				'description' => esc_html__( 'Choose item type to display its purchased list.', 'dtlms' ),
				'admin_label' => true
			),						

		)
	) );

}
?>