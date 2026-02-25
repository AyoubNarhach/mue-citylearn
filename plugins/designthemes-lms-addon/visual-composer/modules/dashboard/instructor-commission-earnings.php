<?php 
add_action( 'vc_before_init', 'dtlms_instructor_commission_earnings_vc_map' );

function dtlms_instructor_commission_earnings_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );
	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => sprintf(esc_html__('%s Commission Earnings', 'dtlms'), $instructor_label),
		"base" => "dtlms_instructor_commission_earnings",
		"icon" => "dtlms_instructor_commission_earnings",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => sprintf(esc_html__('Chart to show %s commissions earnings Over Period and Over Item.', 'dtlms'), $instructor_label),
		"params" => array(

			// Chart Title
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Chart Title', 'dtlms' ),
				'param_name' => 'chart-title',
				'description' => esc_html__( 'Give title for your chart.', 'dtlms' ),
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

			// Instructor Earnings
			array(
			    'type' => 'dropdown',
			    'heading' => esc_html__('Instructor Earnings', 'dtlms'),
			    'param_name' => 'instructor-earnings',
			    'value' => array( 
			                      esc_html__('Over Period', 'dtlms') => 'over-period',
			                      esc_html__('Over Item', 'dtlms') => 'over-item', 
			                ),
			    'description' => sprintf( esc_html__( 'You can choose between content over period ( daily, monthly, yearly ) and content over item ( Course Commisions, %1$s Commissions, Other Amounts, Total Commissions ).', 'dtlms' ), $class_singular_label ),
			    'std' => 'over-period',
			    'admin_label' => true
			), 

			// Content Filter
			array(
			    'type' => 'dropdown',
			    'heading' => esc_html__('Content Filter', 'dtlms'),
			    'param_name' => 'content-filter',
			    'value' => array( 
			                      esc_html__('Both', 'dtlms') => 'both',
			                      esc_html__('Chart', 'dtlms') => 'chart', 
			                      esc_html__('Data', 'dtlms') => 'data', 
			                ),
			    'description' => esc_html__( 'Would you like to show Chart or Data or Both ?', 'dtlms' ),
			    'std' => 'both'
			),

			// Chart Type
			array(
			    'type' => 'dropdown',
			    'heading' => esc_html__('Chart Type', 'dtlms'),
			    'param_name' => 'chart-type',
			    'value' => array( 
			                      esc_html__('Bar', 'dtlms') => 'bar', 
			                      esc_html__('Line', 'dtlms') => 'line', 
			                      esc_html__('Pie', 'dtlms') => 'pie',
			                ),
			    'description' => sprintf(esc_html__('Choose what type of chart to display. "Pie" chart will work only with "Over Item" - "%s Earnings"', 'dtlms'), $instructor_label),
			    'dependency' => array( 'element' => 'content-filter', 'value' => array ('both', 'chart')),
			    'std' => 'bar'
			), 

			// Timeline Filter
			array(
			    'type' => 'dropdown',
			    'heading' => esc_html__('Timeline Filter', 'dtlms'),
			    'param_name' => 'timeline-filter',
			    'value' => array( 
			                      esc_html__('All - With Filter', 'dtlms') => 'all',
			                      esc_html__('Monthly - Without Filter', 'dtlms') => 'daily', 
			                      esc_html__('Yearly - Without Filter', 'dtlms') => 'monthly', 
			                      esc_html__('All Time - Without Filter', 'dtlms') => 'alltime', 
			                ),
			    'description' => esc_html__( 'Choose timeline filter to use for content over item.', 'dtlms' ),
			    'dependency' => array( 'element' => 'instructor-earnings', 'value' => 'over-item')
			),  

			// Include Course Commission
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Course Commission', 'dtlms'),
				'param_name' => 'include-course-commission',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__('If you wish to include course commission amount in the chart.', 'dtlms'),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'true'				
			),

			// Include Class Commission
			array(
				'type' => 'dropdown',
				'heading' => sprintf( esc_html__( 'Include %1$s Commission', 'dtlms' ), $class_singular_label ),
				'param_name' => 'include-class-commission',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => sprintf( esc_html__( 'If you wish to include %1$s commission amount in the chart.', 'dtlms' ), strtolower($class_singular_label) ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Include Other Commission
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Other Commission', 'dtlms'),
				'param_name' => 'include-other-commission',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__('If you wish to include other commission amount in the chart.', 'dtlms'),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Include Total Commission
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Total Commission', 'dtlms'),
				'param_name' => 'include-total-commission',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__('If you wish to include total commission amount in the chart.', 'dtlms'),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
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