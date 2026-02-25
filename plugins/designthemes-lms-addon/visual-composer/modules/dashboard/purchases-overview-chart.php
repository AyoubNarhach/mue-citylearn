<?php 
add_action( 'vc_before_init', 'dtlms_purchases_overview_chart_vc_map' );

function dtlms_purchases_overview_chart_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );
	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => esc_html__( 'Purchases Overview Chart', 'dtlms' ),
		"base" => "dtlms_purchases_overview_chart",
		"icon" => "dtlms_purchases_overview_chart",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('Bar chart to show the purchases over daily, monthly and yearly.', 'dtlms'),
		"params" => array(

			// Chart Title
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Chart Title', 'dtlms' ),
				'param_name' => 'chart-title',
				'description' => esc_html__( 'You can give title for your chart here.', 'dtlms' ),
			),	

			// Include Class Purchases
			array(
				'type' => 'dropdown',
				'heading' => sprintf( esc_html__( 'Include %1$s Purchases', 'dtlms' ), $class_singular_label ),
				'param_name' => 'include-class-purchases',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => sprintf( esc_html__( 'If you wish you can include %1$s purchases in chart.', 'dtlms' ), strtolower($class_singular_label) ),
				'std' => ''				
			),

			// Include Course Purchases
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Course Purchases', 'dtlms'),
				'param_name' => 'include-course-purchases',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__('If you wish you can include course purchases in chart.', 'dtlms'),
				'std' => ''				
			),

			// Include Package Purchases
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Package Purchases', 'dtlms'),
				'param_name' => 'include-package-purchases',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__('If you wish you can include package purchases in chart.', 'dtlms'),
				'std' => ''				
			),								

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

			// Include Data
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Data', 'dtlms'),
				'param_name' => 'include-data',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__('If you wish you can include data along with this chart.', 'dtlms'),
				'std' => ''				
			),

	          // Set Unique Colors
	          array(
	                'type' => 'dropdown',
	                'heading' => esc_html__('Set Unique Colors', 'dtlms'),
	                'param_name' => 'set-unique-colors',
	                'value' => array( 
	                                  esc_html__('False', 'dtlms') => '',
	                                  esc_html__('True', 'dtlms') => 'true', 
	                            ),
	                'description' => esc_html__( 'If you like to set unique colors for your chart choose "True", else colors from "Chart Settings" will be used.', 'dtlms' ),
	          ),  

			// First Color
      		array(
      			'type' => 'colorpicker',
      			'heading' => esc_html__( 'First color', 'dtlms' ),
      			'param_name' => 'first-color',
      			'description' => esc_html__( 'Select first color for your chart', 'dtlms' ),
      			'dependency' => array( 'element' => 'set-unique-colors', 'value' => 'true'),
      		),

			// Second Color
      		array(
      			'type' => 'colorpicker',
      			'heading' => esc_html__( 'Second color', 'dtlms' ),
      			'param_name' => 'second-color',
      			'description' => esc_html__( 'Select second color for your chart', 'dtlms' ),
      			'dependency' => array( 'element' => 'set-unique-colors', 'value' => 'true'),
      		),

			// Third Color
      		array(
      			'type' => 'colorpicker',
      			'heading' => esc_html__( 'Third color', 'dtlms' ),
      			'param_name' => 'third-color',
      			'description' => esc_html__( 'Select third color for your chart', 'dtlms' ),
      			'dependency' => array( 'element' => 'set-unique-colors', 'value' => 'true'),
      		),      		      		

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtlms' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish to have additional class, you can add it here.', 'dtlms' ),
			),								

		)
	) );

}
?>