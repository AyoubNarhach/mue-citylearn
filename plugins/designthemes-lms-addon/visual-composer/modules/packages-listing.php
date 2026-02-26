<?php 
add_action( 'vc_before_init', 'dtlms_packages_listing_vc_map' );

function dtlms_packages_listing_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );

	vc_map( array(
		"name" => esc_html__( 'Packages Listing', 'dtlms' ),
		"base" => "dtlms_packages_listing",
		"icon" => "dtlms_packages_listing",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			// Display Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Display Type','dtlms'),
				'param_name' => 'display-type',
				'value' => array(
					esc_html__('Grid', 'dtlms') => 'grid',
					esc_html__('List', 'dtlms') => 'list',
				),				
				'description' => esc_html__( 'Choose display type for your packages listing.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'grid'				
			),		

			// Post Per Page
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Post Per Page', 'dtlms' ),
				'param_name' => 'post-per-page',
				'description' => esc_html__( 'Number of posts to show.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Columns
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Columns', 'dtlms'),
				'param_name' => 'columns',
				'value' => array( 
							esc_html__('I Column', 'dtlms') => 1 ,
							esc_html__('II Columns', 'dtlms') => 2 ,
							esc_html__('III Columns', 'dtlms') => 3,
						),
				'description' => esc_html__( 'Number of columns you like to display your packages.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 1,
				'dependency' => array( 'element' => 'display-type', 'value' => 'grid'),	
			),

			// Apply Isotope
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Apply Isotope','dtlms'),
				'param_name' => 'apply-isotope',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you like to apply isotope for your packages listing, choose "True".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Type','dtlms'),
				'param_name' => 'type',
				'value' => array(
					esc_html__('Type 1', 'dtlms') => 'type1',
					esc_html__('Type 2', 'dtlms') => 'type2',
					esc_html__('Type 3', 'dtlms') => 'type3',
				),
				'description' => esc_html__( 'Choose any of the available design types.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'type1'				
			),

			// Package Item Ids
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Package Item Ids','dtlms'),
				'param_name' => 'package-item-ids',
				'value' => '',
				'description' => esc_html__( 'Enter package item ids separated by comma to display from.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Carousel Options

			// Enable Carousel
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Carousel','dtlms'),
				'param_name' => 'enable-carousel',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can enable carousel for package listings.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'apply-isotope', 'value' => 'false'),	
				'std' => ''
			),

			/*// Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Effect', 'dtlms'),
				'param_name' => 'carousel-effect',
				'value' => array( 
							esc_html__('Default', 'dtlms') => '', 
							esc_html__('Fade', 'dtlms') => 'fade', 
						),
				'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''
			),*/

			// Auto Play
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Auto Play', 'dtlms'),
				'param_name' => 'carousel-autoplay',
				'description' => esc_html__( 'Delay between transitions ( in ms, ex. 1000 ). Leave empty if you don\'t want to auto play.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
			),

			// Slides Per View
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Slides Per View','dtlms'),
				'param_name' => 'carousel-slidesperview',
				'value' => array( 
							1 => 1, 
							2 => 2, 
							3 => 3, 
						),
				'description' => esc_html__( 'Number slides of to show in view port. If display type is "List", 2 & 3 option in "Slides Per View" won\'t work.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => 2
			),	

			// Enable loop mode
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Loop Mode','dtlms'),
				'param_name' => 'carousel-loopmode',
				'value' => array(
					esc_html__('False','dtlms') => 'false',
					esc_html__('True','dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),	

			// Enable mousewheel control
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Mousewheel Control', 'dtlms'),
				'param_name' => 'carousel-mousewheelcontrol',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),	

			// Enable Bullet Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Bullet Pagination', 'dtlms'),
				'param_name' => 'carousel-bulletpagination',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'To enable bullet pagination.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),

			// Enable Arrow Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow Pagination', 'dtlms'),
				'param_name' => 'carousel-arrowpagination',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'To enable arrow pagination.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),

			// Space Between Sliders
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Space Between Sliders','dtlms'),
				'param_name' => 'carousel-spacebetween',
				'description' => esc_html__( 'Space between sliders can be given here.', 'dtlms' ),	
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
			),								

		)
	) );
}
?>