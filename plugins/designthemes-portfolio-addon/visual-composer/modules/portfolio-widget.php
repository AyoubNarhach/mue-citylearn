<?php add_action( 'vc_before_init', 'dtportfolio_widget_vc_map' );
function dtportfolio_widget_vc_map() {
	vc_map( array(
		"name"     => esc_html__( "Portfolio Widget", 'dtportfolio' ),
		"base"     => "dtportfolio_widget",
		"icon"     => "dtportfolio_widget",
		"category" => __( 'WordPress Widgets', 'dtportfolio' ),
		"params"   => array(

	        # ID
	        array(
				'type'             => 'el_id',
				'param_name'       => 'el_id',
				'edit_field_class' => 'hidden',
				'settings'         => array(
	                'auto_generate' => true,
	            )
	        ),

	        # Title
	        array(
				'type'        => 'textfield',
				'heading'     => __( 'Widget title', 'dtportfolio' ),
				'save_always' => true,
				'param_name'  => 'title',
				'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'dtportfolio' ),
	        ),        

	        # Category
	        array(
				'type'        => 'autocomplete',
				'save_always' => true,
				'heading'     => __( 'Categories', 'dtportfolio'),
				'param_name'  => '_post_categories',
				'settings'    => array( 'multiple' => true ),
				'description' => esc_html__('Choose the categories you want to display (multiple selection possible)','dtportfolio')
	        ),

	        # Portfolio Count
	        array(
				'type'             => 'dt_sc_input_number',
				'heading'          => __( 'Portfolio Count', 'dtportfolio' ),
				'param_name'       => '_post_count',
				'min'              => '1',
				'max'              => '30',
				'step'             => '1',
				'std'              => '3',
				'save_always'      => true,
				'edit_field_class' => 'vc_column vc_col-sm-6',
	        ),
		)
	) );
}?>