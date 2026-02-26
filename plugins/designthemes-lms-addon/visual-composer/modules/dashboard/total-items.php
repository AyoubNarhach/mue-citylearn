<?php 
add_action( 'vc_before_init', 'dtlms_total_items_vc_map' );

function dtlms_total_items_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'plural' );
	$class_plural_label = apply_filters( 'class_label', 'plural' );

	vc_map( array(
		"name" => esc_html__( 'Total Items', 'dtlms' ),
		"base" => "dtlms_total_items",
		"icon" => "dtlms_total_items",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('It will be helpfull to display total items added in LMS.', 'dtlms'),
		"params" => array(

			// Item Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Item Type', 'dtlms'),
				'param_name' => 'item-type',
				'value' => array( 
							esc_html__('Default', 'dtlms') => '', 
							sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ) => 'classes', 
							esc_html__('Courses', 'dtlms') => 'courses', 
							esc_html__('Lessons', 'dtlms') => 'lessons', 
							esc_html__('Quizzes', 'dtlms') => 'quizzes', 
							esc_html__('Questions', 'dtlms') => 'questions', 
							esc_html__('Assignments', 'dtlms') => 'assignments',
							esc_html__('Packages', 'dtlms') => 'packages', 
						),
				'description' => sprintf( esc_html__( 'Choose item type to display its total items count. For %1$s total items added by them will be displayed by default.', 'dtlms' ), $instructor_label ),
				'admin_label' => true
			),

			// Item Title
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Item Title', 'dtlms' ),
				'param_name' => 'item-title',
				'description' => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
			),

			// Content Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Content Type', 'dtlms'),
				'param_name' => 'content-type',
				'value' => array( 
							esc_html__('All Items', 'dtlms') => 'all-items', 
							esc_html__('Individual Items', 'dtlms') => 'individual-items', 
						),
				'description' => esc_html__( 'If administrator wishes to see the items added by him / her or all items data. This option is applicable only for administrator.', 'dtlms' ),
			),				

		)
	) );

}
?>